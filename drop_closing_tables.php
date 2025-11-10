<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Drop tables
DB::statement("IF OBJECT_ID('tr_acc_monthly_closing', 'U') IS NOT NULL DROP TABLE tr_acc_monthly_closing");
DB::statement("IF OBJECT_ID('tr_acc_yearly_closing', 'U') IS NOT NULL DROP TABLE tr_acc_yearly_closing");

echo "Tables dropped successfully!\n";
