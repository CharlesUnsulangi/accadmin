<?php

/**
 * Test script for Table Metadata and Message Relationships
 * Demonstrates the connection between tr_admin_it_aplikasi_table and tr_admin_it_aplikasi_table_msg
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TableMetadata;
use App\Models\TableMessage;

echo "=== Testing Table Metadata & Message Relationships ===\n\n";

// Test 1: Get a table with its metadata
echo "1. Getting table metadata for 'ms_acc_coa'...\n";
try {
    $table = TableMetadata::findByTableName('ms_acc_coa');
    
    if ($table) {
        echo "   ✓ Table found!\n";
        echo "   - Table ID: {$table->tr_aplikasi_table_id}\n";
        echo "   - Table Name: {$table->table_name}\n";
        echo "   - Schema Note: {$table->note_schema}\n";
        echo "   - Record Count: " . number_format($table->record) . "\n";
        echo "   - Date Range: {$table->record_date_start} to {$table->record_date_last}\n";
        echo "   - Last Updated: {$table->date_updated}\n\n";
        
        // Test 2: Get messages for this table using relationship
        echo "2. Getting messages using relationship...\n";
        $messages = $table->messages;
        echo "   ✓ Found {$messages->count()} message(s)\n";
        
        if ($messages->count() > 0) {
            echo "\n   Messages:\n";
            foreach ($messages as $msg) {
                echo "   [{$msg->tr_admin_it_aplikasi_table_msg_id}] {$msg->msg_desc}\n";
                echo "      By: {$msg->user_created} on {$msg->date_created}\n";
            }
        }
        echo "\n";
        
        // Test 3: Add a test message
        echo "3. Adding a test message using relationship...\n";
        $newMessage = TableMessage::addMessage(
            $table->tr_aplikasi_table_id,
            "This is a test message to demonstrate the relationship between tables. Created at " . date('Y-m-d H:i:s'),
            "Test System"
        );
        echo "   ✓ Message added successfully!\n";
        echo "   - Message ID: {$newMessage->tr_admin_it_aplikasi_table_msg_id}\n\n";
        
        // Test 4: Verify the relationship works both ways
        echo "4. Accessing table metadata from message (reverse relationship)...\n";
        $messageWithTable = TableMessage::with('tableMetadata')->find($newMessage->tr_admin_it_aplikasi_table_msg_id);
        
        if ($messageWithTable && $messageWithTable->tableMetadata) {
            echo "   ✓ Relationship works!\n";
            echo "   - Message belongs to table: {$messageWithTable->tableMetadata->table_name}\n";
            echo "   - Table has {$messageWithTable->tableMetadata->record} records\n\n";
        }
        
        // Test 5: Get latest message using relationship
        echo "5. Getting latest message using latestMessage relationship...\n";
        $latestMsg = $table->latestMessage;
        if ($latestMsg) {
            echo "   ✓ Latest message found!\n";
            echo "   - Message: {$latestMsg->msg_desc}\n";
            echo "   - Created: {$latestMsg->date_created} by {$latestMsg->user_created}\n\n";
        }
        
        // Test 6: Count messages
        echo "6. Counting messages for table...\n";
        $messageCount = $table->messages()->count();
        echo "   ✓ Total messages: {$messageCount}\n\n";
        
        // Test 7: Get access logs (if any)
        echo "7. Getting access logs for this table...\n";
        $accessCount = $table->accessLogs()->count();
        echo "   ✓ Total access logs: {$accessCount}\n";
        
        if ($accessCount > 0) {
            $latestAccess = $table->accessLogs()->first();
            echo "   - Latest access: {$latestAccess->accessed_at}\n";
            echo "   - Frontend: {$latestAccess->frontend_type}\n";
            echo "   - User: {$latestAccess->user_name}\n";
        }
        echo "\n";
        
        // Test 8: Clean up test message
        echo "8. Cleaning up test message...\n";
        $newMessage->delete();
        echo "   ✓ Test message deleted\n\n";
        
    } else {
        echo "   ✗ Table 'ms_acc_coa' not found\n\n";
    }
    
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 9: Query tables with messages
echo "9. Finding all tables that have messages...\n";
try {
    $tablesWithMessages = TableMetadata::withMessages()->get();
    echo "   ✓ Found {$tablesWithMessages->count()} table(s) with messages\n";
    
    if ($tablesWithMessages->count() > 0) {
        echo "\n   Tables with messages:\n";
        foreach ($tablesWithMessages->take(5) as $tbl) {
            $msgCount = $tbl->messages->count();
            echo "   - {$tbl->table_name} ({$msgCount} message" . ($msgCount > 1 ? 's' : '') . ")\n";
        }
    }
    echo "\n";
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 10: Query tables without messages
echo "10. Finding tables without messages...\n";
try {
    $tablesWithoutMessages = TableMetadata::withoutMessages()->count();
    echo "   ✓ Found {$tablesWithoutMessages} table(s) without messages\n\n";
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 11: Demonstrate Eloquent queries using relationships
echo "11. Advanced query: Tables with more than 1000 records and have messages...\n";
try {
    $largeTables = TableMetadata::where('record', '>', 1000)
        ->withMessages()
        ->orderBy('record', 'desc')
        ->take(5)
        ->get();
    
    echo "   ✓ Found {$largeTables->count()} large table(s) with messages\n";
    
    if ($largeTables->count() > 0) {
        echo "\n   Results:\n";
        foreach ($largeTables as $tbl) {
            echo "   - {$tbl->table_name}: " . number_format($tbl->record) . " records, {$tbl->messages->count()} message(s)\n";
        }
    }
    echo "\n";
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 12: Show the relationship structure
echo "12. Relationship Summary:\n";
echo "   ┌─────────────────────────────────────────────┐\n";
echo "   │ tr_admin_it_aplikasi_table (TableMetadata) │\n";
echo "   │   PRIMARY KEY: tr_aplikasi_table_id         │\n";
echo "   └─────────────────────────────────────────────┘\n";
echo "                      │\n";
echo "                      │ hasMany (messages)\n";
echo "                      │ hasOne (latestMessage)\n";
echo "                      │\n";
echo "                      ▼\n";
echo "   ┌──────────────────────────────────────────────────┐\n";
echo "   │ tr_admin_it_aplikasi_table_msg (TableMessage)    │\n";
echo "   │   PRIMARY KEY: tr_admin_it_aplikasi_table_msg_id │\n";
echo "   │   FOREIGN KEY: tr_aplikasi_table_id              │\n";
echo "   └──────────────────────────────────────────────────┘\n";
echo "\n";

echo "   Relationships:\n";
echo "   - TableMetadata->messages() : hasMany TableMessage\n";
echo "   - TableMetadata->latestMessage() : hasOne TableMessage\n";
echo "   - TableMessage->tableMetadata() : belongsTo TableMetadata\n";
echo "\n";

echo "=== All Tests Complete ===\n";
