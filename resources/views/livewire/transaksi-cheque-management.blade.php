<div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
                Transaksi Cheque
            </h2>
            <p class="text-muted mb-0">Manajemen transaksi pembayaran menggunakan cheque</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Transaksi</p>
                            <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <div class="fs-1 text-primary opacity-25">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Pending</p>
                            <h3 class="mb-0">{{ number_format($stats['pending']) }}</h3>
                        </div>
                        <div class="fs-1 text-warning opacity-25">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Approved</p>
                            <h3 class="mb-0">{{ number_format($stats['approved']) }}</h3>
                        </div>
                        <div class="fs-1 text-success opacity-25">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Value</p>
                            <h4 class="mb-0">Rp {{ number_format($stats['total_value'], 2, ',', '.') }}</h4>
                        </div>
                        <div class="fs-1 text-info opacity-25">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="card mb-3">
        <div class="card-body p-0">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'all' ? 'active' : '' }}" 
                            wire:click="switchTab('all')" 
                            type="button">
                        <i class="fas fa-list me-2"></i>Semua Transaksi
                        <span class="badge bg-primary ms-2">{{ number_format($stats['total']) }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'orphan' ? 'active' : '' }}" 
                            wire:click="switchTab('orphan')" 
                            type="button">
                        <i class="fas fa-unlink me-2"></i>Orphan (Tanpa Jurnal)
                        <span class="badge bg-warning ms-2">{{ number_format($stats['orphan']) }}</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" 
                           placeholder="Cari kode, vendor, doc, desc...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select wire:model.live="filterStatus" class="form-select">
                        <option value="">Semua</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Dari</label>
                    <input type="date" wire:model.live="filterDateStart" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Sampai</label>
                    <input type="date" wire:model.live="filterDateEnd" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Per Page</label>
                    <select wire:model.live="perPage" class="form-select">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button wire:click="$set('search', ''); $set('filterStatus', ''); $set('filterDateStart', ''); $set('filterDateEnd', '')" 
                        class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-redo me-2"></i>Reset Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Orphan Alert -->
    @if($activeTab === 'orphan')
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
        <div>
            <h5 class="alert-heading mb-1">Data Orphan - Transaksi Tanpa Link Jurnal</h5>
            <p class="mb-0">
                Menampilkan <strong>{{ number_format($stats['orphan']) }}</strong> transaksi cheque yang tidak memiliki link ke jurnal transaksi.
                Data ini perlu diperbaiki atau dihubungkan ke jurnal yang sesuai.
            </p>
        </div>
    </div>
    @endif

    <!-- Transactions List -->
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0">
                    <i class="fas fa-table me-2"></i>
                    @if($activeTab === 'orphan')
                        Daftar Transaksi Orphan
                    @else
                        Daftar Transaksi Cheque
                    @endif
                </h6>
            </div>
            <div class="text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                Click pada header kolom untuk sort
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th wire:click="sortBy('transcheque_code')" style="cursor: pointer;">
                                Kode Transaksi
                                @if($sortField === 'transcheque_code')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fas fa-sort ms-1 text-muted"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('transcheque_date')" style="cursor: pointer;">
                                Tanggal
                                @if($sortField === 'transcheque_date')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fas fa-sort ms-1 text-muted"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('transcheque_vendor')" style="cursor: pointer;">
                                Vendor
                                @if($sortField === 'transcheque_vendor')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fas fa-sort ms-1 text-muted"></i>
                                @endif
                            </th>
                            <th>Doc/Keterangan</th>
                            <th wire:click="sortBy('transcheque_value')" class="text-end" style="cursor: pointer;">
                                Value
                                @if($sortField === 'transcheque_value')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fas fa-sort ms-1 text-muted"></i>
                                @endif
                            </th>
                            <th class="text-center">Jumlah Cheque</th>
                            <th wire:click="sortBy('transcheque_status')" class="text-center" style="cursor: pointer;">
                                Status
                                @if($sortField === 'transcheque_status')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fas fa-sort ms-1 text-muted"></i>
                                @endif
                            </th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $index => $trans)
                            <tr>
                                <td class="text-muted">{{ $transactions->firstItem() + $index }}</td>
                                <td>
                                    <div>
                                        <code class="text-primary">{{ $trans->transcheque_code }}</code>
                                        @if($trans->transcheque_transmaincode)
                                            <br><small class="text-success">
                                                <i class="fas fa-link"></i> {{ $trans->transcheque_transmaincode }}
                                            </small>
                                        @else
                                            <br><small class="text-danger">
                                                <i class="fas fa-unlink"></i> No Jurnal
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <small>{{ \Carbon\Carbon::parse($trans->transcheque_date)->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $trans->transcheque_vendor ?? '-' }}</strong>
                                </td>
                                <td>
                                    <div>
                                        @if($trans->transcheque_doc)
                                            <span class="badge bg-secondary">{{ $trans->transcheque_doc }}</span><br>
                                        @endif
                                        <small class="text-muted">{{ $trans->transcheque_desc ?? '-' }}</small>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">Rp {{ number_format($trans->transcheque_value, 2, ',', '.') }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $trans->cheque_count ?? 0 }} cheque</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $badgeClass = match($trans->transcheque_status) {
                                            'PENDING' => 'bg-warning',
                                            'APPROVED' => 'bg-success',
                                            'PAID' => 'bg-info',
                                            'CANCELLED' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $trans->transcheque_status ?? 'N/A' }}</span>
                                </td>
                                <td class="text-center">
                                    <button wire:click="viewDetail('{{ $trans->rec_comcode }}', '{{ $trans->rec_areacode }}', '{{ $trans->transcheque_code }}')" 
                                            class="btn btn-sm btn-outline-primary"
                                            title="View Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-inbox fs-1 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Tidak ada data transaksi cheque</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    @if($showDetailModal && !empty($selectedTransaction))
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-file-invoice me-2"></i>
                            Detail Transaksi Cheque: <code class="text-white">{{ $selectedTransaction['transcheque_code'] }}</code>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeDetailModal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Transaction Header Info -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Transaksi</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="text-muted" style="width: 180px;">Kode Transaksi:</td>
                                                <td><strong>{{ $selectedTransaction['transcheque_code'] }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Company/Area:</td>
                                                <td>{{ $selectedTransaction['rec_comcode'] }} / {{ $selectedTransaction['rec_areacode'] }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Tanggal:</td>
                                                <td>{{ \Carbon\Carbon::parse($selectedTransaction['transcheque_date'])->format('d F Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Vendor:</td>
                                                <td><strong>{{ $selectedTransaction['transcheque_vendor'] ?? '-' }}</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="text-muted" style="width: 180px;">Link Jurnal:</td>
                                                <td>
                                                    @if(!empty($selectedTransaction['transcheque_transmaincode']))
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-link me-1"></i>
                                                            {{ $selectedTransaction['transcheque_transmaincode'] }}
                                                        </span>
                                                        @if(!empty($selectedTransaction['transmain_codetransaksi']))
                                                            <br><small class="text-muted">{{ $selectedTransaction['transmain_codetransaksi'] }} - {{ $selectedTransaction['transmain_desc'] }}</small>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">Belum ada jurnal</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Document:</td>
                                                <td>{{ $selectedTransaction['transcheque_doc'] ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Status:</td>
                                                <td>
                                                    @php
                                                        $badgeClass = match($selectedTransaction['transcheque_status']) {
                                                            'PENDING' => 'bg-warning',
                                                            'APPROVED' => 'bg-success',
                                                            'PAID' => 'bg-info',
                                                            'CANCELLED' => 'bg-danger',
                                                            default => 'bg-secondary',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ $selectedTransaction['transcheque_status'] }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Total Value:</td>
                                                <td><strong class="text-success">Rp {{ number_format($selectedTransaction['transcheque_value'], 2, ',', '.') }}</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                @if(!empty($selectedTransaction['transcheque_desc']))
                                <div class="mt-2">
                                    <strong class="text-muted">Keterangan:</strong>
                                    <p class="mb-0">{{ $selectedTransaction['transcheque_desc'] }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Cheque Details -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-money-check-alt me-2"></i>Detail Cheque Digunakan 
                                    <span class="badge bg-primary ms-2">{{ count($transactionDetails) }} cheque</span>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th style="width: 40px;">#</th>
                                                <th>Cheque No</th>
                                                <th>Cheque Book</th>
                                                <th>Bank / Rekening</th>
                                                <th>COA</th>
                                                <th>Tanggal Doc</th>
                                                <th class="text-end">Value</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($transactionDetails as $index => $detail)
                                                <tr>
                                                    <td class="text-muted">{{ $index + 1 }}</td>
                                                    <td>
                                                        <code class="text-primary">{{ is_array($detail) ? $detail['transcheque_no'] : $detail->transcheque_no }}</code>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <small class="text-muted">{{ is_array($detail) ? $detail['transcheque_code_h'] : $detail->transcheque_code_h }}</small><br>
                                                            <strong>{{ is_array($detail) ? ($detail['cheque_book_desc'] ?? '-') : ($detail->cheque_book_desc ?? '-') }}</strong>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong>{{ is_array($detail) ? ($detail['cheque_bank'] ?? '-') : ($detail->cheque_bank ?? '-') }}</strong><br>
                                                            <small class="text-muted">{{ is_array($detail) ? ($detail['cheque_rek'] ?? '-') : ($detail->cheque_rek ?? '-') }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <code>{{ is_array($detail) ? $detail['transcheque_coa'] : $detail->transcheque_coa }}</code><br>
                                                            <small>{{ is_array($detail) ? ($detail['coa_desc'] ?? '-') : ($detail->coa_desc ?? '-') }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $dateDoc = is_array($detail) ? $detail['transcheque_datedoc'] : $detail->transcheque_datedoc;
                                                        @endphp
                                                        <small>{{ $dateDoc ? \Carbon\Carbon::parse($dateDoc)->format('d/m/Y') : '-' }}</small>
                                                    </td>
                                                    <td class="text-end">
                                                        <strong>Rp {{ number_format(is_array($detail) ? $detail['transcheque_value'] : $detail->transcheque_value, 2, ',', '.') }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $chequeStatus = is_array($detail) ? $detail['cheque_status'] : $detail->cheque_status;
                                                            $statusBadge = match($chequeStatus) {
                                                                'AVAILABLE' => 'bg-success',
                                                                'USED' => 'bg-info',
                                                                'VOID' => 'bg-danger',
                                                                default => 'bg-secondary',
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $statusBadge }}">{{ $chequeStatus ?? 'N/A' }}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted py-3">
                                                        Tidak ada detail cheque
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if(count($transactionDetails) > 0)
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="6" class="text-end">Total:</th>
                                                <th class="text-end">
                                                    @php
                                                        $total = 0;
                                                        foreach($transactionDetails as $d) {
                                                            $total += is_array($d) ? $d['transcheque_value'] : $d->transcheque_value;
                                                        }
                                                    @endphp
                                                    Rp {{ number_format($total, 2, ',', '.') }}
                                                </th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Jurnal Entries Section -->
                        @if(!empty($jurnalData) && count($jurnalData) > 0)
                        <div class="card mt-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-book me-2"></i>Jurnal Entries (COA Details)
                                    <span class="badge bg-info ms-2">{{ count($jurnalData) }} entries</span>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th style="width: 40px;">#</th>
                                                <th>COA Code</th>
                                                <th>COA Description</th>
                                                <th class="text-end" style="width: 150px;">Debet</th>
                                                <th class="text-end" style="width: 150px;">Kredit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalDebet = 0;
                                                $totalKredit = 0;
                                            @endphp
                                            @foreach($jurnalData as $index => $entry)
                                                @php
                                                    $debet = is_array($entry) ? ($entry['transcoa_debet'] ?? 0) : ($entry->transcoa_debet ?? 0);
                                                    $kredit = is_array($entry) ? ($entry['transcoa_kredit'] ?? 0) : ($entry->transcoa_kredit ?? 0);
                                                    $totalDebet += $debet;
                                                    $totalKredit += $kredit;
                                                @endphp
                                                <tr>
                                                    <td class="text-muted">{{ $index + 1 }}</td>
                                                    <td>
                                                        <code class="text-primary">{{ is_array($entry) ? ($entry['transcoa_coa'] ?? '-') : ($entry->transcoa_coa ?? '-') }}</code>
                                                    </td>
                                                    <td>
                                                        <strong>{{ is_array($entry) ? ($entry['coa_desc'] ?? '-') : ($entry->coa_desc ?? '-') }}</strong>
                                                    </td>
                                                    <td class="text-end">
                                                        @if($debet > 0)
                                                            <strong class="text-success">Rp {{ number_format($debet, 2, ',', '.') }}</strong>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if($kredit > 0)
                                                            <strong class="text-danger">Rp {{ number_format($kredit, 2, ',', '.') }}</strong>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-secondary">
                                                <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                                                <td class="text-end">
                                                    <strong class="text-success">Rp {{ number_format($totalDebet, 2, ',', '.') }}</strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="text-danger">Rp {{ number_format($totalKredit, 2, ',', '.') }}</strong>
                                                </td>
                                            </tr>
                                            @php
                                                $balance = $totalDebet - $totalKredit;
                                            @endphp
                                            @if(abs($balance) > 0.01)
                                            <tr class="table-warning">
                                                <td colspan="3" class="text-end"><strong>BALANCE (D-K):</strong></td>
                                                <td colspan="2" class="text-end">
                                                    <strong class="text-warning">Rp {{ number_format($balance, 2, ',', '.') }}</strong>
                                                    <small class="text-danger ms-2"><i class="fas fa-exclamation-triangle"></i> Not balanced!</small>
                                                </td>
                                            </tr>
                                            @else
                                            <tr class="table-success">
                                                <td colspan="5" class="text-center">
                                                    <strong class="text-success"><i class="fas fa-check-circle"></i> Jurnal Balanced</strong>
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @elseif(!empty($selectedTransaction['transcheque_transmaincode']))
                        <div class="card mt-3">
                            <div class="card-body text-center text-muted py-4">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <p class="mb-0">Link jurnal tersedia tapi detail COA tidak ditemukan</p>
                                <small>Transmain Code: <code>{{ $selectedTransaction['transcheque_transmaincode'] }}</code></small>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetailModal">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
