<div>
    <!-- Flash Message -->
    <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999;" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo e(session('message')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <?php if(session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999;" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Header -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1 fw-bold text-primary">
                        <i class="fas fa-layer-group me-2"></i>COA Modern Management
                    </h2>
                    <p class="text-muted mb-0">H1-H6 Flexible Hierarchy System</p>
                </div>
                <div>
                    <a href="<?php echo e(route('coa.legacy')); ?>" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Legacy View
                    </a>
                    <button type="button" class="btn btn-success" wire:click="create">
                        <i class="fas fa-plus me-1"></i>Add New COA
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" wire:model.live.debounce.300ms="search" 
                               placeholder="Search COA Code, Description, Hierarchy...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="perPage">
                        <option value="10">10 per page</option>
                        <option value="15">15 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-text opacity-75 mb-1">Total COA</p>
                            <h3 class="card-title mb-0 fw-bold"><?php echo e($coas->total()); ?></h3>
                        </div>
                        <i class="fas fa-database fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-text opacity-75 mb-1">Current Page</p>
                            <h3 class="card-title mb-0 fw-bold"><?php echo e($coas->currentPage()); ?></h3>
                        </div>
                        <i class="fas fa-file-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-text opacity-75 mb-1">Showing</p>
                            <h3 class="card-title mb-0 fw-bold"><?php echo e($coas->count()); ?></h3>
                        </div>
                        <i class="fas fa-eye fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>COA Code</th>
                            <th>Description</th>
                            <th>Hierarchy (H1-H6)</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $coas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><code class="fw-bold"><?php echo e($coa->coa_code); ?></code></td>
                                <td><?php echo e($coa->coa_desc); ?></td>
                                <td>
                                    <!--[if BLOCK]><![endif]--><?php if($coa->desc_h1): ?>
                                        <span class="badge bg-primary me-1">H1: <?php echo e($coa->desc_h1); ?></span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <!--[if BLOCK]><![endif]--><?php if($coa->desc_h2): ?>
                                        <span class="badge bg-success me-1">H2: <?php echo e($coa->desc_h2); ?></span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <!--[if BLOCK]><![endif]--><?php if($coa->desc_h3): ?>
                                        <span class="badge bg-warning text-dark me-1">H3: <?php echo e($coa->desc_h3); ?></span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <!--[if BLOCK]><![endif]--><?php if($coa->desc_h4): ?>
                                        <span class="badge bg-secondary me-1">H4: <?php echo e($coa->desc_h4); ?></span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <!--[if BLOCK]><![endif]--><?php if($coa->desc_h5): ?>
                                        <span class="badge bg-danger me-1">H5: <?php echo e($coa->desc_h5); ?></span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <!--[if BLOCK]><![endif]--><?php if($coa->desc_h6): ?>
                                        <span class="badge bg-dark">H6: <?php echo e($coa->desc_h6); ?></span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <!--[if BLOCK]><![endif]--><?php if(!$coa->desc_h1): ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i><?php echo e($coa->rec_usercreated); ?><br>
                                        <i class="fas fa-calendar me-1"></i><?php echo e($coa->rec_datecreated ? $coa->rec_datecreated->format('d M Y') : '-'); ?>

                                    </small>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        <h5>No data found</h5>
                                        <p class="mb-0">Try adjusting your search</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <?php echo e($coas->links('pagination::bootstrap-5')); ?>

        </div>
    </div>

    <!-- Modal Add/Edit -->
    <!--[if BLOCK]><![endif]--><?php if($showModal): ?>
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-plus-circle me-2"></i><?php echo e($editMode ? 'Edit' : 'Add New'); ?> COA
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="<?php echo e($editMode ? 'update' : 'store'); ?>">
                            <!-- Basic Info -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">COA Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['coa_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   wire:model="coa_code" <?php echo e($editMode ? 'readonly' : ''); ?>>
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['coa_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">COA ID</label>
                                            <input type="text" class="form-control" wire:model="coa_id">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['coa_desc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   wire:model="coa_desc">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['coa_desc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- H1 Required -->
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-layer-group me-2"></i>H1 Level (Required)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-info-circle me-2"></i><strong>Required:</strong> H1 adalah level minimum yang harus diisi
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">H1 Description <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['desc_h1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   wire:model="desc_h1" placeholder="e.g., Assets, Liabilities">
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['desc_h1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">H1 ID</label>
                                            <input type="text" class="form-control" wire:model="ms_coa_h1_id">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- H2 Optional -->
                            <div class="card mb-3">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-layer-group me-2"></i>H2 Level (Optional)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">H2 Description</label>
                                            <input type="text" class="form-control" wire:model="desc_h2" 
                                                   placeholder="e.g., Current Assets">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">H2 ID</label>
                                            <input type="text" class="form-control" wire:model="ms_coa_h2_id">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- H3-H6 Collapsible -->
                            <div class="accordion mb-3" id="additionalLevels">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" 
                                                data-bs-toggle="collapse" data-bs-target="#levelsH3H6">
                                            <i class="fas fa-layer-group me-2"></i>H3-H6 Levels (Optional)
                                        </button>
                                    </h2>
                                    <div id="levelsH3H6" class="accordion-collapse collapse" 
                                         data-bs-parent="#additionalLevels">
                                        <div class="accordion-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">H3 Description</label>
                                                    <input type="text" class="form-control" wire:model="desc_h3">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">H3 ID</label>
                                                    <input type="text" class="form-control" wire:model="id_h3">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">H4 Description</label>
                                                    <input type="text" class="form-control" wire:model="desc_h4">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">H4 ID</label>
                                                    <input type="text" class="form-control" wire:model="id_h4">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">H5 Description</label>
                                                    <input type="text" class="form-control" wire:model="desc_h5">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">H5 ID</label>
                                                    <input type="text" class="form-control" wire:model="id_h5">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">H6 Description</label>
                                                    <input type="text" class="form-control" wire:model="desc_h6">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">H6 ID</label>
                                                    <input type="text" class="form-control" wire:model="id_h6">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Info -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" 
                                                data-bs-toggle="collapse" data-bs-target="#additionalInfo">
                                            <i class="fas fa-info-circle me-2"></i>Additional Information
                                        </button>
                                    </h2>
                                    <div id="additionalInfo" class="accordion-collapse collapse" 
                                         data-bs-parent="#additionalLevels">
                                        <div class="accordion-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">COA Note</label>
                                                    <textarea class="form-control" wire:model="coa_note" rows="2"></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Arus Kas Code</label>
                                                    <input type="text" class="form-control" wire:model="arus_kas_code">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="<?php echo e($editMode ? 'update' : 'store'); ?>">
                            <i class="fas fa-save me-1"></i><?php echo e($editMode ? 'Update' : 'Save'); ?>

                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/livewire/coa-modern-management-bootstrap.blade.php ENDPATH**/ ?>