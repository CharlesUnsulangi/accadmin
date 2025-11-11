<?php

/**
 * Script untuk menambahkan aplikasi pertama: ACCADMIN
 * Menambahkan entry ke tabel ms_admin_it_aplikasi
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "  INSERT ACCADMIN APPLICATION TO DATABASE  \n";
echo "===========================================\n\n";

try {
    // Check if table exists
    $tableExists = DB::select("
        SELECT COUNT(*) as count
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_NAME = 'ms_admin_it_aplikasi' AND TABLE_SCHEMA = 'dbo'
    ");

    if ($tableExists[0]->count == 0) {
        echo "❌ ERROR: Table ms_admin_it_aplikasi tidak ditemukan!\n";
        echo "   Silakan buat tabel terlebih dahulu.\n\n";
        exit(1);
    }

    // Check if ACCADMIN already exists
    $exists = DB::table('ms_admin_it_aplikasi')
        ->whereRaw("CAST(apps_desc AS VARCHAR(MAX)) LIKE '%ACCADMIN%'")
        ->exists();

    if ($exists) {
        echo "ℹ️  ACCADMIN sudah terdaftar di database.\n";
        echo "   Menampilkan data...\n\n";
        
        $app = DB::table('ms_admin_it_aplikasi')
            ->whereRaw("CAST(apps_desc AS VARCHAR(MAX)) LIKE '%ACCADMIN%'")
            ->first();
        
        echo "Application ID   : {$app->ms_admin_it_aplikasi_id}\n";
        echo "Name             : {$app->apps_desc}\n";
        echo "Status           : " . ($app->cek_non_aktif ? 'Inactive' : 'Active') . "\n";
        echo "Created By       : {$app->user_created}\n";
        echo "Created Time     : {$app->time_created}\n";
        echo "Notes            : " . ($app->aplikasi_note ?? '-') . "\n\n";
        
        exit(0);
    }

    // Generate ID
    $appId = 'APP-' . strtoupper(substr(md5('ACCADMIN' . time()), 0, 10));

    // Insert ACCADMIN (id is auto-increment, don't include it)
    echo "📝 Menambahkan aplikasi ACCADMIN...\n\n";

    DB::table('ms_admin_it_aplikasi')->insert([
        'ms_admin_it_aplikasi_id' => $appId,
        'apps_desc' => 'AccAdmin - Accounting Administration System',
        'user_created' => 'SYSTEM',
        'time_created' => now()->format('H:i:s'),
        'cek_non_aktif' => 0,
        'aplikasi_note' => 'Sistem administrasi accounting yang mengelola Chart of Accounts (COA), transaksi jurnal, proses closing bulanan, laporan keuangan, dan master data. Database: RCM_DEV_HGS_SB. URL: http://127.0.0.1:8001'
    ]);

    echo "✅ BERHASIL! Aplikasi ACCADMIN berhasil ditambahkan.\n\n";
    echo "Details:\n";
    echo "----------------------------------------\n";
    echo "Application ID   : {$appId}\n";
    echo "Name             : AccAdmin - Accounting Administration System\n";
    echo "Status           : Active\n";
    echo "Database         : RCM_DEV_HGS_SB\n";
    echo "----------------------------------------\n\n";

    echo "🎯 Silakan akses halaman Application Management di:\n";
    echo "   http://127.0.0.1:8001/applications\n\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}
