<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CEK DATA TRANSAKSI ===\n\n";

// Count total transaksi
$total = DB::table('tr_acc_transaksi_coa')
    ->where('transcoa_statusposting', 'Y')
    ->count();

echo "Total transaksi posted: {$total}\n";

if ($total > 0) {
    // First transaction
    $first = DB::table('tr_acc_transaksi_coa')
        ->where('transcoa_statusposting', 'Y')
        ->orderBy('transcoa_tgl')
        ->first();
    
    echo "Transaksi pertama: {$first->transcoa_tgl} - COA: {$first->transcoa_coacode}\n";
    
    // Last transaction
    $last = DB::table('tr_acc_transaksi_coa')
        ->where('transcoa_statusposting', 'Y')
        ->orderBy('transcoa_tgl', 'desc')
        ->first();
    
    echo "Transaksi terakhir: {$last->transcoa_tgl} - COA: {$last->transcoa_coacode}\n\n";
    
    // Transaksi per tahun dan bulan
    $perPeriode = DB::table('tr_acc_transaksi_coa')
        ->selectRaw('YEAR(transcoa_tgl) as tahun, MONTH(transcoa_tgl) as bulan, COUNT(*) as jumlah')
        ->where('transcoa_statusposting', 'Y')
        ->groupBy(DB::raw('YEAR(transcoa_tgl)'), DB::raw('MONTH(transcoa_tgl)'))
        ->orderBy(DB::raw('YEAR(transcoa_tgl)'))
        ->orderBy(DB::raw('MONTH(transcoa_tgl)'))
        ->get();
    
    echo "Distribusi per periode:\n";
    foreach ($perPeriode as $periode) {
        $bulan = str_pad($periode->bulan, 2, '0', STR_PAD_LEFT);
        echo "  {$periode->tahun}-{$bulan}: {$periode->jumlah} transaksi\n";
    }
    
    // Count active COA
    $totalCoa = DB::table('ms_acc_coa')->where('rec_status', 'A')->count();
    echo "\nTotal COA aktif: {$totalCoa}\n";
} else {
    echo "\n⚠️  Tidak ada transaksi posted!\n";
}
