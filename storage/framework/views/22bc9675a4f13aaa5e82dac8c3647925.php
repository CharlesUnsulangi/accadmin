

<?php $__env->startSection('title', 'Closing History'); ?>

<?php $__env->startSection('content'); ?>
<div x-data="closingHistory()" x-init="init()" class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1"><i class="fas fa-history me-2 text-success"></i>Closing History</h3>
                            <p class="text-muted mb-0">Daftar Closing yang Sudah Di-Generate</p>
                        </div>
                        <div>
                            <span class="badge bg-info fs-6" x-text="currentDate"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Pilih Tipe -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Tipe Closing</label>
                            <select x-model="filter.type" @change="loadData()" class="form-select">
                                <option value="monthly">Monthly Closing</option>
                                <option value="yearly">Yearly Closing</option>
                            </select>
                        </div>

                        <!-- Pilih Tahun -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Tahun</label>
                            <select x-model="filter.year" @change="loadData()" class="form-select">
                                <option value="">-- Semua Tahun --</option>
                                <?php for($y = date('Y'); $y >= 2000; $y--): ?>
                                    <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Pilih Bulan (Monthly only) -->
                        <div class="col-md-3" x-show="filter.type === 'monthly'">
                            <label class="form-label fw-bold">Bulan</label>
                            <select x-model="filter.month" @change="loadData()" class="form-select">
                                <option value="">-- Semua Bulan --</option>
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

                        <!-- Status -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Status</label>
                            <select x-model="filter.status" @change="loadData()" class="form-select">
                                <option value="">-- Semua Status --</option>
                                <option value="DRAFT">DRAFT</option>
                                <option value="ACTIVE">ACTIVE</option>
                                <option value="SUPERSEDED">SUPERSEDED</option>
                                <option value="ARCHIVED">ARCHIVED</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Loading data...</p>
    </div>

    <!-- Data Table -->
    <div x-show="!loading && data.length > 0" class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-table me-2 text-primary"></i>
                            Data Closing (<span x-text="data.length"></span> records)
                        </h5>
                        <div>
                            <button @click="exportExcel()" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel me-1"></i>Export Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="text-center" style="width: 50px">#</th>
                                    <th>Periode</th>
                                    <th>COA Code</th>
                                    <th>COA Description</th>
                                    <th class="text-end">Opening Debet</th>
                                    <th class="text-end">Opening Kredit</th>
                                    <th class="text-end">Mutasi Debet</th>
                                    <th class="text-end">Mutasi Kredit</th>
                                    <th class="text-end">Closing Debet</th>
                                    <th class="text-end">Closing Kredit</th>
                                    <th class="text-center">Transaksi</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Version</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in data" :key="item.id">
                                    <tr>
                                        <td class="text-center text-muted" x-text="index + 1"></td>
                                        <td>
                                            <span x-show="filter.type === 'monthly'" x-text="formatPeriode(item.closing_year, item.closing_month)"></span>
                                            <span x-show="filter.type === 'yearly'" x-text="item.closing_year"></span>
                                        </td>
                                        <td><code x-text="item.coa_code"></code></td>
                                        <td x-text="item.coa_desc"></td>
                                        <td class="text-end" x-text="formatNumber(item.opening_debet)"></td>
                                        <td class="text-end" x-text="formatNumber(item.opening_kredit)"></td>
                                        <td class="text-end" x-text="formatNumber(item.mutasi_debet)"></td>
                                        <td class="text-end" x-text="formatNumber(item.mutasi_kredit)"></td>
                                        <td class="text-end fw-bold" x-text="formatNumber(item.closing_debet)"></td>
                                        <td class="text-end fw-bold" x-text="formatNumber(item.closing_kredit)"></td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary" x-text="formatNumber(item.jumlah_transaksi)"></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge" 
                                                  :class="{
                                                      'bg-warning': item.version_status === 'DRAFT',
                                                      'bg-success': item.version_status === 'ACTIVE',
                                                      'bg-danger': item.version_status === 'SUPERSEDED',
                                                      'bg-secondary': item.version_status === 'ARCHIVED'
                                                  }"
                                                  x-text="item.version_status"></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info" x-text="'v' + item.version_number"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">TOTAL:</th>
                                    <th class="text-end" x-text="formatNumber(summary.total_opening_debet)"></th>
                                    <th class="text-end" x-text="formatNumber(summary.total_opening_kredit)"></th>
                                    <th class="text-end" x-text="formatNumber(summary.total_mutasi_debet)"></th>
                                    <th class="text-end" x-text="formatNumber(summary.total_mutasi_kredit)"></th>
                                    <th class="text-end fw-bold" x-text="formatNumber(summary.total_closing_debet)"></th>
                                    <th class="text-end fw-bold" x-text="formatNumber(summary.total_closing_kredit)"></th>
                                    <th class="text-center fw-bold" x-text="formatNumber(summary.total_transaksi)"></th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div x-show="!loading && data.length === 0" class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada data closing</h5>
                    <p class="text-muted">Silakan generate closing terlebih dahulu</p>
                    <a href="<?php echo e(route('closing.process')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Generate Closing
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js Component -->
<script>
function closingHistory() {
    return {
        filter: {
            type: 'monthly',
            year: new Date().getFullYear(),
            month: '',
            status: ''
        },
        loading: false,
        data: [],
        summary: {
            total_opening_debet: 0,
            total_opening_kredit: 0,
            total_mutasi_debet: 0,
            total_mutasi_kredit: 0,
            total_closing_debet: 0,
            total_closing_kredit: 0,
            total_transaksi: 0
        },
        currentDate: '',

        init() {
            this.currentDate = new Date().toLocaleDateString('id-ID', { 
                day: 'numeric', 
                month: 'long', 
                year: 'numeric' 
            });
            this.loadData();
        },

        async loadData() {
            this.loading = true;

            try {
                const params = new URLSearchParams();
                params.append('type', this.filter.type);
                if (this.filter.year) params.append('year', this.filter.year);
                if (this.filter.month) params.append('month', this.filter.month);
                if (this.filter.status) params.append('status', this.filter.status);

                const response = await fetch(`<?php echo e(route('closing.history.data')); ?>?${params}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    this.data = result.data;
                    this.calculateSummary();
                } else {
                    alert('Error: ' + result.message);
                }

            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memuat data');
            } finally {
                this.loading = false;
            }
        },

        calculateSummary() {
            this.summary = {
                total_opening_debet: this.data.reduce((sum, item) => sum + parseFloat(item.opening_debet || 0), 0),
                total_opening_kredit: this.data.reduce((sum, item) => sum + parseFloat(item.opening_kredit || 0), 0),
                total_mutasi_debet: this.data.reduce((sum, item) => sum + parseFloat(item.mutasi_debet || 0), 0),
                total_mutasi_kredit: this.data.reduce((sum, item) => sum + parseFloat(item.mutasi_kredit || 0), 0),
                total_closing_debet: this.data.reduce((sum, item) => sum + parseFloat(item.closing_debet || 0), 0),
                total_closing_kredit: this.data.reduce((sum, item) => sum + parseFloat(item.closing_kredit || 0), 0),
                total_transaksi: this.data.reduce((sum, item) => sum + parseInt(item.jumlah_transaksi || 0), 0)
            };
        },

        formatNumber(num) {
            if (!num) return '0';
            return parseFloat(num).toLocaleString('id-ID', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            });
        },

        formatPeriode(year, month) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return `${months[month - 1]} ${year}`;
        },

        exportExcel() {
            const params = new URLSearchParams();
            params.append('type', this.filter.type);
            if (this.filter.year) params.append('year', this.filter.year);
            if (this.filter.month) params.append('month', this.filter.month);
            if (this.filter.status) params.append('status', this.filter.status);

            window.location.href = `<?php echo e(route('closing.history.export')); ?>?${params}`;
        }
    };
}
</script>

<style>
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/closing-history.blade.php ENDPATH**/ ?>