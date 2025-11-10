<div>
    @section('title', 'Master Transaksi')
    
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Master Transaksi</h2>
            <button wire:click="create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Transaksi
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
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari berdasarkan kode, deskripsi, atau COA...">
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
                                <th width="10%">Kode</th>
                                <th width="20%">Deskripsi</th>
                                <th width="12%">COA Debet</th>
                                <th width="12%">COA Kredit</th>
                                <th width="10%">Tanggal</th>
                                <th width="10%" class="text-end">Debet</th>
                                <th width="10%" class="text-end">Kredit</th>
                                <th width="6%">Status</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksi as $index => $item)
                                <tr>
                                    <td>{{ $transaksi->firstItem() + $index }}</td>
                                    <td>{{ $item->trans_code }}</td>
                                    <td>{{ $item->trans_desc }}</td>
                                    <td>
                                        @if($item->coaDebet)
                                            {{ $item->trans_coa_debet }} - {{ $item->coaDebet->coa_desc }}
                                        @else
                                            {{ $item->trans_coa_debet }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->coaKredit)
                                            {{ $item->trans_coa_kredit }} - {{ $item->coaKredit->coa_desc }}
                                        @else
                                            {{ $item->trans_coa_kredit }}
                                        @endif
                                    </td>
                                    <td>{{ $item->trans_date ? $item->trans_date->format('d/m/Y') : '-' }}</td>
                                    <td class="text-end">{{ $item->trans_debet ? number_format($item->trans_debet, 2, ',', '.') : '-' }}</td>
                                    <td class="text-end">{{ $item->trans_kredit ? number_format($item->trans_kredit, 2, ',', '.') : '-' }}</td>
                                    <td>
                                        @if($item->rec_status == '1')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button wire:click="edit('{{ $item->trans_code }}')" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button wire:click="toggleStatus('{{ $item->trans_code }}')" 
                                                class="btn btn-sm {{ $item->rec_status == '1' ? 'btn-secondary' : 'btn-success' }}" 
                                                title="{{ $item->rec_status == '1' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="bi {{ $item->rec_status == '1' ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $transaksi->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create/Edit -->
    @if($showModal)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEdit ? 'Edit' : 'Tambah' }} Master Transaksi</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="trans_code" class="form-label">Kode Transaksi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('trans_code') is-invalid @enderror" 
                                           id="trans_code" wire:model="trans_code" 
                                           {{ $isEdit ? 'readonly' : '' }}>
                                    @error('trans_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="trans_desc" class="form-label">Deskripsi</label>
                                    <input type="text" class="form-control @error('trans_desc') is-invalid @enderror" 
                                           id="trans_desc" wire:model="trans_desc" maxlength="100">
                                    @error('trans_desc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="trans_coa_debet" class="form-label">COA Debet</label>
                                    <select class="form-select @error('trans_coa_debet') is-invalid @enderror" 
                                            id="trans_coa_debet" wire:model="trans_coa_debet">
                                        <option value="">-- Pilih COA Debet --</option>
                                        @foreach($coaList as $coa)
                                            <option value="{{ $coa['code'] }}">{{ $coa['label'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('trans_coa_debet') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="trans_coa_kredit" class="form-label">COA Kredit</label>
                                    <select class="form-select @error('trans_coa_kredit') is-invalid @enderror" 
                                            id="trans_coa_kredit" wire:model="trans_coa_kredit">
                                        <option value="">-- Pilih COA Kredit --</option>
                                        @foreach($coaList as $coa)
                                            <option value="{{ $coa['code'] }}">{{ $coa['label'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('trans_coa_kredit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="trans_date" class="form-label">Tanggal</label>
                                    <input type="date" class="form-control @error('trans_date') is-invalid @enderror" 
                                           id="trans_date" wire:model="trans_date">
                                    @error('trans_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="trans_debet" class="form-label">Jumlah Debet</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('trans_debet') is-invalid @enderror" 
                                           id="trans_debet" wire:model="trans_debet">
                                    @error('trans_debet') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="trans_kredit" class="form-label">Jumlah Kredit</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('trans_kredit') is-invalid @enderror" 
                                           id="trans_kredit" wire:model="trans_kredit">
                                    @error('trans_kredit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
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
