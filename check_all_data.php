<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CEK SEMUA TRANSAKSI ===\n\n";

// Count ALL transactions
$totalAll = DB::table('tr_acc_transaksi_coa')->count();
echo "Total SEMUA transaksi: {$totalAll}\n";

if ($totalAll > 0) {
    // By status
    $byStatus = DB::table('tr_acc_transaksi_coa')
        ->selectRaw('transcoa_statusposting, COUNT(*) as jumlah')
        ->groupBy('transcoa_statusposting')
        ->get();
    
    echo "\nDistribusi by status:\n";
    foreach ($byStatus as $status) {
        $statusLabel = $status->transcoa_statusposting ?: 'NULL';
        echo "  Status '{$statusLabel}': {$status->jumlah} transaksi\n";
    }
    
    // Sample data
    $sample = DB::table('tr_acc_transaksi_coa')
        ->limit(5)
        ->get();
    
    echo "\nSample 5 transaksi pertama:\n";
    foreach ($sample as $row) {
        $props = get_object_vars($row);
        $firstKey = array_key_first($props);
        echo "  First col: {$props[$firstKey]} | Tgl: {$row->transcoa_tgl} | COA: {$row->transcoa_coacode} | Status: " . ($row->transcoa_statusposting ?? 'NULL') . "\n";
    }
}

// Check COA
$totalCoa = DB::table('ms_acc_coa')->where('rec_status', 'A')->count();
echo "\nTotal COA aktif: {$totalCoa}\n";

// Sample COA with hierarchy
$sampleCoa = DB::table('ms_acc_coa')
    ->where('rec_status', 'A')
    ->limit(3)
    ->get(['coa_code', 'coa_desc', 'coa_coasub2code']);

echo "\nSample 3 COA:\n";
foreach ($sampleCoa as $coa) {
    echo "  {$coa->coa_code} - {$coa->coa_desc} (Sub2: {$coa->coa_coasub2code})\n";
}
