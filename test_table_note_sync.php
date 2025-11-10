<?php

/**
 * Test script for table_note synchronization with latest message
 * Demonstrates automatic update of table_note when messages are added/deleted
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TableMetadata;
use App\Models\TableMessage;
use Illuminate\Support\Facades\DB;

echo "=== Testing table_note Synchronization ===\n\n";

// Get test table
$table = TableMetadata::findByTableName('ms_acc_coa');

if (!$table) {
    echo "❌ Table 'ms_acc_coa' not found\n";
    exit(1);
}

echo "📋 Testing with table: {$table->table_name}\n";
echo "   Table ID: {$table->tr_aplikasi_table_id}\n";
echo "   Current table_note: " . ($table->table_note ?? '(empty)') . "\n\n";

// Test 1: Add first message
echo "1️⃣ Adding first message...\n";
$msg1 = TableMessage::addMessage(
    $table->tr_aplikasi_table_id,
    "Pesan pertama - Testing sync table_note",
    "Test User"
);
echo "   ✓ Message added: {$msg1->msg_desc}\n";

// Check if table_note updated
$table->refresh();
echo "   ✓ table_note sekarang: " . ($table->table_note ?? '(empty)') . "\n\n";

// Test 2: Add second message (should replace table_note)
echo "2️⃣ Adding second message...\n";
$msg2 = TableMessage::addMessage(
    $table->tr_aplikasi_table_id,
    "Pesan kedua - Ini yang terbaru",
    "Test User"
);
echo "   ✓ Message added: {$msg2->msg_desc}\n";

// Check if table_note updated to latest
$table->refresh();
echo "   ✓ table_note sekarang: " . ($table->table_note ?? '(empty)') . "\n";
echo "   ℹ️  Harusnya berisi pesan kedua\n\n";

// Test 3: Add third message
echo "3️⃣ Adding third message...\n";
$msg3 = TableMessage::addMessage(
    $table->tr_aplikasi_table_id,
    "Pesan ketiga - Update terbaru " . date('Y-m-d H:i:s'),
    "Admin"
);
echo "   ✓ Message added: {$msg3->msg_desc}\n";

// Check table_note
$table->refresh();
echo "   ✓ table_note sekarang: " . ($table->table_note ?? '(empty)') . "\n\n";

// Test 4: Show all messages
echo "4️⃣ All messages for this table:\n";
$messages = TableMessage::getTableMessages($table->tr_aplikasi_table_id);
foreach ($messages as $idx => $msg) {
    $marker = ($idx === 0) ? " ← TERBARU (ada di table_note)" : "";
    echo "   [{$msg->tr_admin_it_aplikasi_table_msg_id}] {$msg->msg_desc}{$marker}\n";
    echo "      By: {$msg->user_created} on {$msg->date_created}\n";
}
echo "\n";

// Test 5: Delete latest message
echo "5️⃣ Deleting latest message (msg3)...\n";
$msg3->delete();
echo "   ✓ Message deleted\n";

// Check if table_note updated to previous message
$table->refresh();
echo "   ✓ table_note sekarang: " . ($table->table_note ?? '(empty)') . "\n";
echo "   ℹ️  Harusnya berisi pesan kedua (karena yang ketiga dihapus)\n\n";

// Test 6: Delete second message
echo "6️⃣ Deleting second message (msg2)...\n";
$msg2->delete();
echo "   ✓ Message deleted\n";

// Check table_note
$table->refresh();
echo "   ✓ table_note sekarang: " . ($table->table_note ?? '(empty)') . "\n";
echo "   ℹ️  Harusnya berisi pesan pertama\n\n";

// Test 7: Delete last message
echo "7️⃣ Deleting first/last remaining message (msg1)...\n";
$msg1->delete();
echo "   ✓ Message deleted\n";

// Check if table_note is now empty
$table->refresh();
echo "   ✓ table_note sekarang: " . ($table->table_note ?? '(empty)') . "\n";
echo "   ℹ️  Harusnya kosong (tidak ada message lagi)\n\n";

// Final verification
echo "8️⃣ Final verification:\n";
$remainingMessages = TableMessage::getTableMessages($table->tr_aplikasi_table_id);
echo "   ✓ Remaining messages: {$remainingMessages->count()}\n";
echo "   ✓ table_note: " . ($table->table_note ?? '(null/empty)') . "\n\n";

echo "✅ All tests complete!\n\n";

echo "📊 Summary:\n";
echo "   - When message added → table_note updated with latest message\n";
echo "   - When message deleted → table_note updated with new latest message\n";
echo "   - When all messages deleted → table_note becomes null\n";
