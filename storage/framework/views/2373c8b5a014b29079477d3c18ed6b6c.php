

<?php $__env->startSection('title', 'Closing Process - 3 Layer System'); ?>

<?php $__env->startSection('content'); ?>
<div x-data="closingProcess()" x-init="init()" class="container-fluid py-4">
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
                            <span class="badge bg-info fs-6" x-text="currentDate"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <div x-show="message.show" x-transition class="alert alert-dismissible fade show" 
         :class="message.type === 'success' ? 'alert-success' : message.type === 'error' ? 'alert-danger' : 'alert-info'" role="alert">
        <i class="fas me-2" :class="message.type === 'success' ? 'fa-check-circle' : message.type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'"></i>
        <span x-text="message.text"></span>
        <button type="button" class="btn-close" @click="message.show = false"></button>
    </div>

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
                        <select x-model="config.type" @change="resetPreview()" class="form-select">
                            <option value="monthly">📅 Layer 1: Rekap Bulanan</option>
                            <option value="yearly">📆 Layer 2: Rekap Tahunan</option>
                            <option value="audit">🔍 Layer 3: Audit (Hitung dari Awal)</option>
                        </select>
                        <small class="text-muted">
                            <span x-show="config.type === 'monthly'">Rekap transaksi per bulan dengan opening & closing balance</span>
                            <span x-show="config.type === 'yearly'">Aggregate dari 12 monthly closing</span>
                            <span x-show="config.type === 'audit'">Hitung ulang dari transaksi pertama untuk verifikasi</span>
                        </small>
                    </div>

                    <!-- Pilih Tahun -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tahun</label>
                        <select x-model="config.year" @change="resetPreview()" class="form-select">
                            <option value="">-- Pilih Tahun --</option>
                            <?php for($y = date('Y'); $y >= 2000; $y--): ?>
                                <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- Pilih Bulan -->
                    <div class="mb-3" x-show="config.type !== 'yearly'">
                        <label class="form-label fw-bold">Bulan</label>
                        <select x-model="config.month" @change="resetPreview()" class="form-select">
                            <option value="">-- Pilih Bulan --</option>
                            <option value="1" :selected="config.month === 1">Januari</option>
                            <option value="2" :selected="config.month === 2">Februari</option>
                            <option value="3" :selected="config.month === 3">Maret</option>
                            <option value="4" :selected="config.month === 4">April</option>
                            <option value="5" :selected="config.month === 5">Mei</option>
                            <option value="6" :selected="config.month === 6">Juni</option>
                            <option value="7" :selected="config.month === 7">Juli</option>
                            <option value="8" :selected="config.month === 8">Agustus</option>
                            <option value="9" :selected="config.month === 9">September</option>
                            <option value="10" :selected="config.month === 10">Oktober</option>
                            <option value="11" :selected="config.month === 11">November</option>
                            <option value="12" :selected="config.month === 12">Desember</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2 mt-4">
                        <button @click="preview()" class="btn btn-outline-primary" :disabled="loading">
                            <span x-show="!loading">
                                <i class="fas fa-eye me-2"></i>Preview Data
                            </span>
                            <span x-show="loading">
                                <span class="spinner-border spinner-border-sm me-2"></span>Loading...
                            </span>
                        </button>

                        <template x-if="showPreview && config.type !== 'audit'">
                            <button @click="generate()" class="btn btn-success" :disabled="loading">
                                <span x-show="!loading">
                                    <i class="fas fa-save me-2"></i>Generate & Simpan
                                </span>
                                <span x-show="loading">
                                    <span class="spinner-border spinner-border-sm me-2"></span>Processing...
                                </span>
                            </button>
                        </template>

                        <template x-if="config.type === 'monthly'">
                            <button @click="compareWithAudit()" class="btn btn-warning" :disabled="loading">
                                <i class="fas fa-search me-2"></i>Compare with Audit
                            </button>
                        </template>
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
                        </ul>

                        <p class="mb-2"><strong>Layer 3 - Audit:</strong></p>
                        <ul class="mb-0">
                            <li>Hitung dari transaksi pertama</li>
                            <li>Untuk verifikasi data</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Preview -->
        <div class="col-md-8">
            <template x-if="showPreview && previewData.length > 0">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-table me-2"></i>Preview Data
                            <span x-text="getPeriodeText()"></span>
                        </h5>
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
                                    <template x-for="row in previewData" :key="row.coa_code">
                                        <tr>
                                            <td><code x-text="row.coa_code"></code></td>
                                            <td class="small" x-text="row.coa_desc || '-'"></td>
                                            <td class="text-end fw-bold" x-text="formatNumber(config.type === 'audit' ? row.balance : row.closing_balance)"></td>
                                            <td class="text-center" x-text="row.jumlah_transaksi || 0"></td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <th colspan="2" class="text-end">TOTAL:</th>
                                        <th class="text-end" x-text="formatNumber(summary.total_balance)"></th>
                                        <th class="text-center" x-text="summary.total_transaksi.toLocaleString()"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        Menampilkan <span x-text="summary.total_coa"></span> akun COA
                        <span x-show="config.type === 'audit'" class="badge bg-warning text-dark ms-2">Audit - Tidak disimpan</span>
                    </div>
                </div>
            </template>

            <template x-if="!showPreview || previewData.length === 0">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-table fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada preview</h5>
                        <p class="text-muted">Klik "Preview Data" untuk melihat hasil</p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function closingProcess() {
    return {
        config: {
            type: 'monthly',
            year: new Date().getFullYear(),
            month: new Date().getMonth() + 1
        },
        loading: false,
        showPreview: false,
        previewData: [],
        summary: {
            total_coa: 0,
            total_debet: 0,
            total_kredit: 0,
            total_balance: 0,
            total_transaksi: 0
        },
        existingClosings: [],
        message: {
            show: false,
            type: 'info',
            text: ''
        },
        currentDate: '',

        init() {
            this.currentDate = new Date().toLocaleDateString('id-ID', { 
                day: 'numeric', 
                month: 'long', 
                year: 'numeric' 
            });
        },

        async preview() {
            this.loading = true;
            this.showMessage('info', 'Loading preview...');

            try {
                const response = await fetch('<?php echo e(route("closing.preview")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify(this.config)
                });

                const result = await response.json();

                if (result.success) {
                    this.previewData = result.data;
                    this.summary = result.summary;
                    this.showPreview = true;
                    this.showMessage('success', result.message);
                } else {
                    this.showMessage('error', result.message);
                }
            } catch (error) {
                this.showMessage('error', 'Error: ' + error.message);
            }

            this.loading = false;
        },

        async generate() {
            if (!confirm('Yakin ingin generate dan menyimpan closing ini?')) {
                return;
            }

            this.loading = true;

            try {
                const response = await fetch('<?php echo e(route("closing.generate")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify(this.config)
                });

                const result = await response.json();

                if (result.success) {
                    this.showMessage('success', result.message);
                    this.resetPreview();
                } else {
                    this.showMessage('error', result.message);
                }
            } catch (error) {
                this.showMessage('error', 'Error: ' + error.message);
            }

            this.loading = false;
        },

        async compareWithAudit() {
            this.loading = true;

            try {
                const response = await fetch('<?php echo e(route("closing.compare-audit")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify({
                        year: this.config.year,
                        month: this.config.month
                    })
                });

                const result = await response.json();

                if (result.success) {
                    if (result.has_discrepancies) {
                        alert(`Ditemukan ${result.data.length} discrepancy!`);
                    } else {
                        this.showMessage('success', 'Data closing sesuai dengan audit!');
                    }
                }
            } catch (error) {
                this.showMessage('error', 'Error: ' + error.message);
            }

            this.loading = false;
        },

        resetPreview() {
            this.showPreview = false;
            this.previewData = [];
            this.summary = {
                total_coa: 0,
                total_debet: 0,
                total_kredit: 0,
                total_balance: 0,
                total_transaksi: 0
            };
        },

        showMessage(type, text) {
            this.message = { show: true, type, text };
            setTimeout(() => {
                this.message.show = false;
            }, 5000);
        },

        formatNumber(value) {
            if (!value) return '0.00';
            return parseFloat(value).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        getPeriodeText() {
            const months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                          'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            if (this.config.type === 'monthly') {
                return ` - ${months[this.config.month]} ${this.config.year}`;
            } else if (this.config.type === 'yearly') {
                return ` - Tahun ${this.config.year}`;
            } else {
                return ` - Audit s/d ${months[this.config.month]} ${this.config.year}`;
            }
        }
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/closing-process.blade.php ENDPATH**/ ?>