<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Trait for standard Master Data CRUD operations
 * 
 * Features:
 * - Search & Filter
 * - Pagination
 * - Soft Delete
 * - Export to Excel
 * - Audit Trail
 */
trait HasMasterDataFeatures
{
    // Common properties for all master data
    public $search = '';
    public $filterStatus = '1'; // '1' = Active, '0' = Inactive, '' = All
    public $perPage = 25;
    public $showModal = false;
    public $editMode = false;
    public $sortBy = '';
    public $sortDirection = 'asc';

    /**
     * Reset pagination when search or filter changes
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    /**
     * Sort by column
     */
    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Get sort icon for column header
     */
    public function getSortIcon($column)
    {
        if ($this->sortBy !== $column) {
            return 'fa-sort text-muted';
        }
        return $this->sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
    }

    /**
     * Open modal for creating new record
     */
    public function create()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    /**
     * Open modal for editing record
     */
    public function edit($id)
    {
        $this->editMode = true;
        $this->loadRecord($id);
        $this->showModal = true;
    }

    /**
     * Close modal
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Soft delete record
     */
    public function delete($id)
    {
        try {
            $record = $this->getModelInstance()->findOrFail($id);
            
            $record->update([
                'rec_status' => '0',
                'rec_userupdate' => auth()->user()->name ?? 'system',
                'rec_dateupdate' => now(),
            ]);

            session()->flash('message', $this->getEntityName() . ' berhasil dihapus (soft delete)!');
            session()->flash('type', 'success');
        } catch (\Exception $e) {
            session()->flash('message', 'Error: ' . $e->getMessage());
            session()->flash('type', 'danger');
        }
    }

    /**
     * Restore soft deleted record
     */
    public function restore($id)
    {
        try {
            $record = $this->getModelInstance()->findOrFail($id);
            
            $record->update([
                'rec_status' => '1',
                'rec_userupdate' => auth()->user()->name ?? 'system',
                'rec_dateupdate' => now(),
            ]);

            session()->flash('message', $this->getEntityName() . ' berhasil direstore!');
            session()->flash('type', 'success');
        } catch (\Exception $e) {
            session()->flash('message', 'Error: ' . $e->getMessage());
            session()->flash('type', 'danger');
        }
    }

    /**
     * Export data to Excel
     */
    public function export()
    {
        try {
            $query = $this->getQuery();
            $data = $query->get();
            
            $exportClass = $this->getExportClass();
            
            if ($exportClass && class_exists($exportClass)) {
                return Excel::download(new $exportClass($data), $this->getExportFilename());
            }
            
            session()->flash('message', 'Export class not found!');
            session()->flash('type', 'warning');
        } catch (\Exception $e) {
            session()->flash('message', 'Export error: ' . $e->getMessage());
            session()->flash('type', 'danger');
        }
    }

    /**
     * Get base query with filters applied
     */
    protected function getQuery()
    {
        $query = $this->getModelInstance()->query();

        // Apply search
        if ($this->search) {
            $query = $this->applySearch($query, $this->search);
        }

        // Apply status filter
        if ($this->filterStatus !== '') {
            $query->where('rec_status', $this->filterStatus);
        }

        // Apply sorting
        if ($this->sortBy) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        } else {
            $query = $this->applyDefaultSort($query);
        }

        return $query;
    }

    /**
     * Get statistics for the master data
     */
    public function getStatistics()
    {
        $model = $this->getModelInstance();
        
        return [
            'total' => $model->where('rec_status', '1')->count(),
            'inactive' => $model->where('rec_status', '0')->count(),
            'all' => $model->count(),
        ];
    }

    // ========================================
    // Methods to be implemented by child class
    // ========================================

    /**
     * Get model instance
     * @return \Illuminate\Database\Eloquent\Model
     */
    abstract protected function getModelInstance();

    /**
     * Get entity name for messages
     * @return string
     */
    abstract protected function getEntityName();

    /**
     * Apply search to query
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract protected function applySearch($query, $search);

    /**
     * Apply default sorting
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyDefaultSort($query)
    {
        return $query->orderBy('rec_datecreated', 'desc');
    }

    /**
     * Reset form fields
     */
    abstract protected function resetForm();

    /**
     * Load record for editing
     * @param mixed $id
     */
    abstract protected function loadRecord($id);

    /**
     * Get export class name (optional)
     * @return string|null
     */
    protected function getExportClass()
    {
        return null;
    }

    /**
     * Get export filename
     * @return string
     */
    protected function getExportFilename()
    {
        return strtolower($this->getEntityName()) . '_' . date('Ymd_His') . '.xlsx';
    }
}
