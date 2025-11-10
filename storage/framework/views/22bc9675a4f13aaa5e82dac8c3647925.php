

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

    <!-- View Mode Tabs -->
    <div class="row mb-3">
        <div class="col-12">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button 
                        class="nav-link" 
                        :class="viewMode === 'detail' ? 'active' : ''"
                        @click="viewMode = 'detail'"
                        type="button">
                        <i class="fas fa-list me-2"></i>Detail View
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button 
                        class="nav-link" 
                        :class="viewMode === 'group' ? 'active' : ''"
                        @click="viewMode = 'group'"
                        type="button">
                        <i class="fas fa-sitemap me-2"></i>Group View (Hierarchy)
                    </button>
                </li>
            </ul>
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
    <div x-show="!loading && data.length > 0 && viewMode === 'detail'" class="row">
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
                                    <th>
                                        COA Description
                                        <button @click="toggleAllHierarchy()" class="btn btn-sm btn-link p-0 ms-2" type="button" title="Expand/Collapse All">
                                            <i class="fas" :class="allExpanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                        </button>
                                    </th>
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
                                        <td>
                                            <div class="d-flex align-items-start">
                                                <button 
                                                    @click="item.hierarchyExpanded = !item.hierarchyExpanded" 
                                                    class="btn btn-sm btn-link p-0 me-2" 
                                                    type="button"
                                                    x-show="item.coa_main_desc || item.coasub1_desc || item.coasub2_desc"
                                                    :title="item.hierarchyExpanded ? 'Collapse' : 'Expand'">
                                                    <i class="fas" :class="item.hierarchyExpanded ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                                </button>
                                                <div class="flex-grow-1">
                                                    <div x-text="item.coa_desc" class="text-truncate" :title="item.coa_desc"></div>
                                                    
                                                    <!-- Hierarchy Details - Collapsible -->
                                                    <div x-show="item.hierarchyExpanded" 
                                                         x-transition:enter="transition ease-out duration-200"
                                                         x-transition:enter-start="opacity-0 transform scale-95"
                                                         x-transition:enter-end="opacity-100 transform scale-100"
                                                         x-transition:leave="transition ease-in duration-150"
                                                         x-transition:leave-start="opacity-100 transform scale-100"
                                                         x-transition:leave-end="opacity-0 transform scale-95"
                                                         class="mt-2 pt-2 border-top">
                                                        
                                                        <!-- Main Category -->
                                                        <div x-show="item.coa_main_desc" class="mb-2">
                                                            <small class="text-muted d-block">Main Category:</small>
                                                            <div class="ms-2">
                                                                <code class="text-primary" x-text="item.coa_main_code"></code>
                                                                <span class="ms-1" x-text="item.coa_main_desc"></span>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Sub Category 1 -->
                                                        <div x-show="item.coasub1_desc" class="mb-2">
                                                            <small class="text-muted d-block">Sub Category 1:</small>
                                                            <div class="ms-2">
                                                                <code class="text-info" x-text="item.coasub1_code"></code>
                                                                <span class="ms-1" x-text="item.coasub1_desc"></span>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Sub Category 2 -->
                                                        <div x-show="item.coasub2_desc" class="mb-2">
                                                            <small class="text-muted d-block">Sub Category 2:</small>
                                                            <div class="ms-2">
                                                                <code class="text-warning" x-text="item.coasub2_code"></code>
                                                                <span class="ms-1" x-text="item.coasub2_desc"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
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

    <!-- Group View (Hierarchy) -->
    <div x-show="!loading && data.length > 0 && viewMode === 'group'" class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-sitemap me-2 text-primary"></i>
                            Grouped by Hierarchy (<span x-text="Object.keys(groupedData).length"></span> Main Categories)
                        </h5>
                        <div>
                            <button @click="expandAllGroups()" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-expand-alt me-1"></i>Expand All
                            </button>
                            <button @click="collapseAllGroups()" class="btn btn-sm btn-outline-secondary me-2">
                                <i class="fas fa-compress-alt me-1"></i>Collapse All
                            </button>
                            <button @click="exportExcel()" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel me-1"></i>Export Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th style="width: 50px">#</th>
                                    <th>Hierarchy / COA</th>
                                    <th class="text-end" style="width: 150px">Opening Debet</th>
                                    <th class="text-end" style="width: 150px">Opening Kredit</th>
                                    <th class="text-end" style="width: 150px">Mutasi Debet</th>
                                    <th class="text-end" style="width: 150px">Mutasi Kredit</th>
                                    <th class="text-end" style="width: 150px">Closing Debet</th>
                                    <th class="text-end" style="width: 150px">Closing Kredit</th>
                                    <th class="text-center" style="width: 100px">Transaksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="row in flattenedRows" :key="row.key">
                                    <tr :class="row.class">
                                        <td class="text-center">
                                            <button 
                                                x-show="row.hasChildren"
                                                @click="toggleGroup(row.level, row.groupKey)" 
                                                class="btn btn-sm btn-link p-0"
                                                type="button">
                                                <i class="fas" :class="isGroupExpanded(row.level, row.groupKey) ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                            </button>
                                        </td>
                                        <td :style="'padding-left: ' + (row.indent * 1.5) + 'rem'">
                                            <i class="fas" :class="row.icon" :style="'color: ' + row.iconColor"></i>
                                            <code :class="row.codeClass" x-text="row.code"></code>
                                            <strong class="ms-2" x-text="row.desc"></strong>
                                        </td>
                                        <td class="text-end" x-text="formatNumber(row.opening_debet)"></td>
                                        <td class="text-end" x-text="formatNumber(row.opening_kredit)"></td>
                                        <td class="text-end" x-text="formatNumber(row.mutasi_debet)"></td>
                                        <td class="text-end" x-text="formatNumber(row.mutasi_kredit)"></td>
                                        <td class="text-end fw-bold" x-text="formatNumber(row.closing_debet)"></td>
                                        <td class="text-end fw-bold" x-text="formatNumber(row.closing_kredit)"></td>
                                        <td class="text-center">
                                            <span x-show="row.level === 'coa'" class="badge bg-secondary" x-text="formatNumber(row.transaksi)"></span>
                                            <span x-show="row.level !== 'coa'" x-text="formatNumber(row.transaksi)"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2" class="text-end">GRAND TOTAL:</th>
                                    <th class="text-end" x-text="formatNumber(summary.total_opening_debet)"></th>
                                    <th class="text-end" x-text="formatNumber(summary.total_opening_kredit)"></th>
                                    <th class="text-end" x-text="formatNumber(summary.total_mutasi_debet)"></th>
                                    <th class="text-end" x-text="formatNumber(summary.total_mutasi_kredit)"></th>
                                    <th class="text-end fw-bold" x-text="formatNumber(summary.total_closing_debet)"></th>
                                    <th class="text-end fw-bold" x-text="formatNumber(summary.total_closing_kredit)"></th>
                                    <th class="text-center fw-bold" x-text="formatNumber(summary.total_transaksi)"></th>
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
        allExpanded: false,
        viewMode: 'detail', // 'detail' or 'group'
        groupedData: {},
        expandedGroups: {
            main: {},
            sub1: {},
            sub2: {},
            coa: {}
        },

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
                    // Initialize hierarchyExpanded property for each item
                    this.data = result.data.map(item => ({
                        ...item,
                        hierarchyExpanded: false
                    }));
                    this.calculateSummary();
                    this.buildGroupedData();
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

        toggleAllHierarchy() {
            this.allExpanded = !this.allExpanded;
            // Update each item's hierarchyExpanded property reactively
            this.data.forEach(item => {
                item.hierarchyExpanded = this.allExpanded;
            });
        },

        buildGroupedData() {
            const grouped = {};
            
            this.data.forEach(item => {
                const mainCode = item.coa_main_code || 'NULL';
                const mainDesc = item.coa_main_desc || 'Tidak Ada Main Category';
                const sub1Code = item.coasub1_code || 'NULL';
                const sub1Desc = item.coasub1_desc || 'Tidak Ada Sub Category 1';
                const sub2Code = item.coasub2_code || 'NULL';
                const sub2Desc = item.coasub2_desc || 'Tidak Ada Sub Category 2';

                // Initialize main if not exists
                if (!grouped[mainCode]) {
                    grouped[mainCode] = {
                        desc: mainDesc,
                        sub1: {},
                        totals: {
                            opening_debet: 0,
                            opening_kredit: 0,
                            mutasi_debet: 0,
                            mutasi_kredit: 0,
                            closing_debet: 0,
                            closing_kredit: 0,
                            transaksi: 0
                        }
                    };
                }

                // Initialize sub1 if not exists
                if (!grouped[mainCode].sub1[sub1Code]) {
                    grouped[mainCode].sub1[sub1Code] = {
                        desc: sub1Desc,
                        sub2: {},
                        totals: {
                            opening_debet: 0,
                            opening_kredit: 0,
                            mutasi_debet: 0,
                            mutasi_kredit: 0,
                            closing_debet: 0,
                            closing_kredit: 0,
                            transaksi: 0
                        }
                    };
                }

                // Initialize sub2 if not exists
                if (!grouped[mainCode].sub1[sub1Code].sub2[sub2Code]) {
                    grouped[mainCode].sub1[sub1Code].sub2[sub2Code] = {
                        desc: sub2Desc,
                        coa: {},  // Changed from 'items' to 'coa' for grouping
                        totals: {
                            opening_debet: 0,
                            opening_kredit: 0,
                            mutasi_debet: 0,
                            mutasi_kredit: 0,
                            closing_debet: 0,
                            closing_kredit: 0,
                            transaksi: 0
                        }
                    };
                }

                // Group by COA code
                const coaCode = item.coa_code;
                const coaDesc = item.coa_desc || 'Tidak Ada Deskripsi';
                
                if (!grouped[mainCode].sub1[sub1Code].sub2[sub2Code].coa[coaCode]) {
                    grouped[mainCode].sub1[sub1Code].sub2[sub2Code].coa[coaCode] = {
                        desc: coaDesc,
                        items: [],
                        totals: {
                            opening_debet: 0,
                            opening_kredit: 0,
                            mutasi_debet: 0,
                            mutasi_kredit: 0,
                            closing_debet: 0,
                            closing_kredit: 0,
                            transaksi: 0
                        }
                    };
                }

                // Add item to coa
                grouped[mainCode].sub1[sub1Code].sub2[sub2Code].coa[coaCode].items.push(item);

                // Update totals
                const amounts = {
                    opening_debet: parseFloat(item.opening_debet || 0),
                    opening_kredit: parseFloat(item.opening_kredit || 0),
                    mutasi_debet: parseFloat(item.mutasi_debet || 0),
                    mutasi_kredit: parseFloat(item.mutasi_kredit || 0),
                    closing_debet: parseFloat(item.closing_debet || 0),
                    closing_kredit: parseFloat(item.closing_kredit || 0),
                    transaksi: parseInt(item.jumlah_transaksi || 0)
                };

                // Update coa totals
                Object.keys(amounts).forEach(key => {
                    grouped[mainCode].sub1[sub1Code].sub2[sub2Code].coa[coaCode].totals[key] += amounts[key];
                });

                // Update sub2 totals
                Object.keys(amounts).forEach(key => {
                    grouped[mainCode].sub1[sub1Code].sub2[sub2Code].totals[key] += amounts[key];
                });

                // Update sub1 totals
                Object.keys(amounts).forEach(key => {
                    grouped[mainCode].sub1[sub1Code].totals[key] += amounts[key];
                });

                // Update main totals
                Object.keys(amounts).forEach(key => {
                    grouped[mainCode].totals[key] += amounts[key];
                });
            });

            this.groupedData = grouped;
        },

        get flattenedRows() {
            const rows = [];
            
            // Sort main categories
            Object.keys(this.groupedData).sort().forEach(mainCode => {
                const main = this.groupedData[mainCode];
                
                // Add Main Category Row
                rows.push({
                    key: 'main-' + mainCode,
                    level: 'main',
                    groupKey: mainCode,
                    class: 'table-primary fw-bold',
                    hasChildren: true,
                    indent: 0,
                    icon: 'fa-folder me-2',
                    iconColor: '#0d6efd',
                    code: mainCode,
                    codeClass: 'text-primary',
                    desc: main.desc,
                    ...main.totals
                });
                
                // If Main is expanded, show Sub1
                if (this.isGroupExpanded('main', mainCode)) {
                    Object.keys(main.sub1).sort().forEach(sub1Code => {
                        const sub1 = main.sub1[sub1Code];
                        
                        // Add Sub1 Category Row
                        rows.push({
                            key: 'sub1-' + mainCode + '-' + sub1Code,
                            level: 'sub1',
                            groupKey: mainCode + '-' + sub1Code,
                            class: 'table-info',
                            hasChildren: true,
                            indent: 1,
                            icon: 'fa-folder-open me-2',
                            iconColor: '#0dcaf0',
                            code: sub1Code,
                            codeClass: 'text-info',
                            desc: sub1.desc,
                            ...sub1.totals
                        });
                        
                        // If Sub1 is expanded, show Sub2
                        if (this.isGroupExpanded('sub1', mainCode + '-' + sub1Code)) {
                            Object.keys(sub1.sub2).sort().forEach(sub2Code => {
                                const sub2 = sub1.sub2[sub2Code];
                                
                                // Add Sub2 Category Row
                                rows.push({
                                    key: 'sub2-' + mainCode + '-' + sub1Code + '-' + sub2Code,
                                    level: 'sub2',
                                    groupKey: mainCode + '-' + sub1Code + '-' + sub2Code,
                                    class: 'table-warning',
                                    hasChildren: true,
                                    indent: 2,
                                    icon: 'fa-file-alt me-2',
                                    iconColor: '#ffc107',
                                    code: sub2Code,
                                    codeClass: 'text-warning',
                                    desc: sub2.desc,
                                    ...sub2.totals
                                });
                                
                                // If Sub2 is expanded, show COA groups
                                if (this.isGroupExpanded('sub2', mainCode + '-' + sub1Code + '-' + sub2Code)) {
                                    Object.keys(sub2.coa).sort().forEach(coaCode => {
                                        const coa = sub2.coa[coaCode];
                                        
                                        // Add COA Group Row
                                        rows.push({
                                            key: 'coa-' + mainCode + '-' + sub1Code + '-' + sub2Code + '-' + coaCode,
                                            level: 'coa',
                                            groupKey: mainCode + '-' + sub1Code + '-' + sub2Code + '-' + coaCode,
                                            class: 'table-light',
                                            hasChildren: coa.items.length > 1,  // Only show expand if multiple items
                                            indent: 3,
                                            icon: 'fa-file me-2',
                                            iconColor: '#6c757d',
                                            code: coaCode,
                                            codeClass: 'text-secondary',
                                            desc: coa.desc + (coa.items.length > 1 ? ' (' + coa.items.length + ' records)' : ''),
                                            ...coa.totals
                                        });
                                        
                                        // If COA is expanded and has multiple items, show individual records
                                        if (coa.items.length > 1 && this.isGroupExpanded('coa', mainCode + '-' + sub1Code + '-' + sub2Code + '-' + coaCode)) {
                                            coa.items.forEach((item, index) => {
                                                rows.push({
                                                    key: 'coa-item-' + item.id,
                                                    level: 'coa-item',
                                                    groupKey: null,
                                                    class: '',
                                                    hasChildren: false,
                                                    indent: 4,
                                                    icon: 'fa-circle me-2',
                                                    iconColor: '#adb5bd',
                                                    code: '',
                                                    codeClass: '',
                                                    desc: this.formatPeriodeDetail(item),
                                                    opening_debet: item.opening_debet,
                                                    opening_kredit: item.opening_kredit,
                                                    mutasi_debet: item.mutasi_debet,
                                                    mutasi_kredit: item.mutasi_kredit,
                                                    closing_debet: item.closing_debet,
                                                    closing_kredit: item.closing_kredit,
                                                    transaksi: item.jumlah_transaksi
                                                });
                                            });
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            });
            
            return rows;
        },

        formatPeriodeDetail(item) {
            if (item.closing_month) {
                return 'Detail: ' + this.formatPeriode(item.closing_year, item.closing_month);
            } else {
                return 'Detail: Year ' + item.closing_year;
            }
        },

        toggleGroup(level, key) {
            // Create a new object to trigger reactivity
            const newState = { ...this.expandedGroups };
            if (!newState[level][key]) {
                newState[level][key] = true;
            } else {
                newState[level][key] = false;
            }
            this.expandedGroups = newState;
        },

        isGroupExpanded(level, key) {
            return this.expandedGroups[level][key] === true;
        },

        expandAllGroups() {
            // Create new object for reactivity
            const newState = {
                main: {},
                sub1: {},
                sub2: {},
                coa: {}
            };
            
            // Expand all main categories
            Object.keys(this.groupedData).forEach(mainCode => {
                newState.main[mainCode] = true;
                
                // Expand all sub1 categories
                Object.keys(this.groupedData[mainCode].sub1).forEach(sub1Code => {
                    newState.sub1[mainCode + '-' + sub1Code] = true;
                    
                    // Expand all sub2 categories
                    Object.keys(this.groupedData[mainCode].sub1[sub1Code].sub2).forEach(sub2Code => {
                        newState.sub2[mainCode + '-' + sub1Code + '-' + sub2Code] = true;
                        
                        // Expand all coa categories (if has multiple items)
                        Object.keys(this.groupedData[mainCode].sub1[sub1Code].sub2[sub2Code].coa).forEach(coaCode => {
                            const coa = this.groupedData[mainCode].sub1[sub1Code].sub2[sub2Code].coa[coaCode];
                            if (coa.items.length > 1) {
                                newState.coa[mainCode + '-' + sub1Code + '-' + sub2Code + '-' + coaCode] = true;
                            }
                        });
                    });
                });
            });
            
            this.expandedGroups = newState;
        },

        collapseAllGroups() {
            this.expandedGroups = {
                main: {},
                sub1: {},
                sub2: {},
                coa: {}
            };
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