<div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-database text-primary me-2"></i>
                Stored Procedures Management
            </h2>
            <p class="text-muted mb-0">Manage SQL Server Stored Procedures Configuration</p>
        </div>
        <button wire:click="create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New SP
        </button>
    </div>

    <!-- Alert Message -->
    <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo e(session('message')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total SPs</p>
                            <h3 class="mb-0"><?php echo e(number_format($stats['total'])); ?></h3>
                        </div>
                        <div class="fs-1 text-primary opacity-25">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">With Money Input</p>
                            <h3 class="mb-0"><?php echo e(number_format($stats['with_money'])); ?></h3>
                        </div>
                        <div class="fs-1 text-success opacity-25">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">With Date Range</p>
                            <h3 class="mb-0"><?php echo e(number_format($stats['with_dates'])); ?></h3>
                        </div>
                        <div class="fs-1 text-info opacity-25">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" 
                           placeholder="Search by ID, description, SP name, or varchar input...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Per Page</label>
                    <select wire:model.live="perPage" class="form-select">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button wire:click="$set('search', '')" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-redo me-2"></i>Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SP List Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th wire:click="sortBy('ms_admin_sp_id')" style="cursor: pointer;">
                                SP ID 
                                <!--[if BLOCK]><![endif]--><?php if($sortField === 'ms_admin_sp_id'): ?>
                                    <i class="fas fa-sort-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?>"></i>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </th>
                            <th wire:click="sortBy('sp_desc')" style="cursor: pointer;">
                                Description
                                <!--[if BLOCK]><![endif]--><?php if($sortField === 'sp_desc'): ?>
                                    <i class="fas fa-sort-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?>"></i>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </th>
                            <th wire:click="sortBy('sp_name')" style="cursor: pointer;">
                                SP Name
                                <!--[if BLOCK]><![endif]--><?php if($sortField === 'sp_name'): ?>
                                    <i class="fas fa-sort-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?>"></i>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </th>
                            <th>Date Range</th>
                            <th class="text-end">Money Input</th>
                            <th>Varchar Input</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $spList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <code class="text-primary"><?php echo e($sp->ms_admin_sp_id); ?></code>
                                </td>
                                <td><?php echo e($sp->sp_desc ?? '-'); ?></td>
                                <td>
                                    <!--[if BLOCK]><![endif]--><?php if($sp->sp_name): ?>
                                        <span class="badge bg-info"><?php echo e($sp->sp_name); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i><?php echo e($sp->date_range); ?>

                                    </small>
                                </td>
                                <td class="text-end">
                                    <!--[if BLOCK]><![endif]--><?php if($sp->money_input): ?>
                                        <strong class="text-success"><?php echo e($sp->formatted_money); ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </td>
                                <td>
                                    <!--[if BLOCK]><![endif]--><?php if($sp->varchar_input): ?>
                                        <span class="badge bg-secondary"><?php echo e($sp->varchar_input); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </td>
                                <td class="text-center">
                                    <button wire:click="execute('<?php echo e($sp->ms_admin_sp_id); ?>')" 
                                            class="btn btn-sm btn-success me-1"
                                            title="Execute SP">
                                        <i class="fas fa-play"></i>
                                    </button>
                                    <button wire:click="edit('<?php echo e($sp->ms_admin_sp_id); ?>')" 
                                            class="btn btn-sm btn-outline-primary me-1"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="delete('<?php echo e($sp->ms_admin_sp_id); ?>')" 
                                            wire:confirm="Are you sure you want to delete this SP?"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fs-1 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No stored procedures found</p>
                                </td>
                            </tr>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                <?php echo e($spList->links()); ?>

            </div>
        </div>
    </div>

    <!-- Modal for Create/Edit -->
    <!--[if BLOCK]><![endif]--><?php if($showModal): ?>
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-<?php echo e($editMode ? 'edit' : 'plus'); ?> me-2"></i>
                            <?php echo e($editMode ? 'Edit' : 'Add New'); ?> Stored Procedure
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">SP ID <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="ms_admin_sp_id" 
                                           class="form-control <?php $__errorArgs = ['ms_admin_sp_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           <?php echo e($editMode ? 'readonly' : ''); ?>>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ms_admin_sp_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">SP Name</label>
                                    <input type="text" wire:model="sp_name" 
                                           class="form-control <?php $__errorArgs = ['sp_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['sp_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea wire:model="sp_desc" rows="3"
                                              class="form-control <?php $__errorArgs = ['sp_desc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"></textarea>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['sp_desc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" wire:model="date_start_input" 
                                           class="form-control <?php $__errorArgs = ['date_start_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['date_start_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input type="date" wire:model="date_end_input" 
                                           class="form-control <?php $__errorArgs = ['date_end_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['date_end_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Money Input</label>
                                    <input type="number" step="0.01" wire:model="money_input" 
                                           class="form-control <?php $__errorArgs = ['money_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['money_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Varchar Input</label>
                                    <input type="text" wire:model="varchar_input" 
                                           class="form-control <?php $__errorArgs = ['varchar_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['varchar_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="save">
                            <i class="fas fa-save me-2"></i>Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Modal for Execute SP Results -->
    <!--[if BLOCK]><![endif]--><?php if($showExecuteModal): ?>
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-play-circle me-2"></i>
                            Execute: <?php echo e($executingSp->sp_name ?? $executingSp->ms_admin_sp_id); ?>

                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeExecuteModal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- SP Info -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">SP Information</h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="text-muted" style="width: 150px;">SP Name:</td>
                                                <td><code class="text-success"><?php echo e($executingSp->ms_admin_sp_id); ?></code></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Description:</td>
                                                <td><?php echo e($executingSp->sp_desc ?? '-'); ?></td>
                                            </tr>
                                            <!--[if BLOCK]><![endif]--><?php if($executingSp->sp_name): ?>
                                            <tr>
                                                <td class="text-muted">Alias:</td>
                                                <td><span class="badge bg-info"><?php echo e($executingSp->sp_name); ?></span></td>
                                            </tr>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">Parameters Used</h6>
                                        <table class="table table-sm table-borderless">
                                            <!--[if BLOCK]><![endif]--><?php if($executingSp->date_start_input): ?>
                                            <tr>
                                                <td class="text-muted" style="width: 150px;">Start Date:</td>
                                                <td><?php echo e($executingSp->date_start_input->format('Y-m-d')); ?></td>
                                            </tr>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if($executingSp->date_end_input): ?>
                                            <tr>
                                                <td class="text-muted">End Date:</td>
                                                <td><?php echo e($executingSp->date_end_input->format('Y-m-d')); ?></td>
                                            </tr>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if($executingSp->money_input): ?>
                                            <tr>
                                                <td class="text-muted">Money Input:</td>
                                                <td><?php echo e($executingSp->formatted_money); ?></td>
                                            </tr>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if($executingSp->varchar_input): ?>
                                            <tr>
                                                <td class="text-muted">Varchar Input:</td>
                                                <td><span class="badge bg-secondary"><?php echo e($executingSp->varchar_input); ?></span></td>
                                            </tr>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if(!$executingSp->date_start_input && !$executingSp->date_end_input && !$executingSp->money_input && !$executingSp->varchar_input): ?>
                                            <tr>
                                                <td colspan="2" class="text-muted"><em>No parameters configured</em></td>
                                            </tr>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </table>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="badge bg-info">
                                                <i class="fas fa-clock me-1"></i>Execution Time: <?php echo e($executionTime); ?>ms
                                            </span>
                                            <span class="badge bg-primary ms-2">
                                                <i class="fas fa-database me-1"></i>Rows: <?php echo e(count($executeResults)); ?>

                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <label class="form-label mb-0 me-2 small">Max Rows:</label>
                                            <select wire:model="maxResultRows" class="form-select form-select-sm d-inline-block w-auto">
                                                <option value="100">100</option>
                                                <option value="500">500</option>
                                                <option value="1000">1,000</option>
                                                <option value="5000">5,000</option>
                                                <option value="10000">10,000</option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" wire:click="execute('<?php echo e($executingSp->ms_admin_sp_id); ?>')" 
                                                    class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-sync me-1"></i>Re-execute
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table Information -->
                        <!--[if BLOCK]><![endif]--><?php if(!empty($spTableInfo)): ?>
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-table me-2"></i>Tables Used in SP
                                    <span class="badge bg-secondary ms-2"><?php echo e(count($spTableInfo)); ?> tables</span>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="accordion" id="tableInfoAccordion">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $spTableInfo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading<?php echo e($index); ?>">
                                                <button class="accordion-button <?php echo e($index > 0 ? 'collapsed' : ''); ?>" type="button" 
                                                        data-bs-toggle="collapse" data-bs-target="#collapse<?php echo e($index); ?>" 
                                                        aria-expanded="<?php echo e($index === 0 ? 'true' : 'false'); ?>" 
                                                        aria-controls="collapse<?php echo e($index); ?>">
                                                    <i class="fas fa-database me-2 text-primary"></i>
                                                    <strong><?php echo e($table['name']); ?></strong>
                                                    <span class="badge bg-info ms-2"><?php echo e(count($table['primary_keys'])); ?> PK</span>
                                                    <span class="badge bg-warning ms-1"><?php echo e(count($table['foreign_keys'])); ?> FK</span>
                                                </button>
                                            </h2>
                                            <div id="collapse<?php echo e($index); ?>" 
                                                 class="accordion-collapse collapse <?php echo e($index === 0 ? 'show' : ''); ?>" 
                                                 aria-labelledby="heading<?php echo e($index); ?>" 
                                                 data-bs-parent="#tableInfoAccordion">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <!-- Primary Keys -->
                                                        <div class="col-md-6">
                                                            <h6 class="text-muted mb-2">
                                                                <i class="fas fa-key text-info me-1"></i>Primary Keys
                                                            </h6>
                                                            <!--[if BLOCK]><![endif]--><?php if(!empty($table['primary_keys'])): ?>
                                                                <ul class="list-unstyled mb-0">
                                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $table['primary_keys']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <li class="mb-1">
                                                                            <code class="text-info"><?php echo e($pk); ?></code>
                                                                        </li>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                                </ul>
                                                            <?php else: ?>
                                                                <p class="text-muted mb-0"><em>No primary keys</em></p>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                        
                                                        <!-- Foreign Keys -->
                                                        <div class="col-md-6">
                                                            <h6 class="text-muted mb-2">
                                                                <i class="fas fa-link text-warning me-1"></i>Foreign Keys
                                                            </h6>
                                                            <!--[if BLOCK]><![endif]--><?php if(!empty($table['foreign_keys'])): ?>
                                                                <ul class="list-unstyled mb-0">
                                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $table['foreign_keys']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <li class="mb-2">
                                                                            <code class="text-warning"><?php echo e($fk['column']); ?></code>
                                                                            <i class="fas fa-arrow-right mx-1 text-muted small"></i>
                                                                            <code class="text-success"><?php echo e($fk['references']); ?></code>
                                                                            <br>
                                                                            <small class="text-muted"><?php echo e($fk['fk_name']); ?></small>
                                                                        </li>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                                </ul>
                                                            <?php else: ?>
                                                                <p class="text-muted mb-0"><em>No foreign keys</em></p>
                                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!-- Error Display -->
                        <!--[if BLOCK]><![endif]--><?php if($executeError): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Notice:</strong> <?php echo e($executeError); ?>

                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!-- Results Display -->
                        <!--[if BLOCK]><![endif]--><?php if(!empty($executeResults)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-check-circle me-2"></i>
                                Displaying <strong><?php echo e(number_format(count($executeResults))); ?></strong> rows
                                <!--[if BLOCK]><![endif]--><?php if($executeError && strpos($executeError, 'limited') !== false): ?>
                                    <span class="badge bg-warning text-dark ms-2">Partial Results</span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-table me-2"></i>Query Results
                                        <span class="badge bg-success ms-2"><?php echo e(count($executeResults)); ?> rows</span>
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                        <table class="table table-sm table-striped table-hover mb-0">
                                            <thead class="table-dark sticky-top">
                                                <tr>
                                                    <th style="width: 50px;">#</th>
                                                    <!--[if BLOCK]><![endif]--><?php if(count($executeResults) > 0): ?>
                                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = array_keys((array)$executeResults[0]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <th><?php echo e($column); ?></th>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $executeResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td class="text-muted"><?php echo e($index + 1); ?></td>
                                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = (array)$row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <td>
                                                                <!--[if BLOCK]><![endif]--><?php if(is_null($value)): ?>
                                                                    <span class="text-muted fst-italic">NULL</span>
                                                                <?php elseif(is_bool($value)): ?>
                                                                    <span class="badge bg-<?php echo e($value ? 'success' : 'danger'); ?>">
                                                                        <?php echo e($value ? 'TRUE' : 'FALSE'); ?>

                                                                    </span>
                                                                <?php elseif(is_numeric($value)): ?>
                                                                    <span class="text-end d-block"><?php echo e(number_format($value, 2)); ?></span>
                                                                <?php else: ?>
                                                                    <?php echo e($value); ?>

                                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                            </td>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeExecuteModal">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/livewire/admin-sp-management.blade.php ENDPATH**/ ?>