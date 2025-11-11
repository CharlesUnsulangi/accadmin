<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "  CHECK APPLICATION MANAGEMENT STATUS      \n";
echo "===========================================\n\n";

try {
    $count = DB::table('ms_admin_it_aplikasi')->count();
    
    echo "📊 Total aplikasi: {$count}\n\n";
    
    if ($count > 0) {
        $apps = DB::table('ms_admin_it_aplikasi')
            ->select('ms_admin_it_aplikasi_id', 'apps_desc', 'cek_non_aktif', 'user_created', 'time_created')
            ->get();
        
        echo "Daftar Aplikasi:\n";
        echo "----------------------------------------\n";
        foreach ($apps as $a) {
            $status = $a->cek_non_aktif ? '❌ Inactive' : '✅ Active';
            echo "  • {$a->apps_desc}\n";
            echo "    ID: {$a->ms_admin_it_aplikasi_id}\n";
            echo "    Status: {$status}\n";
            echo "    Created by: {$a->user_created} at {$a->time_created}\n";
            echo "\n";
        }
    }
    
    echo "✅ Application Management System: READY\n";
    echo "🌐 URL: http://127.0.0.1:8001/applications\n\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}
