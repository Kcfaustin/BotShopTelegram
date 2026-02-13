<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\FedaPayWebhookController;

Route::post('/telegram/webhook', [TelegramController::class, 'handle']);
Route::post('/fedapay/webhook', [FedaPayWebhookController::class, 'handle'])
	->name('fedapay.webhook');
