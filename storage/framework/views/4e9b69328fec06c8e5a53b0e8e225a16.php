<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-money-check-alt text-primary me-2"></i>
                Manajemen Buku Cheque
            </h2>
            <p class="text-muted mb-0">Kelola buku cheque dan lembar-lembar cek</p>
        </div>
        <div>
            <button class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Buku Cheque
            </button>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <!-- Search -->
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-search me-1"></i>Pencarian
                    </label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        class="form-control" 
                        placeholder="Cari kode, deskripsi, bank, rekening..."
                    >
                </div>

                <!-- Bank Filter -->
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-university me-1"></i>Bank
                    </label>
                    <select wire:model.live="filterBank" class="form-select">
                        <option value="">Semua Bank</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $bankList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($bank); ?>"><?php echo e($bank); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>

                <!-- COA Filter -->
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-list me-1"></i>COA
                    </label>
                    <select wire:model.live="filterCoa" class="form-select">
                        <option value="">Semua COA</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $coaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($coa); ?>"><?php echo e($coa); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>

                <!-- Type Filter -->
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-tag me-1"></i>Tipe
                    </label>
                    <select wire:model.live="filterType" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="GIRO">Giro</option>
                        <option value="CEK">Cek</option>
                    </select>
                </div>

                <!-- Clear Filters -->
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-2"></i>Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Buku Cheque</h6>
                    <h3 class="card-title mb-0"><?php echo e(number_format($summary->total_books)); ?></h3>
                    <small class="opacity-75">buku cek terdaftar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Lembar Cek</h6>
                    <h3 class="card-title mb-0"><?php echo e(number_format($summary->total_cheques)); ?></h3>
                    <small class="opacity-75">lembar cek</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Cek Tersedia</h6>
                    <h3 class="card-title mb-0"><?php echo e(number_format($summary->available_cheques)); ?></h3>
                    <small class="opacity-75">dari <?php echo e(number_format($summary->total_cheques)); ?> lembar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Nilai</h6>
                    <h3 class="card-title mb-0">Rp <?php echo e(number_format($summary->total_value, 0, ',', '.')); ?></h3>
                    <small class="opacity-75">nilai semua cek</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Cheque Books Accordion -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <strong><?php echo e($chequeBooks->total()); ?> Buku Cheque</strong>
                    <small class="text-muted ms-2">Menampilkan <?php echo e($chequeBooks->firstItem() ?? 0); ?> - <?php echo e($chequeBooks->lastItem() ?? 0); ?></small>
                </div>
                <select wire:model.live="perPage" class="form-select w-auto">
                    <option value="50">50 / halaman</option>
                    <option value="100">100 / halaman</option>
                    <option value="200">200 / halaman</option>
                    <option value="300">300 / halaman</option>
                </select>
            </div>

            <div class="accordion" id="chequeAccordion">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $chequeBooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $totalCheques = $book->details->count();
                        $availableCheques = $book->details->where('cheque_status', 'AVAILABLE')->count();
                        $usedCheques = $book->details->where('cheque_status', 'USED')->count();
                        $voidCheques = $book->details->where('cheque_status', 'VOID')->count();
                        $totalValue = $book->details->sum('cheque_value');
                    ?>
                    <div class="accordion-item mb-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse<?php echo e($loop->index); ?>" aria-expanded="false">
                                <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                    <div class="d-flex align-items-center gap-3 flex-grow-1">
                                        <div>
                                            <span class="badge bg-dark font-monospace">
                                                <?php echo e($book->cheque_code_h); ?>

                                            </span>
                                            <div class="small text-muted"><?php echo e($book->cheque_resino); ?></div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <strong><?php echo e($book->cheque_desc); ?></strong>
                                            <div class="small text-muted">
                                                <i class="fas fa-university me-1"></i><?php echo e($book->cheque_bank); ?>

                                                <!--[if BLOCK]><![endif]--><?php if($book->cheque_cabang): ?>
                                                    - Cabang <?php echo e($book->cheque_cabang); ?>

                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-credit-card me-1"></i><?php echo e($book->cheque_rek); ?>

                                        </span>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i><?php echo e($availableCheques); ?> Tersedia
                                        </span>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-times-circle me-1"></i><?php echo e($usedCheques); ?> Terpakai
                                        </span>
                                        <!--[if BLOCK]><![endif]--><?php if($voidCheques > 0): ?>
                                            <span class="badge bg-danger">
                                                <i class="fas fa-ban me-1"></i><?php echo e($voidCheques); ?> Void
                                            </span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-list me-1"></i><?php echo e($totalCheques); ?> lembar
                                        </span>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse<?php echo e($loop->index); ?>" class="accordion-collapse collapse" 
                             data-bs-parent="#chequeAccordion">
                            <div class="accordion-body p-0">
                                <!-- Book Info -->
                                <div class="bg-light p-3 border-bottom">
                                    <div class="row small">
                                        <div class="col-md-3">
                                            <strong>COA Code:</strong> 
                                            <span 
                                                class="badge bg-primary cursor-pointer ms-1"
                                                wire:click="filterByCoa('<?php echo e($book->cheque_coacode); ?>')"
                                                title="Klik untuk filter COA ini"
                                            >
                                                <?php echo e($book->cheque_coacode); ?>

                                            </span>
                                            <!--[if BLOCK]><![endif]--><?php if($book->coa): ?>
                                                <div class="text-muted"><?php echo e($book->coa->coa_desc); ?></div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Range Nomor:</strong> <?php echo e($book->cheque_startno); ?> - <?php echo e($book->cheque_endno); ?>

                                        </div>
                                        <div class="col-md-3">
                                            <strong>Tipe:</strong> 
                                            <span class="badge bg-info text-white"><?php echo e($book->cheque_type ?? '-'); ?></span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Total Nilai:</strong> Rp <?php echo e(number_format($totalValue, 0, ',', '.')); ?>

                                        </div>
                                    </div>
                                </div>

                                <!-- Cheques Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50">#</th>
                                                <th width="150">Nomor Cek</th>
                                                <th width="120">Tanggal</th>
                                                <th>Tujuan</th>
                                                <th>Catatan</th>
                                                <th class="text-end" width="150">Nilai Awal</th>
                                                <th class="text-end" width="150">Nilai</th>
                                                <th class="text-center" width="120">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!--[if BLOCK]><![endif]--><?php $__empty_2 = true; $__currentLoopData = $book->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                <tr>
                                                    <td class="text-center"><?php echo e($loop->iteration); ?></td>
                                                    <td class="font-monospace">
                                                        <strong><?php echo e($detail->cheque_code_d); ?></strong>
                                                    </td>
                                                    <td>
                                                        <!--[if BLOCK]><![endif]--><?php if($detail->cheque_date): ?>
                                                            <i class="fas fa-calendar me-1 text-muted"></i>
                                                            <?php echo e($detail->cheque_date->format('d/m/Y')); ?>

                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </td>
                                                    <td><?php echo e($detail->cheque_purpose ?? '-'); ?></td>
                                                    <td>
                                                        <!--[if BLOCK]><![endif]--><?php if($detail->cheque_note): ?>
                                                            <small class="text-muted"><?php echo e(Str::limit($detail->cheque_note, 40)); ?></small>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </td>
                                                    <td class="text-end font-monospace">
                                                        <!--[if BLOCK]><![endif]--><?php if($detail->cheque_value_start): ?>
                                                            Rp <?php echo e(number_format($detail->cheque_value_start, 0, ',', '.')); ?>

                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </td>
                                                    <td class="text-end font-monospace">
                                                        <!--[if BLOCK]><![endif]--><?php if($detail->cheque_value): ?>
                                                            <strong>Rp <?php echo e(number_format($detail->cheque_value, 0, ',', '.')); ?></strong>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge <?php echo e($detail->status_badge_class); ?>">
                                                            <?php echo e($detail->status_label); ?>

                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted py-4">
                                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                                        <div>Tidak ada lembar cek dalam buku ini</div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td colspan="5" class="text-end"><strong>Total:</strong></td>
                                                <td class="text-end font-monospace">
                                                    <strong>Rp <?php echo e(number_format($book->details->sum('cheque_value_start'), 0, ',', '.')); ?></strong>
                                                </td>
                                                <td class="text-end font-monospace">
                                                    <strong>Rp <?php echo e(number_format($totalValue, 0, ',', '.')); ?></strong>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-info text-white"><?php echo e($totalCheques); ?> lembar</span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-money-check-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada buku cheque</h5>
                        <p class="text-muted">Silakan tambah buku cheque baru atau sesuaikan filter pencarian</p>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!-- Pagination -->
            <!--[if BLOCK]><![endif]--><?php if($chequeBooks->hasPages()): ?>
                <div class="mt-4">
                    <?php echo e($chequeBooks->links()); ?>

                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="position-fixed top-50 start-50 translate-middle">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    
    <style>
        .cursor-pointer {
            cursor: pointer;
        }
        .cursor-pointer:hover {
            opacity: 0.8;
        }
    </style>
</div>
<?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/livewire/cheque-management-bootstrap.blade.php ENDPATH**/ ?>