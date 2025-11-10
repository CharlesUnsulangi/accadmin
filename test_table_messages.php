<?php

/**
 * Test script for Table Messages feature
 * Tests the TableMessage model and message operations
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TableMessage;
use Illuminate\Support\Facades\DB;

echo "=== Table Messages Feature Test ===\n\n";

// Test 1: Check if table exists
echo "1. Checking if tr_admin_it_aplikasi_table_msg table exists...\n";
try {
    $tableExists = DB::getSchemaBuilder()->hasTable('tr_admin_it_aplikasi_table_msg');
    echo "   ✓ Table exists: " . ($tableExists ? "YES" : "NO") . "\n\n";
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Get next ID
echo "2. Testing getNextId() method...\n";
try {
    $nextId = TableMessage::getNextId();
    echo "   ✓ Next ID: $nextId\n\n";
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 3: Get a sample table ID for testing
echo "3. Getting a sample table for testing...\n";
try {
    $sampleTable = DB::table('tr_admin_it_aplikasi_table')
        ->whereRaw("CAST(table_name AS VARCHAR(MAX)) = ?", ['ms_acc_coa'])
        ->first();
    
    if ($sampleTable) {
        echo "   ✓ Using table: {$sampleTable->table_name}\n";
        echo "   ✓ Table ID: {$sampleTable->tr_aplikasi_table_id}\n\n";
        
        // Test 4: Get existing messages
        echo "4. Getting existing messages for this table...\n";
        $messages = TableMessage::getTableMessages($sampleTable->tr_aplikasi_table_id);
        echo "   ✓ Found " . count($messages) . " message(s)\n";
        
        if (count($messages) > 0) {
            echo "\n   Existing messages:\n";
            foreach ($messages as $msg) {
                echo "   - [{$msg->tr_admin_it_aplikasi_table_msg_id}] {$msg->msg_desc}\n";
                echo "     Created by: {$msg->user_created} on {$msg->date_created}\n";
            }
        }
        echo "\n";
        
        // Test 5: Add a test message
        echo "5. Adding a test message...\n";
        try {
            $testMessage = TableMessage::addMessage(
                $sampleTable->tr_aplikasi_table_id,
                "Test message created at " . date('Y-m-d H:i:s'),
                "Test User"
            );
            echo "   ✓ Message added successfully!\n";
            echo "   ✓ Message ID: {$testMessage->tr_admin_it_aplikasi_table_msg_id}\n";
            echo "   ✓ Description: {$testMessage->msg_desc}\n";
            echo "   ✓ Created by: {$testMessage->user_created}\n\n";
            
            // Test 6: Verify message was added
            echo "6. Verifying message was added...\n";
            $updatedMessages = TableMessage::getTableMessages($sampleTable->tr_aplikasi_table_id);
            echo "   ✓ Now found " . count($updatedMessages) . " message(s)\n\n";
            
            // Test 7: Clean up - delete test message
            echo "7. Cleaning up test message...\n";
            $testMessage->delete();
            echo "   ✓ Test message deleted\n\n";
            
        } catch (\Exception $e) {
            echo "   ✗ Error adding message: " . $e->getMessage() . "\n\n";
        }
        
    } else {
        echo "   ✗ Sample table 'ms_acc_coa' not found\n\n";
    }
    
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 8: Check table structure
echo "8. Checking table structure...\n";
try {
    $columns = DB::getSchemaBuilder()->getColumnListing('tr_admin_it_aplikasi_table_msg');
    echo "   ✓ Columns: " . implode(', ', $columns) . "\n\n";
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 9: Count total messages in system
echo "9. Counting total messages in system...\n";
try {
    $totalMessages = TableMessage::count();
    echo "   ✓ Total messages in database: $totalMessages\n\n";
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

echo "=== Test Complete ===\n";
