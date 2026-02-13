<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELED = 'canceled';

    protected $fillable = [
        'product_id',
        'chat_id',
        'telegram_username',
        'reference',
        'status',
        'total_amount',
        'currency',
        'payment_url',
        'fedapay_transaction_id',
        'fedapay_reference',
        'paid_at',
        'payment_payload',
    ];

    protected $casts = [
        'payment_payload' => 'array',
        'paid_at' => 'datetime',
        'total_amount' => 'integer',
    ];

    public static function generateReference(): string
    {
        return 'TG'.Str::upper(Str::random(8));
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getAmountLabelAttribute(): string
    {
        return number_format($this->total_amount, 0, ',', ' ').' '.$this->currency;
    }

    public function markAsPaid(array $payload = []): void
    {
        $this->forceFill([
            'status' => self::STATUS_PAID,
            'paid_at' => now(),
            'payment_payload' => $payload ?: $this->payment_payload,
        ])->save();
    }
}
