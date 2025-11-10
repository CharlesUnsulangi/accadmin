<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\ItDoc;
use Illuminate\Support\Facades\Auth;

class DocsTables extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 50;
    public $selectedTable = null;
    public $tableColumns = [];
    public $tableInfo = null;
    public $tableNote = '';
    public $lastRecordDate = null;
    public $editingNote = false;
    public $saveToTableDesc = false; // Option to save to table description column
    public $hasDescriptionColumn = false; // Track if table has description column
    public $selectedConnection = 'sqlsrv'; // Default connection
    public $availableConnections = []; // List of available database connections

    protected $queryString = ['search', 'selectedConnection'];

    public function mount()
    {
        // Get available SQL Server connections from config and test accessibility
        $allConnections = config('database.connections');
        $accessibleConnections = [];
        
        foreach ($allConnections as $name => $config) {
            // Only include SQL Server connections
            if (!isset($config['driver']) || $config['driver'] !== 'sqlsrv') {
                continue;
            }
            
            // Test if connection is accessible
            try {
                DB::connection($name)->select('SELECT 1');
                $dbName = $config['database'] ?? $name;
                $accessibleConnections[$name] = $dbName;
            } catch (\Exception $e) {
                // Skip connections that fail
                \Log::warning("Database connection '{$name}' is not accessible: " . $e->getMessage());
            }
        }
        
        $this->availableConnections = $accessibleConnections;

        // Set default if not in query string or not accessible
        if (!$this->selectedConnection || !isset($this->availableConnections[$this->selectedConnection])) {
            $this->selectedConnection = config('database.default');
            
            // If default is not accessible, use first available
            if (!isset($this->availableConnections[$this->selectedConnection]) && !empty($this->availableConnections)) {
                $this->selectedConnection = array_key_first($this->availableConnections);
            }
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedConnection()
    {
        $this->resetPage();
        $this->closeModal(); // Close any open modal when switching database
    }

    public function switchConnection($connection)
    {
        // Validate connection is accessible before switching
        if (!isset($this->availableConnections[$connection])) {
            session()->flash('error', 'Database connection not accessible: ' . $connection);
            return;
        }
        
        $this->selectedConnection = $connection;
        $this->resetPage();
        $this->closeModal();
    }

    public function viewTableSchema($tableName)
    {
        $this->selectedTable = $tableName;
        
        // Get table info using selected connection
        $this->tableInfo = DB::connection($this->selectedConnection)->selectOne("
            SELECT 
                t.TABLE_NAME,
                t.TABLE_TYPE,
                SUM(p.rows) AS row_count
            FROM INFORMATION_SCHEMA.TABLES t
            LEFT JOIN sys.tables st ON st.name = t.TABLE_NAME
            LEFT JOIN sys.partitions p ON st.object_id = p.object_id
            WHERE t.TABLE_NAME = ?
                AND p.index_id IN (0,1)
            GROUP BY t.TABLE_NAME, t.TABLE_TYPE
        ", [$tableName]);
        
        // Get last record date by checking common date columns
        $this->lastRecordDate = $this->getLastRecordDate($tableName);
        
        // Get existing note from IT Documentation (always from default connection)
        $existingNote = ItDoc::where('topik', 'Database Table')
            ->where('project', $tableName)
            ->orderBy('created_date', 'desc')
            ->first();
        
        // If no note in IT Docs, try to get from table extended property
        if (!$existingNote) {
            $extendedProperty = $this->getTableExtendedProperty($tableName);
            $this->tableNote = $extendedProperty ?: '';
        } else {
            $this->tableNote = $existingNote->catatan_text;
        }
        
        $this->editingNote = false;
        
        // Check if table has description column (always true for SQL Server extended properties)
        $this->hasDescriptionColumn = $this->checkHasDescriptionColumn($tableName);
        
        // Get table columns with details using selected connection
        $this->tableColumns = DB::connection($this->selectedConnection)->select("
            SELECT 
                c.COLUMN_NAME,
                c.DATA_TYPE,
                c.CHARACTER_MAXIMUM_LENGTH,
                c.NUMERIC_PRECISION,
                c.NUMERIC_SCALE,
                c.IS_NULLABLE,
                c.COLUMN_DEFAULT,
                c.ORDINAL_POSITION,
                CASE WHEN pk.COLUMN_NAME IS NOT NULL THEN 'YES' ELSE 'NO' END AS IS_PRIMARY_KEY,
                CASE WHEN fk.COLUMN_NAME IS NOT NULL THEN 'YES' ELSE 'NO' END AS IS_FOREIGN_KEY,
                CASE WHEN ic.is_identity = 1 THEN 'YES' ELSE 'NO' END AS IS_IDENTITY
            FROM INFORMATION_SCHEMA.COLUMNS c
            LEFT JOIN (
                SELECT ku.TABLE_CATALOG, ku.TABLE_SCHEMA, ku.TABLE_NAME, ku.COLUMN_NAME
                FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS AS tc
                INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS ku
                    ON tc.CONSTRAINT_TYPE = 'PRIMARY KEY' 
                    AND tc.CONSTRAINT_NAME = ku.CONSTRAINT_NAME
            ) pk 
            ON c.TABLE_CATALOG = pk.TABLE_CATALOG
                AND c.TABLE_SCHEMA = pk.TABLE_SCHEMA
                AND c.TABLE_NAME = pk.TABLE_NAME
                AND c.COLUMN_NAME = pk.COLUMN_NAME
            LEFT JOIN (
                SELECT ku.TABLE_CATALOG, ku.TABLE_SCHEMA, ku.TABLE_NAME, ku.COLUMN_NAME
                FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS AS tc
                INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS ku
                    ON tc.CONSTRAINT_TYPE = 'FOREIGN KEY' 
                    AND tc.CONSTRAINT_NAME = ku.CONSTRAINT_NAME
            ) fk
            ON c.TABLE_CATALOG = fk.TABLE_CATALOG
                AND c.TABLE_SCHEMA = fk.TABLE_SCHEMA
                AND c.TABLE_NAME = fk.TABLE_NAME
                AND c.COLUMN_NAME = fk.COLUMN_NAME
            LEFT JOIN sys.tables st ON st.name = c.TABLE_NAME
            LEFT JOIN sys.columns sc ON sc.object_id = st.object_id AND sc.name = c.COLUMN_NAME
            LEFT JOIN sys.identity_columns ic ON ic.object_id = sc.object_id AND ic.column_id = sc.column_id
            WHERE c.TABLE_NAME = ?
            ORDER BY c.ORDINAL_POSITION
        ", [$tableName]);
    }

    public function closeTableSchema()
    {
        $this->selectedTable = null;
        $this->tableColumns = [];
        $this->tableInfo = null;
        $this->tableNote = '';
        $this->lastRecordDate = null;
        $this->editingNote = false;
        $this->saveToTableDesc = false;
        $this->hasDescriptionColumn = false;
    }

    public function closeModal()
    {
        $this->closeTableSchema();
    }

    public function toggleEditNote()
    {
        $this->editingNote = !$this->editingNote;
    }

    public function saveNote()
    {
        if (empty(trim($this->tableNote))) {
            session()->flash('error', 'Note cannot be empty');
            return;
        }

        // Check if note already exists in IT Documentation
        $existingNote = ItDoc::where('topik', 'Database Table')
            ->where('project', $this->selectedTable)
            ->orderBy('created_date', 'desc')
            ->first();

        if ($existingNote) {
            // Update existing note
            $existingNote->update([
                'catatan_text' => $this->tableNote,
                'created_date' => now()->toDateString(),
                'created_user' => Auth::user()->name ?? 'System',
            ]);
        } else {
            // Create new note
            ItDoc::create([
                'catatan_text' => $this->tableNote,
                'topik' => 'Database Table',
                'project' => $this->selectedTable,
                'created_date' => now()->toDateString(),
                'created_user' => Auth::user()->name ?? 'System',
            ]);
        }
        
        // Save note as table extended property (table metadata) if user opted in
        if ($this->saveToTableDesc) {
            $this->saveNoteAsTableProperty($this->selectedTable, $this->tableNote);
        }

        $this->editingNote = false;
        session()->flash('message', 'Note saved successfully!');
    }
    
    private function checkHasDescriptionColumn($tableName)
    {
        // This now checks if we can add extended properties to the table
        // Extended properties are always available in SQL Server
        return true; // Always show option for SQL Server
    }
    
    private function getTableExtendedProperty($tableName)
    {
        try {
            $property = DB::connection($this->selectedConnection)->select("
                SELECT CAST(value AS NVARCHAR(MAX)) as value 
                FROM sys.extended_properties 
                WHERE major_id = OBJECT_ID(?) 
                AND name = 'MS_Description'
                AND minor_id = 0
            ", [$tableName]);
            
            return !empty($property) ? $property[0]->value : null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    private function saveNoteAsTableProperty($tableName, $note)
    {
        try {
            // Check if extended property already exists for this table using selected connection
            $existingProperty = DB::connection($this->selectedConnection)->select("
                SELECT value 
                FROM sys.extended_properties 
                WHERE major_id = OBJECT_ID(?) 
                AND name = 'MS_Description'
                AND minor_id = 0
            ", [$tableName]);
            
            if (!empty($existingProperty)) {
                // Update existing extended property
                DB::connection($this->selectedConnection)->statement("
                    EXEC sp_updateextendedproperty 
                        @name = N'MS_Description',
                        @value = ?,
                        @level0type = N'SCHEMA',
                        @level0name = N'dbo',
                        @level1type = N'TABLE',
                        @level1name = ?
                ", [$note, $tableName]);
            } else {
                // Add new extended property
                DB::connection($this->selectedConnection)->statement("
                    EXEC sp_addextendedproperty 
                        @name = N'MS_Description',
                        @value = ?,
                        @level0type = N'SCHEMA',
                        @level0name = N'dbo',
                        @level1type = N'TABLE',
                        @level1name = ?
                ", [$note, $tableName]);
            }
            
            session()->flash('message', 'Note saved to IT Documentation and table metadata (Extended Property)!');
            
        } catch (\Exception $e) {
            // Log error but don't break the flow
            \Log::warning("Could not save note as table extended property: " . $e->getMessage());
            session()->flash('message', 'Note saved to IT Documentation (table metadata update failed)');
        }
    }

    private function getLastRecordDate($tableName)
    {
        try {
            // Get all columns for this table using selected connection
            $columns = DB::connection($this->selectedConnection)->select("
                SELECT COLUMN_NAME, DATA_TYPE
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME = ?
                AND DATA_TYPE IN ('date', 'datetime', 'datetime2')
                ORDER BY ORDINAL_POSITION
            ", [$tableName]);

            if (empty($columns)) {
                return null;
            }

            // Try common date column names first
            $commonDateColumns = ['created_date', 'created_at', 'date', 'tanggal', 'updated_at', 'modified_date'];
            
            foreach ($commonDateColumns as $dateCol) {
                foreach ($columns as $col) {
                    if (strtolower($col->COLUMN_NAME) === $dateCol) {
                        $result = DB::connection($this->selectedConnection)->selectOne("SELECT MAX([{$col->COLUMN_NAME}]) as last_date FROM [{$tableName}]");
                        if ($result && $result->last_date) {
                            return $result->last_date;
                        }
                    }
                }
            }

            // If no common column found, use the first date column
            if (count($columns) > 0) {
                $result = DB::connection($this->selectedConnection)->selectOne("SELECT MAX([{$columns[0]->COLUMN_NAME}]) as last_date FROM [{$tableName}]");
                return $result ? $result->last_date : null;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getCreateTableScript()
    {
        if (!$this->selectedTable || empty($this->tableColumns)) {
            return '';
        }

        $script = "CREATE TABLE [dbo].[{$this->selectedTable}] (\n";
        $columnDefs = [];
        
        foreach ($this->tableColumns as $column) {
            $def = "    [{$column->COLUMN_NAME}] [" . strtoupper($column->DATA_TYPE) . "]";
            
            if ($column->CHARACTER_MAXIMUM_LENGTH && $column->CHARACTER_MAXIMUM_LENGTH > 0) {
                $def .= "({$column->CHARACTER_MAXIMUM_LENGTH})";
            } elseif ($column->NUMERIC_PRECISION) {
                $def .= "({$column->NUMERIC_PRECISION},{$column->NUMERIC_SCALE})";
            }
            
            if ($column->IS_IDENTITY === 'YES') {
                $def .= " IDENTITY(1,1)";
            }
            
            $def .= $column->IS_NULLABLE === 'NO' ? " NOT NULL" : " NULL";
            
            $columnDefs[] = $def;
        }
        
        $script .= implode(",\n", $columnDefs);
        $script .= "\n);\nGO";
        
        return $script;
    }

    public function copyCreateScript($tableName)
    {
        // This will be handled by JavaScript to copy to clipboard
        $this->dispatch('copy-to-clipboard', script: $this->generateCreateScript($tableName));
    }

    private function generateCreateScript($tableName)
    {
        $columns = DB::select("
            SELECT 
                c.COLUMN_NAME,
                c.DATA_TYPE,
                c.CHARACTER_MAXIMUM_LENGTH,
                c.NUMERIC_PRECISION,
                c.NUMERIC_SCALE,
                c.IS_NULLABLE,
                c.COLUMN_DEFAULT,
                CASE WHEN ic.is_identity = 1 THEN 'YES' ELSE 'NO' END AS IS_IDENTITY
            FROM INFORMATION_SCHEMA.COLUMNS c
            LEFT JOIN sys.tables st ON st.name = c.TABLE_NAME
            LEFT JOIN sys.columns sc ON sc.object_id = st.object_id AND sc.name = c.COLUMN_NAME
            LEFT JOIN sys.identity_columns ic ON ic.object_id = sc.object_id AND ic.column_id = sc.column_id
            WHERE c.TABLE_NAME = ?
            ORDER BY c.ORDINAL_POSITION
        ", [$tableName]);

        $script = "CREATE TABLE [dbo].[$tableName] (\n";
        $columnDefs = [];
        
        foreach ($columns as $col) {
            $def = "    [{$col->COLUMN_NAME}] [{$col->DATA_TYPE}]";
            
            if ($col->CHARACTER_MAXIMUM_LENGTH) {
                $def .= "({$col->CHARACTER_MAXIMUM_LENGTH})";
            } elseif ($col->NUMERIC_PRECISION) {
                $def .= "({$col->NUMERIC_PRECISION},{$col->NUMERIC_SCALE})";
            }
            
            if ($col->IS_IDENTITY === 'YES') {
                $def .= " IDENTITY(1,1)";
            }
            
            $def .= $col->IS_NULLABLE === 'NO' ? " NOT NULL" : " NULL";
            
            $columnDefs[] = $def;
        }
        
        $script .= implode(",\n", $columnDefs);
        $script .= "\n);\nGO";
        
        return $script;
    }

    public function render()
    {
        // Validate selected connection is still accessible
        if (!isset($this->availableConnections[$this->selectedConnection])) {
            $this->selectedConnection = config('database.default');
            if (!isset($this->availableConnections[$this->selectedConnection]) && !empty($this->availableConnections)) {
                $this->selectedConnection = array_key_first($this->availableConnections);
            }
        }
        
        // Get all tables from the selected database connection
        try {
            $query = "
                SELECT 
                    t.TABLE_SCHEMA,
                    t.TABLE_NAME,
                    t.TABLE_TYPE,
                    ISNULL(SUM(p.rows), 0) AS row_count
                FROM INFORMATION_SCHEMA.TABLES t
                LEFT JOIN sys.tables st ON st.name = t.TABLE_NAME
                LEFT JOIN sys.partitions p ON st.object_id = p.object_id AND p.index_id IN (0,1)
                WHERE t.TABLE_TYPE = 'BASE TABLE'
            ";

            if ($this->search) {
                $query .= " AND t.TABLE_NAME LIKE '%" . $this->search . "%'";
            }

            $query .= "
                GROUP BY t.TABLE_SCHEMA, t.TABLE_NAME, t.TABLE_TYPE
                ORDER BY t.TABLE_NAME
            ";

            $allTables = DB::connection($this->selectedConnection)->select($query);
        } catch (\Exception $e) {
            session()->flash('error', 'Error connecting to database: ' . $e->getMessage());
            $allTables = [];
        }
        
        // Manual pagination
        $total = count($allTables);
        $currentPage = $this->getPage();
        $offset = ($currentPage - 1) * $this->perPage;
        $tables = array_slice($allTables, $offset, $this->perPage);

        return view('livewire.docs-tables', [
            'tables' => $tables,
            'total' => $total,
        ])->layout('layouts.bootstrap');
    }
}
