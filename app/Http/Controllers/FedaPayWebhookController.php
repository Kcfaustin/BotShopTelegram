<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\DigitalProductFulfillment;
use App\Services\FedaPayClient;
use App\Services\TelegramBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FedaPayWebhookController extends Controller
{
    public function handle(
        Request $request,
        FedaPayClient $fedapay,
        TelegramBot $bot,
        DigitalProductFulfillment $fulfillment
    )
    {
        $payload = $request->getContent();
        $signature = $request->header('X-FedaPay-Signature');
        $expectedSignature = $fedapay->computeExpectedSignature($signature, $payload);
        $secret = config('fedapay.webhook_secret');

        if (!$fedapay->verifyWebhook($signature, $payload)) {
            Log::warning('fedapay.invalid_signature', [
                'received_signature' => $signature,
                'expected_signature' => $expectedSignature,
                'has_secret' => (bool) $secret,
            ]);
            return response()->json(['message' => 'invalid signature'], 400);
        }

        $payloadArray = $request->all();

        $orderReference = data_get($payloadArray, 'metadata.order_reference')
            ?? data_get($payloadArray, 'data.metadata.order_reference')
            ?? data_get($payloadArray, 'entity.metadata.order_reference');

        $fedapayReference = data_get($payloadArray, 'data.reference')
            ?? data_get($payloadArray, 'entity.reference')
            ?? data_get($payloadArray, 'data.transaction.reference')
            ?? data_get($payloadArray, 'transaction.reference');

        $fedapayTransactionId = data_get($payloadArray, 'data.id')
            ?? data_get($payloadArray, 'entity.id')
            ?? data_get($payloadArray, 'transaction.id');

        if (!$orderReference && !$fedapayReference && !$fedapayTransactionId) {
            Log::warning('fedapay.missing_reference', ['payload' => $payloadArray]);
            return response()->json(['message' => 'missing reference'], 400);
        }

        $order = null;

        if ($orderReference) {
            $order = Order::where('reference', $orderReference)->first();
        }

        if (!$order && $fedapayReference) {
            $order = Order::where(function ($query) use ($fedapayReference) {
                $query->where('fedapay_reference', $fedapayReference)
                    ->orWhere('reference', $fedapayReference)
                    ->orWhere('fedapay_transaction_id', $fedapayReference);
            })->first();
        }

        if (!$order && $fedapayTransactionId) {
            $order = Order::where(function ($query) use ($fedapayTransactionId) {
                $query->where('fedapay_transaction_id', $fedapayTransactionId)
                    ->orWhere('fedapay_reference', $fedapayTransactionId)
                    ->orWhere('reference', $fedapayTransactionId);
            })->first();
        }

        if (!$order) {
            $order = Order::whereNull('fedapay_reference')
                ->whereNotNull('payment_payload')
                ->where('status', Order::STATUS_PENDING)
                ->get()
                ->first(function (Order $pendingOrder) use ($fedapayReference, $fedapayTransactionId) {
                    $payload = $pendingOrder->payment_payload
                        ? json_encode($pendingOrder->payment_payload)
                        : '';

                    return ($fedapayReference && str_contains($payload, $fedapayReference))
                        || ($fedapayTransactionId && str_contains($payload, (string) $fedapayTransactionId));
                });

            if ($order) {
                Log::info('fedapay.order_matched_via_payload', [
                    'order_id' => $order->id,
                    'fedapay_reference' => $fedapayReference,
                    'fedapay_transaction_id' => $fedapayTransactionId,
                ]);
            }
        }

        if (!$order) {
            Log::warning('fedapay.order_not_found', [
                'order_reference' => $orderReference,
                'fedapay_reference' => $fedapayReference,
                'fedapay_transaction_id' => $fedapayTransactionId,
            ]);
            return response()->json(['message' => 'order not found'], 404);
        }

        $updates = [];

        if ($fedapayReference && $order->fedapay_reference !== $fedapayReference) {
            $updates['fedapay_reference'] = $fedapayReference;
        }

        if ($fedapayTransactionId && $order->fedapay_transaction_id !== $fedapayTransactionId) {
            $updates['fedapay_transaction_id'] = $fedapayTransactionId;
        }

        if ($updates) {
            $order->forceFill($updates)->save();
        }

        $event = strtolower((string) ($request->input('event') ?? $request->input('type')));
        $status = strtolower((string) ($request->input('data.status') ?? $request->input('status')));

        $shouldMarkPaid = str_contains($event, 'transaction.paid')
            || in_array($status, ['approved', 'completed', 'paid'], true);

        if ($shouldMarkPaid) {
            if ($order->status !== Order::STATUS_PAID) {
                $order->markAsPaid($request->all());
                $fulfillment->send($order);
            }
        } elseif (in_array($status, ['canceled', 'declined', 'failed'], true)) {
            $order->update([
                'status' => Order::STATUS_FAILED,
                'payment_payload' => $request->all(),
            ]);

            $bot->sendMessage(
                $order->chat_id,
                sprintf('Le paiement de la commande %s a echoue. Tu peux relancer /shop pour reessayer.', $order->reference)
            );
        }

        return response()->json(['message' => 'ok']);
    }
}
