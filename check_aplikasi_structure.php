<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Struktur tabel ms_admin_it_aplikasi:\n\n";

$columns = DB::select("
    SELECT 
        COLUMN_NAME,
        DATA_TYPE,
        CHARACTER_MAXIMUM_LENGTH,
        IS_NULLABLE,
        COLUMN_DEFAULT
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'ms_admin_it_aplikasi'
    ORDER BY ORDINAL_POSITION
");

foreach ($columns as $col) {
    $type = $col->DATA_TYPE;
    if ($col->CHARACTER_MAXIMUM_LENGTH) {
        $type .= "({$col->CHARACTER_MAXIMUM_LENGTH})";
    }
    
    $nullable = $col->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL';
    $default = $col->COLUMN_DEFAULT ? "DEFAULT {$col->COLUMN_DEFAULT}" : '';
    
    echo sprintf("%-30s %-20s %-10s %s\n", 
        $col->COLUMN_NAME, 
        $type, 
        $nullable,
        $default
    );
}

echo "\n";

// Check sample data
echo "Sample data:\n";
$sample = DB::table('ms_admin_it_aplikasi')->first();
if ($sample) {
    print_r($sample);
} else {
    echo "  (tidak ada data)\n";
}
