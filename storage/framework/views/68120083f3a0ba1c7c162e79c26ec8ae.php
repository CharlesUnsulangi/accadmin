<div>
    <?php $__env->startSection('title', 'Master Transaksi'); ?>
    
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Master Transaksi</h2>
            <button wire:click="create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Transaksi
            </button>
        </div>

        <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('message')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari berdasarkan kode, deskripsi, atau COA...">
                    </div>
                    <div class="col-md-6">
                        <select wire:model.live="perPage" class="form-select">
                            <option value="10">10 per halaman</option>
                            <option value="25">25 per halaman</option>
                            <option value="50">50 per halaman</option>
                            <option value="100">100 per halaman</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="10%">Kode</th>
                                <th width="20%">Deskripsi</th>
                                <th width="12%">COA Debet</th>
                                <th width="12%">COA Kredit</th>
                                <th width="10%">Tanggal</th>
                                <th width="10%" class="text-end">Debet</th>
                                <th width="10%" class="text-end">Kredit</th>
                                <th width="6%">Status</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $transaksi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($transaksi->firstItem() + $index); ?></td>
                                    <td><?php echo e($item->trans_code); ?></td>
                                    <td><?php echo e($item->trans_desc); ?></td>
                                    <td>
                                        <!--[if BLOCK]><![endif]--><?php if($item->coaDebet): ?>
                                            <?php echo e($item->trans_coa_debet); ?> - <?php echo e($item->coaDebet->coa_desc); ?>

                                        <?php else: ?>
                                            <?php echo e($item->trans_coa_debet); ?>

                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td>
                                        <!--[if BLOCK]><![endif]--><?php if($item->coaKredit): ?>
                                            <?php echo e($item->trans_coa_kredit); ?> - <?php echo e($item->coaKredit->coa_desc); ?>

                                        <?php else: ?>
                                            <?php echo e($item->trans_coa_kredit); ?>

                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td><?php echo e($item->trans_date ? $item->trans_date->format('d/m/Y') : '-'); ?></td>
                                    <td class="text-end"><?php echo e($item->trans_debet ? number_format($item->trans_debet, 2, ',', '.') : '-'); ?></td>
                                    <td class="text-end"><?php echo e($item->trans_kredit ? number_format($item->trans_kredit, 2, ',', '.') : '-'); ?></td>
                                    <td>
                                        <!--[if BLOCK]><![endif]--><?php if($item->rec_status == '1'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td>
                                        <button wire:click="edit('<?php echo e($item->trans_code); ?>')" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button wire:click="toggleStatus('<?php echo e($item->trans_code); ?>')" 
                                                class="btn btn-sm <?php echo e($item->rec_status == '1' ? 'btn-secondary' : 'btn-success'); ?>" 
                                                title="<?php echo e($item->rec_status == '1' ? 'Nonaktifkan' : 'Aktifkan'); ?>">
                                            <i class="bi <?php echo e($item->rec_status == '1' ? 'bi-toggle-on' : 'bi-toggle-off'); ?>"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data</td>
                                </tr>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <?php echo e($transaksi->links()); ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create/Edit -->
    <!--[if BLOCK]><![endif]--><?php if($showModal): ?>
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo e($isEdit ? 'Edit' : 'Tambah'); ?> Master Transaksi</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="trans_code" class="form-label">Kode Transaksi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['trans_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="trans_code" wire:model="trans_code" 
                                           <?php echo e($isEdit ? 'readonly' : ''); ?>>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['trans_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <div class="col-md-6">
                                    <label for="trans_desc" class="form-label">Deskripsi</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['trans_desc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="trans_desc" wire:model="trans_desc" maxlength="100">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['trans_desc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="trans_coa_debet" class="form-label">COA Debet</label>
                                    <select class="form-select <?php $__errorArgs = ['trans_coa_debet'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="trans_coa_debet" wire:model="trans_coa_debet">
                                        <option value="">-- Pilih COA Debet --</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $coaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($coa['code']); ?>"><?php echo e($coa['label']); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['trans_coa_debet'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <div class="col-md-6">
                                    <label for="trans_coa_kredit" class="form-label">COA Kredit</label>
                                    <select class="form-select <?php $__errorArgs = ['trans_coa_kredit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="trans_coa_kredit" wire:model="trans_coa_kredit">
                                        <option value="">-- Pilih COA Kredit --</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $coaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($coa['code']); ?>"><?php echo e($coa['label']); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['trans_coa_kredit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="trans_date" class="form-label">Tanggal</label>
                                    <input type="date" class="form-control <?php $__errorArgs = ['trans_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="trans_date" wire:model="trans_date">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['trans_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <div class="col-md-4">
                                    <label for="trans_debet" class="form-label">Jumlah Debet</label>
                                    <input type="number" step="0.01" min="0" class="form-control <?php $__errorArgs = ['trans_debet'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="trans_debet" wire:model="trans_debet">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['trans_debet'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <div class="col-md-4">
                                    <label for="trans_kredit" class="form-label">Jumlah Kredit</label>
                                    <input type="number" step="0.01" min="0" class="form-control <?php $__errorArgs = ['trans_kredit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="trans_kredit" wire:model="trans_kredit">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['trans_kredit'];
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
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                        <button type="button" class="btn btn-primary" wire:click="save">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/livewire/transaksi-management.blade.php ENDPATH**/ ?>