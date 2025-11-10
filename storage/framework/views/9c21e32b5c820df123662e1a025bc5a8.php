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
                            <h3 class="mb-0"><?php echo e(number_format($stats['total'])); ?></h3>
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
                            <h3 class="mb-0"><?php echo e(number_format($stats['pending'])); ?></h3>
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
                            <h3 class="mb-0"><?php echo e(number_format($stats['approved'])); ?></h3>
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
                            <h4 class="mb-0">Rp <?php echo e(number_format($stats['total_value'], 2, ',', '.')); ?></h4>
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
                    <button class="nav-link <?php echo e($activeTab === 'all' ? 'active' : ''); ?>" 
                            wire:click="switchTab('all')" 
                            type="button">
                        <i class="fas fa-list me-2"></i>Semua Transaksi
                        <span class="badge bg-primary ms-2"><?php echo e(number_format($stats['total'])); ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php echo e($activeTab === 'orphan' ? 'active' : ''); ?>" 
                            wire:click="switchTab('orphan')" 
                            type="button">
                        <i class="fas fa-unlink me-2"></i>Orphan (Tanpa Jurnal)
                        <span class="badge bg-warning ms-2"><?php echo e(number_format($stats['orphan'])); ?></span>
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
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($status); ?>"><?php echo e($status); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
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
    <!--[if BLOCK]><![endif]--><?php if($activeTab === 'orphan'): ?>
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
        <div>
            <h5 class="alert-heading mb-1">Data Orphan - Transaksi Tanpa Link Jurnal</h5>
            <p class="mb-0">
                Menampilkan <strong><?php echo e(number_format($stats['orphan'])); ?></strong> transaksi cheque yang tidak memiliki link ke jurnal transaksi.
                Data ini perlu diperbaiki atau dihubungkan ke jurnal yang sesuai.
            </p>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Transactions List -->
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0">
                    <i class="fas fa-table me-2"></i>
                    <!--[if BLOCK]><![endif]--><?php if($activeTab === 'orphan'): ?>
                        Daftar Transaksi Orphan
                    <?php else: ?>
                        Daftar Transaksi Cheque
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
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
                                <!--[if BLOCK]><![endif]--><?php if($sortField === 'transcheque_code'): ?>
                                    <i class="fas fa-sort-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?> ms-1"></i>
                                <?php else: ?>
                                    <i class="fas fa-sort ms-1 text-muted"></i>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </th>
                            <th wire:click="sortBy('transcheque_date')" style="cursor: pointer;">
                                Tanggal
                                <!--[if BLOCK]><![endif]--><?php if($sortField === 'transcheque_date'): ?>
                                    <i class="fas fa-sort-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?> ms-1"></i>
                                <?php else: ?>
                                    <i class="fas fa-sort ms-1 text-muted"></i>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </th>
                            <th wire:click="sortBy('transcheque_vendor')" style="cursor: pointer;">
                                Vendor
                                <!--[if BLOCK]><![endif]--><?php if($sortField === 'transcheque_vendor'): ?>
                                    <i class="fas fa-sort-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?> ms-1"></i>
                                <?php else: ?>
                                    <i class="fas fa-sort ms-1 text-muted"></i>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </th>
                            <th>Doc/Keterangan</th>
                            <th wire:click="sortBy('transcheque_value')" class="text-end" style="cursor: pointer;">
                                Value
                                <!--[if BLOCK]><![endif]--><?php if($sortField === 'transcheque_value'): ?>
                                    <i class="fas fa-sort-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?> ms-1"></i>
                                <?php else: ?>
                                    <i class="fas fa-sort ms-1 text-muted"></i>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </th>
                            <th class="text-center">Jumlah Cheque</th>
                            <th wire:click="sortBy('transcheque_status')" class="text-center" style="cursor: pointer;">
                                Status
                                <!--[if BLOCK]><![endif]--><?php if($sortField === 'transcheque_status'): ?>
                                    <i class="fas fa-sort-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?> ms-1"></i>
                                <?php else: ?>
                                    <i class="fas fa-sort ms-1 text-muted"></i>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $trans): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="text-muted"><?php echo e($transactions->firstItem() + $index); ?></td>
                                <td>
                                    <div>
                                        <code class="text-primary"><?php echo e($trans->transcheque_code); ?></code>
                                        <!--[if BLOCK]><![endif]--><?php if($trans->transcheque_transmaincode): ?>
                                            <br><small class="text-success">
                                                <i class="fas fa-link"></i> <?php echo e($trans->transcheque_transmaincode); ?>

                                            </small>
                                        <?php else: ?>
                                            <br><small class="text-danger">
                                                <i class="fas fa-unlink"></i> No Jurnal
                                            </small>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </td>
                                <td>
                                    <small><?php echo e(\Carbon\Carbon::parse($trans->transcheque_date)->format('d/m/Y')); ?></small>
                                </td>
                                <td>
                                    <strong><?php echo e($trans->transcheque_vendor ?? '-'); ?></strong>
                                </td>
                                <td>
                                    <div>
                                        <!--[if BLOCK]><![endif]--><?php if($trans->transcheque_doc): ?>
                                            <span class="badge bg-secondary"><?php echo e($trans->transcheque_doc); ?></span><br>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <small class="text-muted"><?php echo e($trans->transcheque_desc ?? '-'); ?></small>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">Rp <?php echo e(number_format($trans->transcheque_value, 2, ',', '.')); ?></strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?php echo e($trans->cheque_count ?? 0); ?> cheque</span>
                                </td>
                                <td class="text-center">
                                    <?php
                                        $badgeClass = match($trans->transcheque_status) {
                                            'PENDING' => 'bg-warning',
                                            'APPROVED' => 'bg-success',
                                            'PAID' => 'bg-info',
                                            'CANCELLED' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    ?>
                                    <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($trans->transcheque_status ?? 'N/A'); ?></span>
                                </td>
                                <td class="text-center">
                                    <button wire:click="viewDetail('<?php echo e($trans->rec_comcode); ?>', '<?php echo e($trans->rec_areacode); ?>', '<?php echo e($trans->transcheque_code); ?>')" 
                                            class="btn btn-sm btn-outline-primary"
                                            title="View Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-inbox fs-1 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Tidak ada data transaksi cheque</p>
                                </td>
                            </tr>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                <?php echo e($transactions->links()); ?>

            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <!--[if BLOCK]><![endif]--><?php if($showDetailModal && !empty($selectedTransaction)): ?>
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-file-invoice me-2"></i>
                            Detail Transaksi Cheque: <code class="text-white"><?php echo e($selectedTransaction['transcheque_code']); ?></code>
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
                                                <td><strong><?php echo e($selectedTransaction['transcheque_code']); ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Company/Area:</td>
                                                <td><?php echo e($selectedTransaction['rec_comcode']); ?> / <?php echo e($selectedTransaction['rec_areacode']); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Tanggal:</td>
                                                <td><?php echo e(\Carbon\Carbon::parse($selectedTransaction['transcheque_date'])->format('d F Y')); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Vendor:</td>
                                                <td><strong><?php echo e($selectedTransaction['transcheque_vendor'] ?? '-'); ?></strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="text-muted" style="width: 180px;">Link Jurnal:</td>
                                                <td>
                                                    <!--[if BLOCK]><![endif]--><?php if(!empty($selectedTransaction['transcheque_transmaincode'])): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-link me-1"></i>
                                                            <?php echo e($selectedTransaction['transcheque_transmaincode']); ?>

                                                        </span>
                                                        <!--[if BLOCK]><![endif]--><?php if(!empty($selectedTransaction['transmain_codetransaksi'])): ?>
                                                            <br><small class="text-muted"><?php echo e($selectedTransaction['transmain_codetransaksi']); ?> - <?php echo e($selectedTransaction['transmain_desc']); ?></small>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Belum ada jurnal</span>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Document:</td>
                                                <td><?php echo e($selectedTransaction['transcheque_doc'] ?? '-'); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Status:</td>
                                                <td>
                                                    <?php
                                                        $badgeClass = match($selectedTransaction['transcheque_status']) {
                                                            'PENDING' => 'bg-warning',
                                                            'APPROVED' => 'bg-success',
                                                            'PAID' => 'bg-info',
                                                            'CANCELLED' => 'bg-danger',
                                                            default => 'bg-secondary',
                                                        };
                                                    ?>
                                                    <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($selectedTransaction['transcheque_status']); ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Total Value:</td>
                                                <td><strong class="text-success">Rp <?php echo e(number_format($selectedTransaction['transcheque_value'], 2, ',', '.')); ?></strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php if(!empty($selectedTransaction['transcheque_desc'])): ?>
                                <div class="mt-2">
                                    <strong class="text-muted">Keterangan:</strong>
                                    <p class="mb-0"><?php echo e($selectedTransaction['transcheque_desc']); ?></p>
                                </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>

                        <!-- Cheque Details -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-money-check-alt me-2"></i>Detail Cheque Digunakan 
                                    <span class="badge bg-primary ms-2"><?php echo e(count($transactionDetails)); ?> cheque</span>
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
                                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $transactionDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td class="text-muted"><?php echo e($index + 1); ?></td>
                                                    <td>
                                                        <code class="text-primary"><?php echo e(is_array($detail) ? $detail['transcheque_no'] : $detail->transcheque_no); ?></code>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <small class="text-muted"><?php echo e(is_array($detail) ? $detail['transcheque_code_h'] : $detail->transcheque_code_h); ?></small><br>
                                                            <strong><?php echo e(is_array($detail) ? ($detail['cheque_book_desc'] ?? '-') : ($detail->cheque_book_desc ?? '-')); ?></strong>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong><?php echo e(is_array($detail) ? ($detail['cheque_bank'] ?? '-') : ($detail->cheque_bank ?? '-')); ?></strong><br>
                                                            <small class="text-muted"><?php echo e(is_array($detail) ? ($detail['cheque_rek'] ?? '-') : ($detail->cheque_rek ?? '-')); ?></small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <code><?php echo e(is_array($detail) ? $detail['transcheque_coa'] : $detail->transcheque_coa); ?></code><br>
                                                            <small><?php echo e(is_array($detail) ? ($detail['coa_desc'] ?? '-') : ($detail->coa_desc ?? '-')); ?></small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            $dateDoc = is_array($detail) ? $detail['transcheque_datedoc'] : $detail->transcheque_datedoc;
                                                        ?>
                                                        <small><?php echo e($dateDoc ? \Carbon\Carbon::parse($dateDoc)->format('d/m/Y') : '-'); ?></small>
                                                    </td>
                                                    <td class="text-end">
                                                        <strong>Rp <?php echo e(number_format(is_array($detail) ? $detail['transcheque_value'] : $detail->transcheque_value, 2, ',', '.')); ?></strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php
                                                            $chequeStatus = is_array($detail) ? $detail['cheque_status'] : $detail->cheque_status;
                                                            $statusBadge = match($chequeStatus) {
                                                                'AVAILABLE' => 'bg-success',
                                                                'USED' => 'bg-info',
                                                                'VOID' => 'bg-danger',
                                                                default => 'bg-secondary',
                                                            };
                                                        ?>
                                                        <span class="badge <?php echo e($statusBadge); ?>"><?php echo e($chequeStatus ?? 'N/A'); ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted py-3">
                                                        Tidak ada detail cheque
                                                    </td>
                                                </tr>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </tbody>
                                        <!--[if BLOCK]><![endif]--><?php if(count($transactionDetails) > 0): ?>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="6" class="text-end">Total:</th>
                                                <th class="text-end">
                                                    <?php
                                                        $total = 0;
                                                        foreach($transactionDetails as $d) {
                                                            $total += is_array($d) ? $d['transcheque_value'] : $d->transcheque_value;
                                                        }
                                                    ?>
                                                    Rp <?php echo e(number_format($total, 2, ',', '.')); ?>

                                                </th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Jurnal Entries Section -->
                        <!--[if BLOCK]><![endif]--><?php if(!empty($jurnalData) && count($jurnalData) > 0): ?>
                        <div class="card mt-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-book me-2"></i>Jurnal Entries (COA Details)
                                    <span class="badge bg-info ms-2"><?php echo e(count($jurnalData)); ?> entries</span>
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
                                            <?php
                                                $totalDebet = 0;
                                                $totalKredit = 0;
                                            ?>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $jurnalData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $debet = is_array($entry) ? ($entry['transcoa_debet'] ?? 0) : ($entry->transcoa_debet ?? 0);
                                                    $kredit = is_array($entry) ? ($entry['transcoa_kredit'] ?? 0) : ($entry->transcoa_kredit ?? 0);
                                                    $totalDebet += $debet;
                                                    $totalKredit += $kredit;
                                                ?>
                                                <tr>
                                                    <td class="text-muted"><?php echo e($index + 1); ?></td>
                                                    <td>
                                                        <code class="text-primary"><?php echo e(is_array($entry) ? ($entry['transcoa_coa'] ?? '-') : ($entry->transcoa_coa ?? '-')); ?></code>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo e(is_array($entry) ? ($entry['coa_desc'] ?? '-') : ($entry->coa_desc ?? '-')); ?></strong>
                                                    </td>
                                                    <td class="text-end">
                                                        <!--[if BLOCK]><![endif]--><?php if($debet > 0): ?>
                                                            <strong class="text-success">Rp <?php echo e(number_format($debet, 2, ',', '.')); ?></strong>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </td>
                                                    <td class="text-end">
                                                        <!--[if BLOCK]><![endif]--><?php if($kredit > 0): ?>
                                                            <strong class="text-danger">Rp <?php echo e(number_format($kredit, 2, ',', '.')); ?></strong>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            <tr class="table-secondary">
                                                <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                                                <td class="text-end">
                                                    <strong class="text-success">Rp <?php echo e(number_format($totalDebet, 2, ',', '.')); ?></strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="text-danger">Rp <?php echo e(number_format($totalKredit, 2, ',', '.')); ?></strong>
                                                </td>
                                            </tr>
                                            <?php
                                                $balance = $totalDebet - $totalKredit;
                                            ?>
                                            <!--[if BLOCK]><![endif]--><?php if(abs($balance) > 0.01): ?>
                                            <tr class="table-warning">
                                                <td colspan="3" class="text-end"><strong>BALANCE (D-K):</strong></td>
                                                <td colspan="2" class="text-end">
                                                    <strong class="text-warning">Rp <?php echo e(number_format($balance, 2, ',', '.')); ?></strong>
                                                    <small class="text-danger ms-2"><i class="fas fa-exclamation-triangle"></i> Not balanced!</small>
                                                </td>
                                            </tr>
                                            <?php else: ?>
                                            <tr class="table-success">
                                                <td colspan="5" class="text-center">
                                                    <strong class="text-success"><i class="fas fa-check-circle"></i> Jurnal Balanced</strong>
                                                </td>
                                            </tr>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php elseif(!empty($selectedTransaction['transcheque_transmaincode'])): ?>
                        <div class="card mt-3">
                            <div class="card-body text-center text-muted py-4">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <p class="mb-0">Link jurnal tersedia tapi detail COA tidak ditemukan</p>
                                <small>Transmain Code: <code><?php echo e($selectedTransaction['transcheque_transmaincode']); ?></code></small>
                            </div>
                        </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetailModal">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/livewire/transaksi-cheque-management.blade.php ENDPATH**/ ?>