<div class="container-fluid mt-4">
    <!-- Header Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="card-title mb-1">
                        <i class="fas fa-university me-2 text-success"></i>Master Bank
                    </h2>
                    <p class="text-muted mb-0">
                        <small>Manage bank master data</small>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <button wire:click="export" class="btn btn-success me-2">
                        <i class="fas fa-file-excel me-1"></i>Export Excel
                    </button>
                    <button wire:click="create" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add Bank
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
    <div class="alert alert-{{ session('type', 'success') }} alert-dismissible fade show" role="alert">
        <i class="fas fa-{{ session('type') == 'success' ? 'check-circle' : 'exclamation-circle' }} me-2"></i>
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Active Banks</p>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-university fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Inactive Banks</p>
                            <h3 class="mb-0">{{ $stats['inactive'] }}</h3>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-ban fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-primary shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Records</p>
                            <h3 class="mb-0">{{ $stats['all'] }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-database fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <!-- Search -->
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-search me-1"></i>Search
                    </label>
                    <input type="text" wire:model.live="search" class="form-control" 
                           placeholder="Search bank code, name, or description...">
                </div>

                <!-- Status Filter -->
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-filter me-1"></i>Status
                    </label>
                    <select wire:model.live="filterStatus" class="form-select">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-list-ol me-1"></i>Per Page
                    </label>
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="col-md-3">
                    <label class="form-label d-block">&nbsp;</label>
                    <button wire:click="$set('search', '')" class="btn btn-secondary">
                        <i class="fas fa-undo me-1"></i>Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>
                                <a href="#" wire:click.prevent="sortByColumn('bank_code')" class="text-decoration-none text-dark">
                                    Bank Code
                                    <i class="fas {{ $this->getSortIcon('bank_code') }}"></i>
                                </a>
                            </th>
                            <th>
                                <a href="#" wire:click.prevent="sortByColumn('bank_name')" class="text-decoration-none text-dark">
                                    Bank Name
                                    <i class="fas {{ $this->getSortIcon('bank_name') }}"></i>
                                </a>
                            </th>
                            <th>Description</th>
                            <th class="text-center">Status</th>
                            <th>Created By</th>
                            <th>Updated By</th>
                            <th class="text-center" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banks as $index => $bank)
                        <tr>
                            <td>{{ $banks->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $bank->bank_code }}</strong>
                            </td>
                            <td>{{ $bank->bank_name ?? '-' }}</td>
                            <td>
                                <small class="text-muted">{{ $bank->bank_desc ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                @if($bank->rec_status == '1')
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $bank->rec_usercreated ?? '-' }}<br>
                                    @if($bank->rec_datecreated)
                                    <small>{{ \Carbon\Carbon::parse($bank->rec_datecreated)->format('Y-m-d H:i') }}</small>
                                    @endif
                                </small>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $bank->rec_userupdate ?? '-' }}<br>
                                    @if($bank->rec_dateupdate && $bank->rec_dateupdate != '1900-01-01 00:00:00')
                                    <small>{{ \Carbon\Carbon::parse($bank->rec_dateupdate)->format('Y-m-d H:i') }}</small>
                                    @endif
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button wire:click="edit('{{ $bank->bank_code }}')" 
                                            class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($bank->rec_status == '1')
                                    <button wire:click="delete('{{ $bank->bank_code }}')" 
                                            class="btn btn-outline-danger" 
                                            onclick="return confirm('Are you sure to deactivate this bank?')" 
                                            title="Deactivate">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    @else
                                    <button wire:click="restore('{{ $bank->bank_code }}')" 
                                            class="btn btn-outline-success" title="Restore">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">No banks found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $banks->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-university me-2"></i>
                        {{ $editMode ? 'Edit Bank' : 'Add New Bank' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <!-- Bank Code -->
                        <div class="mb-3">
                            <label class="form-label">Bank Code <span class="text-danger">*</span></label>
                            <input type="text" wire:model="bank_code" class="form-control @error('bank_code') is-invalid @enderror" 
                                   placeholder="Enter bank code" {{ $editMode ? 'readonly' : '' }}>
                            @error('bank_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bank Name -->
                        <div class="mb-3">
                            <label class="form-label">Bank Name</label>
                            <input type="text" wire:model="bank_name" class="form-control @error('bank_name') is-invalid @enderror" 
                                   placeholder="Enter bank name">
                            @error('bank_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea wire:model="bank_desc" class="form-control @error('bank_desc') is-invalid @enderror" 
                                      rows="3" placeholder="Enter description"></textarea>
                            @error('bank_desc')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>{{ $editMode ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
