<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CEK TRANSAKSI BY YEAR-MONTH ===\n\n";

// Count by year-month using _ops date
$perPeriode = DB::select("
    SELECT 
        YEAR(transcoa_coa_date_ops) as tahun,
        MONTH(transcoa_coa_date_ops) as bulan,
        COUNT(*) as jumlah
    FROM tr_acc_transaksi_coa
    WHERE transcoa_coa_date_ops IS NOT NULL
        AND transcoa_head_code IS NOT NULL
        AND transcoa_head_code != ''
        AND transcoa_head_code != 'NONE'
    GROUP BY YEAR(transcoa_coa_date_ops), MONTH(transcoa_coa_date_ops)
    ORDER BY tahun, bulan
");

echo "Distribusi transaksi per periode (dengan head_code valid):\n\n";
foreach ($perPeriode as $p) {
    $bulan = str_pad($p->bulan, 2, '0', STR_PAD_LEFT);
    echo "{$p->tahun}-{$bulan}: " . number_format($p->jumlah) . " transaksi\n";
}

// Sample dari periode terbanyak
if (count($perPeriode) > 0) {
    usort($perPeriode, function($a, $b) {
        return $b->jumlah - $a->jumlah;
    });
    
    $top = $perPeriode[0];
    echo "\nPeriode dengan transaksi terbanyak: {$top->tahun}-" . str_pad($top->bulan, 2, '0', STR_PAD_LEFT) . " ({$top->jumlah} transaksi)\n";
    
    // Sample 3 transaksi
    $sample = DB::table('tr_acc_transaksi_coa')
        ->whereYear('transcoa_coa_date_ops', $top->tahun)
        ->whereMonth('transcoa_coa_date_ops', $top->bulan)
        ->whereNotNull('transcoa_head_code')
        ->where('transcoa_head_code', '!=', 'NONE')
        ->limit(3)
        ->get(['transcoa_head_code', 'transcoa_coa_date_ops', 'transcoa_debet_value_ops', 'transcoa_credit_value_ops']);
    
    echo "\nSample 3 transaksi:\n";
    foreach ($sample as $s) {
        echo "  COA: {$s->transcoa_head_code} | Date: {$s->transcoa_coa_date_ops} | Debet: " . number_format($s->transcoa_debet_value_ops ?? 0, 2) . " | Kredit: " . number_format($s->transcoa_credit_value_ops ?? 0, 2) . "\n";
    }
}
