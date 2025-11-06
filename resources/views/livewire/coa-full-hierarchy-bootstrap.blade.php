<div>
    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; margin-top: 70px;">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Header Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="card-title mb-1">
                        <i class="fas fa-layer-group me-2 text-primary"></i>COA Full Hierarchy Report
                    </h2>
                    <p class="text-muted mb-0">
                        <small>Complete 4-Level Hierarchy: Main → Sub1 → Sub2 → Detail COA</small>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('coa.modern') }}" class="btn btn-primary btn-sm me-1">
                        <i class="fas fa-layer-group me-1"></i>Modern
                    </a>
                    <a href="{{ route('coa.legacy') }}" class="btn btn-info btn-sm me-1">
                        <i class="fas fa-sitemap me-1"></i>Legacy
                    </a>
                    <button wire:click="export" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel me-1"></i>Export
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
                <div class="col-md-5">
                    <label class="form-label">
                        <i class="fas fa-search me-1"></i>Search
                    </label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        class="form-control" 
                        placeholder="Search in all levels..."
                    >
                </div>

                <!-- Filter Main -->
                <div class="col-md-2">
                    <label class="form-label">Main Category</label>
                    <select wire:model.live="filterMain" class="form-select">
                        <option value="">All Main</option>
                        @foreach($mains as $code => $desc)
                            <option value="{{ $code }}">{{ $desc }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Sub1 -->
                <div class="col-md-2">
                    <label class="form-label">Sub Category 1</label>
                    <select wire:model.live="filterSub1" class="form-select">
                        <option value="">All Sub1</option>
                        @foreach($sub1s as $code => $desc)
                            <option value="{{ $code }}">{{ $desc }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Sub2 -->
                <div class="col-md-2">
                    <label class="form-label">Sub Category 2</label>
                    <select wire:model.live="filterSub2" class="form-select">
                        <option value="">All Sub2</option>
                        @foreach($sub2s as $code => $desc)
                            <option value="{{ $code }}">{{ $desc }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Per Page -->
                <div class="col-md-1">
                    <label class="form-label">Per Page</label>
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Records</h6>
                    <h2 class="card-title mb-0">{{ $hierarchy->total() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Current Page</h6>
                    <h2 class="card-title mb-0">{{ $hierarchy->count() }} of {{ $perPage }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card with Collapsible Sections -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="accordion mb-3" id="hierarchyAccordion">
                <!-- Hierarchy Structure Info -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#hierarchyInfo">
                            <i class="fas fa-info-circle me-2"></i>4-Level Hierarchy Structure
                        </button>
                    </h2>
                    <div id="hierarchyInfo" class="accordion-collapse collapse" data-bs-parent="#hierarchyAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <span class="badge bg-success mb-2">Level 1 - Main</span>
                                    <p class="small text-muted mb-0">ms_acc_coa_main<br>Main Categories</p>
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-info mb-2">Level 2 - Sub1</span>
                                    <p class="small text-muted mb-0">ms_acc_coasub1<br>Sub Categories 1</p>
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-primary mb-2">Level 3 - Sub2</span>
                                    <p class="small text-muted mb-0">ms_acc_coasub2<br>Sub Categories 2</p>
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-warning text-dark mb-2">Level 4 - Detail</span>
                                    <p class="small text-muted mb-0">ms_acc_coa<br>Detail COA Accounts</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap">
                                <span class="badge bg-success">Level 1</span><br>
                                <small>Main</small>
                            </th>
                            <th class="text-nowrap">
                                <span class="badge bg-info">Level 2</span><br>
                                <small>Sub1</small>
                            </th>
                            <th class="text-nowrap">
                                <span class="badge bg-primary">Level 3</span><br>
                                <small>Sub2</small>
                            </th>
                            <th class="text-nowrap">
                                <span class="badge bg-warning text-dark">Level 4</span><br>
                                <small>COA Detail</small>
                            </th>
                            <th class="text-nowrap">
                                <small>H1-H6 Hierarchy</small>
                            </th>
                            <th class="text-nowrap">
                                <small>Additional</small>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hierarchy as $item)
                            <tr>
                                <!-- Level 1 - Main -->
                                <td>
                                    <div>
                                        <span class="badge bg-success font-monospace">{{ $item->coa_main_code }}</span>
                                    </div>
                                    <small class="text-muted">{{ $item->coa_main_desc }}</small>
                                </td>

                                <!-- Level 2 - Sub1 -->
                                <td>
                                    @if($item->coasub1_code)
                                        <div>
                                            <span class="badge bg-info font-monospace">{{ $item->coasub1_code }}</span>
                                        </div>
                                        <small class="text-muted">{{ $item->coasub1_desc }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <!-- Level 3 - Sub2 -->
                                <td>
                                    @if($item->coasub2_code)
                                        <div>
                                            <span class="badge bg-primary font-monospace">{{ $item->coasub2_code }}</span>
                                        </div>
                                        <small class="text-muted">{{ $item->coasub2_desc }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <!-- Level 4 - COA Detail -->
                                <td>
                                    @if($item->coa_code)
                                        <div>
                                            <span class="badge bg-warning text-dark font-monospace">{{ $item->coa_code }}</span>
                                        </div>
                                        <small><strong>{{ $item->coa_desc }}</strong></small>
                                        @if($item->coa_note)
                                            <div><small class="text-muted"><i class="fas fa-sticky-note me-1"></i>{{ $item->coa_note }}</small></div>
                                        @endif
                                        @if($item->arus_kas_code)
                                            <div><span class="badge bg-secondary mt-1">Cash Flow: {{ $item->arus_kas_code }}</span></div>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <!-- H1-H6 Hierarchy -->
                                <td>
                                    <div class="small">
                                        @if($item->desc_h1)
                                            <div><span class="badge bg-danger">H1</span> {{ $item->desc_h1 }}</div>
                                        @endif
                                        @if($item->desc_h2)
                                            <div><span class="badge bg-warning text-dark">H2</span> {{ $item->desc_h2 }}</div>
                                        @endif
                                        @if($item->desc_h3)
                                            <div><span class="badge bg-success">H3</span> {{ $item->desc_h3 }}</div>
                                        @endif
                                        @if($item->desc_h4)
                                            <div><span class="badge bg-info">H4</span> {{ $item->desc_h4 }}</div>
                                        @endif
                                        @if($item->desc_h5)
                                            <div><span class="badge bg-primary">H5</span> {{ $item->desc_h5 }}</div>
                                        @endif
                                        @if($item->desc_h6)
                                            <div><span class="badge bg-secondary">H6</span> {{ $item->desc_h6 }}</div>
                                        @endif
                                        @if(!$item->desc_h1 && !$item->desc_h2 && !$item->desc_h3 && !$item->desc_h4 && !$item->desc_h5 && !$item->desc_h6)
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Additional Info -->
                                <td>
                                    <div class="small">
                                        @if($item->main_desc)
                                            <div><strong>Main:</strong> {{ $item->main_desc }}</div>
                                        @endif
                                        @if($item->sub1_desc)
                                            <div><strong>Sub1:</strong> {{ $item->sub1_desc }}</div>
                                        @endif
                                        @if($item->sub2_desc)
                                            <div><strong>Sub2:</strong> {{ $item->sub2_desc }}</div>
                                        @endif
                                        @if(!$item->main_desc && !$item->sub1_desc && !$item->sub2_desc)
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">No hierarchy data found</p>
                                    <small class="text-muted">Try adjusting your filters or search criteria</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $hierarchy->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-2">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>Report Summary
                    </h6>
                    <ul class="list-unstyled mb-0 small">
                        <li><i class="fas fa-check text-success me-2"></i>Total records: <strong>{{ $hierarchy->total() }}</strong></li>
                        <li><i class="fas fa-check text-success me-2"></i>Showing: <strong>{{ $hierarchy->firstItem() ?? 0 }} - {{ $hierarchy->lastItem() ?? 0 }}</strong></li>
                        <li><i class="fas fa-check text-success me-2"></i>Per page: <strong>{{ $perPage }}</strong></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-2">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>Tips
                    </h6>
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li><i class="fas fa-arrow-right me-2"></i>Use filters to narrow down results</li>
                        <li><i class="fas fa-arrow-right me-2"></i>Search works across all hierarchy levels</li>
                        <li><i class="fas fa-arrow-right me-2"></i>Click Export to download full data</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-dismiss alerts after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 3000);
        });
    </script>
</div>
