<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TableMetadata;
use App\Models\TableMessage;

$table = TableMetadata::findByTableName('ms_acc_coa');

echo "Table: {$table->table_name}\n";
echo "table_note: " . ($table->table_note ?? '(empty)') . "\n";
echo "\n";

$msgs = TableMessage::getTableMessages($table->tr_aplikasi_table_id);
echo "Total Messages: {$msgs->count()}\n";

foreach ($msgs as $m) {
    echo "  [{$m->tr_admin_it_aplikasi_table_msg_id}] {$m->msg_desc}\n";
    echo "      By: {$m->user_created} on {$m->date_created}\n";
}
