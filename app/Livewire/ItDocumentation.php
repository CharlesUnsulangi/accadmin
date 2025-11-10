<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ItDoc;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItDocumentation extends Component
{
    use WithPagination;

    public $search = '';
    public $filterTopic = '';
    public $filterProject = '';
    public $perPage = 50;

    // Tab control
    public $activeTab = 'documentation'; // documentation or tables

    // Table search
    public $tableSearch = '';
    public $selectedTable = null;
    public $tableColumns = [];

    // Form fields
    public $catatan_text = '';
    public $topik = '';
    public $project = '';
    public $link = '';
    public $editingId = null;
    public $showForm = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterTopic' => ['except' => ''],
        'filterProject' => ['except' => ''],
    ];

    protected $rules = [
        'catatan_text' => 'required',
        'topik' => 'required|max:255',
        'project' => 'nullable|max:255',
        'link' => 'nullable|max:255',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterTopic()
    {
        $this->resetPage();
    }

    public function updatingFilterProject()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterTopic = '';
        $this->filterProject = '';
        $this->resetPage();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetForm();
        }
    }

    public function resetForm()
    {
        $this->reset(['catatan_text', 'topik', 'project', 'link', 'editingId']);
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'catatan_text' => $this->catatan_text,
            'topik' => $this->topik,
            'project' => $this->project,
            'link' => $this->link,
            'created_date' => now(),
            'created_user' => Auth::user()->name ?? 'System',
        ];

        if ($this->editingId) {
            ItDoc::where('tr_admin_it_doc_id', $this->editingId)->update($data);
            session()->flash('message', 'Dokumentasi berhasil diupdate!');
        } else {
            ItDoc::create($data);
            session()->flash('message', 'Dokumentasi berhasil ditambahkan!');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function edit($id)
    {
        $doc = ItDoc::findOrFail($id);
        
        $this->editingId = $doc->tr_admin_it_doc_id;
        $this->catatan_text = $doc->catatan_text;
        $this->topik = $doc->topik;
        $this->project = $doc->project;
        $this->link = $doc->link;
        $this->showForm = true;
    }

    public function delete($id)
    {
        ItDoc::where('tr_admin_it_doc_id', $id)->delete();
        session()->flash('message', 'Dokumentasi berhasil dihapus!');
    }

    public function render()
    {
        $query = ItDoc::query();

        if ($this->search) {
            $query->search($this->search);
        }

        if ($this->filterTopic) {
            $query->where('topik', $this->filterTopic);
        }

        if ($this->filterProject) {
            $query->where('project', $this->filterProject);
        }

        $docs = $query->orderBy('created_date', 'desc')
                     ->paginate($this->perPage);

        // Get unique topics and projects for filters
        $topics = ItDoc::select('topik')
                       ->distinct()
                       ->whereNotNull('topik')
                       ->where('topik', '!=', '')
                       ->orderBy('topik')
                       ->pluck('topik');

        $projects = ItDoc::select('project')
                        ->distinct()
                        ->whereNotNull('project')
                        ->where('project', '!=', '')
                        ->orderBy('project')
                        ->pluck('project');

        return view('livewire.it-documentation-bootstrap', [
            'docs' => $docs,
            'topics' => $topics,
            'projects' => $projects,
        ])->layout('layouts.bootstrap');
    }
}
