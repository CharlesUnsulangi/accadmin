<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateTableMetadata extends Command
{
    protected $signature = 'metadata:update {table?} {--all}';
    
    protected $description = 'Update table metadata (record count, date range)';

    public function handle()
    {
        $tableName = $this->argument('table');
        $updateAll = $this->option('all');
        
        if ($updateAll || !$tableName) {
            return $this->updateAllTables();
        }
        
        return $this->updateSingleTable($tableName);
    }
    
    protected function updateAllTables()
    {
        $this->info('Fetching all tables from database...');
        
        $tables = DB::select("
            SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_TYPE = 'BASE TABLE' 
            AND TABLE_SCHEMA = 'dbo'
        ");
        
        $this->info('Found ' . count($tables) . ' tables');
        $bar = $this->output->createProgressBar(count($tables));
        $bar->start();
        
        $updated = 0;
        $added = 0;
        $errors = 0;
        
        foreach ($tables as $table) {
            $tableName = $table->TABLE_NAME;
            
            try {
                $count = DB::table($tableName)->count();
                
                $dateStart = null;
                $dateLast = null;
                
                $columns = DB::select("
                    SELECT COLUMN_NAME
                    FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_NAME = ? 
                    AND DATA_TYPE IN ('date', 'datetime', 'datetime2', 'smalldatetime')
                    ORDER BY ORDINAL_POSITION
                ", [$tableName]);

                if (!empty($columns) && $count > 0) {
                    foreach ($columns as $col) {
                        $dateColumn = $col->COLUMN_NAME;
                        
                        try {
                            $dateRange = DB::table($tableName)
                                ->selectRaw("MIN(CAST([{$dateColumn}] AS DATE)) as min_date, MAX(CAST([{$dateColumn}] AS DATE)) as max_date")
                                ->whereNotNull($dateColumn)
                                ->first();
                            
                            if ($dateRange && $dateRange->min_date) {
                                $dateStart = $dateRange->min_date;
                                $dateLast = $dateRange->max_date;
                                break;
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
                
                $exists = DB::table('tr_admin_it_aplikasi_table')
                    ->whereRaw("CAST(table_name AS VARCHAR(MAX)) = ?", [$tableName])
                    ->exists();
                
                if ($exists) {
                    DB::table('tr_admin_it_aplikasi_table')
                        ->whereRaw("CAST(table_name AS VARCHAR(MAX)) = ?", [$tableName])
                        ->update([
                            'record' => $count,
                            'record_date_start' => $dateStart,
                            'record_date_last' => $dateLast,
                            'date_updated' => now()
                        ]);
                    $updated++;
                } else {
                    $tableId = 'TBL-' . strtoupper(substr(md5($tableName), 0, 8));
                    
                    $category = 'System Table';
                    if (str_starts_with($tableName, 'ms_')) $category = 'Master Data';
                    elseif (str_starts_with($tableName, 'tr_')) $category = 'Transaction Data';
                    elseif (str_starts_with($tableName, 'vw_')) $category = 'View';
                    
                    DB::table('tr_admin_it_aplikasi_table')->insert([
                        'tr_aplikasi_table_id' => $tableId,
                        'table_name' => $tableName,
                        'record' => $count,
                        'record_date_start' => $dateStart,
                        'record_date_last' => $dateLast,
                        'date_updated' => now(),
                        'table_note' => $category
                    ]);
                    $added++;
                }
                
            } catch (\Exception $e) {
                $errors++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("✓ Complete!");
        $this->table(['Status', 'Count'], [['Added', $added], ['Updated', $updated], ['Errors', $errors]]);
        
        return Command::SUCCESS;
    }
    
    protected function updateSingleTable($tableName)
    {
        $this->info("Updating: {$tableName}");
        
        try {
            $count = DB::table($tableName)->count();
            
            DB::table('tr_admin_it_aplikasi_table')
                ->whereRaw("CAST(table_name AS VARCHAR(MAX)) = ?", [$tableName])
                ->update([
                    'record' => $count,
                    'date_updated' => now()
                ]);
            
            $this->info("✓ Updated! Records: " . number_format($count));
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

