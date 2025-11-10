<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\ClosingService;
use Illuminate\Support\Facades\DB;

echo "=== GENERATE YEARLY CLOSING 2001-2015 ===\n\n";

$closingService = app(ClosingService::class);
$totalDuration = 0;
$summary = [];

// Loop dari 2001 sampai 2015
for ($year = 2001; $year <= 2015; $year++) {
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
                    SUM(closing_debet) as total_debet,
                    SUM(closing_kredit) as total_kredit,
                    SUM(jumlah_transaksi) as total_transaksi
                ')
                ->first();
            
            echo "Debet: " . number_format($stats->total_debet, 2) . "\n";
            echo "Kredit: " . number_format($stats->total_kredit, 2) . "\n";
            echo "Selisih: " . number_format($stats->total_debet - $stats->total_kredit, 2) . "\n";
            
            $summary[] = [
                'year' => $year,
                'coa_count' => $saved,
                'transaksi' => $stats->total_transaksi,
                'debet' => $stats->total_debet,
                'kredit' => $stats->total_kredit,
                'status' => '✅'
            ];
        } else {
            $summary[] = [
                'year' => $year,
                'coa_count' => 0,
                'transaksi' => 0,
                'debet' => 0,
                'kredit' => 0,
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
            'debet' => 0,
            'kredit' => 0,
            'status' => '❌ Error'
        ];
    }
}

echo "\n=== SUMMARY ===\n\n";
printf("%-6s | %-10s | %-12s | %-20s | %-20s | %s\n", 
    "Tahun", "COA", "Transaksi", "Debet", "Kredit", "Status");
echo str_repeat("-", 100) . "\n";

foreach ($summary as $row) {
    printf("%-6s | %10s | %12s | %20s | %20s | %s\n",
        $row['year'],
        number_format($row['coa_count']),
        number_format($row['transaksi']),
        number_format($row['debet'], 2),
        number_format($row['kredit'], 2),
        $row['status']
    );
}

echo "\n";
echo "Total durasi: " . number_format($totalDuration, 2) . " detik (" . number_format($totalDuration / 60, 2) . " menit)\n";
echo "\n=== SELESAI ===\n";
