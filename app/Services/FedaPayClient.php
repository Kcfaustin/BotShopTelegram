<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FedaPayClient
{
    public function createTransaction(Order $order): array
    {
        $payload = [
            'amount' => $order->total_amount,
            'description' => sprintf('Commande %s - %s', $order->reference, $order->product->name),
            'currency' => ['iso' => $order->currency],
            'callback_url' => route('payment.success', [], true),
            'return_url' => route('payment.success', [], true),
            'cancel_url' => route('payment.failed', [], true),
            'notification_url' => route('fedapay.webhook', [], true),
            'customer' => [
                'firstname' => $order->telegram_username ?? 'Telegram',
                'lastname' => $order->product->slug,
                'email' => config('mail.from.address', 'businessclub93@gmail.com'),
            ],
            'metadata' => [
                'order_reference' => $order->reference,
                'chat_id' => $order->chat_id,
            ],
        ];

        $response = Http::withToken(config('fedapay.secret_key'))
            ->acceptJson()
            ->asJson()
            ->post($this->endpoint('transactions'), $payload);

        if ($response->failed()) {
            Log::error('fedapay.transaction_failed', [
                'order_id' => $order->id,
                'response' => $response->json(),
            ]);
            $response->throw();
        }

        $responseData = $response->json();
        $data = $responseData['data'] ?? $responseData;

        if (isset($data['v1/transaction']) && is_array($data['v1/transaction'])) {
            $data = $data['v1/transaction'];
        } elseif (is_array($data) && count($data) === 1) {
            $first = reset($data);
            if (is_array($first) && isset($first['id'])) {
                $data = $first;
            }
        }

        $reference = data_get($data, 'reference')
            ?? data_get($data, 'entity.reference')
            ?? data_get($data, 'transaction.reference')
            ?? data_get($responseData, 'data.transaction.reference')
            ?? data_get($responseData, 'entity.reference');

        $paymentUrl = data_get($data, 'url')
            ?? data_get($data, 'checkout_url')
            ?? data_get($data, 'payment_url')
            ?? data_get($responseData, 'data.url')
            ?? data_get($responseData, 'data.checkout_url')
            ?? data_get($responseData, 'entity.url');

        $paymentToken = data_get($data, 'payment_token')
            ?? data_get($responseData, 'data.payment_token');

        if (!$paymentUrl && $paymentToken) {
            $paymentUrl = rtrim($this->checkoutBaseUrl(), '/').'/'.$paymentToken;
        }

        $transactionId = data_get($data, 'id')
            ?? data_get($data, 'transaction.id')
            ?? data_get($data, 'entity.id');

        return [
            'raw' => $responseData,
            'transaction_id' => $transactionId,
            'reference' => $reference
                ?? $transactionId
                ?? $order->reference,
            'payment_url' => $paymentUrl,
            'payment_token' => $paymentToken,
        ];
    }

    public function fetchTransactionSnapshot(Order $order): ?array
    {
        $transactionId = $order->fedapay_transaction_id;

        if (!$transactionId) {
            return null;
        }

        $response = Http::withToken(config('fedapay.secret_key'))
            ->acceptJson()
            ->get($this->endpoint('transactions/'.$transactionId));

        if ($response->failed()) {
            Log::warning('fedapay.transaction_lookup_failed', [
                'order_id' => $order->id,
                'transaction_id' => $transactionId,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return null;
        }

        $responseData = $response->json();
        $data = $responseData['data'] ?? $responseData;

        if (isset($data['v1/transaction']) && is_array($data['v1/transaction'])) {
            $data = $data['v1/transaction'];
        } elseif (is_array($data) && count($data) === 1) {
            $first = reset($data);
            if (is_array($first) && isset($first['id'])) {
                $data = $first;
            }
        }

        $status = data_get($data, 'status')
            ?? data_get($data, 'transaction.status')
            ?? data_get($responseData, 'data.transaction.status');

        $reference = data_get($data, 'reference')
            ?? data_get($data, 'entity.reference')
            ?? data_get($data, 'transaction.reference')
            ?? data_get($responseData, 'data.transaction.reference')
            ?? data_get($responseData, 'entity.reference')
            ?? $order->reference;

        return [
            'raw' => $responseData,
            'transaction_id' => data_get($data, 'id')
                ?? data_get($data, 'transaction.id')
                ?? data_get($data, 'entity.id')
                ?? $transactionId,
            'reference' => $reference,
            'status' => $status ? strtolower((string) $status) : null,
        ];
    }

    private function checkoutBaseUrl(): string
    {
        $mode = config('fedapay.mode', 'sandbox');

        return $mode === 'live'
            ? 'https://process.fedapay.com/checkout'
            : 'https://sandbox-process.fedapay.com/checkout';
    }

    public function verifyWebhook(?string $signatureHeader, string $payload): bool
    {
        $secret = config('fedapay.webhook_secret');
        if (!$secret || !$signatureHeader) {
            return true;
        }

        [$timestamp, $signatures] = $this->parseSignatureHeader($signatureHeader);

        if (!$timestamp || empty($signatures)) {
            return false;
        }

        $expected = $this->signPayload($timestamp, $payload, $secret);

        foreach ($signatures as $signature) {
            if (hash_equals($expected, $signature)) {
                return true;
            }
        }

        return false;
    }

    public function computeExpectedSignature(?string $signatureHeader, string $payload): ?string
    {
        $secret = config('fedapay.webhook_secret');
        if (!$secret) {
            return null;
        }

        [$timestamp] = $this->parseSignatureHeader($signatureHeader);

        if ($timestamp) {
            return $this->signPayload($timestamp, $payload, $secret);
        }

        return hash_hmac('sha256', $payload, $secret);
    }

    private function parseSignatureHeader(?string $header): array
    {
        $timestamp = null;
        $signatures = [];

        if (!$header) {
            return [$timestamp, $signatures];
        }

        $segments = explode(',', $header);

        foreach ($segments as $segment) {
            $segment = trim($segment);
            if ($segment === '') {
                continue;
            }

            [$key, $value] = array_pad(explode('=', $segment, 2), 2, null);

            if ($key === 't') {
                $timestamp = $value;
            } elseif ($key === 's' && $value !== null) {
                $signatures[] = $value;
            }
        }

        return [$timestamp, $signatures];
    }

    private function signPayload(string $timestamp, string $payload, string $secret): string
    {
        return hash_hmac('sha256', $timestamp.'.'.$payload, $secret);
    }

    private function endpoint(string $path): string
    {
        return rtrim(config('fedapay.base_url'), '/').'/'.$path;
    }
}
