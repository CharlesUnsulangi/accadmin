<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\ClosingService;
use Illuminate\Support\Facades\DB;

echo "=== REGENERATE YEARLY CLOSING 2000-2015 (BERURUTAN) ===\n\n";

// Hapus data lama
echo "Menghapus data yearly closing lama...\n";
$deleted = DB::table('tr_acc_yearly_closing')->whereBetween('closing_year', [2000, 2015])->delete();
echo "Dihapus: {$deleted} records\n\n";

$closingService = app(ClosingService::class);
$totalDuration = 0;
$summary = [];

// Loop BERURUTAN dari 2000 sampai 2015 agar opening balance benar
for ($year = 2000; $year <= 2015; $year++) {
    echo "--- TAHUN {$year} ---\n";
    
    try {
        // Check if data exists for this year
        $count = DB::table('tr_acc_transaksi_coa')
            ->whereYear('transcoa_coa_date_ops', $year)
            ->count();
        
        echo "Transaksi: " . number_format($count) . "\n";
        
        // Calculate yearly closing
        $startTime = microtime(true);
        $result = $closingService->calculateYearly($year, true);
        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        $totalDuration += $duration;
        
        // Verify saved
        $saved = DB::table('tr_acc_yearly_closing')
            ->where('closing_year', $year)
            ->count();
        
        echo "COA Processed: " . count($result) . "\n";
        echo "Saved: " . number_format($saved) . " records\n";
        echo "Durasi: " . number_format($duration, 2) . " detik\n";
        
        if ($saved > 0) {
            $stats = DB::table('tr_acc_yearly_closing')
                ->where('closing_year', $year)
                ->selectRaw('
                    SUM(opening_debet) as opening_debet,
                    SUM(opening_kredit) as opening_kredit,
                    SUM(closing_debet) as total_debet,
                    SUM(closing_kredit) as total_kredit,
                    SUM(jumlah_transaksi) as total_transaksi
                ')
                ->first();
            
            echo "Opening Debet: " . number_format($stats->opening_debet, 2) . "\n";
            echo "Opening Kredit: " . number_format($stats->opening_kredit, 2) . "\n";
            echo "Closing Debet: " . number_format($stats->total_debet, 2) . "\n";
            echo "Closing Kredit: " . number_format($stats->total_kredit, 2) . "\n";
            echo "Selisih: " . number_format($stats->total_debet - $stats->total_kredit, 2) . "\n";
            
            $summary[] = [
                'year' => $year,
                'coa_count' => $saved,
                'transaksi' => $stats->total_transaksi,
                'opening_debet' => $stats->opening_debet,
                'opening_kredit' => $stats->opening_kredit,
                'closing_debet' => $stats->total_debet,
                'closing_kredit' => $stats->total_kredit,
                'status' => '✅'
            ];
        } else {
            $summary[] = [
                'year' => $year,
                'coa_count' => 0,
                'transaksi' => 0,
                'opening_debet' => 0,
                'opening_kredit' => 0,
                'closing_debet' => 0,
                'closing_kredit' => 0,
                'status' => '⚠️ No data'
            ];
        }
        
        echo "✅ Selesai\n\n";
        
    } catch (\Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n\n";
        $summary[] = [
            'year' => $year,
            'coa_count' => 0,
            'transaksi' => 0,
            'opening_debet' => 0,
            'opening_kredit' => 0,
            'closing_debet' => 0,
            'closing_kredit' => 0,
            'status' => '❌ Error'
        ];
    }
}

echo "\n=== SUMMARY ===\n\n";
printf("%-6s | %-4s | %-8s | %-15s | %-15s | %-15s | %-15s | %s\n", 
    "Tahun", "COA", "Trans", "Open Debet", "Open Kredit", "Close Debet", "Close Kredit", "Status");
echo str_repeat("-", 130) . "\n";

foreach ($summary as $row) {
    printf("%-6s | %4s | %8s | %15s | %15s | %15s | %15s | %s\n",
        $row['year'],
        number_format($row['coa_count']),
        number_format($row['transaksi']),
        number_format($row['opening_debet'], 2),
        number_format($row['opening_kredit'], 2),
        number_format($row['closing_debet'], 2),
        number_format($row['closing_kredit'], 2),
        $row['status']
    );
}

echo "\n";
echo "Total durasi: " . number_format($totalDuration, 2) . " detik (" . number_format($totalDuration / 60, 2) . " menit)\n";
echo "\n=== SELESAI ===\n";
