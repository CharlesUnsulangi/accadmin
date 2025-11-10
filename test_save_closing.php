<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Services\ClosingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\MonthlyClosing;

echo "=== TEST SAVE CLOSING TO DATABASE ===\n\n";

// Login as system user
$user = User::first();
if ($user) {
    Auth::login($user);
    echo "Logged in as: {$user->name}\n\n";
}

$service = new ClosingService();

// Gunakan periode kecil dulu untuk testing: 2024-11 (November 2024)
// Dari data sebelumnya: 2024-11 ada 53,158 transaksi
echo "TEST: Save Monthly Closing ke Database\n";
echo "Periode: 2024-11 (November 2024)\n\n";

try {
    // Hapus data closing lama untuk periode ini jika ada
    $deleted = MonthlyClosing::where('closing_year', 2024)
        ->where('closing_month', 11)
        ->delete();
    
    if ($deleted > 0) {
        echo "Deleted {$deleted} existing closing records\n\n";
    }
    
    // Calculate dan SAVE
    echo "Calculating and saving to database...\n";
    $startTime = microtime(true);
    
    $result = $service->calculateMonthly(2024, 11, true); // TRUE = save to DB
    
    $endTime = microtime(true);
    $duration = round($endTime - $startTime, 2);
    
    echo "✅ SELESAI dalam {$duration} detik\n\n";
    
    // Verify data tersimpan
    echo "--- VERIFIKASI DATA DI DATABASE ---\n";
    
    $totalSaved = MonthlyClosing::where('closing_year', 2024)
        ->where('closing_month', 11)
        ->count();
    
    echo "Total record tersimpan: {$totalSaved}\n";
    
    // Sample 3 record
    $samples = MonthlyClosing::where('closing_year', 2024)
        ->where('closing_month', 11)
        ->limit(3)
        ->get();
    
    echo "\nSample 3 record pertama:\n";
    foreach ($samples as $s) {
        echo "\n  ID: {$s->id}\n";
        echo "  COA: {$s->coa_code} - {$s->coa_desc}\n";
        echo "  Version: {$s->version_number} ({$s->version_status})\n";
        echo "  Opening: " . number_format($s->opening_balance, 2) . "\n";
        echo "  Mutasi Debet: " . number_format($s->mutasi_debet, 2) . "\n";
        echo "  Mutasi Kredit: " . number_format($s->mutasi_kredit, 2) . "\n";
        echo "  Closing: " . number_format($s->closing_balance, 2) . "\n";
        echo "  Transaksi: {$s->jumlah_transaksi}\n";
        echo "  Created: {$s->created_at}\n";
    }
    
    // Summary statistics
    $summary = MonthlyClosing::where('closing_year', 2024)
        ->where('closing_month', 11)
        ->selectRaw('
            SUM(mutasi_debet) as total_debet,
            SUM(mutasi_kredit) as total_kredit,
            SUM(jumlah_transaksi) as total_transaksi,
            SUM(CASE WHEN closing_balance > 0 THEN closing_balance ELSE 0 END) as total_debet_balance,
            SUM(CASE WHEN closing_balance < 0 THEN ABS(closing_balance) ELSE 0 END) as total_kredit_balance
        ')
        ->first();
    
    echo "\n--- SUMMARY FROM DATABASE ---\n";
    echo "Total Mutasi Debet: " . number_format($summary->total_debet, 2) . "\n";
    echo "Total Mutasi Kredit: " . number_format($summary->total_kredit, 2) . "\n";
    echo "Total Transaksi: " . number_format($summary->total_transaksi) . "\n";
    echo "Total Closing Debet: " . number_format($summary->total_debet_balance, 2) . "\n";
    echo "Total Closing Kredit: " . number_format($summary->total_kredit_balance, 2) . "\n";
    
    // Check dengan data langsung dari transaksi
    echo "\n--- CROSS-CHECK WITH TRANSACTIONS ---\n";
    $directSum = DB::table('tr_acc_transaksi_coa')
        ->whereYear('transcoa_coa_date_ops', 2024)
        ->whereMonth('transcoa_coa_date_ops', 11)
        ->whereNotNull('transcoa_head_code')
        ->where('transcoa_head_code', '!=', '')
        ->where('transcoa_head_code', '!=', 'NONE')
        ->selectRaw('
            SUM(COALESCE(transcoa_debet_value_ops, 0)) as debet,
            SUM(COALESCE(transcoa_credit_value_ops, 0)) as kredit,
            COUNT(*) as jumlah
        ')
        ->first();
    
    echo "Direct from transactions:\n";
    echo "  Debet: " . number_format($directSum->debet, 2) . "\n";
    echo "  Kredit: " . number_format($directSum->kredit, 2) . "\n";
    echo "  Count: " . number_format($directSum->jumlah) . "\n";
    
    // Match check
    $debetMatch = abs($summary->total_debet - $directSum->debet) < 0.01;
    $kreditMatch = abs($summary->total_kredit - $directSum->kredit) < 0.01;
    $countMatch = $summary->total_transaksi == $directSum->jumlah;
    
    echo "\nValidation:\n";
    echo "  Debet match: " . ($debetMatch ? "✅ YES" : "❌ NO") . "\n";
    echo "  Kredit match: " . ($kreditMatch ? "✅ YES" : "❌ NO") . "\n";
    echo "  Count match: " . ($countMatch ? "✅ YES" : "❌ NO") . "\n";
    
    if ($debetMatch && $kreditMatch && $countMatch) {
        echo "\n🎉 PERFECT! Data closing 100% match dengan transaksi!\n";
    } else {
        echo "\n⚠️  Ada selisih antara closing dan transaksi\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
