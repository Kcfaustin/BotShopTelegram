<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramSession extends Model
{
    use HasFactory;

    public const STATE_AWAITING_PRODUCT = 'awaiting_product';
    public const STATE_AWAITING_PROMO = 'awaiting_promo';

    protected $fillable = [
        'chat_id',
        'username',
        'state',
        'payload',
        'locale',
        'last_message_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'last_message_at' => 'datetime',
    ];
}
