<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TableAccessLog;

class DatabaseTables extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'table_name';
    public $sortDirection = 'asc';
    public $perPage = 25;
    public $isUpdating = false;

    protected $queryString = ['search', 'sortBy', 'sortDirection'];

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    /**
     * Update metadata for all tables
     */
    public function updateAllMetadata()
    {
        try {
            $this->isUpdating = true;
            
            $tables = DB::select("
                SELECT TABLE_NAME 
                FROM INFORMATION_SCHEMA.TABLES 
                WHERE TABLE_TYPE = 'BASE TABLE' 
                AND TABLE_SCHEMA = 'dbo'
            ");
            
            $updated = 0;
            $added = 0;
            
            foreach ($tables as $table) {
                $tableName = $table->TABLE_NAME;
                
                // Count records
                try {
                    $count = DB::table($tableName)->count();
                } catch (\Exception $e) {
                    $count = 0;
                }
                
                // Get date range
                $dateStart = null;
                $dateLast = null;
                
                try {
                    $columns = DB::select("
                        SELECT COLUMN_NAME, DATA_TYPE, ORDINAL_POSITION
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
                                    ->selectRaw("
                                        MIN(CAST([{$dateColumn}] AS DATE)) as min_date, 
                                        MAX(CAST([{$dateColumn}] AS DATE)) as max_date
                                    ")
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
                } catch (\Exception $e) {
                    // Skip date range if error
                }
                
                // Check if record exists
                $exists = DB::table('tr_admin_it_aplikasi_table')
                    ->whereRaw("CAST(table_name AS VARCHAR(MAX)) = ?", [$tableName])
                    ->exists();
                
                if ($exists) {
                    // Update existing record
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
                    // Insert new record
                    $tableId = 'TBL-' . strtoupper(substr(md5($tableName), 0, 8));
                    
                    // Categorize table
                    $category = 'System Table';
                    if (str_starts_with($tableName, 'ms_')) $category = 'Master Data';
                    elseif (str_starts_with($tableName, 'tr_')) $category = 'Transaction Data';
                    elseif (str_starts_with($tableName, 'vw_')) $category = 'View';
                    elseif (str_starts_with($tableName, 'sp_')) $category = 'Stored Procedure';
                    
                    DB::table('tr_admin_it_aplikasi_table')->insert([
                        'tr_aplikasi_table_id' => $tableId,
                        'ms_aplikasi_id' => null,
                        'note_schema' => 'Auto-generated',
                        'time_created' => now(),
                        'user_created' => auth()->user()?->name ?? 'System',
                        'id' => $tableId,
                        'table_name' => $tableName,
                        'table_schema' => 'dbo',
                        'record' => $count,
                        'record_date_start' => $dateStart,
                        'record_date_last' => $dateLast,
                        'date_updated' => now(),
                        'table_note' => $category
                    ]);
                    $added++;
                }
            }
            
            $this->isUpdating = false;
            session()->flash('success', "Metadata updated! Added: {$added}, Updated: {$updated}");
            
        } catch (\Exception $e) {
            $this->isUpdating = false;
            session()->flash('error', 'Error updating metadata: ' . $e->getMessage());
        }
    }
    
    /**
     * Update metadata for single table
     */
    public function updateTableMetadata($tableName)
    {
        try {
            // Count records
            $count = DB::table($tableName)->count();
            
            // Get date range (same logic as updateAllMetadata)
            $dateStart = null;
            $dateLast = null;
            
            try {
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
                                ->selectRaw("
                                    MIN(CAST([{$dateColumn}] AS DATE)) as min_date, 
                                    MAX(CAST([{$dateColumn}] AS DATE)) as max_date
                                ")
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
            } catch (\Exception $e) {
                // Skip
            }
            
            // Update
            DB::table('tr_admin_it_aplikasi_table')
                ->whereRaw("CAST(table_name AS VARCHAR(MAX)) = ?", [$tableName])
                ->update([
                    'record' => $count,
                    'record_date_start' => $dateStart,
                    'record_date_last' => $dateLast,
                    'date_updated' => now()
                ]);
            
            session()->flash('success', "Metadata for {$tableName} updated!");
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function getTables()
    {
        try {
            // Check if table exists and has data
            $count = DB::table('tr_admin_it_aplikasi_table')->count();
            
            // If empty, try to get from INFORMATION_SCHEMA
            if ($count === 0) {
                return $this->getTablesFromInformationSchema();
            }

            $query = DB::table('tr_admin_it_aplikasi_table')
                ->select(
                    'tr_aplikasi_table_id',
                    'table_name',
                    'table_schema',
                    'record',
                    'record_date_start',
                    'record_date_last',
                    'date_updated',
                    'note_schema',
                    'table_note'
                );

            // Search filter
            if ($this->search) {
                $query->where(function($q) {
                    $q->whereRaw("CAST(table_name AS VARCHAR(MAX)) LIKE ?", ['%' . $this->search . '%'])
                      ->orWhereRaw("CAST(table_schema AS VARCHAR(MAX)) LIKE ?", ['%' . $this->search . '%'])
                      ->orWhereRaw("CAST(note_schema AS VARCHAR(MAX)) LIKE ?", ['%' . $this->search . '%'])
                      ->orWhereRaw("CAST(table_note AS VARCHAR(MAX)) LIKE ?", ['%' . $this->search . '%']);
                });
            }

            // Sorting - CAST text columns to VARCHAR for SQL Server compatibility
            if (in_array($this->sortBy, ['table_name', 'table_schema', 'note_schema', 'table_note'])) {
                $query->orderByRaw("CAST([{$this->sortBy}] AS VARCHAR(MAX)) {$this->sortDirection}");
            } else {
                $query->orderBy($this->sortBy, $this->sortDirection);
            }

            $result = $query->paginate($this->perPage);
            
            \Log::info('DatabaseTables query success', [
                'total' => $result->total(),
                'current_page' => $result->currentPage()
            ]);

            return $result;

        } catch (\Exception $e) {
            \Log::error('DatabaseTables getTables error: ' . $e->getMessage());
            session()->flash('error', 'Error loading tables: ' . $e->getMessage());
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    /**
     * Get tables directly from INFORMATION_SCHEMA if tr_admin_it_aplikasi_table is empty
     */
    private function getTablesFromInformationSchema()
    {
        try {
            $query = "
                SELECT 
                    TABLE_NAME as table_name,
                    NULL as tr_aplikasi_table_id,
                    NULL as table_schema,
                    NULL as record,
                    NULL as record_date_start,
                    NULL as record_date_last,
                    NULL as date_updated,
                    NULL as note_schema,
                    'From INFORMATION_SCHEMA' as table_note
                FROM INFORMATION_SCHEMA.TABLES 
                WHERE TABLE_TYPE = 'BASE TABLE' 
                AND TABLE_SCHEMA = 'dbo'
            ";

            if ($this->search) {
                $query .= " AND TABLE_NAME LIKE '%" . $this->search . "%'";
            }

            $query .= " ORDER BY TABLE_NAME " . $this->sortDirection;

            $tables = DB::select($query);
            
            // Convert to paginator
            $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
            $collection = collect($tables);
            $items = $collection->slice(($currentPage - 1) * $this->perPage, $this->perPage)->all();
            
            return new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $collection->count(),
                $this->perPage,
                $currentPage,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );

        } catch (\Exception $e) {
            \Log::error('DatabaseTables getTablesFromInformationSchema error: ' . $e->getMessage());
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function getTableStats()
    {
        try {
            $count = DB::table('tr_admin_it_aplikasi_table')->count();
            
            if ($count === 0) {
                // Get from INFORMATION_SCHEMA
                $totalTables = DB::select("SELECT COUNT(*) as total FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA = 'dbo'");
                return [
                    'total_tables' => $totalTables[0]->total ?? 0,
                    'total_records' => 0,
                    'latest_update' => null,
                ];
            }
            
            return [
                'total_tables' => DB::table('tr_admin_it_aplikasi_table')->count(),
                'total_records' => DB::table('tr_admin_it_aplikasi_table')->sum('record'),
                'latest_update' => DB::table('tr_admin_it_aplikasi_table')
                    ->max('date_updated'),
            ];
        } catch (\Exception $e) {
            \Log::error('DatabaseTables getTableStats error: ' . $e->getMessage());
            return [
                'total_tables' => 0,
                'total_records' => 0,
                'latest_update' => null,
            ];
        }
    }

    public function render()
    {
        // Log access to database tables page
        TableAccessLog::logAccess(
            'database_tables_page',
            'view',
            [
                'search' => $this->search,
                'sort_by' => $this->sortBy,
                'sort_direction' => $this->sortDirection,
                'per_page' => $this->perPage
            ]
        );
        
        return view('livewire.database-tables', [
            'tables' => $this->getTables(),
            'stats' => $this->getTableStats(),
        ])->layout('layouts.admin');
    }
    
    /**
     * Log individual table view
     */
    public function viewTableDetails($tableName)
    {
        TableAccessLog::logAccess(
            $tableName,
            'detail_view',
            [
                'from_page' => 'database_tables'
            ]
        );
        
        // Add your table detail logic here
        session()->flash('info', "Viewing details for table: {$tableName}");
    }
}
