<div>
    <!-- Header Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="card-title mb-1">
                        <i class="fas fa-book me-2 text-primary"></i>Jurnal Transaksi
                    </h2>
                    <p class="text-muted mb-0">
                        <small>General Journal - Transaksi Debit & Credit (tr_acc_transaksi_coa)</small>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <button wire:click="resetFilters" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-redo me-1"></i>Reset Filter
                    </button>
                    <button class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Entry Jurnal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <strong><i class="fas fa-filter me-2"></i>Filter & Pencarian</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Search -->
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-search me-1"></i>Pencarian
                    </label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        class="form-control" 
                        placeholder="Cari kode, deskripsi, COA..."
                    >
                </div>

                <!-- Date From -->
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-calendar me-1"></i>Dari Tanggal
                    </label>
                    <input 
                        type="date" 
                        wire:model.live="filterDateFrom" 
                        class="form-control"
                    >
                </div>

                <!-- Date To -->
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-calendar me-1"></i>Sampai Tanggal
                    </label>
                    <input 
                        type="date" 
                        wire:model.live="filterDateTo" 
                        class="form-control"
                    >
                </div>

                <!-- COA Filter -->
                <div class="col-md-2">
                    <label class="form-label">Akun COA</label>
                    <select wire:model.live="filterCoa" class="form-select">
                        <option value="">Semua COA</option>
                        @foreach($coaList as $code => $desc)
                            <option value="{{ $code }}">{{ $code }} - {{ Str::limit($desc, 30) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-md-2">
                    <label class="form-label">Status Posting</label>
                    <select wire:model.live="filterStatus" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="POSTED">Posted</option>
                        <option value="UNPOSTED">Unposted</option>
                        <option value="DRAFT">Draft</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Debet</h6>
                    <h3 class="card-title mb-0">Rp {{ number_format($summary->total_debet ?? 0, 2, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Credit</h6>
                    <h3 class="card-title mb-0">Rp {{ number_format($summary->total_credit ?? 0, 2, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Selisih</h6>
                    <h3 class="card-title mb-0">
                        Rp {{ number_format(($summary->total_debet ?? 0) - ($summary->total_credit ?? 0), 2, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Vouchers</h6>
                    <h3 class="card-title mb-0">{{ number_format($summary->total_vouchers ?? 0) }}</h3>
                    <small class="opacity-75">{{ number_format($summary->total_lines ?? 0) }} baris</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Accordion -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <strong>{{ $transactions->total() }} Voucher</strong>
                    <small class="text-muted ms-2">Menampilkan {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }}</small>
                </div>
                <select wire:model.live="perPage" class="form-select w-auto">
                    <option value="50">50 / halaman</option>
                    <option value="100">100 / halaman</option>
                    <option value="200">200 / halaman</option>
                    <option value="300">300 / halaman</option>
                </select>
            </div>

            <div class="accordion" id="jurnalAccordion">
                @forelse($transactions as $trans)
                    @php
                        // Filter details: transcoa_head_code = transmain_codetransaksi
                        $filteredDetails = $trans->filtered_details;
                        $totalDebet = $filteredDetails->sum('transcoa_debet_value');
                        $totalCredit = $filteredDetails->sum('transcoa_credit_value');
                        $isBalanced = abs($totalDebet - $totalCredit) < 0.01;
                        
                        // Debug: Uncomment untuk lihat data
                        // dd($trans->transmain_code, $trans->transmain_codetransaksi, $trans->rec_comcode, $trans->rec_areacode, $filteredDetails->count());
                    @endphp
                    <div class="accordion-item mb-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse{{ $loop->index }}" aria-expanded="false">
                                <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                    <div class="d-flex align-items-center gap-3 flex-grow-1">
                                        <div>
                                            <span 
                                                class="badge bg-dark font-monospace cursor-pointer" 
                                                wire:click.stop="filterByVoucher('{{ $trans->transmain_code }}')"
                                                title="Klik untuk filter voucher ini"
                                            >
                                                {{ $trans->transmain_code }}
                                            </span>
                                            <div class="small text-muted">{{ $trans->transmain_codetransaksi }}</div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <strong>{{ $trans->transmain_desc }}</strong>
                                            @if($trans->transmain_document_note)
                                                <div class="small text-muted">{{ $trans->transmain_document_note }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span 
                                            class="badge bg-secondary cursor-pointer"
                                            wire:click.stop="filterByDate('{{ $trans->transmain_document_date ? $trans->transmain_document_date->format('Y-m-d') : '' }}')"
                                            title="Klik untuk filter tanggal"
                                        >
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $trans->transmain_document_date ? $trans->transmain_document_date->format('d/m/Y') : '-' }}
                                        </span>
                                        <span class="badge {{ $isBalanced ? 'bg-success' : 'bg-danger' }}">
                                            <i class="fas fa-{{ $isBalanced ? 'check-circle' : 'exclamation-triangle' }} me-1"></i>
                                            {{ $isBalanced ? 'Balance' : 'Unbalance' }}
                                        </span>
                                        <span class="badge bg-info text-white">
                                            <i class="fas fa-plus-circle me-1"></i>
                                            Debet: {{ number_format($totalDebet, 2) }}
                                        </span>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-minus-circle me-1"></i>
                                            Credit: {{ number_format($totalCredit, 2) }}
                                        </span>
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-list me-1"></i>{{ $filteredDetails->count() }} baris
                                        </span>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse" 
                             data-bs-parent="#jurnalAccordion">
                            <div class="accordion-body p-0">
                                <!-- Header Info -->
                                <div class="bg-light p-3 border-bottom">
                                    <div class="row small">
                                        <div class="col-md-3">
                                            <strong>Kode Transaksi:</strong> {{ $trans->transmain_codetransaksi }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Operator:</strong> {{ $trans->transmain_operator ?? '-' }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Nilai:</strong> Rp {{ number_format($trans->transmain_value ?? 0, 2, ',', '.') }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Created:</strong> {{ $trans->rec_usercreated }} - {{ \Carbon\Carbon::parse($trans->rec_datecreated)->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Detail Lines Table -->
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="100">Kode COA</th>
                                                <th>Deskripsi COA</th>
                                                <th>Deskripsi Transaksi</th>
                                                <th class="text-end" width="150">Debet</th>
                                                <th class="text-end" width="150">Credit</th>
                                                <th class="text-center" width="100">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($filteredDetails as $detail)
                                                <tr>
                                                    <td>
                                                        @if($detail->transcoa_coa_code)
                                                            <span 
                                                                class="badge bg-dark font-monospace cursor-pointer small" 
                                                                wire:click="filterByCoa('{{ $detail->transcoa_coa_code }}')"
                                                                title="Klik untuk filter COA ini"
                                                            >
                                                                {{ $detail->transcoa_coa_code }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($detail->coa)
                                                            <strong>{{ $detail->coa->coa_desc }}</strong>
                                                            @if($detail->coa->coa_note)
                                                                <div class="small text-muted">{{ Str::limit($detail->coa->coa_note, 40) }}</div>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small>{{ $detail->transcoa_coa_desc }}</small>
                                                        @if($detail->transcoa_coa_type)
                                                            <div><span class="badge bg-light text-dark border small">{{ $detail->transcoa_coa_type }}</span></div>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if($detail->transcoa_debet_value > 0)
                                                            <strong class="text-success">Rp {{ number_format($detail->transcoa_debet_value, 2, ',', '.') }}</strong>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if($detail->transcoa_credit_value > 0)
                                                            <strong class="text-danger">Rp {{ number_format($detail->transcoa_credit_value, 2, ',', '.') }}</strong>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($detail->transcoa_statusposting == 'POSTED')
                                                            <span 
                                                                class="badge bg-success cursor-pointer small" 
                                                                wire:click="filterByStatus('POSTED')"
                                                            >
                                                                Posted
                                                            </span>
                                                        @elseif($detail->transcoa_statusposting == 'UNPOSTED')
                                                            <span 
                                                                class="badge bg-warning cursor-pointer small" 
                                                                wire:click="filterByStatus('UNPOSTED')"
                                                            >
                                                                Unposted
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary small">{{ $detail->transcoa_statusposting ?? 'Draft' }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-3">
                                                        <div class="text-muted">
                                                            <i class="fas fa-info-circle me-2"></i>
                                                            Tidak ada detail transaksi untuk voucher ini
                                                            <div class="small mt-1">
                                                                (Filter: transcoa_head_code = {{ $trans->transmain_codetransaksi }})
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot class="table-secondary">
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                <td class="text-end">
                                                    <strong class="text-success">Rp {{ number_format($totalDebet, 2, ',', '.') }}</strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="text-danger">Rp {{ number_format($totalCredit, 2, ',', '.') }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    <strong class="{{ $isBalanced ? 'text-success' : 'text-danger' }}">
                                                        Rp {{ number_format(abs($totalDebet - $totalCredit), 2, ',', '.') }}
                                                    </strong>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-0">Tidak ada data transaksi</p>
                        <small class="text-muted">Coba ubah filter pencarian</small>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-2">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Informasi
                    </h6>
                    <ul class="list-unstyled mb-0 small">
                        <li><i class="fas fa-check text-success me-2"></i>Periode: {{ date('d/m/Y', strtotime($filterDateFrom)) }} - {{ date('d/m/Y', strtotime($filterDateTo)) }}</li>
                        <li><i class="fas fa-check text-success me-2"></i>Total Voucher: {{ number_format($transactions->total()) }}</li>
                        <li><i class="fas fa-check text-success me-2"></i>Status: {{ $filterStatus ?: 'Semua' }}</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-2">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>Tips
                    </h6>
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li><i class="fas fa-arrow-right me-2"></i>Klik accordion untuk melihat detail baris transaksi</li>
                        <li><i class="fas fa-mouse-pointer me-2 text-primary"></i><strong>Klik pada Badge atau Tanggal untuk filter otomatis!</strong></li>
                        <li><i class="fas fa-arrow-right me-2"></i>Gunakan filter untuk menyaring data berdasarkan COA, status, atau periode</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <style>
        .cursor-pointer {
            cursor: pointer;
            user-select: none;
        }
        .accordion-button:not(.collapsed) {
            background-color: #e9ecef;
            color: #212529;
        }
        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0,0,0,.125);
        }
        .font-monospace {
            font-family: 'Courier New', monospace;
        }
        .badge.cursor-pointer:hover {
            opacity: 0.8;
            transform: scale(1.05);
            transition: all 0.2s ease;
        }
    </style>
</div>
