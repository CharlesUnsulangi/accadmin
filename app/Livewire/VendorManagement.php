<?php

namespace App\Livewire;

use App\Models\Vendor;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class VendorManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    
    // Modal
    public $showModal = false;
    public $editMode = false;
    
    // Form fields
    public $ven_code = '';
    public $ven_name = '';
    public $ven_pic = '';
    public $ven_addrase = '';
    public $ven_phone = '';
    public $ven_email = '';
    
    protected $queryString = ['search'];

    protected $rules = [
        'ven_code' => 'required|max:50',
        'ven_name' => 'nullable|max:100',
        'ven_pic' => 'nullable|max:100',
        'ven_addrase' => 'nullable|max:500',
        'ven_phone' => 'nullable|max:50',
        'ven_email' => 'nullable|max:500',
    ];

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
        $vendor = Vendor::where('ven_code', $code)->first();
        
        if ($vendor) {
            $this->ven_code = $vendor->ven_code;
            $this->ven_name = $vendor->ven_name;
            $this->ven_pic = $vendor->ven_pic;
            $this->ven_addrase = $vendor->ven_addrase;
            $this->ven_phone = $vendor->ven_phone;
            $this->ven_email = $vendor->ven_email;
            $this->editMode = true;
            $this->showModal = true;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->editMode) {
                $vendor = Vendor::where('ven_code', $this->ven_code)->first();
                $vendor->ven_name = $this->ven_name;
                $vendor->ven_pic = $this->ven_pic;
                $vendor->ven_addrase = $this->ven_addrase;
                $vendor->ven_phone = $this->ven_phone;
                $vendor->ven_email = $this->ven_email;
                $vendor->rec_userupdate = Auth::user()->name ?? 'system';
                $vendor->rec_dateupdate = now();
                $vendor->save();
                
                session()->flash('message', 'Vendor berhasil diupdate.');
            } else {
                // Check if code already exists
                $exists = Vendor::where('ven_code', $this->ven_code)->exists();
                if ($exists) {
                    session()->flash('error', 'Kode Vendor sudah ada.');
                    return;
                }

                Vendor::create([
                    'ven_code' => $this->ven_code,
                    'ven_name' => $this->ven_name,
                    'ven_pic' => $this->ven_pic,
                    'ven_addrase' => $this->ven_addrase,
                    'ven_phone' => $this->ven_phone,
                    'ven_email' => $this->ven_email,
                    'rec_usercreated' => Auth::user()->name ?? 'system',
                    'rec_userupdate' => '',
                    'rec_datecreated' => now(),
                    'rec_dateupdate' => '1900-01-01',
                    'rec_status' => '1',
                ]);
                
                session()->flash('message', 'Vendor berhasil ditambahkan.');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function toggleStatus($code)
    {
        try {
            $vendor = Vendor::where('ven_code', $code)->first();
            if ($vendor) {
                $vendor->rec_status = $vendor->rec_status == '1' ? '0' : '1';
                $vendor->rec_userupdate = Auth::user()->name ?? 'system';
                $vendor->rec_dateupdate = now();
                $vendor->save();
                
                session()->flash('message', 'Status vendor berhasil diubah.');
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
        $this->ven_code = '';
        $this->ven_name = '';
        $this->ven_pic = '';
        $this->ven_addrase = '';
        $this->ven_phone = '';
        $this->ven_email = '';
    }

    public function render()
    {
        $vendors = Vendor::query()
            ->when($this->search, function($query) {
                $query->search($this->search);
            })
            ->orderBy('ven_code', 'asc')
            ->paginate($this->perPage);

        return view('livewire.vendor-management', [
            'vendors' => $vendors
        ])->layout('layouts.bootstrap');
    }
}
