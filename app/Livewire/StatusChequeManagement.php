<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StatusCheque;
use Illuminate\Support\Facades\Auth;

class StatusChequeManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 100;

    // Modal properties
    public $showModal = false;
    public $isEdit = false;
    public $stacheq_code;
    public $stacheq_desc;

    protected function rules()
    {
        $rules = [
            'stacheq_desc' => 'nullable|string|max:50',
        ];

        if (!$this->isEdit) {
            $rules['stacheq_code'] = 'required|string|max:50|unique:ms_acc_statuscheque,stacheq_code';
        }

        return $rules;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['stacheq_code', 'stacheq_desc']);
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function edit($code)
    {
        $status = StatusCheque::findOrFail($code);
        
        $this->stacheq_code = $status->stacheq_code;
        $this->stacheq_desc = $status->stacheq_desc;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $username = Auth::user()->name ?? 'System';

        if ($this->isEdit) {
            $status = StatusCheque::findOrFail($this->stacheq_code);
            $status->update([
                'stacheq_desc' => $this->stacheq_desc,
                'rec_userupdate' => $username,
                'rec_dateupdate' => now(),
            ]);
            session()->flash('message', 'Master Status Cheque berhasil diupdate.');
        } else {
            StatusCheque::create([
                'stacheq_code' => $this->stacheq_code,
                'stacheq_desc' => $this->stacheq_desc,
                'rec_usercreated' => $username,
                'rec_userupdate' => $username,
                'rec_datecreated' => now(),
                'rec_dateupdate' => now(),
                'rec_status' => '1',
            ]);
            session()->flash('message', 'Master Status Cheque berhasil ditambahkan.');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function toggleStatus($code)
    {
        $status = StatusCheque::findOrFail($code);
        $newStatus = $status->rec_status == '1' ? '0' : '1';
        
        $status->update([
            'rec_status' => $newStatus,
            'rec_userupdate' => Auth::user()->name ?? 'System',
            'rec_dateupdate' => now(),
        ]);

        $statusText = $newStatus == '1' ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('message', "Master Status Cheque berhasil {$statusText}.");
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['stacheq_code', 'stacheq_desc']);
    }

    public function render()
    {
        $statusCheques = StatusCheque::search($this->search)
            ->orderBy('stacheq_code')
            ->paginate($this->perPage);

        return view('livewire.status-cheque-management', [
            'statusCheques' => $statusCheques,
        ])->layout('layouts.bootstrap');
    }
}
