<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TableAccessLog;
use App\Models\TableMessage;
use App\Models\TableMetadata;

class DatabaseTablesController extends Controller
{
    public function index()
    {
        // Log access
        TableAccessLog::logAccess('database_tables_page', 'view');
        
        return view('database-tables-alpine');
    }
    
    public function detail($tableName)
    {
        try {
            // Get table metadata - CAST TEXT column to VARCHAR for comparison
            $tableInfo = DB::table('tr_admin_it_aplikasi_table')
                ->whereRaw("CAST(table_name AS VARCHAR(MAX)) = ?", [$tableName])
                ->first();
            
            if (!$tableInfo) {
                abort(404, "Table not found");
            }
            
            // Get table columns from SQL Server with comments
            $columns = DB::select("
                SELECT 
                    c.COLUMN_NAME,
                    c.DATA_TYPE,
                    c.CHARACTER_MAXIMUM_LENGTH,
                    c.IS_NULLABLE,
                    c.COLUMN_DEFAULT,
                    CASE WHEN pk.COLUMN_NAME IS NOT NULL THEN 1 ELSE 0 END AS IS_PRIMARY_KEY,
                    CAST(ep.value AS NVARCHAR(MAX)) AS COLUMN_COMMENT
                FROM INFORMATION_SCHEMA.COLUMNS c
                LEFT JOIN (
                    SELECT ku.COLUMN_NAME
                    FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc
                    JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE ku
                        ON tc.CONSTRAINT_NAME = ku.CONSTRAINT_NAME
                    WHERE tc.CONSTRAINT_TYPE = 'PRIMARY KEY'
                        AND ku.TABLE_NAME = ?
                ) pk ON c.COLUMN_NAME = pk.COLUMN_NAME
                LEFT JOIN sys.extended_properties ep
                    ON ep.major_id = OBJECT_ID(?)
                    AND ep.minor_id = COLUMNPROPERTY(OBJECT_ID(?), c.COLUMN_NAME, 'ColumnId')
                    AND ep.name = 'MS_Description'
                WHERE c.TABLE_NAME = ?
                ORDER BY c.ORDINAL_POSITION
            ", [$tableName, $tableName, $tableName, $tableName]);
            
            // Get top 100 most recent records
            $recentData = [];
            try {
                // Try to find date column
                $dateColumn = null;
                foreach ($columns as $col) {
                    if (in_array(strtolower($col->DATA_TYPE), ['datetime', 'datetime2', 'date', 'smalldatetime'])) {
                        $dateColumn = $col->COLUMN_NAME;
                        break;
                    }
                }
                
                if ($dateColumn) {
                    $recentData = DB::table($tableName)
                        ->orderBy($dateColumn, 'desc')
                        ->limit(100)
                        ->get();
                } else {
                    // If no date column, just get first 100
                    $recentData = DB::table($tableName)
                        ->limit(100)
                        ->get();
                }
            } catch (\Exception $e) {
                // If query fails, just continue with empty data
            }
            
            TableAccessLog::logAccess($tableName, 'view');
            
            return view('table-detail', [
                'tableInfo' => $tableInfo,
                'tableName' => $tableName,
                'columns' => $columns,
                'recentData' => $recentData
            ]);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading table details: ' . $e->getMessage());
        }
    }
    
    public function getData(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $sortBy = $request->get('sortBy', 'table_name');
            $sortDirection = $request->get('sortDirection', 'asc');
            $perPage = $request->get('perPage', 25);
            $page = $request->get('page', 1);
            
            // Check if table has data
            $count = DB::table('tr_admin_it_aplikasi_table')->count();
            
            if ($count === 0) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage,
                    'stats' => [
                        'total_tables' => 0,
                        'total_records' => 0,
                        'latest_update' => null
                    ]
                ]);
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
                    'table_note',
                    'cek_priority'
                );
            
            // Search
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->whereRaw("CAST(table_name AS VARCHAR(MAX)) LIKE ?", ['%' . $search . '%'])
                      ->orWhereRaw("CAST(table_schema AS VARCHAR(MAX)) LIKE ?", ['%' . $search . '%'])
                      ->orWhereRaw("CAST(note_schema AS VARCHAR(MAX)) LIKE ?", ['%' . $search . '%'])
                      ->orWhereRaw("CAST(table_note AS VARCHAR(MAX)) LIKE ?", ['%' . $search . '%']);
                });
            }
            
            // Count total before pagination
            $total = $query->count();
            
            // Sorting
            if (in_array($sortBy, ['table_name', 'table_schema', 'note_schema', 'table_note'])) {
                $query->orderByRaw("CAST([{$sortBy}] AS VARCHAR(MAX)) {$sortDirection}");
            } elseif ($sortBy === 'cek_priority') {
                $query->orderBy('cek_priority', $sortDirection);
            } else {
                $query->orderBy($sortBy, $sortDirection);
            }
            
            // Pagination
            $offset = ($page - 1) * $perPage;
            $data = $query->skip($offset)->take($perPage)->get();
            
            // Get stats
            $stats = [
                'total_tables' => DB::table('tr_admin_it_aplikasi_table')->count(),
                'total_records' => DB::table('tr_admin_it_aplikasi_table')->sum('record'),
                'latest_update' => DB::table('tr_admin_it_aplikasi_table')->max('date_updated'),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'total' => $total,
                'current_page' => (int)$page,
                'last_page' => ceil($total / $perPage),
                'per_page' => (int)$perPage,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateMetadata(Request $request)
    {
        try {
            $tableName = $request->get('table');
            
            if (!$tableName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table name required'
                ], 400);
            }
            
            // Count records
            $count = DB::table($tableName)->count();
            
            // Get date range
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
            
            // Update
            DB::table('tr_admin_it_aplikasi_table')
                ->whereRaw("CAST(table_name AS VARCHAR(MAX)) = ?", [$tableName])
                ->update([
                    'record' => $count,
                    'record_date_start' => $dateStart,
                    'record_date_last' => $dateLast,
                    'date_updated' => now()
                ]);
            
            return response()->json([
                'success' => true,
                'message' => "Metadata for {$tableName} updated!",
                'data' => [
                    'record' => $count,
                    'record_date_start' => $dateStart,
                    'record_date_last' => $dateLast,
                    'date_updated' => now()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateAllMetadata(Request $request)
    {
        try {
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
                
                try {
                    $count = DB::table($tableName)->count();
                } catch (\Exception $e) {
                    continue;
                }
                
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
                } catch (\Exception $e) {
                    // Skip
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
            
            return response()->json([
                'success' => true,
                'message' => "Metadata updated! Added: {$added}, Updated: {$updated}",
                'data' => [
                    'added' => $added,
                    'updated' => $updated,
                    'total' => count($tables)
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get messages for a table
     */
    public function getMessages(Request $request, $tableId)
    {
        try {
            $messages = TableMessage::getTableMessages($tableId);
            
            return response()->json([
                'success' => true,
                'data' => $messages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Add a message to a table
     */
    public function addMessage(Request $request)
    {
        try {
            $request->validate([
                'tr_aplikasi_table_id' => 'required|string',
                'msg_desc' => 'required|string'
            ]);
            
            $message = TableMessage::addMessage(
                $request->tr_aplikasi_table_id,
                $request->msg_desc,
                auth()->user()?->name
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Message added successfully',
                'data' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete a message
     */
    public function deleteMessage($messageId)
    {
        try {
            $message = TableMessage::find($messageId);
            
            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'Message not found'
                ], 404);
            }
            
            $message->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function togglePriority(Request $request, $tableId)
    {
        try {
            $request->validate([
                'cek_priority' => 'required|boolean'
            ]);
            
            $table = TableMetadata::where('tr_aplikasi_table_id', $tableId)->first();
            
            if (!$table) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found'
                ], 404);
            }
            
            $table->cek_priority = $request->cek_priority;
            $table->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Priority updated successfully',
                'data' => [
                    'cek_priority' => $table->cek_priority
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateColumnComment(Request $request)
    {
        try {
            $request->validate([
                'table_name' => 'required|string',
                'column_name' => 'required|string',
                'comment' => 'nullable|string'
            ]);
            
            $tableName = $request->table_name;
            $columnName = $request->column_name;
            $comment = $request->comment;
            
            // Check if extended property exists
            $exists = DB::select("
                SELECT COUNT(*) as count
                FROM sys.extended_properties ep
                WHERE ep.major_id = OBJECT_ID(?)
                AND ep.minor_id = COLUMNPROPERTY(OBJECT_ID(?), ?, 'ColumnId')
                AND ep.name = 'MS_Description'
            ", [$tableName, $tableName, $columnName]);
            
            if ($exists[0]->count > 0) {
                // Update existing comment
                if (empty($comment)) {
                    // Delete comment if empty
                    DB::statement("
                        EXEC sp_dropextendedproperty 
                        @name = N'MS_Description',
                        @level0type = N'SCHEMA', @level0name = 'dbo',
                        @level1type = N'TABLE', @level1name = ?,
                        @level2type = N'COLUMN', @level2name = ?
                    ", [$tableName, $columnName]);
                } else {
                    // Update comment
                    DB::statement("
                        EXEC sp_updateextendedproperty 
                        @name = N'MS_Description', @value = ?,
                        @level0type = N'SCHEMA', @level0name = 'dbo',
                        @level1type = N'TABLE', @level1name = ?,
                        @level2type = N'COLUMN', @level2name = ?
                    ", [$comment, $tableName, $columnName]);
                }
            } else if (!empty($comment)) {
                // Add new comment
                DB::statement("
                    EXEC sp_addextendedproperty 
                    @name = N'MS_Description', @value = ?,
                    @level0type = N'SCHEMA', @level0name = 'dbo',
                    @level1type = N'TABLE', @level1name = ?,
                    @level2type = N'COLUMN', @level2name = ?
                ", [$comment, $tableName, $columnName]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Column comment updated successfully',
                'data' => [
                    'comment' => $comment
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
