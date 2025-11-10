<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1"><i class="fas fa-lock me-2 text-primary"></i>Closing Process</h3>
                            <p class="text-muted mb-0">3 Layer System: Bulanan → Tahunan → Audit</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-info fs-6">{{ now()->format('d F Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Panel: Configuration -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Konfigurasi Closing</h5>
                </div>
                <div class="card-body">
                    <!-- Pilih Tipe Closing -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipe Closing</label>
                        <select wire:model.live="closingType" class="form-select">
                            <option value="monthly">📅 Layer 1: Rekap Bulanan</option>
                            <option value="yearly">📆 Layer 2: Rekap Tahunan</option>
                            <option value="audit">🔍 Layer 3: Audit (Hitung dari Awal)</option>
                        </select>
                        <small class="text-muted">
                            @if($closingType === 'monthly')
                                Rekap transaksi per bulan dengan opening & closing balance
                            @elseif($closingType === 'yearly')
                                Aggregate dari 12 monthly closing
                            @else
                                Hitung ulang dari transaksi pertama untuk verifikasi
                            @endif
                        </small>
                    </div>

                    <!-- Pilih Tahun -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tahun</label>
                        <select wire:model="selectedYear" class="form-select">
                            @for($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Pilih Bulan -->
                    @if($closingType !== 'yearly')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Bulan</label>
                        <select wire:model="selectedMonth" class="form-select">
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="d-grid gap-2 mt-4">
                        <button wire:click="preview" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i>Preview Data
                        </button>

                        @if($showPreview && $closingType !== 'audit')
                        <button wire:click="generate" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Generate & Simpan
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Info Panel -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <p class="mb-2"><strong>Layer 1 - Rekap Bulanan:</strong></p>
                        <ul class="mb-3">
                            <li>Hitung per bulan</li>
                            <li>Opening dari bulan/tahun sebelumnya</li>
                            <li>Mutasi bulan berjalan</li>
                            <li>Closing = Opening + Mutasi</li>
                        </ul>

                        <p class="mb-2"><strong>Layer 2 - Rekap Tahunan:</strong></p>
                        <ul class="mb-3">
                            <li>Aggregate 12 monthly closing</li>
                            <li>Summary per bulan tersimpan</li>
                            <li>Untuk laporan tahunan</li>
                        </ul>

                        <p class="mb-2"><strong>Layer 3 - Audit:</strong></p>
                        <ul class="mb-0">
                            <li>Hitung dari transaksi pertama</li>
                            <li>Untuk verifikasi data</li>
                            <li>Deteksi discrepancy</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Preview -->
        <div class="col-md-8">
            @if($showPreview && count($previewData) > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>Preview Data</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 600px;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>COA</th>
                                    <th>Deskripsi</th>
                                    <th class="text-end">Balance</th>
                                    <th class="text-center">Transaksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($previewData as $row)
                                <tr>
                                    <td><code>{{ $closingType === 'audit' ? $row['coa_code'] : $row['coa_code'] }}</code></td>
                                    <td class="small">{{ $closingType === 'audit' ? ($row['coa_desc'] ?? '-') : ($row['coa_desc'] ?? '-') }}</td>
                                    <td class="text-end fw-bold">{{ number_format($closingType === 'audit' ? $row['balance'] : $row['closing_balance'], 2) }}</td>
                                    <td class="text-center">{{ $closingType === 'audit' ? $row['jumlah_transaksi'] : $row['jumlah_transaksi'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-table fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada preview</h5>
                    <p class="text-muted">Klik "Preview Data" untuk melihat hasil</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
