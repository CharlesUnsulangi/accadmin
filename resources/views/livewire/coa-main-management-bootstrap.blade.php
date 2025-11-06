<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; margin-top: 70px;">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; margin-top: 70px;">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
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
                        <i class="fas fa-folder-tree me-2 text-primary"></i>COA Main Management
                    </h2>
                    <p class="text-muted mb-0">
                        <small>Legacy Level 1 - Main Categories (ms_acc_coa_main)</small>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('coa.legacy') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-list me-1"></i>Legacy Hierarchy
                    </a>
                    <button wire:click="create" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add New
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search" 
                            class="form-control" 
                            placeholder="Search Code, ID, Description..."
                        >
                    </div>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10 / page</option>
                        <option value="15">15 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Main Categories</h6>
                    <h2 class="card-title mb-0">{{ $coaMains->total() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Current Page</h6>
                    <h2 class="card-title mb-0">{{ $coaMains->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Per Page</h6>
                    <h2 class="card-title mb-0">{{ $perPage }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Main Code</th>
                            <th>Main ID</th>
                            <th>Description</th>
                            <th>Reference Code</th>
                            <th>Children (Sub1)</th>
                            <th>Audit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coaMains as $main)
                            <tr>
                                <td>
                                    <span class="badge bg-primary font-monospace">{{ $main->coa_main_code }}</span>
                                </td>
                                <td>{{ $main->coa_main_id ?? '-' }}</td>
                                <td>
                                    <strong>{{ $main->coa_main_desc }}</strong>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $main->coa_main_coamain2code ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($main->coa_sub1s_count > 0)
                                        <span class="badge bg-success">{{ $main->coa_sub1s_count }} Sub Categories</span>
                                    @else
                                        <span class="badge bg-secondary">No Sub1</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <div><i class="fas fa-user me-1"></i>{{ $main->user_created ?? '-' }}</div>
                                        <div><i class="fas fa-clock me-1"></i>{{ $main->dt_created ? \Carbon\Carbon::parse($main->dt_created)->format('d/m/Y H:i') : '-' }}</div>
                                    </small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">No data found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $coaMains->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-{{ $editMode ? 'edit' : 'plus-circle' }} me-2"></i>
                        {{ $editMode ? 'Edit COA Main' : 'Add New COA Main' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>
                
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <!-- Basic Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong><i class="fas fa-info-circle me-2"></i>Basic Information</strong>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- Main Code -->
                                    <div class="col-md-6">
                                        <label class="form-label">Main Code <span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            wire:model="coa_main_code" 
                                            class="form-control @error('coa_main_code') is-invalid @enderror"
                                            placeholder="e.g., 1000"
                                            {{ $editMode ? 'readonly' : '' }}
                                        >
                                        @error('coa_main_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Main ID -->
                                    <div class="col-md-6">
                                        <label class="form-label">Main ID <span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            wire:model="coa_main_id" 
                                            class="form-control @error('coa_main_id') is-invalid @enderror"
                                            placeholder="e.g., M001"
                                        >
                                        @error('coa_main_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Description -->
                                    <div class="col-12">
                                        <label class="form-label">Description <span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            wire:model="coa_main_desc" 
                                            class="form-control @error('coa_main_desc') is-invalid @enderror"
                                            placeholder="Main category description"
                                        >
                                        @error('coa_main_desc')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Reference Code -->
                                    <div class="col-md-6">
                                        <label class="form-label">Reference Code</label>
                                        <input 
                                            type="text" 
                                            wire:model="coa_main_coamain2code" 
                                            class="form-control @error('coa_main_coamain2code') is-invalid @enderror"
                                            placeholder="Optional reference"
                                        >
                                        @error('coa_main_coamain2code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Active Status -->
                                    <div class="col-md-6">
                                        <label class="form-label">Status</label>
                                        <div class="form-check form-switch mt-2">
                                            <input 
                                                type="checkbox" 
                                                wire:model="cek_aktif" 
                                                class="form-check-input" 
                                                role="switch"
                                                id="statusSwitch"
                                            >
                                            <label class="form-check-label" for="statusSwitch">
                                                <span class="badge bg-{{ $cek_aktif ? 'success' : 'secondary' }}">
                                                    {{ $cek_aktif ? 'Active' : 'Inactive' }}
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($editMode && $id_h)
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Main Code cannot be edited for existing records.
                        </div>
                        @endif
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
