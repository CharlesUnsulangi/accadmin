<div>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"><i class="fas fa-users me-2"></i>Master Vendor/Supplier</h2>
                <p class="text-muted mb-0">Kelola data master vendor dan supplier</p>
            </div>
            <button wire:click="create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Vendor
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
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Cari kode, nama, PIC, alamat, email...">
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

        <!-- Vendor List -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Kode</th>
                                <th>Nama Vendor</th>
                                <th>PIC</th>
                                <th>Alamat</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendors as $index => $vendor)
                                <tr>
                                    <td class="text-muted">{{ $vendors->firstItem() + $index }}</td>
                                    <td>
                                        <strong class="text-primary">{{ $vendor->ven_code }}</strong>
                                    </td>
                                    <td>
                                        <strong>{{ $vendor->ven_name ?: '-' }}</strong>
                                    </td>
                                    <td>
                                        <small>{{ $vendor->ven_pic ?: '-' }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $vendor->ven_addrase ?: '-' }}</small>
                                    </td>
                                    <td>
                                        <small>
                                            @if($vendor->ven_phone)
                                                <i class="fas fa-phone me-1"></i>{{ $vendor->ven_phone }}
                                            @else
                                                -
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <small>{{ $vendor->ven_email ?: '-' }}</small>
                                    </td>
                                    <td>
                                        <button wire:click="toggleStatus('{{ $vendor->ven_code }}')" 
                                                class="btn btn-sm {{ $vendor->rec_status == '1' ? 'btn-success' : 'btn-secondary' }}">
                                            {{ $vendor->rec_status == '1' ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <button wire:click="edit('{{ $vendor->ven_code }}')" 
                                                class="btn btn-sm btn-outline-primary" 
                                                title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        Tidak ada data vendor
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $vendors->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-users me-2"></i>
                            {{ $editMode ? 'Edit Vendor' : 'Tambah Vendor' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ven_code" class="form-label">Kode Vendor <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           wire:model="ven_code" 
                                           class="form-control @error('ven_code') is-invalid @enderror" 
                                           id="ven_code"
                                           {{ $editMode ? 'readonly' : '' }}
                                           placeholder="Contoh: VEN001">
                                    @error('ven_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="ven_name" class="form-label">Nama Vendor</label>
                                    <input type="text" 
                                           wire:model="ven_name" 
                                           class="form-control @error('ven_name') is-invalid @enderror" 
                                           id="ven_name"
                                           placeholder="Nama vendor/supplier">
                                    @error('ven_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="ven_pic" class="form-label">PIC (Person In Charge)</label>
                                <input type="text" 
                                       wire:model="ven_pic" 
                                       class="form-control @error('ven_pic') is-invalid @enderror" 
                                       id="ven_pic"
                                       placeholder="Nama PIC">
                                @error('ven_pic')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="ven_addrase" class="form-label">Alamat</label>
                                <textarea wire:model="ven_addrase" 
                                          class="form-control @error('ven_addrase') is-invalid @enderror" 
                                          id="ven_addrase"
                                          rows="3"
                                          placeholder="Alamat lengkap vendor"></textarea>
                                @error('ven_addrase')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ven_phone" class="form-label">Telepon</label>
                                    <input type="text" 
                                           wire:model="ven_phone" 
                                           class="form-control @error('ven_phone') is-invalid @enderror" 
                                           id="ven_phone"
                                           placeholder="021-xxx atau 08xx-xxx-xxx">
                                    @error('ven_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="ven_email" class="form-label">Email</label>
                                    <input type="text" 
                                           wire:model="ven_email" 
                                           class="form-control @error('ven_email') is-invalid @enderror" 
                                           id="ven_email"
                                           placeholder="email@vendor.com">
                                    @error('ven_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Bisa lebih dari satu, pisahkan dengan koma</small>
                                </div>
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
