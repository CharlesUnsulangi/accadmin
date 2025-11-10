<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\ClosingService;
use Illuminate\Support\Facades\DB;

echo "=== GENERATE MONTHLY CLOSING SEQUENTIAL (2016 - 2025) ===\n\n";

// Hapus data monthly closing lama
echo "Menghapus data monthly closing lama (2016-2025)...\n";
$deleted = DB::table('tr_acc_monthly_closing')
    ->whereBetween('closing_year', [2016, 2025])
    ->delete();
echo "Dihapus: {$deleted} records\n\n";

$service = new ClosingService();
$startTime = microtime(true);
$summary = [];

// Loop dari 2016 sampai 2025
for ($year = 2016; $year <= 2025; $year++) {
    // Tentukan bulan terakhir
    $lastMonth = ($year == 2025) ? 11 : 12; // 2025 hanya sampai Nov
    
    for ($month = 1; $month <= $lastMonth; $month++) {
        $periodeStart = microtime(true);
        
        echo "--- {$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . " ---\n";
        
        try {
            // Hitung transaksi dulu
            $startDate = date("Y-m-01", strtotime("{$year}-{$month}-01"));
            $endDate = date("Y-m-t", strtotime("{$year}-{$month}-01"));
            
            $transCount = DB::table('tr_acc_transaksi_coa')
                ->whereBetween('transcoa_coa_date_ops', [$startDate, $endDate])
                ->whereNotNull('transcoa_coa_code')
                ->where('transcoa_coa_code', '!=', '')
                ->where('transcoa_coa_code', '!=', 'NONE')
                ->count();
            
            if ($transCount == 0) {
                echo "⚠️ Tidak ada transaksi - skip\n\n";
                $summary[] = [
                    'year' => $year,
                    'month' => $month,
                    'periode' => "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT),
                    'coa_count' => 0,
                    'trans_count' => 0,
                    'duration' => 0,
                    'status' => 'SKIP',
                ];
                continue;
            }
            
            // Calculate monthly closing
            $result = $service->calculateMonthly($year, $month, true, 'system');
            
            $duration = round(microtime(true) - $periodeStart, 2);
            
            echo "Transaksi: {$transCount}\n";
            echo "COA Processed: " . count($result) . "\n";
            echo "Durasi: {$duration} detik\n";
            
            // Hitung total
            $totalDebet = array_sum(array_column($result, 'closing_debet'));
            $totalKredit = array_sum(array_column($result, 'closing_kredit'));
            $selisih = abs($totalDebet - $totalKredit);
            
            echo "Closing Debet: " . number_format($totalDebet, 2, ',', '.') . "\n";
            echo "Closing Kredit: " . number_format($totalKredit, 2, ',', '.') . "\n";
            echo "Selisih: " . number_format($selisih, 2, ',', '.') . "\n";
            
            $summary[] = [
                'year' => $year,
                'month' => $month,
                'periode' => "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT),
                'coa_count' => count($result),
                'trans_count' => $transCount,
                'closing_debet' => $totalDebet,
                'closing_kredit' => $totalKredit,
                'selisih' => $selisih,
                'duration' => $duration,
                'status' => '✅',
            ];
            
            echo "✅ Selesai\n\n";
            
        } catch (Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n\n";
            $summary[] = [
                'year' => $year,
                'month' => $month,
                'periode' => "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT),
                'coa_count' => 0,
                'trans_count' => 0,
                'duration' => 0,
                'status' => '❌ ERROR',
            ];
        }
    }
}

$totalDuration = round(microtime(true) - $startTime, 2);

echo "\n=== SUMMARY ===\n\n";
echo str_pad("Periode", 10) . " | ";
echo str_pad("COA", 6) . " | ";
echo str_pad("Trans", 10) . " | ";
echo str_pad("Close Debet", 18) . " | ";
echo str_pad("Close Kredit", 18) . " | ";
echo str_pad("Durasi", 8) . " | ";
echo "Status\n";
echo str_repeat("-", 100) . "\n";

$totalCOA = 0;
$totalTrans = 0;

foreach ($summary as $row) {
    echo str_pad($row['periode'], 10) . " | ";
    echo str_pad($row['coa_count'], 6) . " | ";
    echo str_pad(number_format($row['trans_count']), 10) . " | ";
    
    if (isset($row['closing_debet'])) {
        echo str_pad(number_format($row['closing_debet'], 2, ',', '.'), 18) . " | ";
        echo str_pad(number_format($row['closing_kredit'], 2, ',', '.'), 18) . " | ";
    } else {
        echo str_pad("0,00", 18) . " | ";
        echo str_pad("0,00", 18) . " | ";
    }
    
    echo str_pad($row['duration'] . 's', 8) . " | ";
    echo $row['status'] . "\n";
    
    $totalCOA += $row['coa_count'];
    $totalTrans += $row['trans_count'];
}

echo "\nTotal durasi: {$totalDuration} detik (" . round($totalDuration / 60, 2) . " menit)\n";
echo "Total COA records: " . number_format($totalCOA) . "\n";
echo "Total transaksi processed: " . number_format($totalTrans) . "\n";

echo "\n=== SELESAI ===\n";
