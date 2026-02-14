<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\TelegramBot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SendDigitalProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order, public bool $force = false)
    {
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [(new WithoutOverlapping($this->order->id))->dontRelease()];
    }

    /**
     * Execute the job.
     */
    public function handle(TelegramBot $bot): void
    {
        // Check if already delivered (idempotency) unless forced
        if (!$this->force && $this->order->refresh()->delivered_at) {
            Log::info('telegram.fulfillment_skipped', ['order_id' => $this->order->id]);
            return;
        }

        $product = $this->order->product;

        if (!$product) {
            $bot->sendMessage(
                $this->order->chat_id,
                "Paiement reçu. Notre équipe t'enverra ton fichier sous peu."
            );
            // Mark as delivered to prevent loop, but maybe log warning
            $this->order->update(['delivered_at' => now()]);
            return;
        }

        $disk = $product->file_disk ?: 'local';
        $relativePath = $product->file_path;

        if (!$relativePath || !Storage::disk($disk)->exists($relativePath)) {
            Log::error('fedapay.file_missing', [
                'order_id' => $this->order->id,
                'disk' => $disk,
                'path' => $relativePath,
            ]);

            $bot->sendMessage(
                $this->order->chat_id,
                "Paiement reçu mais le fichier est indisponible. Nous t'envoyons une solution rapidement."
            );
            
            // Should we mark as delivered? Probably, to avoid retry spam. 
            // Better to let admin handle manual resend.
            $this->order->update(['delivered_at' => now()]);
            return;
        }

        $absolutePath = Storage::disk($disk)->path($relativePath);

        try {
            $bot->sendDocument(
                $this->order->chat_id,
                $absolutePath, // Use absolute path for TelegramBot wrapper
                basename($relativePath),
                sprintf('Merci pour ton achat ! Voici le fichier %s.', $product->name)
            );

            $this->order->update(['delivered_at' => now()]);
            Log::info('telegram.fulfillment_delivered', ['order_id' => $this->order->id]);

        } catch (\Throwable $e) {
            Log::error('telegram.fulfillment_failed', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage()
            ]);
            // Do NOT mark as delivered, so job can retry (or fail after max attempts)
            throw $e;
        }
    }
}
