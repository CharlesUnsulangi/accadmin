<?php

/**
 * Script untuk menambahkan topik secara manual ke aplikasi
 * Usage: php add_topic_manually.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Ambil aplikasi yang tersedia
    echo "=== Daftar Aplikasi ===\n";
    $applications = DB::table('ms_admin_it_aplikasi')
        ->select('ms_admin_it_aplikasi_id', DB::raw('CAST(apps_desc AS VARCHAR(MAX)) as apps_desc'))
        ->get();
    
    if ($applications->isEmpty()) {
        echo "Tidak ada aplikasi. Buat aplikasi dulu!\n";
        exit(1);
    }
    
    foreach ($applications as $app) {
        echo "{$app->ms_admin_it_aplikasi_id} - {$app->apps_desc}\n";
    }
    
    echo "\n=== Menambahkan Topik ke AccAdmin ===\n";
    
    // Cari aplikasi AccAdmin (cari yang mengandung "AccAdmin")
    $accadmin = DB::table('ms_admin_it_aplikasi')
        ->select('ms_admin_it_aplikasi_id', DB::raw('CAST(apps_desc AS VARCHAR(MAX)) as apps_desc'))
        ->where(DB::raw('CAST(apps_desc AS VARCHAR(MAX))'), 'like', '%AccAdmin%')
        ->first();
    
    if (!$accadmin) {
        echo "❌ Aplikasi AccAdmin tidak ditemukan!\n";
        exit(1);
    }
    
    $accadminId = $accadmin->ms_admin_it_aplikasi_id;
    echo "Aplikasi: {$accadmin->apps_desc} (ID: {$accadminId})\n\n";
    
    // Topik yang akan ditambahkan
    $topics = [
        ['topic_desc' => 'User Authentication & Authorization', 'priority' => 1],
        ['topic_desc' => 'Database Backup & Recovery', 'priority' => 2],
        ['topic_desc' => 'API Documentation', 'priority' => 3],
        ['topic_desc' => 'Report Generation', 'priority' => 4],
        ['topic_desc' => 'Data Export & Import', 'priority' => 5],
    ];
    
    foreach ($topics as $topic) {
        // Cek apakah topik sudah ada
        $exists = DB::table('ms_admin_it_aplikasi_topic')
            ->where('ms_admin_it_aplikasi_id', $accadminId)
            ->where(DB::raw('CAST(topic_desc AS VARCHAR(MAX))'), $topic['topic_desc'])
            ->exists();
        
        if ($exists) {
            echo "❌ Topik '{$topic['topic_desc']}' sudah ada\n";
            continue;
        }
        
        // Insert topik baru (tanpa created_at/updated_at)
        DB::table('ms_admin_it_aplikasi_topic')->insert([
            'topic_desc' => $topic['topic_desc'],
            'value_priority' => $topic['priority'],
            'ms_admin_it_aplikasi_id' => $accadminId,
        ]);
        
        echo "✓ Topik '{$topic['topic_desc']}' berhasil ditambahkan (Priority: {$topic['priority']})\n";
    }
    
    // Tampilkan hasil akhir
    echo "\n=== Daftar Topik AccAdmin ===\n";
    $finalTopics = DB::table('ms_admin_it_aplikasi_topic')
        ->select(
            'ms_admin_it_topic',
            DB::raw('CAST(topic_desc AS VARCHAR(MAX)) as topic_desc'),
            'value_priority'
        )
        ->where('ms_admin_it_aplikasi_id', $accadminId)
        ->orderBy('value_priority')
        ->get();
    
    foreach ($finalTopics as $t) {
        echo "[{$t->value_priority}] {$t->topic_desc} (ID: {$t->ms_admin_it_topic})\n";
    }
    
    echo "\n✅ Selesai! Total topik: " . $finalTopics->count() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
