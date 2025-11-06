<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking COA-related tables:\n";
echo str_repeat("=", 50) . "\n";

$tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE '%coa%' ORDER BY TABLE_NAME");

foreach ($tables as $table) {
    $count = DB::table($table->TABLE_NAME)->count();
    echo sprintf("%-40s : %d records\n", $table->TABLE_NAME, $count);
}

echo "\nChecking specific tables:\n";
echo str_repeat("=", 50) . "\n";

$checkTables = [
    'ms_acc_coa',
    'ms_acc_coa_main',
    'ms_acc_coa_main_sub1',
    'ms_acc_coa_main_sub2',
    'ms_acc_main_sub1',
    'ms_acc_main_sub2',
];

foreach ($checkTables as $tableName) {
    try {
        $count = DB::table($tableName)->count();
        echo sprintf("%-40s : EXISTS (%d records)\n", $tableName, $count);
    } catch (\Exception $e) {
        echo sprintf("%-40s : NOT FOUND\n", $tableName);
    }
}
