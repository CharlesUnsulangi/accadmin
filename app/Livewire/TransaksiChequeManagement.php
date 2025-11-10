<?php

namespace App\Livewire;

use App\Models\TransaksiCheque;
use App\Models\TransaksiChequeD;
use App\Models\TransaksiMain;
use App\Models\StatusCheque;
use App\Models\ChequeD;
use App\Models\Coa;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class TransaksiChequeManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterDateStart = '';
    public $filterDateEnd = '';
    public $perPage = 50;
    
    // Tab Management
    public $activeTab = 'all'; // all, orphan
    
    // Sorting
    public $sortField = 'transcheque_date';
    public $sortDirection = 'desc';

    // Modal
    public $showDetailModal = false;
    public $selectedTransaction = [];
    public $transactionDetails = [];
    public $jurnalData = null;

    protected $queryString = ['search', 'filterStatus', 'activeTab', 'sortField', 'sortDirection'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    
    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
        $this->search = '';
        $this->filterStatus = '';
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function viewDetail($comcode, $areacode, $code)
    {
        $transaction = DB::table('tr_acc_transaksi_cheque as tc')
            ->leftJoin('tr_acc_transaksi_main as tm', function($join) {
                $join->on('tc.transcheque_transmaincode', '=', 'tm.transmain_code')
                     ->on('tc.rec_comcode', '=', 'tm.rec_comcode')
                     ->on('tc.rec_areacode', '=', 'tm.rec_areacode');
            })
            ->where('tc.rec_comcode', $comcode)
            ->where('tc.rec_areacode', $areacode)
            ->where('tc.transcheque_code', $code)
            ->select('tc.*', 'tm.transmain_codetransaksi', 'tm.transmain_desc', 'tm.transmain_date')
            ->first();

        if ($transaction) {
            // Convert stdClass to array
            $this->selectedTransaction = (array) $transaction;
            
            // Get jurnal data if transmaincode exists
            if (!empty($transaction->transcheque_transmaincode)) {
                $this->jurnalData = DB::table('tr_acc_transaksi_coa')
                    ->leftJoin('ms_acc_coa', 'tr_acc_transaksi_coa.transcoa_coa', '=', 'ms_acc_coa.coa_code')
                    ->where('tr_acc_transaksi_coa.transcoa_transaksi_main_code', $transaction->transcheque_transmaincode)
                    ->where('tr_acc_transaksi_coa.rec_comcode', $comcode)
                    ->where('tr_acc_transaksi_coa.rec_areacode', $areacode)
                    ->select(
                        'tr_acc_transaksi_coa.*',
                        'ms_acc_coa.coa_desc',
                        'ms_acc_coa.coa_code'
                    )
                    ->get()
                    ->toArray();
            }
            
            // Get cheque details
            $this->transactionDetails = DB::table('tr_acc_transaksi_cheque_d as tcd')
                ->leftJoin('ms_acc_cheque_d as cd', function($join) {
                    $join->on('tcd.transcheque_no', '=', 'cd.cheque_code_d')
                         ->on('tcd.transcheque_code_h', '=', 'cd.cheque_code_h');
                })
                ->leftJoin('ms_acc_cheque_h as ch', 'cd.cheque_code_h', '=', 'ch.cheque_code_h')
                ->leftJoin('ms_acc_coa as coa', 'tcd.transcheque_coa', '=', 'coa.coa_code')
                ->leftJoin('ms_acc_statuscheque as st', 'cd.cheque_status', '=', 'st.stacheq_code')
                ->where('tcd.rec_comcode', $comcode)
                ->where('tcd.rec_areacode', $areacode)
                ->where('tcd.transcheque_code_h', $code)
                ->select(
                    'tcd.*',
                    'cd.cheque_date',
                    'cd.cheque_status',
                    'st.stacheq_desc as status_desc',
                    'ch.cheque_desc as cheque_book_desc',
                    'ch.cheque_bank',
                    'ch.cheque_rek',
                    'coa.coa_desc'
                )
                ->get()
                ->toArray();

            $this->showDetailModal = true;
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedTransaction = [];
        $this->transactionDetails = [];
        $this->jurnalData = null;
    }

    public function render()
    {
        $query = DB::table('tr_acc_transaksi_cheque as tc')
            ->leftJoin('tr_acc_transaksi_main as tm', function($join) {
                $join->on('tc.transcheque_transmaincode', '=', 'tm.transmain_code')
                     ->on('tc.rec_comcode', '=', 'tm.rec_comcode')
                     ->on('tc.rec_areacode', '=', 'tm.rec_areacode');
            })
            ->select(
                'tc.*',
                'tm.transmain_codetransaksi',
                'tm.transmain_desc',
                'tm.transmain_date',
                DB::raw('(SELECT COUNT(*) FROM tr_acc_transaksi_cheque_d WHERE rec_comcode = tc.rec_comcode AND rec_areacode = tc.rec_areacode AND transcheque_code_h = tc.transcheque_code) as cheque_count')
            );

        // Filter by tab
        if ($this->activeTab === 'orphan') {
            $query->where(function($q) {
                $q->whereNull('tc.transcheque_transmaincode')
                  ->orWhere('tc.transcheque_transmaincode', '');
            });
        }

        // Apply filters
        if ($this->search) {
            $query->where(function($q) {
                $q->where('tc.transcheque_code', 'like', '%' . $this->search . '%')
                  ->orWhere('tc.transcheque_vendor', 'like', '%' . $this->search . '%')
                  ->orWhere('tc.transcheque_doc', 'like', '%' . $this->search . '%')
                  ->orWhere('tc.transcheque_desc', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus) {
            $query->where('tc.transcheque_status', $this->filterStatus);
        }

        if ($this->filterDateStart && $this->filterDateEnd) {
            $query->whereBetween('tc.transcheque_date', [$this->filterDateStart, $this->filterDateEnd]);
        } elseif ($this->filterDateStart) {
            $query->where('tc.transcheque_date', '>=', $this->filterDateStart);
        } elseif ($this->filterDateEnd) {
            $query->where('tc.transcheque_date', '<=', $this->filterDateEnd);
        }

        // Apply sorting
        $sortColumn = $this->sortField;
        
        // Map sortable fields to actual column names
        $columnMap = [
            'transcheque_code' => 'tc.transcheque_code',
            'transcheque_date' => 'tc.transcheque_date',
            'transcheque_vendor' => 'tc.transcheque_vendor',
            'transcheque_value' => 'tc.transcheque_value',
            'transcheque_status' => 'tc.transcheque_status',
            'transmain_code' => 'tc.transcheque_transmaincode',
        ];
        
        $orderByColumn = $columnMap[$sortColumn] ?? 'tc.transcheque_date';
        
        $transactions = $query->orderBy($orderByColumn, $this->sortDirection)
                             ->orderBy('tc.transcheque_code', 'desc')
                             ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total' => DB::table('tr_acc_transaksi_cheque')->count(),
            'pending' => DB::table('tr_acc_transaksi_cheque')->where('transcheque_status', 'PENDING')->count(),
            'approved' => DB::table('tr_acc_transaksi_cheque')->where('transcheque_status', 'APPROVED')->count(),
            'paid' => DB::table('tr_acc_transaksi_cheque')->where('transcheque_status', 'PAID')->count(),
            'total_value' => DB::table('tr_acc_transaksi_cheque')->sum('transcheque_value'),
            'with_jurnal' => DB::table('tr_acc_transaksi_cheque')
                ->whereNotNull('transcheque_transmaincode')
                ->where('transcheque_transmaincode', '!=', '')
                ->count(),
            'orphan' => DB::table('tr_acc_transaksi_cheque')
                ->where(function($q) {
                    $q->whereNull('transcheque_transmaincode')
                      ->orWhere('transcheque_transmaincode', '');
                })
                ->count(),
        ];

        // Get available statuses from master
        $statuses = StatusCheque::active()
            ->orderBy('stacheq_code')
            ->pluck('stacheq_code', 'stacheq_code');

        return view('livewire.transaksi-cheque-management', [
            'transactions' => $transactions,
            'stats' => $stats,
            'statuses' => $statuses,
        ])->layout('layouts.bootstrap');
    }
}
