<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\ClosingService;
use Illuminate\Support\Facades\DB;

echo "=== TEST CARRY FORWARD (TAHUN 2000-2002) ===\n\n";

// Hapus data test
DB::table('tr_acc_yearly_closing')
    ->whereBetween('closing_year', [2000, 2002])
    ->delete();

$service = new ClosingService();

// Generate tahun 2000
echo "--- TAHUN 2000 ---\n";
$result2000 = $service->calculateYearly(2000, true, 'system', true);
echo "Total COA: " . count($result2000) . "\n";
foreach ($result2000 as $row) {
    echo "  {$row['coa_code']}: Opening={$row['opening_balance']}, Mutasi={$row['mutasi_netto']}, Closing={$row['closing_balance']}\n";
}
echo "\n";

// Generate tahun 2001 (tidak ada transaksi, tapi harus carry forward dari 2000)
echo "--- TAHUN 2001 (Tidak ada transaksi, harus carry forward) ---\n";
$result2001 = $service->calculateYearly(2001, true, 'system', true);
echo "Total COA: " . count($result2001) . "\n";
if (count($result2001) > 0) {
    echo "✅ CARRY FORWARD BERHASIL!\n";
    foreach ($result2001 as $row) {
        echo "  {$row['coa_code']}: Opening={$row['opening_balance']}, Mutasi={$row['mutasi_netto']}, Closing={$row['closing_balance']}\n";
    }
} else {
    echo "❌ CARRY FORWARD GAGAL - tidak ada COA yang dibawa dari tahun 2000\n";
}
echo "\n";

// Generate tahun 2002 (tidak ada transaksi, harus carry forward dari 2001)
echo "--- TAHUN 2002 (Tidak ada transaksi, harus carry forward dari 2001) ---\n";
$result2002 = $service->calculateYearly(2002, true, 'system', true);
echo "Total COA: " . count($result2002) . "\n";
if (count($result2002) > 0) {
    echo "✅ CARRY FORWARD BERHASIL!\n";
    foreach ($result2002 as $row) {
        echo "  {$row['coa_code']}: Opening={$row['opening_balance']}, Mutasi={$row['mutasi_netto']}, Closing={$row['closing_balance']}\n";
    }
} else {
    echo "❌ CARRY FORWARD GAGAL\n";
}
echo "\n";

// Verifikasi di database
echo "=== VERIFIKASI DATABASE ===\n";
$dbData = DB::table('tr_acc_yearly_closing')
    ->whereBetween('closing_year', [2000, 2002])
    ->orderBy('closing_year')
    ->orderBy('coa_code')
    ->get();

$grouped = $dbData->groupBy('closing_year');
foreach ($grouped as $year => $records) {
    echo "Tahun {$year}: " . count($records) . " COA\n";
}

echo "\n=== SELESAI ===\n";
