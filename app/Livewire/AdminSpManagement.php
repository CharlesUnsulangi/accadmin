<?php

namespace App\Livewire;

use App\Models\AdminSp;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class AdminSpManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 50;
    public $sortField = 'ms_admin_sp_id';
    public $sortDirection = 'asc';

    // Modal properties
    public $showModal = false;
    public $editMode = false;
    public $selectedId = null;

    // Execute SP Modal
    public $showExecuteModal = false;
    public $executingSp = null;
    public $executeResults = [];
    public $executeError = null;
    public $executionTime = 0;
    public $spTableInfo = [];
    public $maxResultRows = 1000; // Limit result rows to prevent memory issues

    // Form properties
    public $ms_admin_sp_id = '';
    public $sp_desc = '';
    public $date_start_input = '';
    public $date_end_input = '';
    public $money_input = '';
    public $varchar_input = '';
    public $sp_name = '';

    protected $queryString = ['search', 'perPage'];

    protected function rules()
    {
        return [
            'ms_admin_sp_id' => 'required|string|max:100|unique:ms_admin_sp,ms_admin_sp_id,' . $this->selectedId . ',ms_admin_sp_id',
            'sp_desc' => 'nullable|string|max:50',
            'date_start_input' => 'nullable|date',
            'date_end_input' => 'nullable|date',
            'money_input' => 'nullable|numeric',
            'varchar_input' => 'nullable|string|max:50',
            'sp_name' => 'nullable|string|max:50',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function create()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $sp = AdminSp::findOrFail($id);
        
        $this->selectedId = $sp->ms_admin_sp_id;
        $this->ms_admin_sp_id = $sp->ms_admin_sp_id;
        $this->sp_desc = $sp->sp_desc;
        $this->date_start_input = $sp->date_start_input?->format('Y-m-d');
        $this->date_end_input = $sp->date_end_input?->format('Y-m-d');
        $this->money_input = $sp->money_input;
        $this->varchar_input = $sp->varchar_input;
        $this->sp_name = $sp->sp_name;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'ms_admin_sp_id' => $this->ms_admin_sp_id,
            'sp_desc' => $this->sp_desc,
            'date_start_input' => $this->date_start_input ?: null,
            'date_end_input' => $this->date_end_input ?: null,
            'money_input' => $this->money_input ?: null,
            'varchar_input' => $this->varchar_input,
            'sp_name' => $this->sp_name,
        ];

        if ($this->editMode) {
            $sp = AdminSp::findOrFail($this->selectedId);
            $sp->update($data);
            session()->flash('message', 'Stored Procedure updated successfully!');
        } else {
            AdminSp::create($data);
            session()->flash('message', 'Stored Procedure created successfully!');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        if (confirm('Are you sure you want to delete this SP?')) {
            AdminSp::findOrFail($id)->delete();
            session()->flash('message', 'Stored Procedure deleted successfully!');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function execute($id)
    {
        // Increase memory limit for this operation
        ini_set('memory_limit', '1024M');
        
        $sp = AdminSp::findOrFail($id);
        $this->executingSp = $sp;
        $this->executeResults = [];
        $this->executeError = null;
        $this->executionTime = 0;
        
        try {
            $startTime = microtime(true);
            
            // Build parameter list from SP configuration
            $params = [];
            
            if ($sp->date_start_input) {
                $params[] = "'" . $sp->date_start_input->format('Y-m-d') . "'";
            }
            
            if ($sp->date_end_input) {
                $params[] = "'" . $sp->date_end_input->format('Y-m-d') . "'";
            }
            
            if ($sp->money_input) {
                $params[] = $sp->money_input;
            }
            
            if ($sp->varchar_input) {
                $params[] = "'" . str_replace("'", "''", $sp->varchar_input) . "'";
            }
            
            // Execute the stored procedure using ms_admin_sp_id as SP name
            $spName = $sp->sp_name ?: $sp->ms_admin_sp_id; // Use sp_name if exists, otherwise use ID
            $sql = "EXEC " . $spName . " " . implode(', ', $params);
            
            // Try DB::select first (for SPs that return result sets)
            // If it fails with "no fields" error, use DB::statement (for SPs that don't return rows)
            try {
                // Execute with limited rows using cursor to prevent memory issues
                $sql = "EXEC " . $spName . " " . implode(', ', $params);
                
                // Use cursor to limit memory usage
                $pdo = DB::connection()->getPdo();
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                
                $results = [];
                $rowCount = 0;
                $maxRows = $this->maxResultRows;
                
                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                    if ($rowCount >= $maxRows) {
                        $this->executeError = "Result limited to first {$maxRows} rows (result set too large for display)";
                        break;
                    }
                    $results[] = (object) $row;
                    $rowCount++;
                }
                
                $stmt->closeCursor();
                
                $endTime = microtime(true);
                $this->executionTime = round(($endTime - $startTime) * 1000, 2); // in milliseconds
                
                $this->executeResults = $results;
                
                if (empty($results) && !$this->executeError) {
                    $this->executeError = 'SP executed successfully but returned no results.';
                }
            } catch (\Exception $selectException) {
                // Check if error is about "no fields" - this means SP doesn't return a result set
                if (strpos($selectException->getMessage(), 'no fields') !== false || 
                    strpos($selectException->getMessage(), 'IMSSP') !== false) {
                    
                    // Use DB::statement for SPs that don't return rows (like INSERT/UPDATE/DELETE only)
                    $executed = DB::statement($sql);
                    
                    $endTime = microtime(true);
                    $this->executionTime = round(($endTime - $startTime) * 1000, 2); // in milliseconds
                    
                    $this->executeResults = [];
                    
                    if ($executed) {
                        $this->executeError = 'SP executed successfully (no result set returned).';
                    } else {
                        throw new \Exception('SP execution failed.');
                    }
                } else {
                    // Re-throw other exceptions
                    throw $selectException;
                }
            }
            
        } catch (\Exception $e) {
            $this->executeError = $e->getMessage();
        }
        
        // Get table information from SP definition
        $this->spTableInfo = $this->getSpTableInfo($sp->ms_admin_sp_id);
        
        $this->showExecuteModal = true;
    }
    
    private function getSpTableInfo($spName)
    {
        try {
            // Get SP definition
            $spDefinition = DB::select("
                SELECT OBJECT_DEFINITION(OBJECT_ID(?)) AS definition
            ", [$spName]);
            
            if (empty($spDefinition) || !$spDefinition[0]->definition) {
                return [];
            }
            
            $definition = $spDefinition[0]->definition;
            
            // Extract table names from SP (looking for FROM and JOIN clauses)
            preg_match_all('/(?:FROM|JOIN)\s+\[?(\w+)\]?\.?\[?(\w+)\]?/i', $definition, $matches);
            
            $tables = [];
            if (!empty($matches[2])) {
                $tableNames = array_unique($matches[2]);
                
                foreach ($tableNames as $tableName) {
                    // Skip common SQL keywords
                    if (in_array(strtoupper($tableName), ['INSERTED', 'DELETED', 'OUTPUT'])) {
                        continue;
                    }
                    
                    // Get primary keys
                    $primaryKeys = DB::select("
                        SELECT COLUMN_NAME
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                        WHERE OBJECTPROPERTY(OBJECT_ID(CONSTRAINT_SCHEMA + '.' + CONSTRAINT_NAME), 'IsPrimaryKey') = 1
                        AND TABLE_NAME = ?
                        ORDER BY ORDINAL_POSITION
                    ", [$tableName]);
                    
                    // Get foreign keys
                    $foreignKeys = DB::select("
                        SELECT 
                            fk.name AS FK_Name,
                            OBJECT_NAME(fk.parent_object_id) AS Table_Name,
                            COL_NAME(fc.parent_object_id, fc.parent_column_id) AS Column_Name,
                            OBJECT_NAME(fk.referenced_object_id) AS Referenced_Table,
                            COL_NAME(fc.referenced_object_id, fc.referenced_column_id) AS Referenced_Column
                        FROM sys.foreign_keys AS fk
                        INNER JOIN sys.foreign_key_columns AS fc 
                            ON fk.object_id = fc.constraint_object_id
                        WHERE OBJECT_NAME(fk.parent_object_id) = ?
                        ORDER BY fk.name, fc.constraint_column_id
                    ", [$tableName]);
                    
                    $tables[] = [
                        'name' => $tableName,
                        'primary_keys' => array_map(fn($pk) => $pk->COLUMN_NAME, $primaryKeys),
                        'foreign_keys' => array_map(function($fk) {
                            return [
                                'column' => $fk->Column_Name,
                                'references' => $fk->Referenced_Table . '.' . $fk->Referenced_Column,
                                'fk_name' => $fk->FK_Name
                            ];
                        }, $foreignKeys)
                    ];
                }
            }
            
            return $tables;
            
        } catch (\Exception $e) {
            return [];
        }
    }

    public function closeExecuteModal()
    {
        $this->showExecuteModal = false;
        $this->executingSp = null;
        $this->executeResults = [];
        $this->executeError = null;
        $this->executionTime = 0;
        $this->spTableInfo = [];
    }

    private function resetForm()
    {
        $this->selectedId = null;
        $this->ms_admin_sp_id = '';
        $this->sp_desc = '';
        $this->date_start_input = '';
        $this->date_end_input = '';
        $this->money_input = '';
        $this->varchar_input = '';
        $this->sp_name = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $spList = AdminSp::search($this->search)
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $stats = [
            'total' => AdminSp::count(),
            'with_money' => AdminSp::whereNotNull('money_input')->count(),
            'with_dates' => AdminSp::whereNotNull('date_start_input')->orWhereNotNull('date_end_input')->count(),
        ];

        return view('livewire.admin-sp-management', [
            'spList' => $spList,
            'stats' => $stats,
        ])->layout('layouts.bootstrap');
    }
}
