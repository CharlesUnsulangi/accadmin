<?php

/**
 * Clean up test messages from database
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TableMessage;
use Illuminate\Support\Facades\DB;

echo "=== Cleaning Up Test Messages ===\n\n";

// Count current messages
$totalMessages = TableMessage::count();
echo "Current total messages: {$totalMessages}\n\n";

// Find test messages (messages created by 'Test User' or 'Test System')
$testMessages = TableMessage::whereIn('user_created', ['Test User', 'Test System', 'System'])
    ->orWhere('msg_desc', 'like', '%Test%')
    ->orWhere('msg_desc', 'like', '%test%')
    ->get();

echo "Found {$testMessages->count()} test message(s) to clean:\n";

if ($testMessages->count() > 0) {
    foreach ($testMessages as $msg) {
        echo "  - [{$msg->tr_admin_it_aplikasi_table_msg_id}] {$msg->msg_desc}\n";
        echo "    (Table: {$msg->tr_aplikasi_table_id}, By: {$msg->user_created})\n";
    }
    
    echo "\n";
    
    // Delete test messages
    foreach ($testMessages as $msg) {
        echo "Deleting message {$msg->tr_admin_it_aplikasi_table_msg_id}... ";
        $msg->delete(); // This will auto-update table_note
        echo "✓\n";
    }
    
    echo "\n";
}

// Show remaining messages
$remainingMessages = TableMessage::count();
echo "Remaining messages: {$remainingMessages}\n";

// Show tables that still have messages
$tablesWithMessages = DB::table('tr_admin_it_aplikasi_table_msg')
    ->select('tr_aplikasi_table_id')
    ->groupBy('tr_aplikasi_table_id')
    ->get();

echo "\nTables with messages ({$tablesWithMessages->count()}):\n";
foreach ($tablesWithMessages as $table) {
    $count = TableMessage::where('tr_aplikasi_table_id', $table->tr_aplikasi_table_id)->count();
    
    // Get table name
    $tableName = DB::table('tr_admin_it_aplikasi_table')
        ->where('tr_aplikasi_table_id', $table->tr_aplikasi_table_id)
        ->value(DB::raw('CAST(table_name AS VARCHAR(MAX))'));
    
    echo "  - {$tableName} ({$table->tr_aplikasi_table_id}): {$count} message(s)\n";
}

echo "\n✅ Cleanup complete!\n";
