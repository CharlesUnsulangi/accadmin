<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ANALISIS COA CODE ===\n\n";

// Check transcoa_coa_code distribution
$coaCodeDist = DB::table('tr_acc_transaksi_coa')
    ->selectRaw('transcoa_coa_code, COUNT(*) as jumlah')
    ->groupBy('transcoa_coa_code')
    ->orderBy('jumlah', 'desc')
    ->limit(10)
    ->get();

echo "Top 10 transcoa_coa_code:\n";
foreach ($coaCodeDist as $row) {
    $code = $row->transcoa_coa_code ?: 'NULL';
    echo "  {$code}: " . number_format($row->jumlah) . " transaksi\n";
}

// Check transcoa_head_code
echo "\n--- transcoa_head_code ---\n";
$headCodeDist = DB::table('tr_acc_transaksi_coa')
    ->selectRaw('transcoa_head_code, COUNT(*) as jumlah')
    ->whereNotNull('transcoa_head_code')
    ->where('transcoa_head_code', '!=', '')
    ->where('transcoa_head_code', '!=', 'NONE')
    ->groupBy('transcoa_head_code')
    ->orderBy('jumlah', 'desc')
    ->limit(10)
    ->get();

echo "Top 10 transcoa_head_code (bukan NULL/NONE):\n";
foreach ($headCodeDist as $row) {
    echo "  {$row->transcoa_head_code}: " . number_format($row->jumlah) . " transaksi\n";
}

// Check if head_code exists in ms_acc_coa
if (count($headCodeDist) > 0) {
    $sampleHeadCode = $headCodeDist[0]->transcoa_head_code;
    $exists = DB::table('ms_acc_coa')->where('coa_code', $sampleHeadCode)->exists();
    echo "\nCek COA '{$sampleHeadCode}' exists di ms_acc_coa: " . ($exists ? 'YA ✅' : 'TIDAK ❌') . "\n";
}

// Sample dengan date ops
echo "\n--- Sample transaksi dengan data OPS ---\n";
$sampleOps = DB::table('tr_acc_transaksi_coa')
    ->whereNotNull('transcoa_coa_date_ops')
    ->where('transcoa_coa_date_ops', '>', '2023-01-01')
    ->limit(3)
    ->get(['transcoa_code', 'transcoa_head_code', 'transcoa_coa_date_ops', 'transcoa_debet_value_ops', 'transcoa_credit_value_ops']);

foreach ($sampleOps as $row) {
    echo "Code: {$row->transcoa_code}\n";
    echo "  Head: {$row->transcoa_head_code}\n";
    echo "  Date: {$row->transcoa_coa_date_ops}\n";
    echo "  Debet: " . number_format($row->transcoa_debet_value_ops ?? 0, 2) . "\n";
    echo "  Kredit: " . number_format($row->transcoa_credit_value_ops ?? 0, 2) . "\n\n";
}
