<?php

namespace App\Livewire;

use App\Models\Coa;
use App\Models\CoaSub2;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * COA Management Livewire Component
 * Handles CRUD operations for Chart of Accounts
 * Following AI_DEVELOPMENT_GUIDELINES.md
 */
class CoaManagement extends Component
{
    use WithPagination, AuthorizesRequests;

    // Search & Filter
    public $search = '';
    public $filterStatus = '';
    public $filterParent = '';
    public $perPage = 10;

    // Form fields
    public $coa_code = '';
    public $coa_id = '';
    public $coa_coasub2code = '';
    public $coa_desc = '';
    public $coa_note = '';
    public $arus_kas_code = '';

    // Modal states
    public $showModal = false;
    public $editMode = false;
    public $editingId = null;

    // Validation rules
    protected function rules()
    {
        return [
            'coa_code' => 'required|string|max:50|unique:ms_acc_coa,coa_code,' . $this->editingId . ',coa_code',
            'coa_id' => 'required|string|max:50',
            'coa_coasub2code' => 'required|string|max:50|exists:ms_acc_coasub2,coasub2_code',
            'coa_desc' => 'nullable|string|max:50',
            'coa_note' => 'nullable|string|max:50',
            'arus_kas_code' => 'nullable|string|max:50',
        ];
    }

    protected $messages = [
        'coa_code.required' => 'COA Code is required',
        'coa_code.unique' => 'COA Code already exists',
        'coa_id.required' => 'COA ID is required',
        'coa_coasub2code.required' => 'Parent COA Sub2 is required',
        'coa_coasub2code.exists' => 'Selected parent does not exist',
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
     * Open edit modal
     */
    public function edit($coaCode)
    {
        $coa = Coa::findOrFail($coaCode);
        $this->authorize('update', $coa);

        $this->editMode = true;
        $this->editingId = $coa->coa_code;
        $this->coa_code = $coa->coa_code;
        $this->coa_id = $coa->coa_id;
        $this->coa_coasub2code = $coa->coa_coasub2code;
        $this->coa_desc = $coa->coa_desc;
        $this->coa_note = $coa->coa_note;
        $this->arus_kas_code = $coa->arus_kas_code;
        
        $this->showModal = true;
    }

    /**
     * Save (create or update)
     */
    public function save()
    {
        $this->validate();

        try {
            if ($this->editMode) {
                // Update existing
                $coa = Coa::findOrFail($this->editingId);
                $this->authorize('update', $coa);

                $coa->update([
                    'coa_id' => $this->coa_id,
                    'coa_coasub2code' => $this->coa_coasub2code,
                    'coa_desc' => $this->coa_desc,
                    'coa_note' => $this->coa_note,
                    'arus_kas_code' => $this->arus_kas_code,
                ]);

                session()->flash('success', 'COA updated successfully');
            } else {
                // Create new
                $this->authorize('create', Coa::class);

                Coa::create([
                    'coa_code' => $this->coa_code,
                    'coa_id' => $this->coa_id,
                    'coa_coasub2code' => $this->coa_coasub2code,
                    'coa_desc' => $this->coa_desc,
                    'coa_note' => $this->coa_note,
                    'arus_kas_code' => $this->arus_kas_code,
                ]);

                session()->flash('success', 'COA created successfully');
            }

            $this->closeModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Operation failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete COA
     */
    public function delete($coaCode)
    {
        try {
            $coa = Coa::findOrFail($coaCode);
            $this->authorize('delete', $coa);

            $coa->delete();
            
            session()->flash('success', 'COA deleted successfully');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    /**
     * Close modal and reset form
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    /**
     * Reset form fields
     */
    private function resetForm()
    {
        $this->coa_code = '';
        $this->coa_id = '';
        $this->coa_coasub2code = '';
        $this->coa_desc = '';
        $this->coa_note = '';
        $this->arus_kas_code = '';
        $this->editingId = null;
    }

    /**
     * Render component
     */
    public function render()
    {
        $query = Coa::query()->withHierarchy();

        // Apply search
        if ($this->search) {
            $query->search($this->search);
        }

        // Apply status filter
        if ($this->filterStatus) {
            $query->where('rec_status', $this->filterStatus);
        }

        // Apply parent filter
        if ($this->filterParent) {
            $query->byParent($this->filterParent);
        }

        // Get parents for dropdown
        $parents = CoaSub2::active()->get();

        return view('livewire.coa-management', [
            'coas' => $query->paginate($this->perPage),
            'parents' => $parents,
        ]);
    }
}
