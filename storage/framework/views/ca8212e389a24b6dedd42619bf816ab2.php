<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">COA Legacy Management</h2>
                <p class="text-sm text-gray-600 mt-1">Sistem 4 Level: Main → Sub1 → Sub2 → COA Detail (ms_acc_coa)</p>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('coa.modern')); ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-arrow-right mr-2"></i>Switch to Modern
                </a>
                <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-plus mr-2"></i>Add New
                </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search code, description..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Filter Main -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Main Category</label>
                <select wire:model.live="filterMain" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Main</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $mains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($id); ?>"><?php echo e($desc); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
            </div>

            <!-- Filter Sub1 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sub Category 1</label>
                <select wire:model.live="filterSub1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Sub1</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $sub1s; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($id); ?>"><?php echo e($desc); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-4">
            <div class="text-white">
                <p class="text-sm font-medium opacity-90">Total Sub2 (Level 3)</p>
                <p class="text-3xl font-bold mt-1"><?php echo e($coaSub2s->total()); ?></p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-4">
            <div class="text-white">
                <p class="text-sm font-medium opacity-90">Main Categories</p>
                <p class="text-3xl font-bold mt-1"><?php echo e($mains->count()); ?></p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-4">
            <div class="text-white">
                <p class="text-sm font-medium opacity-90">Sub1 Categories</p>
                <p class="text-3xl font-bold mt-1"><?php echo e($sub1s->count()); ?></p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-4">
            <div class="text-white">
                <p class="text-sm font-medium opacity-90">This Page</p>
                <p class="text-3xl font-bold mt-1"><?php echo e($coaSub2s->count()); ?></p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('coasub2_code')" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center gap-2">
                                <span>Sub2 Code</span>
                                <!--[if BLOCK]><![endif]--><?php if($sortBy === 'coasub2_code'): ?>
                                    <i class="fas fa-sort-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?>"></i>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                            Hierarchy (3 Levels)
                        </th>
                        <th wire:click="sortBy('coasub2_desc')" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center gap-2">
                                Description
                                <!--[if BLOCK]><![endif]--><?php if($sortBy === 'coasub2_desc'): ?>
                                    <i class="fas fa-sort-<?php echo e($sortDirection === 'asc' ? 'up' : 'down'); ?>"></i>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                            COA Count
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $coaSub2s; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono font-semibold text-gray-900"><?php echo e($sub2->coasub2_code); ?></div>
                                <div class="text-xs text-gray-500">ID: <?php echo e($sub2->coasub2_id ?? '-'); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs space-y-1">
                                    <!--[if BLOCK]><![endif]--><?php if($sub2->coaSub1 && $sub2->coaSub1->coaMain): ?>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs font-medium">L1</span>
                                            <span class="text-gray-700"><?php echo e($sub2->coaSub1->coaMain->coa_main_desc); ?></span>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <!--[if BLOCK]><![endif]--><?php if($sub2->coaSub1): ?>
                                        <div class="flex items-center gap-2 ml-4">
                                            <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-xs font-medium">L2</span>
                                            <span class="text-gray-700"><?php echo e($sub2->coaSub1->coasub1_desc); ?></span>
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <div class="flex items-center gap-2 ml-8">
                                        <span class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded text-xs font-medium">L3</span>
                                        <span class="text-gray-700 font-semibold"><?php echo e($sub2->coasub2_desc); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-medium"><?php echo e($sub2->coasub2_desc); ?></div>
                                <div class="text-xs text-gray-500 mt-1">Code: <?php echo e($sub2->coasub2_code); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <a href="<?php echo e(route('coa.modern')); ?>?filter_sub2=<?php echo e($sub2->coasub2_code); ?>" 
                                       class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition cursor-pointer"
                                       title="Click to view <?php echo e($sub2->coas->count()); ?> detail accounts">
                                        <i class="fas fa-link mr-1"></i>
                                        <?php echo e($sub2->coas->count()); ?> accounts
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo e($sub2->rec_status === '1' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                                    <?php echo e($sub2->rec_status === '1' ? 'Active' : 'Inactive'); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?php echo e(route('coa.modern')); ?>?filter_sub2=<?php echo e($sub2->coasub2_code); ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-3"
                                   title="View Level 4 COA Details">
                                    <i class="fas fa-list"></i>
                                </a>
                                <button class="text-yellow-600 hover:text-yellow-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="text-lg">No legacy COA Sub2 found</p>
                                    <p class="text-sm mt-1">Try adjusting your search or filters</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <?php echo e($coaSub2s->links()); ?>

        </div>
    </div>

    <!-- Info Alert -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Legacy System (4 Level Hierarchy)</p>
                <p><strong>Level 1:</strong> <code class="bg-blue-100 px-1 rounded">ms_acc_coa_main</code> (10 records) → Main Categories (Assets, Liabilities, etc.)</p>
                <p class="mt-1"><strong>Level 2:</strong> <code class="bg-blue-100 px-1 rounded">ms_acc_coasub1</code> (18 records) → Sub Category 1 (Current Assets, Fixed Assets, etc.)</p>
                <p class="mt-1"><strong>Level 3:</strong> <code class="bg-blue-100 px-1 rounded">ms_acc_coasub2</code> (58 records) → Sub Category 2 (Cash & Bank, Inventory, etc.) <strong>← You are here</strong></p>
                <p class="mt-1"><strong>Level 4:</strong> <code class="bg-blue-100 px-1 rounded">ms_acc_coa</code> (501+ records) → Detail COA Accounts (actual accounts used in transactions)</p>
                <p class="mt-2">Each Sub2 (Level 3) connects to multiple COA accounts (Level 4) via <code class="bg-blue-100 px-1 rounded">coa_coasub2code → coasub2_code</code></p>
                <p class="mt-2">For the new flexible system (H1-H6 in same table), use <a href="<?php echo e(route('coa.modern')); ?>" class="underline font-semibold">COA Modern</a></p>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/livewire/coa-legacy.blade.php ENDPATH**/ ?>