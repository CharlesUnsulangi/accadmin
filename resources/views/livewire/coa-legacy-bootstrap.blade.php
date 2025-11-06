<div>
    <!-- Header Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="card-title mb-1">
                        <i class="fas fa-sitemap me-2 text-primary"></i>COA Legacy Management
                    </h2>
                    <p class="text-muted mb-0">
                        <small>4-Level System: Main → Sub1 → Sub2 → COA Detail (ms_acc_coa)</small>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('coa.modern') }}" class="btn btn-primary me-2">
                        <i class="fas fa-arrow-right me-1"></i>Modern View
                    </a>
                    <button class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Add New
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <!-- Search -->
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fas fa-search me-1"></i>Search
                    </label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        class="form-control" 
                        placeholder="Search code, description..."
                    >
                </div>

                <!-- Filter Main -->
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-filter me-1"></i>Main Category
                    </label>
                    <select wire:model.live="filterMain" class="form-select">
                        <option value="">All Main</option>
                        @foreach($mains as $id => $desc)
                            <option value="{{ $id }}">{{ $desc }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Sub1 -->
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-filter me-1"></i>Sub Category 1
                    </label>
                    <select wire:model.live="filterSub1" class="form-select">
                        <option value="">All Sub1</option>
                        @foreach($sub1s as $id => $desc)
                            <option value="{{ $id }}">{{ $desc }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Main Categories</h6>
                    <h2 class="card-title mb-0">{{ $coaMains->total() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">All Main Categories</h6>
                    <h2 class="card-title mb-0">{{ $mains->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">All Sub1 Categories</h6>
                    <h2 class="card-title mb-0">{{ $sub1s->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">This Page</h6>
                    <h2 class="card-title mb-0">{{ $coaMains->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Accordion Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="accordion" id="coaLegacyAccordion">
                @forelse($coaMains as $main)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapseMain{{ $main->coa_main_code }}" aria-expanded="false">
                                <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge bg-secondary font-monospace fs-6">{{ $main->coa_main_code }}</span>
                                        <strong class="fs-5">{{ $main->coa_main_desc }}</strong>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-secondary">
                                            Level 1: Main
                                        </span>
                                        @if($main->coaSub1s && $main->coaSub1s->count() > 0)
                                            <span class="badge bg-light text-dark border">
                                                <i class="fas fa-layer-group me-1"></i>{{ $main->coaSub1s->count() }} Sub1
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapseMain{{ $main->coa_main_code }}" class="accordion-collapse collapse" 
                             data-bs-parent="#coaLegacyAccordion">
                            <div class="accordion-body">
                                <!-- Main Category Info -->
                                <div class="card border-secondary mb-3">
                                    <div class="card-header bg-light border-bottom">
                                        <strong><i class="fas fa-info-circle me-2"></i>Main Category Information</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Code:</strong> <span class="badge bg-secondary">{{ $main->coa_main_code }}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>ID:</strong> {{ $main->coa_main_id ?? '-' }}
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Description:</strong> {{ $main->coa_main_desc }}
                                            </div>
                                            <div class="col-md-2">
                                                @if($main->rec_status == '1')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sub1 Categories (Level 2) -->
                                @if($main->coaSub1s && $main->coaSub1s->count() > 0)
                                    <div class="accordion" id="accordionSub1Main{{ $main->coa_main_code }}">
                                        @foreach($main->coaSub1s as $sub1)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" 
                                                            data-bs-target="#collapseSub1{{ $sub1->coasub1_code }}" aria-expanded="false">
                                                        <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                                            <div class="d-flex align-items-center gap-3">
                                                                <span class="badge bg-secondary font-monospace">{{ $sub1->coasub1_code }}</span>
                                                                <strong>{{ $sub1->coasub1_desc }}</strong>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="badge bg-secondary">Level 2: Sub1</span>
                                                                @if($sub1->coaSub2s && $sub1->coaSub2s->count() > 0)
                                                                    <span class="badge bg-light text-dark border">
                                                                        <i class="fas fa-layer-group me-1"></i>{{ $sub1->coaSub2s->count() }} Sub2
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapseSub1{{ $sub1->coasub1_code }}" class="accordion-collapse collapse" 
                                                     data-bs-parent="#accordionSub1Main{{ $main->coa_main_code }}">
                                                    <div class="accordion-body bg-light">
                                                        <!-- Sub2 Categories (Level 3) -->
                                                        @if($sub1->coaSub2s && $sub1->coaSub2s->count() > 0)
                                                            <div class="accordion" id="accordionSub2Sub1{{ $sub1->coasub1_code }}">
                                                                @foreach($sub1->coaSub2s as $sub2)
                                                                    <div class="accordion-item">
                                                                        <h2 class="accordion-header">
                                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                                                    data-bs-target="#collapseSub2{{ $sub2->coasub2_code }}" aria-expanded="false">
                                                                                <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                                                                    <div class="d-flex align-items-center gap-3">
                                                                                        <span class="badge bg-dark font-monospace">{{ $sub2->coasub2_code }}</span>
                                                                                        <strong>{{ $sub2->coasub2_desc }}</strong>
                                                                                    </div>
                                                                                    <div class="d-flex align-items-center gap-2">
                                                                                        <span class="badge bg-dark">Level 3: Sub2</span>
                                                                                        @if($sub2->coas && $sub2->coas->count() > 0)
                                                                                            <span class="badge bg-light text-dark border">
                                                                                                <i class="fas fa-layer-group me-1"></i>{{ $sub2->coas->count() }} COAs
                                                                                            </span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </button>
                                                                        </h2>
                                                                        <div id="collapseSub2{{ $sub2->coasub2_code }}" class="accordion-collapse collapse" 
                                                                             data-bs-parent="#accordionSub2Sub1{{ $sub1->coasub1_code }}">
                                                                            <div class="accordion-body">
                                                                                <!-- Detail COAs (Level 4) -->
                                                                                @if($sub2->coas && $sub2->coas->count() > 0)
                                                                                    <div class="card">
                                                                                        <div class="card-header bg-light">
                                                                                            <strong><i class="fas fa-layer-group me-2"></i>Detail COAs (Level 4)</strong>
                                                                                        </div>
                                                                                        <div class="card-body p-0">
                                                                                            <div class="list-group list-group-flush">
                                                                                                @foreach($sub2->coas as $coa)
                                                                                                    <div class="list-group-item">
                                                                                                        <div class="d-flex justify-content-between align-items-start">
                                                                                                            <div class="flex-grow-1">
                                                                                                                <div class="mb-2">
                                                                                                                    <span class="badge bg-dark font-monospace">{{ $coa->coa_code }}</span>
                                                                                                                    @if($coa->rec_status == '1')
                                                                                                                        <span class="badge bg-success">Active</span>
                                                                                                                    @else
                                                                                                                        <span class="badge bg-secondary">Inactive</span>
                                                                                                                    @endif
                                                                                                                </div>
                                                                                                                <div><strong>{{ $coa->coa_desc }}</strong></div>
                                                                                                                @if($coa->coa_note)
                                                                                                                    <div class="mt-1 small text-muted">
                                                                                                                        <i class="fas fa-sticky-note me-1"></i>{{ $coa->coa_note }}
                                                                                                                    </div>
                                                                                                                @endif
                                                                                                                @if($coa->arus_kas_code)
                                                                                                                    <div class="mt-1">
                                                                                                                        <span class="badge bg-secondary">Cash Flow: {{ $coa->arus_kas_code }}</span>
                                                                                                                    </div>
                                                                                                                @endif
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                @endforeach
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @else
                                                                                    <div class="alert alert-light border mb-0">
                                                                                        <i class="fas fa-info-circle me-2"></i>No COA details under this Sub2
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="alert alert-light border mb-0">
                                                                <i class="fas fa-info-circle me-2"></i>No Sub2 categories under this Sub1
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-light border mb-0">
                                        <i class="fas fa-info-circle me-2"></i>No Sub1 categories under this Main
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-0">No Main categories found</p>
                        <small class="text-muted">Try adjusting your filters</small>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $coaMains->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Legend Card -->
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h6 class="card-title mb-3">
                <i class="fas fa-info-circle me-2"></i>Legacy System Hierarchy
            </h6>
            <div class="row">
                <div class="col-md-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-success">Level 1</span>
                        <small>Main Category (ms_acc_coa_main)</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-info">Level 2</span>
                        <small>Sub1 Category (ms_acc_coasub1)</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary">Level 3</span>
                        <small>Sub2 Category (ms_acc_coasub2)</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark">Level 4</span>
                        <small>Detail COAs (ms_acc_coa)</small>
                    </div>
                </div>
            </div>
            <div class="mt-3 pt-3 border-top">
                <small class="text-muted">
                    <i class="fas fa-lightbulb me-1"></i>
                    <strong>Tip:</strong> This page displays Level 3 (Sub2) with relationships to all parent levels and child COAs count.
                </small>
            </div>
        </div>
    </div>

    <style>
        .cursor-pointer {
            cursor: pointer;
            user-select: none;
        }
        .cursor-pointer:hover {
            background-color: rgba(0,0,0,0.05);
        }
    </style>
</div>
