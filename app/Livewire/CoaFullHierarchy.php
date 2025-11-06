<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * COA Full Hierarchy Report
 * 
 * Menampilkan full hierarchy 4 level dengan JOIN
 * Level 1: ms_acc_coa_main
 * Level 2: ms_acc_coasub1
 * Level 3: ms_acc_coasub2
 * Level 4: ms_acc_coa
 */
class CoaFullHierarchy extends Component
{
    use WithPagination;

    // Search & Filter
    public $search = '';
    public $filterMain = '';
    public $filterSub1 = '';
    public $filterSub2 = '';
    public $perPage = 25;

    /**
     * Reset pagination when search changes
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Render component
     */
    public function render()
    {
        $query = DB::table('ms_acc_coa_main as m')
            ->leftJoin('ms_acc_coasub1 as s1', 's1.coasub1_maincode', '=', 'm.coa_main_code')
            ->leftJoin('ms_acc_coasub2 as s2', 's2.coasub2_coasub1code', '=', 's1.coasub1_code')
            ->leftJoin('ms_acc_coa as d', 'd.coa_coasub2code', '=', 's2.coasub2_code')
            ->select([
                // Level 1 - Main
                'm.coa_main_code',
                'm.coa_main_desc',
                
                // Level 2 - Sub1
                's1.coasub1_code',
                's1.coasub1_desc',
                
                // Level 3 - Sub2
                's2.coasub2_code',
                's2.coasub2_desc',
                
                // Level 4 - Detail
                'd.coa_code',
                'd.coa_desc',
                'd.coa_note',
                'd.arus_kas_code',
                
                // Hierarki tambahan
                'd.desc_h1',
                'd.desc_h2',
                'd.desc_h3',
                'd.desc_h4',
                'd.desc_h5',
                'd.desc_h6',
                'd.main_desc',
                'd.sub1_desc',
                'd.sub2_desc',
            ])
            ->where('m.rec_status', '1')
            ->where(function($q) {
                $q->where('s1.rec_status', '1')
                  ->orWhereNull('s1.rec_status');
            })
            ->where(function($q) {
                $q->where('s2.rec_status', '1')
                  ->orWhereNull('s2.rec_status');
            })
            ->where('d.rec_status', '1');

        // Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('m.coa_main_desc', 'like', '%' . $this->search . '%')
                  ->orWhere('s1.coasub1_desc', 'like', '%' . $this->search . '%')
                  ->orWhere('s2.coasub2_desc', 'like', '%' . $this->search . '%')
                  ->orWhere('d.coa_code', 'like', '%' . $this->search . '%')
                  ->orWhere('d.coa_desc', 'like', '%' . $this->search . '%');
            });
        }

        // Filter Main
        if ($this->filterMain) {
            $query->where('m.coa_main_code', $this->filterMain);
        }

        // Filter Sub1
        if ($this->filterSub1) {
            $query->where('s1.coasub1_code', $this->filterSub1);
        }

        // Filter Sub2
        if ($this->filterSub2) {
            $query->where('s2.coasub2_code', $this->filterSub2);
        }

        $hierarchy = $query->orderBy('m.coa_main_code')
                          ->orderBy('s1.coasub1_code')
                          ->orderBy('s2.coasub2_code')
                          ->orderBy('d.coa_code')
                          ->paginate($this->perPage);

        // Get filter options
        $mains = DB::table('ms_acc_coa_main')
                   ->where('rec_status', '1')
                   ->orderBy('coa_main_code')
                   ->pluck('coa_main_desc', 'coa_main_code');

        $sub1s = DB::table('ms_acc_coasub1')
                   ->where('rec_status', '1')
                   ->when($this->filterMain, function($q) {
                       $q->where('coasub1_maincode', $this->filterMain);
                   })
                   ->orderBy('coasub1_code')
                   ->pluck('coasub1_desc', 'coasub1_code');

        $sub2s = DB::table('ms_acc_coasub2')
                   ->where('rec_status', '1')
                   ->when($this->filterSub1, function($q) {
                       $q->where('coasub2_coasub1code', $this->filterSub1);
                   })
                   ->orderBy('coasub2_code')
                   ->pluck('coasub2_desc', 'coasub2_code');

        return view('livewire.coa-full-hierarchy', [
            'hierarchy' => $hierarchy,
            'mains' => $mains,
            'sub1s' => $sub1s,
            'sub2s' => $sub2s,
        ])->layout('layouts.admin');
    }

    /**
     * Export to Excel (placeholder)
     */
    public function export()
    {
        session()->flash('message', 'Export feature coming soon!');
    }
}
