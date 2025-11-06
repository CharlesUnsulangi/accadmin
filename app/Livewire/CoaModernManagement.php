<?php

namespace App\Livewire;

use App\Models\Coa;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * COA Modern Management - H1-H6 Flexible Hierarchy
 * 
 * Sistem hierarki fleksibel 1-6 level dalam 1 tabel ms_acc_coa
 * Setiap level memiliki: ms_coa_hX_id, id_hX, desc_hX
 * User bisa create COA di level mana saja (H1-H6)
 */
class CoaModernManagement extends Component
{
    use WithPagination, AuthorizesRequests;

    // Search & Filter
    public $search = '';
    public $perPage = 15;

    // Form fields - Required
    public $coa_code = '';
    public $coa_desc = '';
    public $coa_id = '';
    
    // Form fields - Hierarchy H1-H6
    public $ms_coa_h1_id = '';
    public $desc_h1 = '';
    public $id_h1 = null;
    
    public $ms_coa_h2_id = '';
    public $desc_h2 = '';
    public $id_h2 = null;
    
    public $ms_coa_h3_id = '';
    public $desc_h3 = '';
    public $id_h3 = null;
    
    public $ms_coa_h4_id = '';
    public $desc_h4 = '';
    public $id_h4 = null;
    
    public $ms_coa_h5_id = '';
    public $desc_h5 = '';
    public $id_h5 = null;
    
    public $ms_coa_h6_id = '';
    public $desc_h6 = '';
    public $id_h6 = null;
    
    // Form fields - Optional
    public $coa_note = '';
    public $arus_kas_code = '';
    public $ms_acc_coa_h = '';
    
    // Legacy fields (Old system reference)
    public $coa_coasub2code = '';
    public $id_old_sub_2 = '';
    public $id_old_sub1 = '';
    public $id_old_main = '';
    public $sub2_desc = '';
    public $sub1_desc = '';
    public $main_desc = '';
    
    // Modal states
    public $showModal = false;
    public $editMode = false;
    public $editingCode = null;

    /**
     * Validation rules
     */
    protected function rules()
    {
        return [
            'coa_code' => [
                'required',
                'string',
                'max:50',
                $this->editMode 
                    ? 'unique:ms_acc_coa,coa_code,' . $this->editingCode . ',coa_code'
                    : 'unique:ms_acc_coa,coa_code'
            ],
            'coa_desc' => 'required|string|max:255',
            'coa_id' => 'required|string|max:50',
            
            // H1 required (minimum 1 level)
            'ms_coa_h1_id' => 'required|string|max:50',
            'desc_h1' => 'required|string|max:255',
            'id_h1' => 'nullable|integer',
            
            // H2-H6 optional
            'ms_coa_h2_id' => 'nullable|string|max:50',
            'desc_h2' => 'nullable|string|max:255',
            'id_h2' => 'nullable|integer',
            
            'ms_coa_h3_id' => 'nullable|string|max:50',
            'desc_h3' => 'nullable|string|max:255',
            'id_h3' => 'nullable|integer',
            
            'ms_coa_h4_id' => 'nullable|string|max:50',
            'desc_h4' => 'nullable|string|max:255',
            'id_h4' => 'nullable|integer',
            
            'ms_coa_h5_id' => 'nullable|string|max:50',
            'desc_h5' => 'nullable|string|max:255',
            'id_h5' => 'nullable|integer',
            
            'ms_coa_h6_id' => 'nullable|string|max:50',
            'desc_h6' => 'nullable|string|max:255',
            'id_h6' => 'nullable|integer',
            
            'coa_note' => 'nullable|string|max:255',
            'arus_kas_code' => 'nullable|string|max:50',
            'ms_acc_coa_h' => 'nullable|string|max:50',
            
            // Legacy fields
            'coa_coasub2code' => 'nullable|string|max:50',
            'id_old_sub_2' => 'nullable|string|max:50',
            'id_old_sub1' => 'nullable|string|max:50',
            'id_old_main' => 'nullable|string|max:50',
            'sub2_desc' => 'nullable|string|max:50',
            'sub1_desc' => 'nullable|string|max:50',
            'main_desc' => 'nullable|string|max:50',
        ];
    }

    protected $messages = [
        'coa_code.required' => 'COA Code wajib diisi',
        'coa_code.unique' => 'COA Code sudah digunakan',
        'coa_desc.required' => 'Description wajib diisi',
        'coa_id.required' => 'COA ID wajib diisi',
        'ms_coa_h1_id.required' => 'H1 ID wajib diisi (minimum 1 level hierarchy)',
        'desc_h1.required' => 'H1 Description wajib diisi',
    ];

    /**
     * Reset pagination when search changes
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Open create modal
     */
    public function create()
    {
        $this->authorize('create', Coa::class);
        
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    /**
     * Store new COA
     */
    public function store()
    {
        $this->authorize('create', Coa::class);
        
        $validated = $this->validate();
        
        Coa::create([
            'coa_code' => $validated['coa_code'],
            'coa_desc' => $validated['coa_desc'],
            'coa_id' => $validated['coa_id'],
            'ms_coa_h1_id' => $validated['ms_coa_h1_id'],
            'desc_h1' => $validated['desc_h1'],
            'id_h1' => $validated['id_h1'],
            'ms_coa_h2_id' => $validated['ms_coa_h2_id'],
            'desc_h2' => $validated['desc_h2'],
            'id_h2' => $validated['id_h2'],
            'ms_coa_h3_id' => $validated['ms_coa_h3_id'],
            'desc_h3' => $validated['desc_h3'],
            'id_h3' => $validated['id_h3'],
            'ms_coa_h4_id' => $validated['ms_coa_h4_id'],
            'desc_h4' => $validated['desc_h4'],
            'id_h4' => $validated['id_h4'],
            'ms_coa_h5_id' => $validated['ms_coa_h5_id'],
            'desc_h5' => $validated['desc_h5'],
            'id_h5' => $validated['id_h5'],
            'ms_coa_h6_id' => $validated['ms_coa_h6_id'],
            'desc_h6' => $validated['desc_h6'],
            'id_h6' => $validated['id_h6'],
            'coa_note' => $validated['coa_note'],
            'arus_kas_code' => $validated['arus_kas_code'],
            'ms_acc_coa_h' => $validated['ms_acc_coa_h'],
            'coa_coasub2code' => $validated['coa_coasub2code'],
            'id_old_sub_2' => $validated['id_old_sub_2'],
            'id_old_sub1' => $validated['id_old_sub1'],
            'id_old_main' => $validated['id_old_main'],
            'sub2_desc' => $validated['sub2_desc'],
            'sub1_desc' => $validated['sub1_desc'],
            'main_desc' => $validated['main_desc'],
            'rec_status' => '1', // Active by default
            'rec_usercreated' => auth()->user()->name ?? 'system',
            'rec_userupdate' => auth()->user()->name ?? 'system',
            'rec_datecreated' => now(),
            'rec_dateupdate' => now(),
        ]);

        session()->flash('message', 'COA berhasil ditambahkan!');
        
        $this->resetForm();
        $this->showModal = false;
    }

    /**
     * Edit COA
     */
    public function edit($coa_code)
    {
        $coa = Coa::where('coa_code', $coa_code)->firstOrFail();
        
        $this->authorize('update', $coa);
        
        $this->editMode = true;
        $this->editingCode = $coa_code;
        
        $this->coa_code = $coa->coa_code;
        $this->coa_desc = $coa->coa_desc;
        $this->coa_id = $coa->coa_id;
        $this->ms_coa_h1_id = $coa->ms_coa_h1_id;
        $this->desc_h1 = $coa->desc_h1;
        $this->id_h1 = $coa->id_h1;
        $this->ms_coa_h2_id = $coa->ms_coa_h2_id;
        $this->desc_h2 = $coa->desc_h2;
        $this->id_h2 = $coa->id_h2;
        $this->ms_coa_h3_id = $coa->ms_coa_h3_id;
        $this->desc_h3 = $coa->desc_h3;
        $this->id_h3 = $coa->id_h3;
        $this->ms_coa_h4_id = $coa->ms_coa_h4_id;
        $this->desc_h4 = $coa->desc_h4;
        $this->id_h4 = $coa->id_h4;
        $this->ms_coa_h5_id = $coa->ms_coa_h5_id;
        $this->desc_h5 = $coa->desc_h5;
        $this->id_h5 = $coa->id_h5;
        $this->ms_coa_h6_id = $coa->ms_coa_h6_id;
        $this->desc_h6 = $coa->desc_h6;
        $this->id_h6 = $coa->id_h6;
        $this->coa_note = $coa->coa_note;
        $this->arus_kas_code = $coa->arus_kas_code;
        $this->ms_acc_coa_h = $coa->ms_acc_coa_h;
        $this->coa_coasub2code = $coa->coa_coasub2code;
        $this->id_old_sub_2 = $coa->id_old_sub_2;
        $this->id_old_sub1 = $coa->id_old_sub1;
        $this->id_old_main = $coa->id_old_main;
        $this->sub2_desc = $coa->sub2_desc;
        $this->sub1_desc = $coa->sub1_desc;
        $this->main_desc = $coa->main_desc;
        
        $this->showModal = true;
    }

    /**
     * Update COA
     */
    public function update()
    {
        $coa = Coa::where('coa_code', $this->editingCode)->firstOrFail();
        
        $this->authorize('update', $coa);
        
        $validated = $this->validate();
        
        $coa->update([
            'coa_code' => $validated['coa_code'],
            'coa_desc' => $validated['coa_desc'],
            'coa_id' => $validated['coa_id'],
            'ms_coa_h1_id' => $validated['ms_coa_h1_id'],
            'desc_h1' => $validated['desc_h1'],
            'id_h1' => $validated['id_h1'],
            'ms_coa_h2_id' => $validated['ms_coa_h2_id'],
            'desc_h2' => $validated['desc_h2'],
            'id_h2' => $validated['id_h2'],
            'ms_coa_h3_id' => $validated['ms_coa_h3_id'],
            'desc_h3' => $validated['desc_h3'],
            'id_h3' => $validated['id_h3'],
            'ms_coa_h4_id' => $validated['ms_coa_h4_id'],
            'desc_h4' => $validated['desc_h4'],
            'id_h4' => $validated['id_h4'],
            'ms_coa_h5_id' => $validated['ms_coa_h5_id'],
            'desc_h5' => $validated['desc_h5'],
            'id_h5' => $validated['id_h5'],
            'ms_coa_h6_id' => $validated['ms_coa_h6_id'],
            'desc_h6' => $validated['desc_h6'],
            'id_h6' => $validated['id_h6'],
            'coa_note' => $validated['coa_note'],
            'arus_kas_code' => $validated['arus_kas_code'],
            'ms_acc_coa_h' => $validated['ms_acc_coa_h'],
            'coa_coasub2code' => $validated['coa_coasub2code'],
            'id_old_sub_2' => $validated['id_old_sub_2'],
            'id_old_sub1' => $validated['id_old_sub1'],
            'id_old_main' => $validated['id_old_main'],
            'sub2_desc' => $validated['sub2_desc'],
            'sub1_desc' => $validated['sub1_desc'],
            'main_desc' => $validated['main_desc'],
            'rec_userupdate' => auth()->user()->name ?? 'system',
            'rec_dateupdate' => now(),
        ]);

        session()->flash('message', 'COA berhasil diupdate!');
        
        $this->resetForm();
        $this->showModal = false;
    }

    /**
     * Delete COA
     */
    public function delete($coa_code)
    {
        $coa = Coa::where('coa_code', $coa_code)->firstOrFail();
        
        $this->authorize('delete', $coa);
        
        // Soft delete via rec_status
        $coa->update([
            'rec_status' => '0',
            'rec_userupdate' => auth()->user()->name ?? 'system',
            'rec_dateupdate' => now(),
        ]);

        session()->flash('message', 'COA berhasil dihapus!');
    }

    /**
     * Reset form
     */
    private function resetForm()
    {
        $this->coa_code = '';
        $this->coa_desc = '';
        $this->coa_id = '';
        $this->ms_coa_h1_id = '';
        $this->desc_h1 = '';
        $this->id_h1 = null;
        $this->ms_coa_h2_id = '';
        $this->desc_h2 = '';
        $this->id_h2 = null;
        $this->ms_coa_h3_id = '';
        $this->desc_h3 = '';
        $this->id_h3 = null;
        $this->ms_coa_h4_id = '';
        $this->desc_h4 = '';
        $this->id_h4 = null;
        $this->ms_coa_h5_id = '';
        $this->desc_h5 = '';
        $this->id_h5 = null;
        $this->ms_coa_h6_id = '';
        $this->desc_h6 = '';
        $this->id_h6 = null;
        $this->coa_note = '';
        $this->arus_kas_code = '';
        $this->ms_acc_coa_h = '';
        $this->coa_coasub2code = '';
        $this->id_old_sub_2 = '';
        $this->id_old_sub1 = '';
        $this->id_old_main = '';
        $this->sub2_desc = '';
        $this->sub1_desc = '';
        $this->main_desc = '';
        $this->editingCode = null;
        $this->resetErrorBag();
    }

    /**
     * Close modal
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Render component
     */
    public function render()
    {
        $coas = Coa::query()
            ->where('rec_status', '1')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('coa_code', 'like', '%' . $this->search . '%')
                      ->orWhere('coa_desc', 'like', '%' . $this->search . '%')
                      ->orWhere('desc_h1', 'like', '%' . $this->search . '%')
                      ->orWhere('desc_h2', 'like', '%' . $this->search . '%')
                      ->orWhere('desc_h3', 'like', '%' . $this->search . '%')
                      ->orWhere('desc_h4', 'like', '%' . $this->search . '%')
                      ->orWhere('desc_h5', 'like', '%' . $this->search . '%')
                      ->orWhere('desc_h6', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('coa_code')
            ->paginate($this->perPage);

        return view('livewire.coa-modern-management', [
            'coas' => $coas,
        ])->layout('layouts.admin');
    }
}
