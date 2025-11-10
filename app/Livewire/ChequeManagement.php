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

    // Modal properties
    public $showModal = false;
    public $isEdit = false;
    public $cheque_code_h;
    public $cheque_desc;
    public $cheque_bank;
    public $cheque_rek;
    public $cheque_cabang;
    public $cheque_coacode;
    public $cheque_type;
    public $cheque_startno;
    public $cheque_endno;

    // Active tab
    public $activeTab = 'books'; // 'books' or 'open'

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

    /**
     * Switch tab
     */
    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->clearFilters();
    }

    /**
     * Open modal to create new cheque book
     */
    public function create()
    {
        $this->reset(['cheque_code_h', 'cheque_desc', 'cheque_bank', 'cheque_rek', 'cheque_cabang', 'cheque_coacode', 'cheque_type', 'cheque_startno', 'cheque_endno']);
        $this->isEdit = false;
        $this->showModal = true;
    }

    /**
     * Open modal to edit cheque book
     */
    public function edit($code)
    {
        $cheque = ChequeH::findOrFail($code);
        
        $this->cheque_code_h = $cheque->cheque_code_h;
        $this->cheque_desc = $cheque->cheque_desc;
        $this->cheque_bank = $cheque->cheque_bank;
        $this->cheque_rek = $cheque->cheque_rek;
        $this->cheque_cabang = $cheque->cheque_cabang;
        $this->cheque_coacode = $cheque->cheque_coacode;
        $this->cheque_type = $cheque->cheque_type;
        $this->cheque_startno = $cheque->cheque_startno;
        $this->cheque_endno = $cheque->cheque_endno;

        $this->isEdit = true;
        $this->showModal = true;
    }

    /**
     * Save cheque book (create or update)
     */
    public function save()
    {
        $rules = [
            'cheque_desc' => 'nullable|string|max:250',
            'cheque_bank' => 'nullable|string|max:100',
            'cheque_rek' => 'nullable|string|max:100',
            'cheque_cabang' => 'nullable|string|max:100',
            'cheque_coacode' => 'nullable|string|max:50',
            'cheque_type' => 'nullable|string|max:50',
            'cheque_startno' => 'nullable|integer|min:1',
            'cheque_endno' => 'nullable|integer|min:1',
        ];

        if (!$this->isEdit) {
            $rules['cheque_code_h'] = 'required|string|max:50|unique:ms_acc_cheque_h,cheque_code_h';
        }

        $this->validate($rules);

        $username = auth()->user()->name ?? 'System';

        if ($this->isEdit) {
            $cheque = ChequeH::findOrFail($this->cheque_code_h);
            $cheque->update([
                'cheque_desc' => $this->cheque_desc,
                'cheque_bank' => $this->cheque_bank,
                'cheque_rek' => $this->cheque_rek,
                'cheque_cabang' => $this->cheque_cabang,
                'cheque_coacode' => $this->cheque_coacode,
                'cheque_type' => $this->cheque_type,
                'cheque_startno' => $this->cheque_startno,
                'cheque_endno' => $this->cheque_endno,
                'rec_userupdate' => $username,
                'rec_dateupdate' => now(),
            ]);
            session()->flash('message', 'Buku cheque berhasil diupdate.');
        } else {
            $cheque = ChequeH::create([
                'cheque_code_h' => $this->cheque_code_h,
                'cheque_desc' => $this->cheque_desc,
                'cheque_bank' => $this->cheque_bank,
                'cheque_rek' => $this->cheque_rek,
                'cheque_cabang' => $this->cheque_cabang,
                'cheque_coacode' => $this->cheque_coacode,
                'cheque_type' => $this->cheque_type,
                'cheque_startno' => $this->cheque_startno,
                'cheque_endno' => $this->cheque_endno,
                'rec_usercreated' => $username,
                'rec_userupdate' => $username,
                'rec_datecreated' => now(),
                'rec_dateupdate' => now(),
                'rec_status' => '1',
            ]);

            // Generate cheque details if start and end numbers are provided
            if ($this->cheque_startno && $this->cheque_endno && $this->cheque_startno <= $this->cheque_endno) {
                $this->generateChequeDetails($cheque);
            }

            session()->flash('message', 'Buku cheque berhasil ditambahkan.');
        }

        $this->closeModal();
        $this->resetPage();
    }

    /**
     * Generate cheque details based on start and end numbers
     */
    private function generateChequeDetails($cheque)
    {
        for ($i = $this->cheque_startno; $i <= $this->cheque_endno; $i++) {
            ChequeD::create([
                'cheque_code_d' => $cheque->cheque_code_h . '-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'cheque_code_h' => $cheque->cheque_code_h,
                'cheque_no' => $i,
                'cheque_status' => 'AVAILABLE',
                'cheque_value' => 0,
                'rec_usercreated' => auth()->user()->name ?? 'System',
                'rec_userupdate' => auth()->user()->name ?? 'System',
                'rec_datecreated' => now(),
                'rec_dateupdate' => now(),
                'rec_status' => '1',
            ]);
        }
    }

    /**
     * Close modal
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['cheque_code_h', 'cheque_desc', 'cheque_bank', 'cheque_rek', 'cheque_cabang', 'cheque_coacode', 'cheque_type', 'cheque_startno', 'cheque_endno']);
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

        // Get list of COAs for filter and dropdown
        $coaList = Coa::where('rec_status', '1')
            ->orderBy('coa_code')
            ->get()
            ->map(function($coa) {
                return [
                    'code' => $coa->coa_code,
                    'label' => $coa->coa_code . ' - ' . $coa->coa_desc
                ];
            });

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

        // Query open cheques (available)
        $openCheques = ChequeD::query()
            ->with(['chequeBook.coa'])
            ->where('cheque_status', 'AVAILABLE')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('cheque_code_d', 'like', '%' . $this->search . '%')
                      ->orWhere('cheque_code_h', 'like', '%' . $this->search . '%')
                      ->orWhereHas('chequeBook', function($q) {
                          $q->where('cheque_bank', 'like', '%' . $this->search . '%')
                            ->orWhere('cheque_desc', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->filterBank, function ($query) {
                $query->whereHas('chequeBook', function($q) {
                    $q->where('cheque_bank', $this->filterBank);
                });
            })
            ->when($this->filterCoa, function ($query) {
                $query->whereHas('chequeBook', function($q) {
                    $q->where('cheque_coacode', $this->filterCoa);
                });
            })
            ->orderBy('cheque_code_h')
            ->orderBy('cheque_no')
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
            'openCheques' => $openCheques,
            'bankList' => $bankList,
            'coaList' => $coaList,
            'summary' => $summary,
        ])->layout('layouts.bootstrap');
    }
}
