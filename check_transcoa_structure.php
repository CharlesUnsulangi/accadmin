<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Struktur Tabel tr_acc_transaksi_coa:\n";
echo str_repeat("=", 100) . "\n";

$columns = DB::select("
    SELECT 
        COLUMN_NAME, 
        DATA_TYPE, 
        CHARACTER_MAXIMUM_LENGTH, 
        IS_NULLABLE, 
        COLUMN_DEFAULT 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'tr_acc_transaksi_coa' 
    ORDER BY ORDINAL_POSITION
");

foreach ($columns as $col) {
    echo sprintf("%-35s %-15s %-10s %-10s %s\n", 
        $col->COLUMN_NAME, 
        $col->DATA_TYPE, 
        $col->CHARACTER_MAXIMUM_LENGTH ?? '', 
        $col->IS_NULLABLE, 
        $col->COLUMN_DEFAULT ?? ''
    );
}

echo "\n\nSample Data (5 records):\n";
echo str_repeat("=", 100) . "\n";

$samples = DB::table('tr_acc_transaksi_coa')
    ->orderBy('transcoa_coa_date', 'desc')
    ->limit(5)
    ->get();

foreach ($samples as $sample) {
    echo "\nRecord:\n";
    echo "  Code: {$sample->transcoa_code}\n";
    echo "  COA: {$sample->transcoa_coa_code} - {$sample->transcoa_coa_desc}\n";
    echo "  Date: {$sample->transcoa_coa_date}\n";
    echo "  Debet: {$sample->transcoa_debet_value}\n";
    echo "  Credit: {$sample->transcoa_credit_value}\n";
    echo "  Status: {$sample->transcoa_statusposting}\n";
}

echo "\n\nTotal Records: " . DB::table('tr_acc_transaksi_coa')->count() . "\n";
