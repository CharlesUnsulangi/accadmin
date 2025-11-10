<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== SAMPLE DATA YEARLY CLOSING (WITH coa_desc) ===\n\n";

$data = DB::table('tr_acc_yearly_closing')
    ->select('closing_year', 'coa_code', 'coa_desc', 'closing_debet', 'closing_kredit')
    ->orderBy('closing_year')
    ->orderBy('coa_code')
    ->get();

echo "Total records: " . count($data) . "\n\n";

foreach ($data as $row) {
    echo "Tahun {$row->closing_year} | COA: {$row->coa_code}\n";
    echo "  Deskripsi: " . ($row->coa_desc ?: '(NULL)') . "\n";
    echo "  Closing: Rp " . number_format($row->closing_debet, 2, ',', '.') . " / Rp " . number_format($row->closing_kredit, 2, ',', '.') . "\n";
    echo "\n";
}

echo "=== SELESAI ===\n";
