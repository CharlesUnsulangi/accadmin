<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaksi;
use App\Models\Coa;
use Illuminate\Support\Facades\Auth;

class TransaksiManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 100;

    // Modal properties
    public $showModal = false;
    public $isEdit = false;
    public $trans_code;
    public $trans_desc;
    public $trans_coa_debet;
    public $trans_coa_kredit;
    public $trans_date;
    public $trans_debet;
    public $trans_kredit;

    // COA list for dropdown
    public $coaList = [];

    public function mount()
    {
        $this->loadCoaList();
    }

    public function loadCoaList()
    {
        $this->coaList = Coa::where('rec_status', '1')
            ->orderBy('coa_code')
            ->get()
            ->map(function($coa) {
                return [
                    'code' => $coa->coa_code,
                    'label' => $coa->coa_code . ' - ' . $coa->coa_desc
                ];
            })
            ->toArray();
    }

    protected function rules()
    {
        $rules = [
            'trans_desc' => 'nullable|string|max:100',
            'trans_coa_debet' => 'nullable|string|max:50',
            'trans_coa_kredit' => 'nullable|string|max:50',
            'trans_date' => 'nullable|date',
            'trans_debet' => 'nullable|numeric|min:0',
            'trans_kredit' => 'nullable|numeric|min:0',
        ];

        if (!$this->isEdit) {
            $rules['trans_code'] = 'required|string|max:50|unique:ms_transaksi,trans_code';
        }

        return $rules;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['trans_code', 'trans_desc', 'trans_coa_debet', 'trans_coa_kredit', 'trans_date', 'trans_debet', 'trans_kredit']);
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function edit($code)
    {
        $transaksi = Transaksi::findOrFail($code);
        
        $this->trans_code = $transaksi->trans_code;
        $this->trans_desc = $transaksi->trans_desc;
        $this->trans_coa_debet = $transaksi->trans_coa_debet;
        $this->trans_coa_kredit = $transaksi->trans_coa_kredit;
        $this->trans_date = $transaksi->trans_date ? $transaksi->trans_date->format('Y-m-d') : null;
        $this->trans_debet = $transaksi->trans_debet;
        $this->trans_kredit = $transaksi->trans_kredit;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $username = Auth::user()->name ?? 'System';

        if ($this->isEdit) {
            $transaksi = Transaksi::findOrFail($this->trans_code);
            $transaksi->update([
                'trans_desc' => $this->trans_desc,
                'trans_coa_debet' => $this->trans_coa_debet,
                'trans_coa_kredit' => $this->trans_coa_kredit,
                'trans_date' => $this->trans_date,
                'trans_debet' => $this->trans_debet,
                'trans_kredit' => $this->trans_kredit,
                'rec_userupdate' => $username,
                'rec_dateupdate' => now(),
            ]);
            session()->flash('message', 'Master Transaksi berhasil diupdate.');
        } else {
            Transaksi::create([
                'trans_code' => $this->trans_code,
                'trans_desc' => $this->trans_desc,
                'trans_coa_debet' => $this->trans_coa_debet,
                'trans_coa_kredit' => $this->trans_coa_kredit,
                'trans_date' => $this->trans_date,
                'trans_debet' => $this->trans_debet,
                'trans_kredit' => $this->trans_kredit,
                'rec_usercreated' => $username,
                'rec_userupdate' => $username,
                'rec_datecreated' => now(),
                'rec_dateupdate' => '1900-01-01',
                'rec_status' => '1',
            ]);
            session()->flash('message', 'Master Transaksi berhasil ditambahkan.');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function toggleStatus($code)
    {
        $transaksi = Transaksi::findOrFail($code);
        $newStatus = $transaksi->rec_status == '1' ? '0' : '1';
        
        $transaksi->update([
            'rec_status' => $newStatus,
            'rec_userupdate' => Auth::user()->name ?? 'System',
            'rec_dateupdate' => now(),
        ]);

        $statusText = $newStatus == '1' ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('message', "Master Transaksi berhasil {$statusText}.");
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['trans_code', 'trans_desc', 'trans_coa_debet', 'trans_coa_kredit', 'trans_date', 'trans_debet', 'trans_kredit']);
    }

    public function render()
    {
        $transaksi = Transaksi::with(['coaDebet', 'coaKredit'])
            ->search($this->search)
            ->orderBy('trans_code')
            ->paginate($this->perPage);

        return view('livewire.transaksi-management', [
            'transaksi' => $transaksi,
            'coaList' => $this->coaList,
        ])->layout('layouts.bootstrap');
    }
}
