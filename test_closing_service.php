<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Services\ClosingService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

echo "=== TEST CLOSING SERVICE ===\n\n";

// Login as system user (gunakan user pertama atau buat mock)
$user = User::first();
if ($user) {
    Auth::login($user);
    echo "Logged in as: {$user->name}\n\n";
}

$service = new ClosingService();

// Test 1: Calculate Monthly Closing untuk bulan dengan data terbanyak
echo "TEST 1: Monthly Closing - Preview (tidak save ke DB)\n";
echo "Calculating for: 2024-10 (Oktober 2024)\n";

try {
    $result = $service->calculateMonthly(2024, 10, false); // false = preview only
    
    echo "Total COA processed: " . count($result) . "\n";
    
    if (count($result) > 0) {
        echo "\nSample 3 COA pertama:\n";
        $counter = 0;
        foreach ($result as $coa) {
            if ($counter >= 3) break;
            
            echo "\n  COA: {$coa['coa_code']} - {$coa['coa_desc']}\n";
            echo "    Hierarchy:\n";
            echo "      Main: {$coa['coa_main_code']} - {$coa['coa_main_desc']}\n";
            echo "      Sub1: {$coa['coasub1_code']} - {$coa['coasub1_desc']}\n";
            echo "      Sub2: {$coa['coasub2_code']} - {$coa['coasub2_desc']}\n";
            echo "    Opening: " . number_format($coa['opening_balance'], 2) . "\n";
            echo "    Mutasi Debet: " . number_format($coa['mutasi_debet'], 2) . "\n";
            echo "    Mutasi Kredit: " . number_format($coa['mutasi_kredit'], 2) . "\n";
            echo "    Mutasi Netto: " . number_format($coa['mutasi_netto'], 2) . "\n";
            echo "    Closing: " . number_format($coa['closing_balance'], 2) . "\n";
            echo "    Transaksi: {$coa['jumlah_transaksi']} record\n";
            
            $counter++;
        }
        
        // Summary
        $totalMutasiDebet = array_sum(array_column($result, 'mutasi_debet'));
        $totalMutasiKredit = array_sum(array_column($result, 'mutasi_kredit'));
        $totalTransaksi = array_sum(array_column($result, 'jumlah_transaksi'));
        
        echo "\n--- SUMMARY ---\n";
        echo "Total Mutasi Debet: " . number_format($totalMutasiDebet, 2) . "\n";
        echo "Total Mutasi Kredit: " . number_format($totalMutasiKredit, 2) . "\n";
        echo "Total Transaksi: " . number_format($totalTransaksi) . "\n";
        
        echo "\n✅ TEST BERHASIL!\n";
    } else {
        echo "⚠️  Tidak ada data closing yang dihasilkan\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
