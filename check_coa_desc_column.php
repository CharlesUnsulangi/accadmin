<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== CEK KOLOM coa_desc ===\n\n";

$tables = ['tr_acc_monthly_closing', 'tr_acc_yearly_closing'];

foreach ($tables as $table) {
    echo "Table: {$table}\n";
    
    if (Schema::hasTable($table)) {
        $hasCoaDesc = Schema::hasColumn($table, 'coa_desc');
        echo "  Kolom coa_desc: " . ($hasCoaDesc ? '✅ ADA' : '❌ TIDAK ADA') . "\n";
        
        if ($hasCoaDesc) {
            // Cek sample data
            $sample = DB::table($table)
                ->select('coa_code', 'coa_desc')
                ->whereNotNull('coa_desc')
                ->first();
            
            if ($sample) {
                echo "  Sample: {$sample->coa_code} - {$sample->coa_desc}\n";
            } else {
                $count = DB::table($table)->count();
                echo "  Total records: {$count}\n";
                echo "  ⚠️ Semua coa_desc NULL\n";
            }
        }
    } else {
        echo "  ❌ Table tidak ada\n";
    }
    
    echo "\n";
}

echo "=== SELESAI ===\n";
