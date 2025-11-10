<div>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>Master Area</h2>
                <p class="text-muted mb-0">Kelola data master area/wilayah</p>
            </div>
            <button wire:click="create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Area
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
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Cari kode, nama, alamat, PIC...">
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

        <!-- Area List -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Kode</th>
                                <th>Nama Area</th>
                                <th>Alamat</th>
                                <th>Contact</th>
                                <th>PIC</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($areas as $index => $area)
                                <tr>
                                    <td class="text-muted">{{ $areas->firstItem() + $index }}</td>
                                    <td>
                                        <strong class="text-primary">{{ $area->Are_Code }}</strong>
                                    </td>
                                    <td>
                                        <strong>{{ $area->Are_Name ?: '-' }}</strong>
                                    </td>
                                    <td>
                                        <small>{{ $area->Are_Address ?: '-' }}</small>
                                    </td>
                                    <td>
                                        <small>
                                            @if($area->Are_ContactNo)
                                                <i class="fas fa-phone me-1"></i>{{ $area->Are_ContactNo }}<br>
                                            @endif
                                            @if($area->Are_Fax)
                                                <i class="fas fa-fax me-1"></i>{{ $area->Are_Fax }}
                                            @endif
                                            @if(!$area->Are_ContactNo && !$area->Are_Fax)
                                                -
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <small>
                                            {{ $area->Are_PIC ?: '-' }}
                                            @if($area->Are_PIChp)
                                                <br><i class="fas fa-mobile-alt me-1"></i>{{ $area->Are_PIChp }}
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <small>{{ $area->Are_Email ?: '-' }}</small>
                                    </td>
                                    <td>
                                        <button wire:click="toggleStatus('{{ $area->Are_Code }}')" 
                                                class="btn btn-sm {{ $area->rec_status == '1' ? 'btn-success' : 'btn-secondary' }}">
                                            {{ $area->rec_status == '1' ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <button wire:click="edit('{{ $area->Are_Code }}')" 
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
                                        Tidak ada data area
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $areas->links() }}
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
                            <i class="fas fa-map-marker-alt me-2"></i>
                            {{ $editMode ? 'Edit Area' : 'Tambah Area' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="area_code" class="form-label">Kode Area <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           wire:model="area_code" 
                                           class="form-control @error('area_code') is-invalid @enderror" 
                                           id="area_code"
                                           {{ $editMode ? 'readonly' : '' }}
                                           placeholder="Contoh: JKT">
                                    @error('area_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="area_name" class="form-label">Nama Area</label>
                                    <input type="text" 
                                           wire:model="area_name" 
                                           class="form-control @error('area_name') is-invalid @enderror" 
                                           id="area_name"
                                           placeholder="Contoh: Jakarta">
                                    @error('area_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="area_address" class="form-label">Alamat</label>
                                <textarea wire:model="area_address" 
                                          class="form-control @error('area_address') is-invalid @enderror" 
                                          id="area_address"
                                          rows="2"
                                          placeholder="Alamat lengkap area"></textarea>
                                @error('area_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="area_contactno" class="form-label">Telepon</label>
                                    <input type="text" 
                                           wire:model="area_contactno" 
                                           class="form-control @error('area_contactno') is-invalid @enderror" 
                                           id="area_contactno"
                                           placeholder="021-xxx">
                                    @error('area_contactno')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="area_fax" class="form-label">Fax</label>
                                    <input type="text" 
                                           wire:model="area_fax" 
                                           class="form-control @error('area_fax') is-invalid @enderror" 
                                           id="area_fax"
                                           placeholder="021-xxx">
                                    @error('area_fax')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="area_pic" class="form-label">PIC (Person In Charge)</label>
                                    <input type="text" 
                                           wire:model="area_pic" 
                                           class="form-control @error('area_pic') is-invalid @enderror" 
                                           id="area_pic"
                                           placeholder="Nama PIC">
                                    @error('area_pic')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="area_pichp" class="form-label">HP PIC</label>
                                    <input type="text" 
                                           wire:model="area_pichp" 
                                           class="form-control @error('area_pichp') is-invalid @enderror" 
                                           id="area_pichp"
                                           placeholder="08xx-xxx-xxx">
                                    @error('area_pichp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="area_email" class="form-label">Email</label>
                                <input type="email" 
                                       wire:model="area_email" 
                                       class="form-control @error('area_email') is-invalid @enderror" 
                                       id="area_email"
                                       placeholder="email@example.com">
                                @error('area_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="area_db" class="form-label">Database Name</label>
                                    <input type="text" 
                                           wire:model="area_db" 
                                           class="form-control @error('area_db') is-invalid @enderror" 
                                           id="area_db"
                                           placeholder="Nama database">
                                    @error('area_db')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="area_dbtype" class="form-label">DB Type</label>
                                    <select wire:model="area_dbtype" 
                                            class="form-select @error('area_dbtype') is-invalid @enderror" 
                                            id="area_dbtype">
                                        <option value="">-- Pilih --</option>
                                        <option value="S">SQL Server</option>
                                        <option value="M">MySQL</option>
                                        <option value="P">PostgreSQL</option>
                                        <option value="O">Oracle</option>
                                    </select>
                                    @error('area_dbtype')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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

