<div>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"><i class="fas fa-users me-2"></i>Master Vendor/Supplier</h2>
                <p class="text-muted mb-0">Kelola data master vendor dan supplier</p>
            </div>
            <button wire:click="create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Vendor
            </button>
        </div>

        <!-- Flash Messages -->
        <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo e(session('message')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <?php if(session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Search</label>
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Cari kode, nama, PIC, alamat, email...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Per Page</label>
                        <select wire:model.live="perPage" class="form-select">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor List -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Kode</th>
                                <th>Nama Vendor</th>
                                <th>PIC</th>
                                <th>Alamat</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="text-muted"><?php echo e($vendors->firstItem() + $index); ?></td>
                                    <td>
                                        <strong class="text-primary"><?php echo e($vendor->ven_code); ?></strong>
                                    </td>
                                    <td>
                                        <strong><?php echo e($vendor->ven_name ?: '-'); ?></strong>
                                    </td>
                                    <td>
                                        <small><?php echo e($vendor->ven_pic ?: '-'); ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo e($vendor->ven_addrase ?: '-'); ?></small>
                                    </td>
                                    <td>
                                        <small>
                                            <!--[if BLOCK]><![endif]--><?php if($vendor->ven_phone): ?>
                                                <i class="fas fa-phone me-1"></i><?php echo e($vendor->ven_phone); ?>

                                            <?php else: ?>
                                                -
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </small>
                                    </td>
                                    <td>
                                        <small><?php echo e($vendor->ven_email ?: '-'); ?></small>
                                    </td>
                                    <td>
                                        <button wire:click="toggleStatus('<?php echo e($vendor->ven_code); ?>')" 
                                                class="btn btn-sm <?php echo e($vendor->rec_status == '1' ? 'btn-success' : 'btn-secondary'); ?>">
                                            <?php echo e($vendor->rec_status == '1' ? 'Active' : 'Inactive'); ?>

                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <button wire:click="edit('<?php echo e($vendor->ven_code); ?>')" 
                                                class="btn btn-sm btn-outline-primary" 
                                                title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        Tidak ada data vendor
                                    </td>
                                </tr>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    <?php echo e($vendors->links()); ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <!--[if BLOCK]><![endif]--><?php if($showModal): ?>
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-users me-2"></i>
                            <?php echo e($editMode ? 'Edit Vendor' : 'Tambah Vendor'); ?>

                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ven_code" class="form-label">Kode Vendor <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           wire:model="ven_code" 
                                           class="form-control <?php $__errorArgs = ['ven_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="ven_code"
                                           <?php echo e($editMode ? 'readonly' : ''); ?>

                                           placeholder="Contoh: VEN001">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ven_code'];
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
                                
                                <div class="col-md-6 mb-3">
                                    <label for="ven_name" class="form-label">Nama Vendor</label>
                                    <input type="text" 
                                           wire:model="ven_name" 
                                           class="form-control <?php $__errorArgs = ['ven_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="ven_name"
                                           placeholder="Nama vendor/supplier">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ven_name'];
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
                            </div>

                            <div class="mb-3">
                                <label for="ven_pic" class="form-label">PIC (Person In Charge)</label>
                                <input type="text" 
                                       wire:model="ven_pic" 
                                       class="form-control <?php $__errorArgs = ['ven_pic'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="ven_pic"
                                       placeholder="Nama PIC">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ven_pic'];
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
                                <label for="ven_addrase" class="form-label">Alamat</label>
                                <textarea wire:model="ven_addrase" 
                                          class="form-control <?php $__errorArgs = ['ven_addrase'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                          id="ven_addrase"
                                          rows="3"
                                          placeholder="Alamat lengkap vendor"></textarea>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ven_addrase'];
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

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ven_phone" class="form-label">Telepon</label>
                                    <input type="text" 
                                           wire:model="ven_phone" 
                                           class="form-control <?php $__errorArgs = ['ven_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="ven_phone"
                                           placeholder="021-xxx atau 08xx-xxx-xxx">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ven_phone'];
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
                                
                                <div class="col-md-6 mb-3">
                                    <label for="ven_email" class="form-label">Email</label>
                                    <input type="text" 
                                           wire:model="ven_email" 
                                           class="form-control <?php $__errorArgs = ['ven_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="ven_email"
                                           placeholder="email@vendor.com">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ven_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    <small class="text-muted">Bisa lebih dari satu, pisahkan dengan koma</small>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <i class="fas fa-times me-2"></i>Batal
                        </button>
                        <button type="button" wire:click="save" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/livewire/vendor-management.blade.php ENDPATH**/ ?>