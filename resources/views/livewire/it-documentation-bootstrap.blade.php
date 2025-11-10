<div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-book"></i> Dokumentasi IT
        </h2>
        <button wire:click="toggleForm" class="btn btn-primary">
            <i class="fas fa-plus"></i> {{ $showForm ? 'Tutup Form' : 'Tambah Dokumentasi' }}
        </button>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form -->
    @if ($showForm)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-edit"></i> {{ $editingId ? 'Edit' : 'Tambah' }} Dokumentasi
                </h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Topik <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('topik') is-invalid @enderror" 
                                   wire:model="topik" placeholder="e.g., Database Schema, API Documentation">
                            @error('topik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Project</label>
                            <input type="text" class="form-control @error('project') is-invalid @enderror" 
                                   wire:model="project" placeholder="e.g., AccAdmin, HRD System">
                            @error('project')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Link / Referensi</label>
                        <input type="text" class="form-control @error('link') is-invalid @enderror" 
                               wire:model="link" placeholder="https://...">
                        @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan / Dokumentasi <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('catatan_text') is-invalid @enderror" 
                                  wire:model="catatan_text" rows="10" 
                                  placeholder="Masukkan dokumentasi, schema table, atau catatan teknis..."></textarea>
                        @error('catatan_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Gunakan format SQL, markdown, atau teks biasa</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> {{ $editingId ? 'Update' : 'Simpan' }}
                        </button>
                        <button type="button" wire:click="resetForm" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <button type="button" wire:click="toggleForm" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" 
                           placeholder="🔍 Cari dokumentasi...">
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterTopic">
                        <option value="">Semua Topik</option>
                        @foreach($topics as $topic)
                            <option value="{{ $topic }}">{{ $topic }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterProject">
                        <option value="">Semua Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project }}">{{ $project }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-eraser"></i> Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentation List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list"></i> Daftar Dokumentasi ({{ $docs->total() }})</span>
            <select class="form-select form-select-sm" style="width: auto;" wire:model.live="perPage">
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
            </select>
        </div>
        <div class="card-body">
            @if($docs->count() > 0)
                <div class="accordion" id="documentationAccordion">
                    @foreach($docs as $index => $doc)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#doc{{ $doc->tr_admin_it_doc_id }}">
                                    <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                        <div>
                                            <strong>{{ $doc->topik }}</strong>
                                            @if($doc->project)
                                                <span class="badge bg-info ms-2">{{ $doc->project }}</span>
                                            @endif
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fas fa-calendar"></i> {{ $doc->created_date?->format('d M Y') }}
                                            <i class="fas fa-user ms-2"></i> {{ $doc->created_user }}
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="doc{{ $doc->tr_admin_it_doc_id }}" 
                                 class="accordion-collapse collapse" 
                                 data-bs-parent="#documentationAccordion">
                                <div class="accordion-body">
                                    @if($doc->link)
                                        <div class="mb-3">
                                            <strong>Link:</strong> 
                                            <a href="{{ $doc->link }}" target="_blank" class="text-primary">
                                                {{ $doc->link }} <i class="fas fa-external-link-alt fa-xs"></i>
                                            </a>
                                        </div>
                                    @endif
                                    
                                    <div class="mb-3">
                                        <strong>Dokumentasi:</strong>
                                        <pre class="bg-light p-3 rounded mt-2" style="white-space: pre-wrap; word-wrap: break-word;">{{ $doc->catatan_text }}</pre>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button wire:click="edit({{ $doc->tr_admin_it_doc_id }})" 
                                                class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button wire:click="delete({{ $doc->tr_admin_it_doc_id }})" 
                                                onclick="return confirm('Hapus dokumentasi ini?')"
                                                class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $docs->links() }}
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>Belum ada dokumentasi</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        .accordion-button:not(.collapsed) {
            background-color: #e7f1ff;
            color: #0c63e4;
        }
        
        pre {
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            max-height: 500px;
            overflow-y: auto;
        }
        
        .gap-2 {
            gap: 0.5rem;
        }
        
        .gap-3 {
            gap: 1rem;
        }
    </style>
</div>
