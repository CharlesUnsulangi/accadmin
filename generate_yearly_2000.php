<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\ClosingService;
use Illuminate\Support\Facades\DB;

// Get year from command line argument or default to 2000
$year = isset($argv[1]) ? (int)$argv[1] : 2000;

echo "=== GENERATE YEARLY CLOSING {$year} ===\n\n";

try {
    // Check if data exists for this year
    $count = DB::table('tr_acc_transaksi_coa')
        ->whereYear('transcoa_coa_date_ops', $year)
        ->count();
    
    if ($count == 0) {
        echo "⚠️  Tidak ada transaksi untuk tahun {$year}\n";
        echo "   Yearly closing tetap akan di-generate (balance dari tahun sebelumnya).\n\n";
    } else {
        echo "📊 Ditemukan " . number_format($count) . " transaksi untuk tahun {$year}\n\n";
    }
    
    $closingService = app(ClosingService::class);
    
    echo "Memulai perhitungan yearly closing {$year}...\n";
    $startTime = microtime(true);
    
    // Calculate yearly closing untuk tahun yang diminta
    $result = $closingService->calculateYearly($year, true);
    
    $endTime = microtime(true);
    $duration = $endTime - $startTime;
    
    echo "✅ Selesai!\n\n";
    echo "Total COA: " . count($result) . "\n";
    echo "Durasi: " . number_format($duration, 2) . " detik\n\n";
    
    // Verify data tersimpan
    $saved = DB::table('tr_acc_yearly_closing')
        ->where('closing_year', $year)
        ->count();
    
    echo "Data tersimpan di database: " . number_format($saved) . " records\n";
    
    if ($saved > 0) {
        // Summary
        $summary = DB::table('tr_acc_yearly_closing')
            ->where('closing_year', $year)
            ->selectRaw('
                SUM(closing_debet) as total_debet,
                SUM(closing_kredit) as total_kredit,
                SUM(jumlah_transaksi) as total_transaksi
            ')
            ->first();
        
        echo "\nSUMMARY:\n";
        echo "Total Closing Debet: " . number_format($summary->total_debet, 2) . "\n";
        echo "Total Closing Kredit: " . number_format($summary->total_kredit, 2) . "\n";
        echo "Total Transaksi: " . number_format($summary->total_transaksi) . "\n";
        echo "Selisih: " . number_format($summary->total_debet - $summary->total_kredit, 2) . "\n";
    }
    
    echo "\n=== SELESAI ===\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
