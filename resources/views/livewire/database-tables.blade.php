<div class="container-fluid py-4">
    <!-- Error Alert -->
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 text-white-50">Total Tables</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($stats['total_tables']) }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-table"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 text-white-50">Total Records</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($stats['total_records']) }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 text-white-50">Last Updated</h6>
                            <h2 class="mb-0 fw-bold">
                                @if($stats['latest_update'])
                                    {{ \Carbon\Carbon::parse($stats['latest_update'])->format('d M Y') }}
                                @else
                                    N/A
                                @endif
                            </h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Database Tables</h5>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button wire:click="updateAllMetadata" 
                                class="btn btn-success btn-sm" 
                                wire:loading.attr="disabled"
                                wire:target="updateAllMetadata">
                            <span wire:loading.remove wire:target="updateAllMetadata">
                                <i class="fas fa-sync-alt me-1"></i>Update All Metadata
                            </span>
                            <span wire:loading wire:target="updateAllMetadata">
                                <i class="fas fa-spinner fa-spin me-1"></i>Updating...
                            </span>
                        </button>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i> Options
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-file-export me-2"></i>Export to CSV</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Export to Excel</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-info-circle me-2"></i>View Documentation</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" 
                               class="form-control border-start-0 bg-light" 
                               placeholder="Search table name, schema, or description..."
                               wire:model.live.debounce.300ms="search">
                        @if($search)
                        <button class="btn btn-outline-secondary" wire:click="$set('search', '')">
                            <i class="fas fa-times"></i>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">
                                <a href="#" wire:click.prevent="sortByColumn('table_name')" class="text-decoration-none text-dark">
                                    Table Name
                                    @if($sortBy === 'table_name')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="border-0">Schema Description</th>
                            <th class="border-0 text-end">
                                <a href="#" wire:click.prevent="sortByColumn('record')" class="text-decoration-none text-dark">
                                    Records
                                    @if($sortBy === 'record')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="border-0">
                                <a href="#" wire:click.prevent="sortByColumn('record_date_start')" class="text-decoration-none text-dark">
                                    First Record
                                    @if($sortBy === 'record_date_start')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="border-0">
                                <a href="#" wire:click.prevent="sortByColumn('record_date_last')" class="text-decoration-none text-dark">
                                    Last Record
                                    @if($sortBy === 'record_date_last')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="border-0">Updated</th>
                            <th class="border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tables as $table)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-2">
                                        <i class="fas fa-table"></i>
                                    </div>
                                    <div>
                                        <strong class="text-primary">{{ $table->table_name }}</strong>
                                        <br>
                                        @if($table->tr_aplikasi_table_id)
                                            <small class="text-muted">ID: {{ $table->tr_aplikasi_table_id }}</small>
                                        @else
                                            <small class="text-muted fst-italic">System Table</small>
                                        @endif
                                        @if($table->table_note)
                                            <br>
                                            <span class="badge bg-info bg-opacity-10 text-info mt-1">
                                                <i class="fas fa-sticky-note me-1"></i>{{ Str::limit($table->table_note, 40) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($table->note_schema)
                                    <span class="text-muted" style="font-size: 0.9rem;">{{ Str::limit($table->note_schema, 80) }}</span>
                                @else
                                    <span class="text-muted fst-italic">No description</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($table->record)
                                    <span class="badge bg-info">{{ number_format($table->record) }}</span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($table->record_date_start)
                                    <small class="text-muted">
                                        <i class="far fa-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($table->record_date_start)->format('d M Y') }}
                                    </small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td>
                                @if($table->record_date_last)
                                    <small class="text-muted">
                                        <i class="far fa-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($table->record_date_last)->format('d M Y') }}
                                    </small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td>
                                @if($table->date_updated)
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($table->date_updated)->diffForHumans() }}
                                    </small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($table->table_name && $table->tr_aplikasi_table_id)
                                    <button wire:click="updateTableMetadata('{{ $table->table_name }}')" 
                                            class="btn btn-sm btn-outline-primary"
                                            wire:loading.attr="disabled"
                                            wire:target="updateTableMetadata('{{ $table->table_name }}')">
                                        <span wire:loading.remove wire:target="updateTableMetadata('{{ $table->table_name }}')">
                                            <i class="fas fa-sync-alt"></i>
                                        </span>
                                        <span wire:loading wire:target="updateTableMetadata('{{ $table->table_name }}')">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </span>
                                    </button>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fs-1 mb-3 d-block opacity-50"></i>
                                    <p class="mb-0">No tables found</p>
                                    @if($search)
                                        <small>Try adjusting your search criteria</small>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($tables->hasPages())
        <div class="card-footer bg-light border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <select wire:model.live="perPage" class="form-select form-select-sm">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
                <div>
                    {{ $tables->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
