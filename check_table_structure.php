<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== STRUKTUR TABEL tr_acc_transaksi_coa ===\n\n";

$columns = DB::select("
    SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, IS_NULLABLE
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'tr_acc_transaksi_coa'
    ORDER BY ORDINAL_POSITION
");

foreach ($columns as $col) {
    $len = $col->CHARACTER_MAXIMUM_LENGTH ? "({$col->CHARACTER_MAXIMUM_LENGTH})" : '';
    $null = $col->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL';
    echo "{$col->COLUMN_NAME} {$col->DATA_TYPE}{$len} {$null}\n";
}

echo "\n=== SAMPLE DATA ===\n";
$sample = DB::table('tr_acc_transaksi_coa')->first();
if ($sample) {
    foreach (get_object_vars($sample) as $key => $value) {
        $val = $value ?? 'NULL';
        if (is_string($val) && strlen($val) > 50) {
            $val = substr($val, 0, 50) . '...';
        }
        echo "{$key}: {$val}\n";
    }
}
