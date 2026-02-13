<?php

return [
    'public_key' => env('FEDAPAY_PUBLIC_KEY'),
    'secret_key' => env('FEDAPAY_SECRET_KEY'),
    'webhook_secret' => env('FEDAPAY_WEBHOOK_SECRET'),
    'mode' => env('FEDAPAY_MODE', 'sandbox'),
    'base_url' => env('FEDAPAY_MODE', 'sandbox') === 'live'
        ? 'https://api.fedapay.com/v1'
        : 'https://sandbox-api.fedapay.com/v1',
];
