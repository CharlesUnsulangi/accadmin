<?php

namespace App\Livewire;

use App\Models\ChequeH;
use App\Models\ChequeD;
use App\Models\Coa;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Cheque Management
 * Manajemen buku cheque (ms_acc_cheque_h) dan detail cheque (ms_acc_cheque_d)
 */
class ChequeManagement extends Component
{
    use WithPagination;

    // Search & Filter
    public $search = '';
    public $filterBank = '';
    public $filterStatus = '';
    public $filterType = '';
    public $filterCoa = '';
    public $perPage = 50;

    // Sorting
    public $sortBy = 'cheque_code_h';
    public $sortDirection = 'desc';

    protected $queryString = ['search', 'filterBank', 'filterStatus'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterBank()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterCoa()
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

    /**
     * Filter by specific bank
     */
    public function filterByBank($bank)
    {
        $this->filterBank = $this->filterBank === $bank ? '' : $bank;
        $this->resetPage();
    }

    /**
     * Filter by specific COA
     */
    public function filterByCoa($coa)
    {
        $this->filterCoa = $this->filterCoa === $coa ? '' : $coa;
        $this->resetPage();
    }

    /**
     * Clear all filters
     */
    public function clearFilters()
    {
        $this->reset(['search', 'filterBank', 'filterStatus', 'filterType', 'filterCoa']);
        $this->resetPage();
    }

    public function render()
    {
        // Get list of banks for filter
        $bankList = ChequeH::where('rec_status', '1')
            ->whereNotNull('cheque_bank')
            ->distinct()
            ->pluck('cheque_bank')
            ->filter()
            ->sort();

        // Get list of COAs for filter
        $coaList = ChequeH::where('rec_status', '1')
            ->whereNotNull('cheque_coacode')
            ->distinct()
            ->pluck('cheque_coacode')
            ->filter()
            ->sort();

        // Query cheque books
        $chequeBooks = ChequeH::query()
            ->with(['details', 'coa'])
            ->where('rec_status', '1')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('cheque_code_h', 'like', '%' . $this->search . '%')
                      ->orWhere('cheque_desc', 'like', '%' . $this->search . '%')
                      ->orWhere('cheque_bank', 'like', '%' . $this->search . '%')
                      ->orWhere('cheque_rek', 'like', '%' . $this->search . '%')
                      ->orWhere('cheque_cabang', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterBank, function ($query) {
                $query->where('cheque_bank', $this->filterBank);
            })
            ->when($this->filterCoa, function ($query) {
                $query->where('cheque_coacode', $this->filterCoa);
            })
            ->when($this->filterType, function ($query) {
                $query->where('cheque_type', $this->filterType);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        // Calculate summary
        $summary = (object) [
            'total_books' => ChequeH::where('rec_status', '1')->count(),
            'total_cheques' => ChequeD::count(),
            'available_cheques' => ChequeD::where('cheque_status', 'AVAILABLE')->count(),
            'used_cheques' => ChequeD::where('cheque_status', 'USED')->count(),
            'total_value' => ChequeD::sum('cheque_value') ?? 0,
        ];

        return view('livewire.cheque-management-bootstrap', [
            'chequeBooks' => $chequeBooks,
            'bankList' => $bankList,
            'coaList' => $coaList,
            'summary' => $summary,
        ])->layout('layouts.bootstrap');
    }
}
