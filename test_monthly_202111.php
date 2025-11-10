<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\ClosingService;
use Illuminate\Support\Facades\DB;

echo "=== TEST MONTHLY CLOSING (2021-11) ===\n\n";

// Hapus data test
DB::table('tr_acc_monthly_closing')
    ->where('closing_year', 2021)
    ->where('closing_month', 11)
    ->delete();

$service = new ClosingService();

try {
    echo "Generating monthly closing 2021-11...\n";
    $result = $service->calculateMonthly(2021, 11, true, 'system');
    
    echo "✅ SUKSES!\n";
    echo "Total COA: " . count($result) . "\n\n";
    
    // Tampilkan 5 sample
    echo "Sample 5 COA:\n";
    $count = 0;
    foreach ($result as $row) {
        echo "  {$row['coa_code']}: {$row['coa_desc']}\n";
        echo "    Opening: {$row['opening_debet']} / {$row['opening_kredit']}\n";
        echo "    Mutasi: {$row['mutasi_debet']} / {$row['mutasi_kredit']}\n";
        echo "    Closing: {$row['closing_debet']} / {$row['closing_kredit']}\n\n";
        
        $count++;
        if ($count >= 5) break;
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "=== SELESAI ===\n";
