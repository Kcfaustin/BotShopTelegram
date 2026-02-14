<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Http\Controllers\TelegramController;
use App\Services\DigitalProductFulfillment;
use App\Services\FedaPayClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPendingOrders extends Command
{
    protected $signature = 'orders:check-pending';
    protected $description = 'Check status of pending orders via FedaPay API';

    public function handle(FedaPayClient $fedapay, DigitalProductFulfillment $fulfillment)
    {
        // Find orders pending for more than 2 minutes (to give webhook time to arrive first)
        $orders = Order::where('status', Order::STATUS_PENDING)
            ->where('created_at', '<=', now()->subMinutes(2))
            ->where('created_at', '>=', now()->subHours(24)) // Don't check ancient orders
            ->get();

        $this->info("Checking " . $orders->count() . " pending orders...");

        foreach ($orders as $order) {
            try {
                $this->checkOrder($order, $fedapay, $fulfillment);
            } catch (\Throwable $e) {
                Log::error('order.check_failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            }
        }
    }

    private function checkOrder(Order $order, FedaPayClient $fedapay, DigitalProductFulfillment $fulfillment)
    {
        $snapshot = $fedapay->fetchTransactionSnapshot($order);

        if (!$snapshot) {
            return;
        }

        $status = $snapshot['status'] ?? null;
        
        // Update references if available
        if (!empty($snapshot['reference']) && $snapshot['reference'] !== $order->fedapay_reference) {
            $order->fedapay_reference = $snapshot['reference'];
        }
        if (!empty($snapshot['transaction_id']) && $snapshot['transaction_id'] !== $order->fedapay_transaction_id) {
            $order->fedapay_transaction_id = $snapshot['transaction_id'];
        }
        if ($order->isDirty()) {
            $order->save();
        }

        if (in_array($status, ['approved', 'completed', 'paid', 'successful'], true)) {
            $this->info("Order {$order->reference} is PAID (detected by poller). Fulfiling...");
            
            $order->markAsPaid($snapshot['raw'] ?? $snapshot);
            $order->refresh();
            $fulfillment->send($order);
            
            Log::info('fedapay.poller_marking_paid', ['order_id' => $order->id]);
        } elseif (in_array($status, ['canceled', 'declined', 'failed'], true)) {
             $order->update([
                'status' => Order::STATUS_FAILED,
                'payment_payload' => $snapshot['raw'] ?? $snapshot,
            ]);
            $this->info("Order {$order->reference} marked as FAILED.");
        }
    }
}
