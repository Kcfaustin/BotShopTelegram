<?php

// Prevent direct access if not running from CLI (cron)
if (php_sapi_name() !== 'cli') {
    die('Not allowed');
}

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Processing jobs...\n";

// Queue worker with fail-safe options for shared hosting
$status = $kernel->call('queue:work', [
    '--stop-when-empty' => true,
    '--tries' => 3,
    '--timeout' => 90
]);

echo "Done. Status: " . $status . "\n";
