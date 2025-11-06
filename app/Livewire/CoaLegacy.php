<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Coa;
use App\Models\CoaSub2;
use App\Models\CoaSub1;
use App\Models\CoaMain;

/**
 * COA Legacy Management - 4 Level Hierarchy
 * Level 1: ms_acc_coa_main (Main Category)
 * Level 2: ms_acc_coa_main_sub1 (Sub Category 1)
 * Level 3: ms_acc_coa_main_sub2 (Sub Category 2) ← This component displays this level
 * Level 4: ms_acc_coa (Detail COA Accounts - 501+ records)
 * 
 * Relationship: Level 4 (ms_acc_coa) → Level 3 via coa_coasub2code
 * Target: User yang terbiasa dengan sistem lama
 */
class CoaLegacy extends Component
{
    use WithPagination;

    // Search & Filter
    public $search = '';
    public $filterLevel = '';
    public $filterMain = '';
    public $filterSub1 = '';
    
    // Sorting
    public $sortBy = 'coasub2_code';
    public $sortDirection = 'asc';

    protected $queryString = ['search', 'filterLevel'];

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
        // Query dari tabel CoaSub2 (Legacy Level 3) dengan relationship
        $coaSub2s = CoaSub2::query()
            ->with(['coaSub1.coaMain', 'coas']) // Eager load relationships
            ->where('rec_status', '1')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('coasub2_code', 'like', '%' . $this->search . '%')
                      ->orWhere('coasub2_desc', 'like', '%' . $this->search . '%')
                      ->orWhereHas('coaSub1', function($q2) {
                          $q2->where('coasub1_desc', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('coaSub1.coaMain', function($q3) {
                          $q3->where('coa_main_desc', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->filterMain, function ($query) {
                $query->whereHas('coaSub1', function($q) {
                    $q->where('coasub1_code', 'like', $this->filterMain . '%');
                });
            })
            ->when($this->filterSub1, function ($query) {
                $query->where('coasub2_code', 'like', $this->filterSub1 . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(20);

        // Get unique values for filters
        $mains = CoaMain::where('rec_status', '1')
                        ->orderBy('coa_main_code')
                        ->pluck('coa_main_desc', 'coa_main_code');
        
        $sub1s = CoaSub1::where('rec_status', '1')
                        ->when($this->filterMain, function ($query) {
                            $query->where('coasub1_code', 'like', $this->filterMain . '%');
                        })
                        ->orderBy('coasub1_code')
                        ->pluck('coasub1_desc', 'coasub1_code');

        return view('livewire.coa-legacy', [
            'coaSub2s' => $coaSub2s,
            'mains' => $mains,
            'sub1s' => $sub1s,
        ])->layout('layouts.admin');
    }
}
