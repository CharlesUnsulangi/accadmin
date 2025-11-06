<?php

namespace App\Livewire;

use App\Models\TransaksiMain;
use App\Models\TransaksiCoa;
use App\Models\Coa;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

/**
 * Jurnal Transaksi Management
 * Menampilkan data dari tr_acc_transaksi_main dengan detail tr_acc_transaksi_coa
 */
class JurnalTransaksi extends Component
{
    use WithPagination;

    // Search & Filter
    public $search = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterCoa = '';
    public $filterStatus = '';
    public $filterType = '';
    public $perPage = 25;

    // Sorting
    public $sortBy = 'transcoa_coa_date';
    public $sortDirection = 'desc';

    protected $queryString = ['search', 'filterDateFrom', 'filterDateTo'];

    public function mount()
    {
        // Set default date filter to current month
        $this->filterDateFrom = date('Y-m-01');
        $this->filterDateTo = date('Y-m-t');
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

    public function resetFilters()
    {
        $this->search = '';
        $this->filterDateFrom = date('Y-m-01');
        $this->filterDateTo = date('Y-m-t');
        $this->filterCoa = '';
        $this->filterStatus = '';
        $this->filterType = '';
        $this->resetPage();
    }

    /**
     * Quick filter by clicking on field
     */
    public function filterByCoa($coaCode)
    {
        $this->filterCoa = $coaCode;
        $this->resetPage();
    }

    public function filterByDate($date)
    {
        $this->filterDateFrom = $date;
        $this->filterDateTo = $date;
        $this->resetPage();
    }

    public function filterByVoucher($voucherCode)
    {
        $this->search = $voucherCode;
        $this->resetPage();
    }

    public function filterByStatus($status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function render()
    {
        // Query transaksi main (header) dengan detail
        $transactions = TransaksiMain::query()
            ->with(['details.coa'])
            ->where('rec_status', '1')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('transmain_code', 'like', '%' . $this->search . '%')
                      ->orWhere('transmain_codetransaksi', 'like', '%' . $this->search . '%')
                      ->orWhere('transmain_desc', 'like', '%' . $this->search . '%')
                      ->orWhereHas('details', function($q2) {
                          $q2->where('transcoa_coa_code', 'like', '%' . $this->search . '%')
                             ->orWhere('transcoa_coa_desc', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->filterDateFrom, function ($query) {
                $query->where('transmain_document_date', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function ($query) {
                $query->where('transmain_document_date', '<=', $this->filterDateTo);
            })
            ->when($this->filterCoa, function ($query) {
                $query->whereHas('details', function($q) {
                    $q->where('transcoa_coa_code', $this->filterCoa);
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->whereHas('details', function($q) {
                    $q->where('transcoa_statusposting', $this->filterStatus);
                });
            })
            ->orderBy('transmain_document_date', 'desc')
            ->orderBy('transmain_code', 'desc')
            ->paginate($this->perPage);

        // Get unique COA codes for filter
        $coaList = Coa::where('rec_status', '1')
                      ->orderBy('coa_code')
                      ->pluck('coa_desc', 'coa_code');

        // Get summary statistics from details
        $summary = DB::table('tr_acc_transaksi_main as m')
            ->join('tr_acc_transaksi_coa as d', function($join) {
                $join->on('d.transcoa_transaksi_main_code', '=', 'm.transmain_code')
                     ->on('d.rec_comcode', '=', 'm.rec_comcode')
                     ->on('d.rec_areacode', '=', 'm.rec_areacode');
            })
            ->where('m.rec_status', '1')
            ->where('d.rec_status', '1')
            ->when($this->filterDateFrom, function ($query) {
                $query->where('m.transmain_document_date', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function ($query) {
                $query->where('m.transmain_document_date', '<=', $this->filterDateTo);
            })
            ->selectRaw('
                SUM(d.transcoa_debet_value) as total_debet,
                SUM(d.transcoa_credit_value) as total_credit,
                COUNT(DISTINCT m.transmain_code) as total_vouchers,
                COUNT(*) as total_lines
            ')
            ->first();

        return view('livewire.jurnal-transaksi-bootstrap', [
            'transactions' => $transactions,
            'coaList' => $coaList,
            'summary' => $summary,
        ])->layout('layouts.bootstrap');
    }
}
