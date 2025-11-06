<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Coa;

/**
 * COA Modern Management - Flexible H1-H6 Hierarchy
 * Menggunakan field modern: ms_coa_h1_id ... ms_coa_h6_id
 * Target: User baru, sistem masa depan
 */
class CoaModern extends Component
{
    use WithPagination;

    // Search & Filter
    public $search = '';
    public $filterLevel = ''; // Filter by hierarchy level (1-6)
    public $filterH1 = '';
    public $filterH2 = '';
    public $filterH3 = '';
    public $filter_sub2 = ''; // Filter by Legacy Level 3 (coa_coasub2code)
    
    // Sorting
    public $sortBy = 'coa_code';
    public $sortDirection = 'asc';

    protected $queryString = ['search', 'filterLevel', 'filter_sub2'];

    public function mount()
    {
        // Get filter_sub2 from query string if exists
        $this->filter_sub2 = request()->query('filter_sub2', '');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $coas = Coa::query()
            ->where('rec_status', 'A')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('coa_code', 'like', '%' . $this->search . '%')
                      ->orWhere('coa_desc', 'like', '%' . $this->search . '%')
                      ->orWhere('desc_h1', 'like', '%' . $this->search . '%')
                      ->orWhere('desc_h2', 'like', '%' . $this->search . '%')
                      ->orWhere('desc_h3', 'like', '%' . $this->search . '%')
                      ->orWhere('desc_h4', 'like', '%' . $this->search . '%')
                      ->orWhere('desc_h5', 'like', '%' . $this->search . '%')
                      ->orWhere('desc_h6', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterLevel, function ($query) {
                $query->hasHierarchyLevel($this->filterLevel);
            })
            ->when($this->filterH1, function ($query) {
                $query->where('ms_coa_h1_id', $this->filterH1);
            })
            ->when($this->filterH2, function ($query) {
                $query->where('ms_coa_h2_id', $this->filterH2);
            })
            ->when($this->filterH3, function ($query) {
                $query->where('ms_coa_h3_id', $this->filterH3);
            })
            ->when($this->filter_sub2, function ($query) {
                $query->where('coa_coasub2code', $this->filter_sub2);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(20);

        // Get unique values for filters
        $h1s = Coa::where('rec_status', 'A')
                  ->whereNotNull('ms_coa_h1_id')
                  ->distinct()
                  ->pluck('desc_h1', 'ms_coa_h1_id');
        
        $h2s = Coa::where('rec_status', 'A')
                  ->whereNotNull('ms_coa_h2_id')
                  ->when($this->filterH1, function ($query) {
                      $query->where('ms_coa_h1_id', $this->filterH1);
                  })
                  ->distinct()
                  ->pluck('desc_h2', 'ms_coa_h2_id');

        $h3s = Coa::where('rec_status', 'A')
                  ->whereNotNull('ms_coa_h3_id')
                  ->when($this->filterH2, function ($query) {
                      $query->where('ms_coa_h2_id', $this->filterH2);
                  })
                  ->distinct()
                  ->pluck('desc_h3', 'ms_coa_h3_id');

        return view('livewire.coa-modern', [
            'coas' => $coas,
            'h1s' => $h1s,
            'h2s' => $h2s,
            'h3s' => $h3s,
        ])->layout('layouts.admin');
    }
}
