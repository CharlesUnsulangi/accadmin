<?php

namespace App\Livewire;

use App\Models\Bank;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class BankManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    
    // Modal
    public $showModal = false;
    public $editMode = false;
    
    // Form fields
    public $bank_code = '';
    
    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($code)
    {
        $bank = Bank::where('Bank_Code', $code)->first();
        
        if ($bank) {
            $this->bank_code = $bank->Bank_Code;
            $this->editMode = true;
            $this->showModal = true;
        }
    }

    public function save()
    {
        $this->validate([
            'bank_code' => 'required|max:100|unique:ms_bank,Bank_Code,' . ($this->editMode ? $this->bank_code : 'NULL') . ',Bank_Code',
        ]);

        try {
            if ($this->editMode) {
                $bank = Bank::where('Bank_Code', $this->bank_code)->first();
                $bank->rec_userupdate = Auth::user()->name ?? 'system';
                $bank->rec_dateupdate = now();
                $bank->save();
                
                session()->flash('message', 'Bank berhasil diupdate.');
            } else {
                Bank::create([
                    'Bank_Code' => $this->bank_code,
                    'rec_usercreated' => Auth::user()->name ?? 'system',
                    'rec_userupdate' => '',
                    'rec_datecreated' => now(),
                    'rec_dateupdate' => '1900-01-01',
                    'rec_status' => '1',
                ]);
                
                session()->flash('message', 'Bank berhasil ditambahkan.');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function toggleStatus($code)
    {
        try {
            $bank = Bank::where('Bank_Code', $code)->first();
            if ($bank) {
                $bank->rec_status = $bank->rec_status == '1' ? '0' : '1';
                $bank->rec_userupdate = Auth::user()->name ?? 'system';
                $bank->rec_dateupdate = now();
                $bank->save();
                
                session()->flash('message', 'Status bank berhasil diubah.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->bank_code = '';
    }

    public function render()
    {
        $banks = Bank::query()
            ->when($this->search, function($query) {
                $query->search($this->search);
            })
            ->orderBy('Bank_Code', 'asc')
            ->paginate($this->perPage);

        return view('livewire.bank-management', [
            'banks' => $banks
        ])->layout('layouts.bootstrap');
    }
}
