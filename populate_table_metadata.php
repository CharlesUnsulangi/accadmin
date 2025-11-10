<?php

/**
 * Script to populate tr_admin_it_aplikasi_table with database table metadata
 * Run this file with: php populate_table_metadata.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Populating tr_admin_it_aplikasi_table ===\n\n";

try {
    // Get all tables from INFORMATION_SCHEMA
    $tables = DB::select("
        SELECT 
            TABLE_NAME,
            TABLE_SCHEMA
        FROM INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_TYPE = 'BASE TABLE' 
        AND TABLE_SCHEMA = 'dbo'
        ORDER BY TABLE_NAME
    ");

    echo "Found " . count($tables) . " tables in database\n\n";

    $inserted = 0;
    $skipped = 0;

    foreach ($tables as $table) {
        $tableName = $table->TABLE_NAME;
        
        // Check if already exists (convert text to varchar for comparison)
        $exists = DB::table('tr_admin_it_aplikasi_table')
            ->whereRaw("CAST(table_name AS VARCHAR(MAX)) = ?", [$tableName])
            ->exists();
        
        if ($exists) {
            echo "SKIP: {$tableName} (already exists)\n";
            $skipped++;
            continue;
        }

        // Generate ID
        $id = 'TBL-' . strtoupper(substr(md5($tableName . time()), 0, 10));

        // Get row count
        try {
            $count = DB::table($tableName)->count();
        } catch (\Exception $e) {
            $count = 0;
        }

        // Get date range if possible
        $dateStart = null;
        $dateLast = null;
        
        try {
            // Try to find date columns
            $columns = DB::select("
                SELECT COLUMN_NAME, DATA_TYPE, ORDINAL_POSITION
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_NAME = ? 
                AND DATA_TYPE IN ('date', 'datetime', 'datetime2', 'smalldatetime')
                ORDER BY ORDINAL_POSITION
            ", [$tableName]);

            if (!empty($columns) && $count > 0) {
                // Try multiple date columns to find the best one
                foreach ($columns as $col) {
                    $dateColumn = $col->COLUMN_NAME;
                    
                    try {
                        $dateRange = DB::table($tableName)
                            ->selectRaw("
                                MIN(CAST([{$dateColumn}] AS DATE)) as min_date, 
                                MAX(CAST([{$dateColumn}] AS DATE)) as max_date
                            ")
                            ->whereNotNull($dateColumn)
                            ->first();
                        
                        if ($dateRange && $dateRange->min_date) {
                            $dateStart = $dateRange->min_date;
                            $dateLast = $dateRange->max_date;
                            break; // Found valid date range, stop looking
                        }
                    } catch (\Exception $e) {
                        // Try next column if this one fails
                        continue;
                    }
                }
            }
        } catch (\Exception $e) {
            // Skip date range if error
        }

        // Determine table category/note
        $tableNote = '';
        if (str_starts_with($tableName, 'ms_')) {
            $tableNote = 'Master Data';
        } elseif (str_starts_with($tableName, 'tr_')) {
            $tableNote = 'Transaction Data';
        } elseif (str_starts_with($tableName, 'vw_')) {
            $tableNote = 'View';
        } elseif (str_starts_with($tableName, 'sp_')) {
            $tableNote = 'Stored Procedure';
        } else {
            $tableNote = 'System Table';
        }

        // Insert data
        DB::table('tr_admin_it_aplikasi_table')->insert([
            'tr_aplikasi_table_id' => $id,
            'ms_aplikasi_id' => null,
            'note_schema' => "Auto-generated metadata for {$tableName}",
            'time_created' => now()->format('H:i:s'),
            'user_created' => 'SYSTEM',
            'id' => null,
            'table_name' => $tableName,
            'table_schema' => $table->TABLE_SCHEMA,
            'record' => $count,
            'record_date_start' => $dateStart,
            'record_date_last' => $dateLast,
            'date_updated' => now()->format('Y-m-d'),
            'table_note' => $tableNote,
        ]);

        echo "INSERT: {$tableName} ({$count} records) - {$tableNote}\n";
        $inserted++;
    }

    echo "\n=== SUMMARY ===\n";
    echo "Total tables found: " . count($tables) . "\n";
    echo "Inserted: {$inserted}\n";
    echo "Skipped: {$skipped}\n";
    echo "\nDone!\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
