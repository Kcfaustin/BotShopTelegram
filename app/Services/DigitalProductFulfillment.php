<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DigitalProductFulfillment
{
    public function __construct(private readonly TelegramBot $bot)
    {
    }

    public function send(Order $order): void
    {
        $product = $order->product;

        if (!$product) {
            $this->bot->sendMessage(
                $order->chat_id,
                "Paiement recu. Notre equipe t'enverra ton fichier sous peu."
            );
            return;
        }

        $disk = $product->file_disk ?: 'local';
        $relativePath = $product->file_path;

        if (!$relativePath || !Storage::disk($disk)->exists($relativePath)) {
            Log::error('fedapay.file_missing', [
                'order_id' => $order->id,
                'disk' => $disk,
                'path' => $relativePath,
            ]);

            $this->bot->sendMessage(
                $order->chat_id,
                "Paiement recu mais le fichier est indisponible. Nous t'envoyons une solution rapidement."
            );

            return;
        }

        $absolutePath = Storage::disk($disk)->path($relativePath);

        $this->bot->sendDocument(
            $order->chat_id,
            $absolutePath,
            basename($relativePath),
            sprintf('Merci pour ton achat ! Voici le fichier %s.', $product->name)
        );
    }
}
