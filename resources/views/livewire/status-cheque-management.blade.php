<div>
    @section('title', 'Master Status Cheque')
    
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Master Status Cheque</h2>
            <button wire:click="create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Status
            </button>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari berdasarkan kode atau deskripsi...">
                    </div>
                    <div class="col-md-6">
                        <select wire:model.live="perPage" class="form-select">
                            <option value="10">10 per halaman</option>
                            <option value="25">25 per halaman</option>
                            <option value="50">50 per halaman</option>
                            <option value="100">100 per halaman</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="20%">Kode Status</th>
                                <th width="50%">Deskripsi</th>
                                <th width="15%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($statusCheques as $index => $item)
                                <tr>
                                    <td>{{ $statusCheques->firstItem() + $index }}</td>
                                    <td><strong>{{ $item->stacheq_code }}</strong></td>
                                    <td>{{ $item->stacheq_desc }}</td>
                                    <td>
                                        @if($item->rec_status == '1')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button wire:click="edit('{{ $item->stacheq_code }}')" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button wire:click="toggleStatus('{{ $item->stacheq_code }}')" 
                                                class="btn btn-sm {{ $item->rec_status == '1' ? 'btn-secondary' : 'btn-success' }}" 
                                                title="{{ $item->rec_status == '1' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="bi {{ $item->rec_status == '1' ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $statusCheques->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create/Edit -->
    @if($showModal)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEdit ? 'Edit' : 'Tambah' }} Master Status Cheque</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="mb-3">
                                <label for="stacheq_code" class="form-label">Kode Status <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('stacheq_code') is-invalid @enderror" 
                                       id="stacheq_code" wire:model="stacheq_code" 
                                       {{ $isEdit ? 'readonly' : '' }}
                                       placeholder="Misal: AVAILABLE, USED, VOID, dll">
                                @error('stacheq_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="stacheq_desc" class="form-label">Deskripsi</label>
                                <input type="text" class="form-control @error('stacheq_desc') is-invalid @enderror" 
                                       id="stacheq_desc" wire:model="stacheq_desc"
                                       placeholder="Deskripsi status">
                                @error('stacheq_desc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                        <button type="button" class="btn btn-primary" wire:click="save">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
