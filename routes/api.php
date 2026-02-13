<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\FedaPayWebhookController;

Route::post('/telegram/webhook', [TelegramController::class, 'handle']);
Route::post('/fedapay/webhook', [FedaPayWebhookController::class, 'handle'])
	->name('fedapay.webhook');

// Route de test pour vérifier que le webhook est accessible
Route::match(['get', 'post'], '/fedapay/test', function () {
    Log::info('fedapay.test_endpoint_hit', [
        'method' => request()->method(),
        'ip' => request()->ip(),
        'all' => request()->all(),
    ]);
    return response()->json(['status' => 'ok', 'message' => 'Webhook endpoint is reachable']);
});
