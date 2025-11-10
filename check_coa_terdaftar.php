<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CEK coa_desc (TERDAFTAR vs TIDAK TERDAFTAR) ===\n\n";

// Cek yearly closing
$yearly = DB::table('tr_acc_yearly_closing')
    ->select('coa_code', 'coa_desc', 'closing_debet', 'closing_kredit')
    ->orderBy('coa_code')
    ->get();

echo "YEARLY CLOSING:\n";
echo "Total COA: " . count($yearly) . "\n\n";

$terdaftar = 0;
$tidakTerdaftar = 0;

foreach ($yearly as $row) {
    if ($row->coa_desc === 'Tidak Terdaftar') {
        $tidakTerdaftar++;
        echo "❌ COA {$row->coa_code}: {$row->coa_desc}\n";
        echo "   Closing: Rp " . number_format($row->closing_debet, 2, ',', '.') . " / Rp " . number_format($row->closing_kredit, 2, ',', '.') . "\n";
    } else {
        $terdaftar++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "✅ Terdaftar di ms_acc_coa: {$terdaftar}\n";
echo "❌ Tidak Terdaftar: {$tidakTerdaftar}\n";

if ($terdaftar > 0) {
    echo "\nSample COA Terdaftar:\n";
    $samples = DB::table('tr_acc_yearly_closing')
        ->where('coa_desc', '!=', 'Tidak Terdaftar')
        ->select('coa_code', 'coa_desc')
        ->limit(5)
        ->get();
    
    foreach ($samples as $sample) {
        echo "  ✅ {$sample->coa_code}: {$sample->coa_desc}\n";
    }
}

echo "\n=== SELESAI ===\n";
