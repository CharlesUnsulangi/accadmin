<div>
    <!-- Flash Messages -->
    <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo e(session('message')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <?php if(session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Header Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="card-title mb-1">
                        <i class="fas fa-sitemap me-2 text-primary"></i>COA Legacy Management
                    </h2>
                    <p class="text-muted mb-0">
                        <small>4-Level System: Main → Sub1 → Sub2 → COA Detail (ms_acc_coa)</small>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="<?php echo e(route('coa.modern')); ?>" class="btn btn-primary me-2">
                        <i class="fas fa-arrow-right me-1"></i>Modern View
                    </a>
                    <button wire:click="openAddModal('main')" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Add Main Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <!-- Search -->
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fas fa-search me-1"></i>Search
                    </label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        class="form-control" 
                        placeholder="Search code, description..."
                    >
                </div>

                <!-- Filter Main -->
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-filter me-1"></i>Main Category
                    </label>
                    <select wire:model.live="filterMain" class="form-select">
                        <option value="">All Main</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $mains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($id); ?>"><?php echo e($desc); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>

                <!-- Filter Sub1 -->
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-filter me-1"></i>Sub Category 1
                    </label>
                    <select wire:model.live="filterSub1" class="form-select">
                        <option value="">All Sub1</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $sub1s; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($id); ?>"><?php echo e($desc); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Main Categories</h6>
                    <h2 class="card-title mb-0"><?php echo e($coaMains->total()); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">All Main Categories</h6>
                    <h2 class="card-title mb-0"><?php echo e($mains->count()); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">All Sub1 Categories</h6>
                    <h2 class="card-title mb-0"><?php echo e($sub1s->count()); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">This Page</h6>
                    <h2 class="card-title mb-0"><?php echo e($coaMains->count()); ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Accordion Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="accordion" id="coaLegacyAccordion">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $coaMains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $main): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapseMain<?php echo e($main->coa_main_code); ?>" aria-expanded="false">
                                <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge bg-secondary font-monospace fs-6"><?php echo e($main->coa_main_code); ?></span>
                                        <strong class="fs-5"><?php echo e($main->coa_main_desc); ?></strong>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-secondary">
                                            Level 1: Main
                                        </span>
                                        <!--[if BLOCK]><![endif]--><?php if($main->coaSub1s && $main->coaSub1s->count() > 0): ?>
                                            <span class="badge bg-light text-dark border">
                                                <i class="fas fa-layer-group me-1"></i><?php echo e($main->coaSub1s->count()); ?> Sub1
                                            </span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapseMain<?php echo e($main->coa_main_code); ?>" class="accordion-collapse collapse" 
                             data-bs-parent="#coaLegacyAccordion">
                            <div class="accordion-body">
                                <!-- Main Category Info -->
                                <div class="card border-secondary mb-3">
                                    <div class="card-header bg-light border-bottom">
                                        <strong><i class="fas fa-info-circle me-2"></i>Main Category Information</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Code:</strong> <span class="badge bg-secondary"><?php echo e($main->coa_main_code); ?></span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>ID:</strong> <?php echo e($main->coa_main_id ?? '-'); ?>

                                            </div>
                                            <div class="col-md-4">
                                                <strong>Description:</strong> <?php echo e($main->coa_main_desc); ?>

                                            </div>
                                            <div class="col-md-2">
                                                <!--[if BLOCK]><![endif]--><?php if($main->rec_status == '1'): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-12 text-end">
                                                <button wire:click="openAddModal('sub1', '<?php echo e($main->coa_main_code); ?>', '<?php echo e($main->coa_main_desc); ?>')" 
                                                        class="btn btn-success btn-sm">
                                                    <i class="fas fa-plus me-1"></i>Add Sub1 Category under this Main
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sub1 Categories (Level 2) -->
                                <!--[if BLOCK]><![endif]--><?php if($main->coaSub1s && $main->coaSub1s->count() > 0): ?>
                                    <div class="accordion" id="accordionSub1Main<?php echo e($main->coa_main_code); ?>">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $main->coaSub1s; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" 
                                                            data-bs-target="#collapseSub1<?php echo e($sub1->coasub1_code); ?>" aria-expanded="false">
                                                        <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                                            <div class="d-flex align-items-center gap-3">
                                                                <span class="badge bg-secondary font-monospace"><?php echo e($sub1->coasub1_code); ?></span>
                                                                <strong><?php echo e($sub1->coasub1_desc); ?></strong>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="badge bg-secondary">Level 2: Sub1</span>
                                                                <!--[if BLOCK]><![endif]--><?php if($sub1->coaSub2s && $sub1->coaSub2s->count() > 0): ?>
                                                                    <span class="badge bg-light text-dark border">
                                                                        <i class="fas fa-layer-group me-1"></i><?php echo e($sub1->coaSub2s->count()); ?> Sub2
                                                                    </span>
                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                            </div>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapseSub1<?php echo e($sub1->coasub1_code); ?>" class="accordion-collapse collapse" 
                                                     data-bs-parent="#accordionSub1Main<?php echo e($main->coa_main_code); ?>">
                                                    <div class="accordion-body bg-light">
                                                        <!-- Sub1 Info with Add Button -->
                                                        <div class="card bg-white mb-3">
                                                            <div class="card-body py-2">
                                                                <div class="row align-items-center">
                                                                    <div class="col-md-8">
                                                                        <strong>Sub1 Code:</strong> <code><?php echo e($sub1->coasub1_code); ?></code> | 
                                                                        <strong>Description:</strong> <?php echo e($sub1->coasub1_desc); ?>

                                                                    </div>
                                                                    <div class="col-md-4 text-end">
                                                                        <button wire:click="openAddModal('sub2', '<?php echo e($sub1->coasub1_code); ?>', '<?php echo e($sub1->coasub1_desc); ?>')" 
                                                                                class="btn btn-success btn-sm">
                                                                            <i class="fas fa-plus me-1"></i>Add Sub2
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Sub2 Categories (Level 3) -->
                                                        <!--[if BLOCK]><![endif]--><?php if($sub1->coaSub2s && $sub1->coaSub2s->count() > 0): ?>
                                                            <div class="accordion" id="accordionSub2Sub1<?php echo e($sub1->coasub1_code); ?>">
                                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $sub1->coaSub2s; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <div class="accordion-item">
                                                                        <h2 class="accordion-header">
                                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                                                    data-bs-target="#collapseSub2<?php echo e($sub2->coasub2_code); ?>" aria-expanded="false">
                                                                                <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                                                                    <div class="d-flex align-items-center gap-3">
                                                                                        <span class="badge bg-dark font-monospace"><?php echo e($sub2->coasub2_code); ?></span>
                                                                                        <strong><?php echo e($sub2->coasub2_desc); ?></strong>
                                                                                    </div>
                                                                                    <div class="d-flex align-items-center gap-2">
                                                                                        <span class="badge bg-dark">Level 3: Sub2</span>
                                                                                        <!--[if BLOCK]><![endif]--><?php if($sub2->coas && $sub2->coas->count() > 0): ?>
                                                                                            <span class="badge bg-light text-dark border">
                                                                                                <i class="fas fa-layer-group me-1"></i><?php echo e($sub2->coas->count()); ?> COAs
                                                                                            </span>
                                                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                                    </div>
                                                                                </div>
                                                                            </button>
                                                                        </h2>
                                                                        <div id="collapseSub2<?php echo e($sub2->coasub2_code); ?>" class="accordion-collapse collapse" 
                                                                             data-bs-parent="#accordionSub2Sub1<?php echo e($sub1->coasub1_code); ?>">
                                                                            <div class="accordion-body">
                                                                                <!-- Sub2 Info with Add Button -->
                                                                                <div class="alert alert-light border mb-3">
                                                                                    <div class="row align-items-center">
                                                                                        <div class="col-md-8">
                                                                                            <strong>Sub2 Code:</strong> <code><?php echo e($sub2->coasub2_code); ?></code> | 
                                                                                            <strong>Description:</strong> <?php echo e($sub2->coasub2_desc); ?>

                                                                                        </div>
                                                                                        <div class="col-md-4 text-end">
                                                                                            <button wire:click="openAddModal('coa', '<?php echo e($sub2->coasub2_code); ?>', '<?php echo e($sub2->coasub2_desc); ?>')" 
                                                                                                    class="btn btn-success btn-sm">
                                                                                                <i class="fas fa-plus me-1"></i>Add Detail COA
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                
                                                                                <!-- Detail COAs (Level 4) -->
                                                                                <!--[if BLOCK]><![endif]--><?php if($sub2->coas && $sub2->coas->count() > 0): ?>
                                                                                    <div class="card">
                                                                                        <div class="card-header bg-light">
                                                                                            <strong><i class="fas fa-layer-group me-2"></i>Detail COAs (Level 4)</strong>
                                                                                        </div>
                                                                                        <div class="card-body p-0">
                                                                                            <div class="list-group list-group-flush">
                                                                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $sub2->coas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                    <div class="list-group-item">
                                                                                                        <div class="d-flex justify-content-between align-items-start">
                                                                                                            <div class="flex-grow-1">
                                                                                                                <div class="mb-2">
                                                                                                                    <span class="badge bg-dark font-monospace"><?php echo e($coa->coa_code); ?></span>
                                                                                                                    <!--[if BLOCK]><![endif]--><?php if($coa->rec_status == '1'): ?>
                                                                                                                        <span class="badge bg-success">Active</span>
                                                                                                                    <?php else: ?>
                                                                                                                        <span class="badge bg-secondary">Inactive</span>
                                                                                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                                                                </div>
                                                                                                                <div><strong><?php echo e($coa->coa_desc); ?></strong></div>
                                                                                                                <!--[if BLOCK]><![endif]--><?php if($coa->coa_note): ?>
                                                                                                                    <div class="mt-1 small text-muted">
                                                                                                                        <i class="fas fa-sticky-note me-1"></i><?php echo e($coa->coa_note); ?>

                                                                                                                    </div>
                                                                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                                                                <!--[if BLOCK]><![endif]--><?php if($coa->arus_kas_code): ?>
                                                                                                                    <div class="mt-1">
                                                                                                                        <span class="badge bg-secondary">Cash Flow: <?php echo e($coa->arus_kas_code); ?></span>
                                                                                                                    </div>
                                                                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php else: ?>
                                                                                    <div class="alert alert-light border mb-0">
                                                                                        <i class="fas fa-info-circle me-2"></i>No COA details under this Sub2
                                                                                    </div>
                                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="alert alert-light border mb-0">
                                                                <i class="fas fa-info-circle me-2"></i>No Sub2 categories under this Sub1
                                                            </div>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-light border mb-0">
                                        <i class="fas fa-info-circle me-2"></i>No Sub1 categories under this Main
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-0">No Main categories found</p>
                        <small class="text-muted">Try adjusting your filters</small>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                <?php echo e($coaMains->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    </div>

    <!-- Legend Card -->
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h6 class="card-title mb-3">
                <i class="fas fa-info-circle me-2"></i>Legacy System Hierarchy
            </h6>
            <div class="row">
                <div class="col-md-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-success">Level 1</span>
                        <small>Main Category (ms_acc_coa_main)</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-info">Level 2</span>
                        <small>Sub1 Category (ms_acc_coasub1)</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary">Level 3</span>
                        <small>Sub2 Category (ms_acc_coasub2)</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark">Level 4</span>
                        <small>Detail COAs (ms_acc_coa)</small>
                    </div>
                </div>
            </div>
            <div class="mt-3 pt-3 border-top">
                <small class="text-muted">
                    <i class="fas fa-lightbulb me-1"></i>
                    <strong>Tip:</strong> This page displays Level 3 (Sub2) with relationships to all parent levels and child COAs count.
                </small>
            </div>
        </div>
    </div>

    <!-- Add New Modal -->
    <!--[if BLOCK]><![endif]--><?php if($showAddModal): ?>
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-plus-circle me-2"></i>
                            Add New <?php echo e(strtoupper($addLevel)); ?>

                            <!--[if BLOCK]><![endif]--><?php if($parentCode): ?>
                                under <code class="text-white"><?php echo e($parentCode); ?></code>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeAddModal"></button>
                    </div>
                    <div class="modal-body">
                        <!--[if BLOCK]><![endif]--><?php if($parentCode): ?>
                            <div class="alert alert-info">
                                <strong><i class="fas fa-info-circle me-2"></i>Parent:</strong> 
                                <code><?php echo e($parentCode); ?></code> - <?php echo e($parentDesc); ?>

                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <form wire:submit.prevent="saveNew">
                            <div class="mb-3">
                                <label class="form-label">
                                    <strong>Code <span class="text-danger">*</span></strong>
                                </label>
                                <input type="text" 
                                       wire:model="newCode" 
                                       class="form-control <?php $__errorArgs = ['newCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       placeholder="Enter code (e.g., 101, A01, etc.)"
                                       required>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['newCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <strong>Description <span class="text-danger">*</span></strong>
                                </label>
                                <input type="text" 
                                       wire:model="newDesc" 
                                       class="form-control <?php $__errorArgs = ['newDesc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       placeholder="Enter description"
                                       required>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['newDesc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <!--[if BLOCK]><![endif]--><?php if($addLevel === 'coa'): ?>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>Note</strong>
                                    </label>
                                    <textarea wire:model="newNote" 
                                              class="form-control" 
                                              rows="3"
                                              placeholder="Additional notes (optional)"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>Cash Flow Code (Arus Kas)</strong>
                                    </label>
                                    <input type="text" 
                                           wire:model="newArusKas" 
                                           class="form-control" 
                                           placeholder="e.g., O (Operasional), I (Investasi), F (Financing)">
                                    <small class="text-muted">Common values: O, I, F</small>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Note:</strong> Make sure the code is unique and follows your COA numbering convention.
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeAddModal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-success" wire:click="saveNew">
                            <i class="fas fa-save me-1"></i>Save New <?php echo e(strtoupper($addLevel)); ?>

                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <style>
        .cursor-pointer {
            cursor: pointer;
            user-select: none;
        }
        .cursor-pointer:hover {
            background-color: rgba(0,0,0,0.05);
        }
    </style>
</div>
<?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/livewire/coa-legacy-bootstrap.blade.php ENDPATH**/ ?>