<?php

namespace App\Livewire;

use App\Models\Area;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AreaManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    
    // Modal
    public $showModal = false;
    public $editMode = false;
    
    // Form fields
    public $area_code = '';
    public $area_name = '';
    public $area_address = '';
    public $area_contactno = '';
    public $area_fax = '';
    public $area_pic = '';
    public $area_pichp = '';
    public $area_email = '';
    public $area_db = '';
    public $area_dbtype = '';
    
    protected $queryString = ['search'];

    protected $rules = [
        'area_code' => 'required|max:10',
        'area_name' => 'nullable|max:50',
        'area_address' => 'nullable|max:255',
        'area_contactno' => 'nullable|max:50',
        'area_fax' => 'nullable|max:50',
        'area_pic' => 'nullable|max:50',
        'area_pichp' => 'nullable|max:50',
        'area_email' => 'nullable|email|max:25',
        'area_db' => 'nullable|max:50',
        'area_dbtype' => 'nullable|max:1',
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
        $area = Area::where('Are_Code', $code)->first();
        
        if ($area) {
            $this->area_code = $area->Are_Code;
            $this->area_name = $area->Are_Name;
            $this->area_address = $area->Are_Address;
            $this->area_contactno = $area->Are_ContactNo;
            $this->area_fax = $area->Are_Fax;
            $this->area_pic = $area->Are_PIC;
            $this->area_pichp = $area->Are_PIChp;
            $this->area_email = $area->Are_Email;
            $this->area_db = $area->Are_db;
            $this->area_dbtype = $area->Are_dbtype;
            $this->editMode = true;
            $this->showModal = true;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->editMode) {
                $area = Area::where('Are_Code', $this->area_code)->first();
                $area->Are_Name = $this->area_name;
                $area->Are_Address = $this->area_address;
                $area->Are_ContactNo = $this->area_contactno;
                $area->Are_Fax = $this->area_fax;
                $area->Are_PIC = $this->area_pic;
                $area->Are_PIChp = $this->area_pichp;
                $area->Are_Email = $this->area_email;
                $area->Are_db = $this->area_db;
                $area->Are_dbtype = $this->area_dbtype;
                $area->rec_userupdate = Auth::user()->name ?? 'system';
                $area->rec_dateupdate = now();
                $area->save();
                
                session()->flash('message', 'Area berhasil diupdate.');
            } else {
                // Check if code already exists
                $exists = Area::where('Are_Code', $this->area_code)->exists();
                if ($exists) {
                    session()->flash('error', 'Kode Area sudah ada.');
                    return;
                }

                Area::create([
                    'Are_Code' => $this->area_code,
                    'Are_Name' => $this->area_name,
                    'Are_Address' => $this->area_address,
                    'Are_ContactNo' => $this->area_contactno,
                    'Are_Fax' => $this->area_fax,
                    'Are_PIC' => $this->area_pic,
                    'Are_PIChp' => $this->area_pichp,
                    'Are_Email' => $this->area_email,
                    'Are_db' => $this->area_db,
                    'Are_dbtype' => $this->area_dbtype,
                    'rec_usercreated' => Auth::user()->name ?? 'system',
                    'rec_userupdate' => '',
                    'rec_datecreated' => now(),
                    'rec_dateupdate' => '1900-01-01',
                    'rec_status' => '1',
                ]);
                
                session()->flash('message', 'Area berhasil ditambahkan.');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function toggleStatus($code)
    {
        try {
            $area = Area::where('Are_Code', $code)->first();
            if ($area) {
                $area->rec_status = $area->rec_status == '1' ? '0' : '1';
                $area->rec_userupdate = Auth::user()->name ?? 'system';
                $area->rec_dateupdate = now();
                $area->save();
                
                session()->flash('message', 'Status area berhasil diubah.');
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
        $this->area_code = '';
        $this->area_name = '';
        $this->area_address = '';
        $this->area_contactno = '';
        $this->area_fax = '';
        $this->area_pic = '';
        $this->area_pichp = '';
        $this->area_email = '';
        $this->area_db = '';
        $this->area_dbtype = '';
    }

    public function render()
    {
        $areas = Area::query()
            ->when($this->search, function($query) {
                $query->search($this->search);
            })
            ->orderBy('Are_Code', 'asc')
            ->paginate($this->perPage);

        return view('livewire.area-management', [
            'areas' => $areas
        ])->layout('layouts.bootstrap');
    }
}
