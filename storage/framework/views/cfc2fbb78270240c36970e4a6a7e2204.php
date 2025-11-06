<div>
    <!-- Flash Messages -->
    <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; margin-top: 70px;">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo e(session('message')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    
    <?php if(session()->has('error')): ?>
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; margin-top: 70px;">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Header Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="card-title mb-1">
                        <i class="fas fa-folder-tree me-2 text-primary"></i>COA Main Management
                    </h2>
                    <p class="text-muted mb-0">
                        <small>Legacy Level 1 - Main Categories (ms_acc_coa_main)</small>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="<?php echo e(route('coa.legacy')); ?>" class="btn btn-secondary me-2">
                        <i class="fas fa-list me-1"></i>Legacy Hierarchy
                    </a>
                    <button wire:click="create" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add New
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search" 
                            class="form-control" 
                            placeholder="Search Code, ID, Description..."
                        >
                    </div>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10 / page</option>
                        <option value="15">15 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Total Main Categories</h6>
                    <h2 class="card-title mb-0"><?php echo e($coaMains->total()); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Current Page</h6>
                    <h2 class="card-title mb-0"><?php echo e($coaMains->count()); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">Per Page</h6>
                    <h2 class="card-title mb-0"><?php echo e($perPage); ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Main Code</th>
                            <th>Main ID</th>
                            <th>Description</th>
                            <th>Reference Code</th>
                            <th>Children (Sub1)</th>
                            <th>Audit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $coaMains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $main): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary font-monospace"><?php echo e($main->coa_main_code); ?></span>
                                </td>
                                <td><?php echo e($main->coa_main_id ?? '-'); ?></td>
                                <td>
                                    <strong><?php echo e($main->coa_main_desc); ?></strong>
                                </td>
                                <td>
                                    <span class="text-muted"><?php echo e($main->coa_main_coamain2code ?? '-'); ?></span>
                                </td>
                                <td>
                                    <!--[if BLOCK]><![endif]--><?php if($main->coa_sub1s_count > 0): ?>
                                        <span class="badge bg-success"><?php echo e($main->coa_sub1s_count); ?> Sub Categories</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No Sub1</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <div><i class="fas fa-user me-1"></i><?php echo e($main->user_created ?? '-'); ?></div>
                                        <div><i class="fas fa-clock me-1"></i><?php echo e($main->dt_created ? \Carbon\Carbon::parse($main->dt_created)->format('d/m/Y H:i') : '-'); ?></div>
                                    </small>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">No data found</p>
                                </td>
                            </tr>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                <?php echo e($coaMains->links('pagination::bootstrap-5')); ?>

            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <!--[if BLOCK]><![endif]--><?php if($showModal): ?>
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-<?php echo e($editMode ? 'edit' : 'plus-circle'); ?> me-2"></i>
                        <?php echo e($editMode ? 'Edit COA Main' : 'Add New COA Main'); ?>

                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>
                
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <!-- Basic Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong><i class="fas fa-info-circle me-2"></i>Basic Information</strong>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- Main Code -->
                                    <div class="col-md-6">
                                        <label class="form-label">Main Code <span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            wire:model="coa_main_code" 
                                            class="form-control <?php $__errorArgs = ['coa_main_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            placeholder="e.g., 1000"
                                            <?php echo e($editMode ? 'readonly' : ''); ?>

                                        >
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['coa_main_code'];
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

                                    <!-- Main ID -->
                                    <div class="col-md-6">
                                        <label class="form-label">Main ID <span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            wire:model="coa_main_id" 
                                            class="form-control <?php $__errorArgs = ['coa_main_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            placeholder="e.g., M001"
                                        >
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['coa_main_id'];
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

                                    <!-- Description -->
                                    <div class="col-12">
                                        <label class="form-label">Description <span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            wire:model="coa_main_desc" 
                                            class="form-control <?php $__errorArgs = ['coa_main_desc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            placeholder="Main category description"
                                        >
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['coa_main_desc'];
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

                                    <!-- Reference Code -->
                                    <div class="col-md-6">
                                        <label class="form-label">Reference Code</label>
                                        <input 
                                            type="text" 
                                            wire:model="coa_main_coamain2code" 
                                            class="form-control <?php $__errorArgs = ['coa_main_coamain2code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            placeholder="Optional reference"
                                        >
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['coa_main_coamain2code'];
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

                                    <!-- Active Status -->
                                    <div class="col-md-6">
                                        <label class="form-label">Status</label>
                                        <div class="form-check form-switch mt-2">
                                            <input 
                                                type="checkbox" 
                                                wire:model="cek_aktif" 
                                                class="form-check-input" 
                                                role="switch"
                                                id="statusSwitch"
                                            >
                                            <label class="form-check-label" for="statusSwitch">
                                                <span class="badge bg-<?php echo e($cek_aktif ? 'success' : 'secondary'); ?>">
                                                    <?php echo e($cek_aktif ? 'Active' : 'Inactive'); ?>

                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--[if BLOCK]><![endif]--><?php if($editMode && $id_h): ?>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Main Code cannot be edited for existing records.
                        </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i><?php echo e($editMode ? 'Update' : 'Save'); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <script>
        // Auto-dismiss alerts after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 3000);
        });
    </script>
</div>
<?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/livewire/coa-main-management-bootstrap.blade.php ENDPATH**/ ?>