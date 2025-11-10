<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TableAccessLog;

echo "Testing Table Access Log...\n\n";

// Simulate access from different frontends
$testData = [
    [
        'table' => 'ms_acc_coa',
        'type' => 'view',
        'frontend' => 'Web - Chrome',
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/91.0.4472.124',
    ],
    [
        'table' => 'tr_acc_closing_d',
        'type' => 'query',
        'frontend' => 'Web - Firefox',
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
    ],
    [
        'table' => 'tr_admin_it_aplikasi_table',
        'type' => 'view',
        'frontend' => 'Mobile - Android',
        'user_agent' => 'Mozilla/5.0 (Linux; Android 10) AppleWebKit/537.36 Chrome/91.0.4472.120 Mobile',
    ],
    [
        'table' => 'users',
        'type' => 'export',
        'frontend' => 'API - Postman',
        'user_agent' => 'PostmanRuntime/7.28.0',
    ],
];

foreach ($testData as $data) {
    try {
        TableAccessLog::create([
            'table_name' => $data['table'],
            'access_type' => $data['type'],
            'frontend_type' => $data['frontend'],
            'user_agent' => $data['user_agent'],
            'ip_address' => '127.0.0.1',
            'user_id' => 1,
            'user_name' => 'Test User',
            'additional_info' => json_encode(['test' => true]),
            'accessed_at' => now()
        ]);
        
        echo "✓ Logged access: {$data['table']} via {$data['frontend']}\n";
    } catch (\Exception $e) {
        echo "✗ Error logging {$data['table']}: " . $e->getMessage() . "\n";
    }
}

echo "\n=== STATISTICS ===\n";
echo "Total access logs: " . TableAccessLog::count() . "\n";
echo "Unique tables accessed: " . TableAccessLog::distinct('table_name')->count('table_name') . "\n";
echo "Unique frontends: " . TableAccessLog::distinct('frontend_type')->count('frontend_type') . "\n";

echo "\n=== MOST ACCESSED TABLES ===\n";
$mostAccessed = TableAccessLog::getMostAccessedTables(5);
foreach ($mostAccessed as $item) {
    echo "{$item->table_name}: {$item->access_count} times\n";
}

echo "\n=== ACCESS BY FRONTEND ===\n";
$byFrontend = TableAccessLog::getAccessByFrontend();
foreach ($byFrontend as $item) {
    echo "{$item->frontend_type}: {$item->access_count} accesses\n";
}

echo "\nDone!\n";
