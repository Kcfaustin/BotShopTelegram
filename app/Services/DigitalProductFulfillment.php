<?php

namespace App\Services;

use App\Models\Order;
use App\Jobs\SendDigitalProduct;

class DigitalProductFulfillment
{
    public function __construct(private readonly TelegramBot $bot)
    {
    }

    public function send(Order $order, bool $force = false): void
    {
        // Dispatch job for async processing
        SendDigitalProduct::dispatch($order, $force);
    }
}
