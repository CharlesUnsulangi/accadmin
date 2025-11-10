<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CEK MATCHING COA ===\n\n";

// Get sample COA from transactions
$transCoa = DB::table('tr_acc_transaksi_coa')
    ->whereYear('transcoa_coa_date_ops', 2024)
    ->whereMonth('transcoa_coa_date_ops', 10)
    ->whereNotNull('transcoa_head_code')
    ->where('transcoa_head_code', '!=', 'NONE')
    ->distinct()
    ->limit(10)
    ->pluck('transcoa_head_code');

echo "Sample 10 COA code dari transaksi Oktober 2024:\n";
foreach ($transCoa as $code) {
    echo "  {$code}";
    
    // Check if exists in ms_acc_coa
    $exists = DB::table('ms_acc_coa')->where('coa_code', $code)->exists();
    echo " → " . ($exists ? "✅ ADA di ms_acc_coa" : "❌ TIDAK ADA di ms_acc_coa");
    
    if (!$exists) {
        // Coba cari dengan LIKE
        $similar = DB::table('ms_acc_coa')
            ->where('coa_code', 'LIKE', "%{$code}%")
            ->orWhere('coa_desc', 'LIKE', "%{$code}%")
            ->first();
        
        if ($similar) {
            echo " (mirip: {$similar->coa_code})";
        }
    }
    
    echo "\n";
}

echo "\nTotal distinct COA di ms_acc_coa: " . DB::table('ms_acc_coa')->count() . "\n";
echo "Total distinct head_code di transaksi Oktober 2024: " . DB::table('tr_acc_transaksi_coa')
    ->whereYear('transcoa_coa_date_ops', 2024)
    ->whereMonth('transcoa_coa_date_ops', 10)
    ->whereNotNull('transcoa_head_code')
    ->where('transcoa_head_code', '!=', 'NONE')
    ->distinct()
    ->count('transcoa_head_code') . "\n";
