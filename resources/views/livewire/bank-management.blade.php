<div>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"><i class="fas fa-university me-2"></i>Master Bank</h2>
                <p class="text-muted mb-0">Kelola data master bank</p>
            </div>
            <button wire:click="create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Bank
            </button>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Search</label>
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Cari kode bank...">
                    </div>
                    <div class="col-md-2">
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

        <!-- Bank List -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Kode Bank</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Created Date</th>
                                <th>Updated By</th>
                                <th>Updated Date</th>
                                <th class="text-center" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($banks as $index => $bank)
                                <tr>
                                    <td class="text-muted">{{ $banks->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $bank->Bank_Code }}</strong>
                                    </td>
                                    <td>
                                        <button wire:click="toggleStatus('{{ $bank->Bank_Code }}')" 
                                                class="btn btn-sm {{ $bank->rec_status == '1' ? 'btn-success' : 'btn-secondary' }}">
                                            {{ $bank->rec_status == '1' ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td>
                                        <small>{{ $bank->rec_usercreated }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $bank->rec_datecreated ? $bank->rec_datecreated->format('d/m/Y H:i') : '-' }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $bank->rec_userupdate ?: '-' }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $bank->rec_dateupdate && $bank->rec_dateupdate->format('Y') != '1900' ? $bank->rec_dateupdate->format('d/m/Y H:i') : '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <button wire:click="edit('{{ $bank->Bank_Code }}')" 
                                                class="btn btn-sm btn-outline-primary" 
                                                title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        Tidak ada data bank
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
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-university me-2"></i>
                            {{ $editMode ? 'Edit Bank' : 'Tambah Bank' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="mb-3">
                                <label for="bank_code" class="form-label">Kode Bank <span class="text-danger">*</span></label>
                                <input type="text" 
                                       wire:model="bank_code" 
                                       class="form-control @error('bank_code') is-invalid @enderror" 
                                       id="bank_code"
                                       {{ $editMode ? 'readonly' : '' }}
                                       placeholder="Masukkan kode bank">
                                @error('bank_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Maksimal 100 karakter</small>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <i class="fas fa-times me-2"></i>Batal
                        </button>
                        <button type="button" wire:click="save" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
