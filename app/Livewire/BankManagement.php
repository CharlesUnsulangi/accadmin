<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Traits\HasMasterDataFeatures;
use App\Exports\BankExport;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class BankManagement extends Component
{
    use WithPagination, HasMasterDataFeatures;

    // Form fields specific to Bank
    public $bank_code = '';
    public $bank_name = '';
    public $bank_desc = '';
    
    protected $queryString = ['search', 'filterStatus'];

    /**
     * Validation rules
     */
    protected function rules()
    {
        $rules = [
            'bank_code' => 'required|max:100',
            'bank_name' => 'nullable|max:255',
            'bank_desc' => 'nullable|max:500',
        ];

        // Add unique validation for bank_code when creating
        if (!$this->editMode) {
            $rules['bank_code'] .= '|unique:ms_acc_bank,bank_code';
        }

        return $rules;
    }

    /**
     * Save bank (create or update)
     */
    public function save()
    {
        $this->validate();

        try {
            if ($this->editMode) {
                $bank = Bank::where('bank_code', $this->bank_code)->firstOrFail();
                $bank->update([
                    'bank_name' => $this->bank_name,
                    'bank_desc' => $this->bank_desc,
                    'rec_userupdate' => auth()->user()->name ?? 'system',
                    'rec_dateupdate' => now(),
                ]);
                
                session()->flash('message', 'Bank berhasil diupdate!');
                session()->flash('type', 'success');
            } else {
                Bank::create([
                    'bank_code' => $this->bank_code,
                    'bank_name' => $this->bank_name,
                    'bank_desc' => $this->bank_desc,
                    'rec_usercreated' => auth()->user()->name ?? 'system',
                    'rec_datecreated' => now(),
                    'rec_userupdate' => '',
                    'rec_dateupdate' => now(),
                    'rec_status' => '1',
                ]);
                
                session()->flash('message', 'Bank berhasil ditambahkan!');
                session()->flash('type', 'success');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('message', 'Error: ' . $e->getMessage());
            session()->flash('type', 'danger');
        }
    }

    /**
     * Export to Excel
     */
    public function export()
    {
        try {
            $banks = $this->getQuery()->get();
            return Excel::download(new BankExport($banks), 'bank_master_' . date('Ymd_His') . '.xlsx');
        } catch (\Exception $e) {
            session()->flash('message', 'Export error: ' . $e->getMessage());
            session()->flash('type', 'danger');
        }
    }

    // ========================================
    // Implementation of abstract methods from trait
    // ========================================

    protected function getModelInstance()
    {
        return new Bank();
    }

    protected function getEntityName()
    {
        return 'Bank';
    }

    protected function applySearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('bank_code', 'like', '%' . $search . '%')
              ->orWhere('bank_name', 'like', '%' . $search . '%')
              ->orWhere('bank_desc', 'like', '%' . $search . '%');
        });
    }

    protected function applyDefaultSort($query)
    {
        return $query->orderBy('bank_code', 'asc');
    }

    protected function resetForm()
    {
        $this->bank_code = '';
        $this->bank_name = '';
        $this->bank_desc = '';
        $this->resetErrorBag();
    }

    protected function loadRecord($id)
    {
        $bank = Bank::where('bank_code', $id)->firstOrFail();
        $this->bank_code = $bank->bank_code;
        $this->bank_name = $bank->bank_name ?? '';
        $this->bank_desc = $bank->bank_desc ?? '';
    }

    protected function getExportClass()
    {
        return BankExport::class;
    }

    /**
     * Render component
     */
    public function render()
    {
        $banks = $this->getQuery()->paginate($this->perPage);
        $stats = $this->getStatistics();

        return view('livewire.bank-management', [
            'banks' => $banks,
            'stats' => $stats
        ])->layout('layouts.bootstrap');
    }
}
