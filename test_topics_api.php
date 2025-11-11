<?php

/**
 * Test API Topics Endpoint
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Get AccAdmin ID
    $accadmin = DB::table('ms_admin_it_aplikasi')
        ->select('ms_admin_it_aplikasi_id', DB::raw('CAST(apps_desc AS VARCHAR(MAX)) as apps_desc'))
        ->where(DB::raw('CAST(apps_desc AS VARCHAR(MAX))'), 'like', '%AccAdmin%')
        ->first();
    
    if (!$accadmin) {
        echo "❌ AccAdmin tidak ditemukan\n";
        exit(1);
    }
    
    $appId = $accadmin->ms_admin_it_aplikasi_id;
    echo "=== Testing Topics API ===\n";
    echo "Application: {$accadmin->apps_desc}\n";
    echo "ID: {$appId}\n\n";
    
    // Test getTopics endpoint
    echo "1. Testing GET /api/applications/{$appId}/topics\n";
    $topics = DB::table('ms_admin_it_aplikasi_topic')
        ->select(
            'ms_admin_it_topic',
            DB::raw('CAST(topic_desc AS VARCHAR(MAX)) as topic_desc'),
            'value_priority'
        )
        ->where('ms_admin_it_aplikasi_id', $appId)
        ->orderBy('value_priority')
        ->get();
    
    echo "   Found: " . $topics->count() . " topics\n";
    
    if ($topics->count() > 0) {
        echo "\n   Topics List:\n";
        foreach ($topics as $topic) {
            echo "   - [{$topic->value_priority}] {$topic->topic_desc} (ID: {$topic->ms_admin_it_topic})\n";
        }
    }
    
    echo "\n✅ API endpoint siap digunakan!\n";
    echo "\nURL untuk test di browser:\n";
    echo "http://127.0.0.1:8000/applications\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
