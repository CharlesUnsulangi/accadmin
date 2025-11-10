<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$result = DB::select("
    SELECT DATA_TYPE, CHARACTER_MAXIMUM_LENGTH 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'ms_acc_coa' 
    AND COLUMN_NAME = 'coa_code'
");

echo "ms_acc_coa.coa_code type: " . $result[0]->DATA_TYPE;
if ($result[0]->CHARACTER_MAXIMUM_LENGTH) {
    echo "(" . $result[0]->CHARACTER_MAXIMUM_LENGTH . ")";
}
echo "\n";
