<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Messages in Database ===\n\n";

$messages = DB::select('SELECT * FROM tr_admin_it_aplikasi_table_msg');

echo "Total messages: " . count($messages) . "\n\n";

foreach ($messages as $msg) {
    echo "ID: {$msg->tr_admin_it_aplikasi_table_msg_id}\n";
    echo "Table ID: {$msg->tr_aplikasi_table_id}\n";
    echo "Message: {$msg->msg_desc}\n";
    echo "User: {$msg->user_created}\n";
    echo "Date: {$msg->date_created}\n";
    echo "---\n";
}
