<div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-book"></i> Dokumentasi IT
        </h2>
        <button wire:click="toggleForm" class="btn btn-primary">
            <i class="fas fa-plus"></i> <?php echo e($showForm ? 'Tutup Form' : 'Tambah Dokumentasi'); ?>

        </button>
    </div>

    <!-- Flash Message -->
    <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo e(session('message')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Form -->
    <!--[if BLOCK]><![endif]--><?php if($showForm): ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-edit"></i> <?php echo e($editingId ? 'Edit' : 'Tambah'); ?> Dokumentasi
                </h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Topik <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['topik'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   wire:model="topik" placeholder="e.g., Database Schema, API Documentation">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['topik'];
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
                            <label class="form-label">Project</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['project'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   wire:model="project" placeholder="e.g., AccAdmin, HRD System">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['project'];
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
                        <label class="form-label">Link / Referensi</label>
                        <input type="text" class="form-control <?php $__errorArgs = ['link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               wire:model="link" placeholder="https://...">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['link'];
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
                        <label class="form-label">Catatan / Dokumentasi <span class="text-danger">*</span></label>
                        <textarea class="form-control <?php $__errorArgs = ['catatan_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                  wire:model="catatan_text" rows="10" 
                                  placeholder="Masukkan dokumentasi, schema table, atau catatan teknis..."></textarea>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['catatan_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        <small class="text-muted">Gunakan format SQL, markdown, atau teks biasa</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> <?php echo e($editingId ? 'Update' : 'Simpan'); ?>

                        </button>
                        <button type="button" wire:click="resetForm" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <button type="button" wire:click="toggleForm" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" 
                           placeholder="🔍 Cari dokumentasi...">
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterTopic">
                        <option value="">Semua Topik</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($topic); ?>"><?php echo e($topic); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterProject">
                        <option value="">Semua Project</option>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($project); ?>"><?php echo e($project); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </select>
                </div>
                <div class="col-md-2">
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-eraser"></i> Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentation List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list"></i> Daftar Dokumentasi (<?php echo e($docs->total()); ?>)</span>
            <select class="form-select form-select-sm" style="width: auto;" wire:model.live="perPage">
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
            </select>
        </div>
        <div class="card-body">
            <!--[if BLOCK]><![endif]--><?php if($docs->count() > 0): ?>
                <div class="accordion" id="documentationAccordion">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $docs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#doc<?php echo e($doc->tr_admin_it_doc_id); ?>">
                                    <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                        <div>
                                            <strong><?php echo e($doc->topik); ?></strong>
                                            <!--[if BLOCK]><![endif]--><?php if($doc->project): ?>
                                                <span class="badge bg-info ms-2"><?php echo e($doc->project); ?></span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fas fa-calendar"></i> <?php echo e($doc->created_date?->format('d M Y')); ?>

                                            <i class="fas fa-user ms-2"></i> <?php echo e($doc->created_user); ?>

                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="doc<?php echo e($doc->tr_admin_it_doc_id); ?>" 
                                 class="accordion-collapse collapse" 
                                 data-bs-parent="#documentationAccordion">
                                <div class="accordion-body">
                                    <!--[if BLOCK]><![endif]--><?php if($doc->link): ?>
                                        <div class="mb-3">
                                            <strong>Link:</strong> 
                                            <a href="<?php echo e($doc->link); ?>" target="_blank" class="text-primary">
                                                <?php echo e($doc->link); ?> <i class="fas fa-external-link-alt fa-xs"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    
                                    <div class="mb-3">
                                        <strong>Dokumentasi:</strong>
                                        <pre class="bg-light p-3 rounded mt-2" style="white-space: pre-wrap; word-wrap: break-word;"><?php echo e($doc->catatan_text); ?></pre>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button wire:click="edit(<?php echo e($doc->tr_admin_it_doc_id); ?>)" 
                                                class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button wire:click="delete(<?php echo e($doc->tr_admin_it_doc_id); ?>)" 
                                                onclick="return confirm('Hapus dokumentasi ini?')"
                                                class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    <?php echo e($docs->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>Belum ada dokumentasi</p>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    <style>
        .accordion-button:not(.collapsed) {
            background-color: #e7f1ff;
            color: #0c63e4;
        }
        
        pre {
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            max-height: 500px;
            overflow-y: auto;
        }
        
        .gap-2 {
            gap: 0.5rem;
        }
        
        .gap-3 {
            gap: 1rem;
        }
    </style>
</div>
<?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/livewire/it-documentation-bootstrap.blade.php ENDPATH**/ ?>