<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h2 class="h3">COA Legacy Dashboard</h2>
            <p class="text-muted">Sistem 4 Level: Main → Sub1 → Sub2 → COA Detail</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('coa.modern') }}" class="btn btn-primary">
                <i class="fas fa-arrow-right me-2"></i>Switch to Modern
            </a>
            <button class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Add New
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="coa-tab" data-bs-toggle="tab" data-bs-target="#coa-content" type="button" role="tab">
                <i class="fas fa-list me-2"></i>Chart of Accounts
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="closing-tab" data-bs-toggle="tab" data-bs-target="#closing-content" type="button" role="tab">
                <i class="fas fa-lock me-2"></i>Closing & Audit
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- COA Content Tab -->
        <div class="tab-pane fade show active" id="coa-content" role="tabpanel">
            <!-- Statistics Cards -->
            <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-layer-group me-2"></i>Main Categories (L1)
                </div>
                <div class="card-body">
                    <h3 class="card-title text-primary">{{ $mains->count() }}</h3>
                    <p class="card-text text-muted small">Asset, Liability, Equity, dll</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-sitemap me-2"></i>Sub1 Categories (L2)
                </div>
                <div class="card-body">
                    <h3 class="card-title text-success">{{ $sub1s->count() }}</h3>
                    <p class="card-text text-muted small">Current Assets, Fixed Assets, dll</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-folder-tree me-2"></i>Sub2 Categories (L3)
                </div>
                <div class="card-body">
                    <h3 class="card-title text-warning">{{ $coaSub2s->total() }}</h3>
                    <p class="card-text text-muted small">Cash & Bank, Inventory, dll</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-list-alt me-2"></i>Detail Accounts (L4)
                </div>
                <div class="card-body">
                    <h3 class="card-title text-info">501+</h3>
                    <p class="card-text text-muted small">Actual COA for transactions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <i class="fas fa-filter me-2"></i>Filters & Search
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        class="form-control" 
                        placeholder="Search code, description..."
                    >
                </div>
                <div class="col-md-3">
                    <label class="form-label">Main Category</label>
                    <select wire:model.live="filterMain" class="form-select">
                        <option value="">All Main</option>
                        @foreach($mains as $id => $desc)
                            <option value="{{ $id }}">{{ $desc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sub Category 1</label>
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

    <!-- COA Sub2 Table -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>COA Level 3 (Sub2) - Hierarchy View</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th wire:click="sortBy('coasub2_code')" style="cursor: pointer;">
                                Sub2 Code
                                @if($sortBy === 'coasub2_code')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Hierarchy (3 Levels)</th>
                            <th wire:click="sortBy('coasub2_desc')" style="cursor: pointer;">
                                Description
                                @if($sortBy === 'coasub2_desc')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>COA Count</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coaSub2s as $sub2)
                            <tr>
                                <td>
                                    <div class="fw-bold font-monospace">{{ $sub2->coasub2_code }}</div>
                                    <small class="text-muted">ID: {{ $sub2->coasub2_id ?? '-' }}</small>
                                </td>
                                <td>
                                    <div class="small">
                                        @if($sub2->coaSub1 && $sub2->coaSub1->coaMain)
                                            <div class="mb-1">
                                                <span class="badge bg-primary me-2">L1</span>
                                                <span>{{ $sub2->coaSub1->coaMain->coa_main_desc }}</span>
                                            </div>
                                        @endif
                                        @if($sub2->coaSub1)
                                            <div class="mb-1 ms-3">
                                                <span class="badge bg-success me-2">L2</span>
                                                <span>{{ $sub2->coaSub1->coasub1_desc }}</span>
                                            </div>
                                        @endif
                                        <div class="ms-4">
                                            <span class="badge bg-warning me-2">L3</span>
                                            <span class="fw-semibold">{{ $sub2->coasub2_desc }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $sub2->coasub2_desc }}</div>
                                    <small class="text-muted">Code: {{ $sub2->coasub2_code }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('coa.modern') }}?filter_sub2={{ $sub2->coasub2_code }}" 
                                       class="badge bg-info text-decoration-none"
                                       title="Click to view {{ $sub2->coas->count() }} detail accounts">
                                        <i class="fas fa-link me-1"></i>{{ $sub2->coas->count() }} accounts
                                    </a>
                                </td>
                                <td>
                                    <span class="badge {{ $sub2->rec_status === '1' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $sub2->rec_status === '1' ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('coa.modern') }}?filter_sub2={{ $sub2->coasub2_code }}" 
                                       class="btn btn-sm btn-outline-primary me-1"
                                       title="View Level 4 COA Details">
                                        <i class="fas fa-list"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-warning me-1">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p class="fs-5">No legacy COA Sub2 found</p>
                                        <p class="small">Try adjusting your search or filters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            {{ $coaSub2s->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Info Alert -->
    <div class="alert alert-info">
        <div class="d-flex align-items-start">
            <i class="fas fa-info-circle me-3 mt-1"></i>
            <div class="small">
                <p class="fw-bold mb-2">Legacy System (4 Level Hierarchy)</p>
                <p><strong>Level 1:</strong> <code class="bg-white px-2 py-1 rounded">ms_acc_coa_main</code> (10 records) → Main Categories (Assets, Liabilities, etc.)</p>
                <p class="mt-1"><strong>Level 2:</strong> <code class="bg-white px-2 py-1 rounded">ms_acc_coasub1</code> (18 records) → Sub Category 1 (Current Assets, Fixed Assets, etc.)</p>
                <p class="mt-1"><strong>Level 3:</strong> <code class="bg-white px-2 py-1 rounded">ms_acc_coasub2</code> (58 records) → Sub Category 2 (Cash & Bank, Inventory, etc.) <strong>← You are here</strong></p>
                <p class="mt-1"><strong>Level 4:</strong> <code class="bg-white px-2 py-1 rounded">ms_acc_coa</code> (501+ records) → Detail COA Accounts (actual accounts used in transactions)</p>
                <p class="mt-2">Each Sub2 (Level 3) connects to multiple COA accounts (Level 4) via <code class="bg-white px-2 py-1 rounded">coa_coasub2code → coasub2_code</code></p>
                <p class="mt-2">For the new flexible system (H1-H6 in same table), use <a href="{{ route('coa.modern') }}" class="alert-link fw-bold">COA Modern</a></p>
            </div>
        </div>
    </div>
        </div>
        <!-- End COA Content Tab -->

        <!-- Closing & Audit Tab -->
        <div class="tab-pane fade" id="closing-content" role="tabpanel">
            <div class="row">
                <!-- Balance Sheet Card -->
                <div class="col-md-6 mb-4">
                    <div class="card border-success h-100">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-balance-scale me-2"></i>Balance Sheet Report
                        </div>
                        <div class="card-body">
                            <p class="card-text">Laporan neraca berdasarkan data closing bulanan dengan versioning system.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Monthly closing data</li>
                                <li><i class="fas fa-check text-success me-2"></i>Active version tracking</li>
                                <li><i class="fas fa-check text-success me-2"></i>Saldo awal & akhir</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-light">
                            <a href="{{ route('closing.balance-sheet') }}" class="btn btn-success w-100">
                                <i class="fas fa-arrow-right me-2"></i>Open Balance Sheet
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Closing Process Card -->
                <div class="col-md-6 mb-4">
                    <div class="card border-primary h-100">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-tasks me-2"></i>Closing Process
                        </div>
                        <div class="card-body">
                            <p class="card-text">Proses closing 4 layer: Monthly → Yearly → Audit → Total Archive.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-primary me-2"></i>Multi-layer workflow</li>
                                <li><i class="fas fa-check text-primary me-2"></i>Status tracking</li>
                                <li><i class="fas fa-check text-primary me-2"></i>Audit trail</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-light">
                            <a href="{{ route('closing.process') }}" class="btn btn-primary w-100">
                                <i class="fas fa-arrow-right me-2"></i>Start Closing
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Version History Card -->
                <div class="col-md-6 mb-4">
                    <div class="card border-info h-100">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-history me-2"></i>Version History
                        </div>
                        <div class="card-body">
                            <p class="card-text">Riwayat perubahan versi closing dengan detail waktu dan user.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-info me-2"></i>Version timeline</li>
                                <li><i class="fas fa-check text-info me-2"></i>Change tracking</li>
                                <li><i class="fas fa-check text-info me-2"></i>User audit</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-light">
                            <a href="{{ route('closing.version-history') }}" class="btn btn-info w-100">
                                <i class="fas fa-arrow-right me-2"></i>View History
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Version Comparison Card -->
                <div class="col-md-6 mb-4">
                    <div class="card border-warning h-100">
                        <div class="card-header bg-warning text-dark">
                            <i class="fas fa-code-compare me-2"></i>Version Comparison
                        </div>
                        <div class="card-body">
                            <p class="card-text">Bandingkan 2 versi closing untuk melihat perbedaan data.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-warning me-2"></i>Side-by-side comparison</li>
                                <li><i class="fas fa-check text-warning me-2"></i>Difference highlights</li>
                                <li><i class="fas fa-check text-warning me-2"></i>Change summary</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-light">
                            <a href="{{ route('closing.comparison') }}" class="btn btn-warning w-100">
                                <i class="fas fa-arrow-right me-2"></i>Compare Versions
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Rollback Interface Card -->
                <div class="col-md-12 mb-4">
                    <div class="card border-danger h-100">
                        <div class="card-header bg-danger text-white">
                            <i class="fas fa-undo me-2"></i>Rollback Interface
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <p class="card-text">Kembalikan data closing ke versi sebelumnya jika terjadi kesalahan.</p>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-danger me-2"></i>Safe rollback mechanism</li>
                                        <li><i class="fas fa-check text-danger me-2"></i>Backup before rollback</li>
                                        <li><i class="fas fa-check text-danger me-2"></i>Confirmation required</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Warning:</strong> Rollback will modify active data. Use with caution!
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <a href="{{ route('closing.rollback') }}" class="btn btn-danger">
                                <i class="fas fa-arrow-right me-2"></i>Rollback Manager
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Closing System Info -->
            <div class="alert alert-primary">
                <div class="d-flex align-items-start">
                    <i class="fas fa-info-circle me-3 mt-1"></i>
                    <div class="small">
                        <p class="fw-bold mb-2">Multi-Layer Closing System</p>
                        <p><strong>Layer 1:</strong> <code class="bg-white px-2 py-1 rounded">tr_acc_monthly_closing</code> → Monthly closing dengan versioning</p>
                        <p class="mt-1"><strong>Layer 2:</strong> <code class="bg-white px-2 py-1 rounded">tr_acc_yearly_closing</code> → Yearly aggregation dari monthly</p>
                        <p class="mt-1"><strong>Layer 3:</strong> <code class="bg-white px-2 py-1 rounded">tr_acc_yearly_audit</code> → Yearly audit records</p>
                        <p class="mt-1"><strong>Layer 4:</strong> <code class="bg-white px-2 py-1 rounded">tr_acc_total_audit</code> → Final archive & total audit</p>
                        <p class="mt-2">Setiap layer memiliki versioning system dengan status: DRAFT, ACTIVE, ARCHIVED, SUPERSEDED</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Closing & Audit Tab -->
    </div>
    <!-- End Tab Content -->
</div>
