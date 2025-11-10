<div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-database text-primary me-2"></i>
                Stored Procedures Management
            </h2>
            <p class="text-muted mb-0">Manage SQL Server Stored Procedures Configuration</p>
        </div>
        <button wire:click="create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New SP
        </button>
    </div>

    <!-- Alert Message -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total SPs</p>
                            <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <div class="fs-1 text-primary opacity-25">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">With Money Input</p>
                            <h3 class="mb-0">{{ number_format($stats['with_money']) }}</h3>
                        </div>
                        <div class="fs-1 text-success opacity-25">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">With Date Range</p>
                            <h3 class="mb-0">{{ number_format($stats['with_dates']) }}</h3>
                        </div>
                        <div class="fs-1 text-info opacity-25">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" 
                           placeholder="Search by ID, description, SP name, or varchar input...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Per Page</label>
                    <select wire:model.live="perPage" class="form-select">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="$set('search', '')" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-redo me-2"></i>Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SP List Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th wire:click="sortBy('ms_admin_sp_id')" style="cursor: pointer;">
                                SP ID 
                                @if($sortField === 'ms_admin_sp_id')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('sp_desc')" style="cursor: pointer;">
                                Description
                                @if($sortField === 'sp_desc')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('sp_name')" style="cursor: pointer;">
                                SP Name
                                @if($sortField === 'sp_name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Date Range</th>
                            <th class="text-end">Money Input</th>
                            <th>Varchar Input</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($spList as $sp)
                            <tr>
                                <td>
                                    <code class="text-primary">{{ $sp->ms_admin_sp_id }}</code>
                                </td>
                                <td>{{ $sp->sp_desc ?? '-' }}</td>
                                <td>
                                    @if($sp->sp_name)
                                        <span class="badge bg-info">{{ $sp->sp_name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>{{ $sp->date_range }}
                                    </small>
                                </td>
                                <td class="text-end">
                                    @if($sp->money_input)
                                        <strong class="text-success">{{ $sp->formatted_money }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($sp->varchar_input)
                                        <span class="badge bg-secondary">{{ $sp->varchar_input }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button wire:click="execute('{{ $sp->ms_admin_sp_id }}')" 
                                            class="btn btn-sm btn-success me-1"
                                            title="Execute SP">
                                        <i class="fas fa-play"></i>
                                    </button>
                                    <button wire:click="edit('{{ $sp->ms_admin_sp_id }}')" 
                                            class="btn btn-sm btn-outline-primary me-1"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="delete('{{ $sp->ms_admin_sp_id }}')" 
                                            wire:confirm="Are you sure you want to delete this SP?"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fs-1 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No stored procedures found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $spList->links() }}
            </div>
        </div>
    </div>

    <!-- Modal for Create/Edit -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-{{ $editMode ? 'edit' : 'plus' }} me-2"></i>
                            {{ $editMode ? 'Edit' : 'Add New' }} Stored Procedure
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">SP ID <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="ms_admin_sp_id" 
                                           class="form-control @error('ms_admin_sp_id') is-invalid @enderror"
                                           {{ $editMode ? 'readonly' : '' }}>
                                    @error('ms_admin_sp_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">SP Name</label>
                                    <input type="text" wire:model="sp_name" 
                                           class="form-control @error('sp_name') is-invalid @enderror">
                                    @error('sp_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea wire:model="sp_desc" rows="3"
                                              class="form-control @error('sp_desc') is-invalid @enderror"></textarea>
                                    @error('sp_desc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" wire:model="date_start_input" 
                                           class="form-control @error('date_start_input') is-invalid @enderror">
                                    @error('date_start_input') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input type="date" wire:model="date_end_input" 
                                           class="form-control @error('date_end_input') is-invalid @enderror">
                                    @error('date_end_input') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Money Input</label>
                                    <input type="number" step="0.01" wire:model="money_input" 
                                           class="form-control @error('money_input') is-invalid @enderror">
                                    @error('money_input') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Varchar Input</label>
                                    <input type="text" wire:model="varchar_input" 
                                           class="form-control @error('varchar_input') is-invalid @enderror">
                                    @error('varchar_input') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="save">
                            <i class="fas fa-save me-2"></i>Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal for Execute SP Results -->
    @if($showExecuteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-play-circle me-2"></i>
                            Execute: {{ $executingSp->sp_name ?? $executingSp->ms_admin_sp_id }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeExecuteModal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- SP Info -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">SP Information</h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="text-muted" style="width: 150px;">SP Name:</td>
                                                <td><code class="text-success">{{ $executingSp->ms_admin_sp_id }}</code></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Description:</td>
                                                <td>{{ $executingSp->sp_desc ?? '-' }}</td>
                                            </tr>
                                            @if($executingSp->sp_name)
                                            <tr>
                                                <td class="text-muted">Alias:</td>
                                                <td><span class="badge bg-info">{{ $executingSp->sp_name }}</span></td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">Parameters Used</h6>
                                        <table class="table table-sm table-borderless">
                                            @if($executingSp->date_start_input)
                                            <tr>
                                                <td class="text-muted" style="width: 150px;">Start Date:</td>
                                                <td>{{ $executingSp->date_start_input->format('Y-m-d') }}</td>
                                            </tr>
                                            @endif
                                            @if($executingSp->date_end_input)
                                            <tr>
                                                <td class="text-muted">End Date:</td>
                                                <td>{{ $executingSp->date_end_input->format('Y-m-d') }}</td>
                                            </tr>
                                            @endif
                                            @if($executingSp->money_input)
                                            <tr>
                                                <td class="text-muted">Money Input:</td>
                                                <td>{{ $executingSp->formatted_money }}</td>
                                            </tr>
                                            @endif
                                            @if($executingSp->varchar_input)
                                            <tr>
                                                <td class="text-muted">Varchar Input:</td>
                                                <td><span class="badge bg-secondary">{{ $executingSp->varchar_input }}</span></td>
                                            </tr>
                                            @endif
                                            @if(!$executingSp->date_start_input && !$executingSp->date_end_input && !$executingSp->money_input && !$executingSp->varchar_input)
                                            <tr>
                                                <td colspan="2" class="text-muted"><em>No parameters configured</em></td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="badge bg-info">
                                                <i class="fas fa-clock me-1"></i>Execution Time: {{ $executionTime }}ms
                                            </span>
                                            <span class="badge bg-primary ms-2">
                                                <i class="fas fa-database me-1"></i>Rows: {{ count($executeResults) }}
                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <label class="form-label mb-0 me-2 small">Max Rows:</label>
                                            <select wire:model="maxResultRows" class="form-select form-select-sm d-inline-block w-auto">
                                                <option value="100">100</option>
                                                <option value="500">500</option>
                                                <option value="1000">1,000</option>
                                                <option value="5000">5,000</option>
                                                <option value="10000">10,000</option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" wire:click="execute('{{ $executingSp->ms_admin_sp_id }}')" 
                                                    class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-sync me-1"></i>Re-execute
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table Information -->
                        @if(!empty($spTableInfo))
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-table me-2"></i>Tables Used in SP
                                    <span class="badge bg-secondary ms-2">{{ count($spTableInfo) }} tables</span>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="accordion" id="tableInfoAccordion">
                                    @foreach($spTableInfo as $index => $table)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading{{ $index }}">
                                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" 
                                                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" 
                                                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                                                        aria-controls="collapse{{ $index }}">
                                                    <i class="fas fa-database me-2 text-primary"></i>
                                                    <strong>{{ $table['name'] }}</strong>
                                                    <span class="badge bg-info ms-2">{{ count($table['primary_keys']) }} PK</span>
                                                    <span class="badge bg-warning ms-1">{{ count($table['foreign_keys']) }} FK</span>
                                                </button>
                                            </h2>
                                            <div id="collapse{{ $index }}" 
                                                 class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                                 aria-labelledby="heading{{ $index }}" 
                                                 data-bs-parent="#tableInfoAccordion">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <!-- Primary Keys -->
                                                        <div class="col-md-6">
                                                            <h6 class="text-muted mb-2">
                                                                <i class="fas fa-key text-info me-1"></i>Primary Keys
                                                            </h6>
                                                            @if(!empty($table['primary_keys']))
                                                                <ul class="list-unstyled mb-0">
                                                                    @foreach($table['primary_keys'] as $pk)
                                                                        <li class="mb-1">
                                                                            <code class="text-info">{{ $pk }}</code>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <p class="text-muted mb-0"><em>No primary keys</em></p>
                                                            @endif
                                                        </div>
                                                        
                                                        <!-- Foreign Keys -->
                                                        <div class="col-md-6">
                                                            <h6 class="text-muted mb-2">
                                                                <i class="fas fa-link text-warning me-1"></i>Foreign Keys
                                                            </h6>
                                                            @if(!empty($table['foreign_keys']))
                                                                <ul class="list-unstyled mb-0">
                                                                    @foreach($table['foreign_keys'] as $fk)
                                                                        <li class="mb-2">
                                                                            <code class="text-warning">{{ $fk['column'] }}</code>
                                                                            <i class="fas fa-arrow-right mx-1 text-muted small"></i>
                                                                            <code class="text-success">{{ $fk['references'] }}</code>
                                                                            <br>
                                                                            <small class="text-muted">{{ $fk['fk_name'] }}</small>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <p class="text-muted mb-0"><em>No foreign keys</em></p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Error Display -->
                        @if($executeError)
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Notice:</strong> {{ $executeError }}
                            </div>
                        @endif

                        <!-- Results Display -->
                        @if(!empty($executeResults))
                            <div class="alert alert-info">
                                <i class="fas fa-check-circle me-2"></i>
                                Displaying <strong>{{ number_format(count($executeResults)) }}</strong> rows
                                @if($executeError && strpos($executeError, 'limited') !== false)
                                    <span class="badge bg-warning text-dark ms-2">Partial Results</span>
                                @endif
                            </div>
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-table me-2"></i>Query Results
                                        <span class="badge bg-success ms-2">{{ count($executeResults) }} rows</span>
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                        <table class="table table-sm table-striped table-hover mb-0">
                                            <thead class="table-dark sticky-top">
                                                <tr>
                                                    <th style="width: 50px;">#</th>
                                                    @if(count($executeResults) > 0)
                                                        @foreach(array_keys((array)$executeResults[0]) as $column)
                                                            <th>{{ $column }}</th>
                                                        @endforeach
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($executeResults as $index => $row)
                                                    <tr>
                                                        <td class="text-muted">{{ $index + 1 }}</td>
                                                        @foreach((array)$row as $value)
                                                            <td>
                                                                @if(is_null($value))
                                                                    <span class="text-muted fst-italic">NULL</span>
                                                                @elseif(is_bool($value))
                                                                    <span class="badge bg-{{ $value ? 'success' : 'danger' }}">
                                                                        {{ $value ? 'TRUE' : 'FALSE' }}
                                                                    </span>
                                                                @elseif(is_numeric($value))
                                                                    <span class="text-end d-block">{{ number_format($value, 2) }}</span>
                                                                @else
                                                                    {{ $value }}
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeExecuteModal">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
