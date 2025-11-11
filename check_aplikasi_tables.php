<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Mencari tabel aplikasi...\n\n";

$tables = DB::select("
    SELECT TABLE_NAME 
    FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_NAME LIKE '%aplikasi%' OR TABLE_NAME LIKE '%admin_it%'
    ORDER BY TABLE_NAME
");

if (empty($tables)) {
    echo "Tidak ada tabel ditemukan.\n";
} else {
    echo "Tabel ditemukan:\n";
    foreach ($tables as $t) {
        echo "  - {$t->TABLE_NAME}\n";
    }
}

echo "\n";
