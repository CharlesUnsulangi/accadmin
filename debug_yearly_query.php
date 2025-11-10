<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\YearlyClosing;
use Illuminate\Support\Facades\DB;

echo "=== DEBUG YEARLY CLOSING QUERY ===\n\n";

// Cek data tahun 2000
echo "Data di database (tahun 2000):\n";
$db = DB::table('tr_acc_yearly_closing')
    ->where('closing_year', 2000)
    ->select('coa_code', 'closing_balance', 'version_status')
    ->get();
echo "Total: " . count($db) . "\n";
foreach ($db as $row) {
    echo "  {$row->coa_code}: {$row->closing_balance} (status: {$row->version_status})\n";
}
echo "\n";

// Cek pakai Model
echo "Pakai Model YearlyClosing:\n";
$model = YearlyClosing::where('closing_year', 2000)
    ->where('version_status', 'ACTIVE')
    ->get();
echo "Total: " . count($model) . "\n";
foreach ($model as $row) {
    echo "  {$row->coa_code}: {$row->closing_balance}\n";
}
echo "\n";

// Cek pakai keyBy
echo "Pakai keyBy:\n";
$keyed = YearlyClosing::where('closing_year', 2000)
    ->where('version_status', 'ACTIVE')
    ->get()
    ->keyBy('coa_code');
echo "Total: " . count($keyed) . "\n";
echo "Keys: " . implode(', ', $keyed->keys()->toArray()) . "\n";
echo "\n";

// Test get by key
$test = $keyed->get('43');
echo "Get key '43': " . ($test ? "Found - {$test->closing_balance}" : "Not found") . "\n";

echo "\n=== SELESAI ===\n";
