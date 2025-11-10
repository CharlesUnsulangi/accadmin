<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CEK DATA TRANSAKSI PALING LAMA ===\n\n";

// Cek tahun paling lama di tr_acc_transaksi_coa
echo "1. Cek dari kolom transcoa_coa_date_ops:\n";
$oldest = DB::table('tr_acc_transaksi_coa')
    ->whereNotNull('transcoa_coa_date_ops')
    ->where('transcoa_coa_date_ops', '!=', '1900-01-01')
    ->orderBy('transcoa_coa_date_ops', 'asc')
    ->first();

if ($oldest) {
    echo "   Tanggal paling lama: {$oldest->transcoa_coa_date_ops}\n";
    echo "   COA Code: {$oldest->transcoa_head_code}\n\n";
}

// Cek range tahun yang ada
echo "2. Range tahun yang ada di data:\n";
$years = DB::table('tr_acc_transaksi_coa')
    ->selectRaw("YEAR(transcoa_coa_date_ops) as tahun, COUNT(*) as jumlah")
    ->whereNotNull('transcoa_coa_date_ops')
    ->where('transcoa_coa_date_ops', '!=', '1900-01-01')
    ->groupBy(DB::raw("YEAR(transcoa_coa_date_ops)"))
    ->orderBy('tahun', 'asc')
    ->get();

echo "\n";
printf("   %-10s | %15s\n", "Tahun", "Jumlah Transaksi");
echo "   " . str_repeat("-", 30) . "\n";

foreach ($years as $year) {
    printf("   %-10s | %15s\n", $year->tahun, number_format($year->jumlah));
}

echo "\n";
echo "   Tahun paling lama: " . $years->first()->tahun . "\n";
echo "   Tahun paling baru: " . $years->last()->tahun . "\n";
echo "   Total tahun: " . $years->count() . " tahun\n";

echo "\n=== SELESAI ===\n";
