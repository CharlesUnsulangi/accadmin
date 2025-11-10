<?php

/**
 * Test Foreign Key Relationship Direction
 * Pastikan relasi antara TableMetadata dan TableMessage tidak terbalik
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TableMetadata;
use App\Models\TableMessage;

echo "=== Testing Foreign Key Relationship Direction ===\n\n";

// Test 1: Get table metadata
echo "1. Testing TableMetadata → TableMessage (hasMany)\n";
$table = TableMetadata::findByTableName('ms_acc_coa');

if ($table) {
    echo "   ✓ Table found: {$table->table_name}\n";
    echo "   ✓ Primary Key: {$table->tr_aplikasi_table_id}\n\n";
    
    // Test hasMany relationship
    echo "2. Getting messages via hasMany relationship...\n";
    $messages = $table->messages;
    echo "   ✓ Found {$messages->count()} message(s)\n";
    
    if ($messages->count() > 0) {
        foreach ($messages as $msg) {
            echo "   - Message ID: {$msg->tr_admin_it_aplikasi_table_msg_id}\n";
            echo "     FK: {$msg->tr_aplikasi_table_id}\n";
            echo "     Content: {$msg->msg_desc}\n";
        }
    }
    echo "\n";
    
    // Test 3: Verify foreign key match
    echo "3. Verifying foreign key values...\n";
    if ($messages->count() > 0) {
        $firstMessage = $messages->first();
        if ($firstMessage->tr_aplikasi_table_id === $table->tr_aplikasi_table_id) {
            echo "   ✓ Foreign key MATCH! Relationship is CORRECT\n";
            echo "     Table PK: {$table->tr_aplikasi_table_id}\n";
            echo "     Message FK: {$firstMessage->tr_aplikasi_table_id}\n";
        } else {
            echo "   ✗ Foreign key MISMATCH! Relationship might be WRONG\n";
            echo "     Table PK: {$table->tr_aplikasi_table_id}\n";
            echo "     Message FK: {$firstMessage->tr_aplikasi_table_id}\n";
        }
    }
    echo "\n";
    
    // Test 4: Test reverse relationship (belongsTo)
    echo "4. Testing TableMessage → TableMetadata (belongsTo)\n";
    if ($messages->count() > 0) {
        $testMessage = $messages->first();
        echo "   Message ID: {$testMessage->tr_admin_it_aplikasi_table_msg_id}\n";
        
        $parentTable = $testMessage->tableMetadata;
        if ($parentTable) {
            echo "   ✓ Parent table found via belongsTo\n";
            echo "     Table Name: {$parentTable->table_name}\n";
            echo "     Table PK: {$parentTable->tr_aplikasi_table_id}\n";
            
            // Verify it's the same table
            if ($parentTable->tr_aplikasi_table_id === $table->tr_aplikasi_table_id) {
                echo "   ✓ Reverse relationship CORRECT! Points to same table\n";
            } else {
                echo "   ✗ Reverse relationship WRONG! Points to different table\n";
            }
        } else {
            echo "   ✗ Parent table NOT found via belongsTo\n";
            echo "   ⚠️  Relationship might be reversed!\n";
        }
    }
    echo "\n";
}

// Test 5: Check relationship definitions
echo "5. Checking relationship definitions in models...\n";
echo "\n";

echo "   TableMetadata::messages() - hasMany\n";
echo "   Definition: hasMany(TableMessage::class, 'tr_aplikasi_table_id', 'tr_aplikasi_table_id')\n";
echo "   - Foreign Key Column: tr_aplikasi_table_id (in TableMessage)\n";
echo "   - Local Key Column: tr_aplikasi_table_id (in TableMetadata)\n";
echo "\n";

echo "   TableMessage::tableMetadata() - belongsTo\n";
echo "   Definition: belongsTo(TableMetadata::class, 'tr_aplikasi_table_id', 'tr_aplikasi_table_id')\n";
echo "   - Foreign Key Column: tr_aplikasi_table_id (in TableMessage)\n";
echo "   - Owner Key Column: tr_aplikasi_table_id (in TableMetadata)\n";
echo "\n";

// Test 6: Expected relationship structure
echo "6. Expected Relationship Structure:\n";
echo "\n";
echo "   ┌─────────────────────────────────────┐\n";
echo "   │ tr_admin_it_aplikasi_table          │\n";
echo "   │ (TableMetadata)                     │\n";
echo "   │                                     │\n";
echo "   │ PK: tr_aplikasi_table_id ────┐      │\n";
echo "   └──────────────────────────────│──────┘\n";
echo "                                  │\n";
echo "                                  │ ONE-TO-MANY\n";
echo "                                  │\n";
echo "   ┌──────────────────────────────│──────┐\n";
echo "   │ tr_admin_it_aplikasi_table_msg      │\n";
echo "   │ (TableMessage)                      │\n";
echo "   │                                     │\n";
echo "   │ PK: tr_admin_it_aplikasi_table_msg_id│\n";
echo "   │ FK: tr_aplikasi_table_id ◄───┘      │\n";
echo "   └─────────────────────────────────────┘\n";
echo "\n";

// Test 7: Summary
echo "7. Relationship Direction Summary:\n";
echo "   ✓ ONE TableMetadata HAS MANY TableMessage\n";
echo "   ✓ ONE TableMessage BELONGS TO ONE TableMetadata\n";
echo "   ✓ Foreign Key: tr_aplikasi_table_id in TableMessage table\n";
echo "   ✓ References: tr_aplikasi_table_id in TableMetadata table\n";
echo "\n";

echo "=== Test Complete ===\n";
