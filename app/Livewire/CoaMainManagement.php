<?php

namespace App\Livewire;

use App\Models\CoaMain;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * COA Main Management - Level 1 Legacy System
 * 
 * Table: ms_acc_coa_main
 * Level 1 dari hierarki legacy (Main Categories)
 */
class CoaMainManagement extends Component
{
    use WithPagination, AuthorizesRequests;

    // Search & Filter
    public $search = '';
    public $perPage = 15;

    // Form fields
    public $coa_main_code = '';
    public $coa_main_id = '';
    public $coa_main_desc = '';
    public $coa_main_coamain2code = '';
    public $cek_aktif = false;
    public $id_h = null;
    
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
            'coa_main_code' => [
                'required',
                'string',
                'max:50',
                $this->editMode 
                    ? 'unique:ms_acc_coa_main,coa_main_code,' . $this->editingCode . ',coa_main_code'
                    : 'unique:ms_acc_coa_main,coa_main_code'
            ],
            'coa_main_id' => 'nullable|string|max:50',
            'coa_main_desc' => 'required|string|max:50',
            'coa_main_coamain2code' => 'nullable|string|max:50',
            'cek_aktif' => 'nullable|boolean',
            'id_h' => 'nullable|integer',
        ];
    }

    protected $messages = [
        'coa_main_code.required' => 'COA Main Code wajib diisi',
        'coa_main_code.unique' => 'COA Main Code sudah digunakan',
        'coa_main_desc.required' => 'Description wajib diisi',
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
        $this->authorize('create', CoaMain::class);
        
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    /**
     * Store new COA Main
     */
    public function store()
    {
        $this->authorize('create', CoaMain::class);
        
        $validated = $this->validate();
        
        CoaMain::create([
            'coa_main_code' => $validated['coa_main_code'],
            'coa_main_id' => $validated['coa_main_id'],
            'coa_main_desc' => $validated['coa_main_desc'],
            'coa_main_coamain2code' => $validated['coa_main_coamain2code'],
            'cek_aktif' => $validated['cek_aktif'] ?? false,
            'id_h' => $validated['id_h'],
            'rec_status' => '1',
            'rec_usercreated' => auth()->user()->name ?? 'system',
            'rec_userupdate' => auth()->user()->name ?? 'system',
            'rec_datecreated' => now(),
            'rec_dateupdate' => now(),
        ]);

        session()->flash('message', 'COA Main berhasil ditambahkan!');
        
        $this->resetForm();
        $this->showModal = false;
    }

    /**
     * Edit COA Main
     */
    public function edit($coa_main_code)
    {
        $coaMain = CoaMain::where('coa_main_code', $coa_main_code)->firstOrFail();
        
        $this->authorize('update', $coaMain);
        
        $this->editMode = true;
        $this->editingCode = $coa_main_code;
        
        $this->coa_main_code = $coaMain->coa_main_code;
        $this->coa_main_id = $coaMain->coa_main_id;
        $this->coa_main_desc = $coaMain->coa_main_desc;
        $this->coa_main_coamain2code = $coaMain->coa_main_coamain2code;
        $this->cek_aktif = $coaMain->cek_aktif ?? false;
        $this->id_h = $coaMain->id_h;
        
        $this->showModal = true;
    }

    /**
     * Update COA Main
     */
    public function update()
    {
        $coaMain = CoaMain::where('coa_main_code', $this->editingCode)->firstOrFail();
        
        $this->authorize('update', $coaMain);
        
        $validated = $this->validate();
        
        $coaMain->update([
            'coa_main_code' => $validated['coa_main_code'],
            'coa_main_id' => $validated['coa_main_id'],
            'coa_main_desc' => $validated['coa_main_desc'],
            'coa_main_coamain2code' => $validated['coa_main_coamain2code'],
            'cek_aktif' => $validated['cek_aktif'] ?? false,
            'id_h' => $validated['id_h'],
            'rec_userupdate' => auth()->user()->name ?? 'system',
            'rec_dateupdate' => now(),
        ]);

        session()->flash('message', 'COA Main berhasil diupdate!');
        
        $this->resetForm();
        $this->showModal = false;
    }

    /**
     * Delete COA Main
     */
    public function delete($coa_main_code)
    {
        $coaMain = CoaMain::where('coa_main_code', $coa_main_code)->firstOrFail();
        
        $this->authorize('delete', $coaMain);
        
        // Check if has children
        if ($coaMain->coaSub1s()->count() > 0) {
            session()->flash('error', 'Tidak bisa hapus! COA Main ini masih memiliki Sub1.');
            return;
        }
        
        // Soft delete
        $coaMain->update([
            'rec_status' => '0',
            'rec_userupdate' => auth()->user()->name ?? 'system',
            'rec_dateupdate' => now(),
        ]);

        session()->flash('message', 'COA Main berhasil dihapus!');
    }

    /**
     * Reset form
     */
    private function resetForm()
    {
        $this->coa_main_code = '';
        $this->coa_main_id = '';
        $this->coa_main_desc = '';
        $this->coa_main_coamain2code = '';
        $this->cek_aktif = false;
        $this->id_h = null;
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
        $coaMains = CoaMain::query()
            ->where('rec_status', '1')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('coa_main_code', 'like', '%' . $this->search . '%')
                      ->orWhere('coa_main_desc', 'like', '%' . $this->search . '%')
                      ->orWhere('coa_main_id', 'like', '%' . $this->search . '%');
                });
            })
            ->withCount('coaSub1s')
            ->orderBy('coa_main_code')
            ->paginate($this->perPage);

        return view('livewire.coa-main-management', [
            'coaMains' => $coaMains,
        ])->layout('layouts.admin');
    }
}
