<div wire:poll.<?php echo e($refreshInterval); ?>ms>
    <!-- Welcome Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Welcome back, <?php echo e(Auth::user()->name); ?>! 👋</h1>
        <p class="text-gray-700 mt-1">Here's what's happening with your accounting system today.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total COA Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white text-sm font-semibold">Total COA</p>
                    <h3 class="text-4xl font-bold mt-2 text-white"><?php echo e($coaStats['total']); ?></h3>
                    <p class="text-blue-50 text-xs mt-1">All accounts</p>
                </div>
                <div class="bg-white bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active COA Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white text-sm font-semibold">Active Accounts</p>
                    <h3 class="text-4xl font-bold mt-2 text-white"><?php echo e($coaStats['active']); ?></h3>
                    <p class="text-green-50 text-xs mt-1">Ready to use</p>
                </div>
                <div class="bg-white bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Inactive COA Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white text-sm font-semibold">Inactive Accounts</p>
                    <h3 class="text-4xl font-bold mt-2 text-white"><?php echo e($coaStats['inactive']); ?></h3>
                    <p class="text-orange-50 text-xs mt-1">Needs attention</p>
                </div>
                <div class="bg-white bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Hierarchy Levels Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white text-sm font-semibold">Hierarchy Levels</p>
                    <h3 class="text-4xl font-bold mt-2 text-white">1-6</h3>
                    <p class="text-purple-50 text-xs mt-1">Flexible structure (H1-H6)</p>
                </div>
                <div class="bg-white bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V4zM8 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H9a1 1 0 01-1-1V4zM15 3a1 1 0 00-1 1v12a1 1 0 001 1h2a1 1 0 001-1V4a1 1 0 00-1-1h-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Details Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Hierarchy Breakdown -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">COA Hierarchy Breakdown (H1-H6)</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700">H1 - Level 1</span>
                        <span class="text-sm font-bold text-blue-600"><?php echo e($hierarchyStats['h1']); ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo e($hierarchyStats['h1'] > 0 ? ($hierarchyStats['h1'] / max(array_values($hierarchyStats)) * 100) : 0); ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700">H2 - Level 2</span>
                        <span class="text-sm font-bold text-green-600"><?php echo e($hierarchyStats['h2']); ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: <?php echo e($hierarchyStats['h2'] > 0 ? ($hierarchyStats['h2'] / max(array_values($hierarchyStats)) * 100) : 0); ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700">H3 - Level 3</span>
                        <span class="text-sm font-bold text-purple-600"><?php echo e($hierarchyStats['h3']); ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: <?php echo e($hierarchyStats['h3'] > 0 ? ($hierarchyStats['h3'] / max(array_values($hierarchyStats)) * 100) : 0); ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700">H4 - Level 4</span>
                        <span class="text-sm font-bold text-orange-600"><?php echo e($hierarchyStats['h4']); ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-600 h-2 rounded-full" style="width: <?php echo e($hierarchyStats['h4'] > 0 ? ($hierarchyStats['h4'] / max(array_values($hierarchyStats)) * 100) : 0); ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700">H5 - Level 5</span>
                        <span class="text-sm font-bold text-pink-600"><?php echo e($hierarchyStats['h5']); ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-pink-600 h-2 rounded-full" style="width: <?php echo e($hierarchyStats['h5'] > 0 ? ($hierarchyStats['h5'] / max(array_values($hierarchyStats)) * 100) : 0); ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700">H6 - Level 6</span>
                        <span class="text-sm font-bold text-indigo-600"><?php echo e($hierarchyStats['h6']); ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: <?php echo e($hierarchyStats['h6'] > 0 ? ($hierarchyStats['h6'] / max(array_values($hierarchyStats)) * 100) : 0); ?>%"></div>
                    </div>
                </div>

                <div class="border-t pt-3 mt-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-900">Total Accounts</span>
                        <span class="text-sm font-bold text-gray-900"><?php echo e($hierarchyStats['total']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Type Distribution -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Type Distribution</h3>
            <div class="space-y-3">
                <?php $__currentLoopData = $accountTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <!--[if BLOCK]><![endif]--><?php if($count > 0): ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-3 
                                <?php echo e($type === 'Asset' ? 'bg-blue-500' : ''); ?>

                                <?php echo e($type === 'Liability' ? 'bg-red-500' : ''); ?>

                                <?php echo e($type === 'Equity' ? 'bg-green-500' : ''); ?>

                                <?php echo e($type === 'Revenue' ? 'bg-purple-500' : ''); ?>

                                <?php echo e($type === 'Expense' ? 'bg-orange-500' : ''); ?>

                                <?php echo e($type === 'Other' ? 'bg-gray-500' : ''); ?>">
                            </div>
                            <span class="text-sm text-gray-700"><?php echo e($type); ?></span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm font-semibold text-gray-900 mr-2"><?php echo e($count); ?></span>
                            <span class="text-xs text-gray-500">
                                (<?php echo e($coaStats['total'] > 0 ? round($count / $coaStats['total'] * 100, 1) : 0); ?>%)
                            </span>
                        </div>
                    </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recently Created COAs -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recently Created</h3>
                <a href="<?php echo e(route('coa.index')); ?>" class="text-sm text-blue-600 hover:text-blue-800">View All →</a>
            </div>
            <div class="space-y-3">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $recentCoas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900"><?php echo e($coa->coa_code); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($coa->coa_desc); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500"><?php echo e($coa->rec_datecreated->diffForHumans()); ?></p>
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                            <?php echo e($coa->account_type); ?>

                        </span>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="mt-2">No recent COAs</p>
                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>

        <!-- Recently Updated COAs -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recently Updated</h3>
                <a href="<?php echo e(route('coa.index')); ?>" class="text-sm text-blue-600 hover:text-blue-800">View All →</a>
            </div>
            <div class="space-y-3">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $recentUpdates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900"><?php echo e($coa->coa_code); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($coa->coa_desc); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500"><?php echo e($coa->rec_dateupdate->diffForHumans()); ?></p>
                        <p class="text-xs text-gray-400">by <?php echo e($coa->rec_userupdate); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <p class="mt-2">No recent updates</p>
                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="<?php echo e(route('coa.index')); ?>" 
               class="flex flex-col items-center justify-center p-4 bg-white rounded-lg shadow hover:shadow-lg hover:bg-blue-50 transition">
                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span class="text-sm font-semibold text-gray-800">Add COA</span>
            </a>

            <a href="<?php echo e(route('coa.index')); ?>" 
               class="flex flex-col items-center justify-center p-4 bg-white rounded-lg shadow hover:shadow-lg hover:bg-green-50 transition">
                <svg class="w-8 h-8 text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-sm font-semibold text-gray-800">View COA</span>
            </a>

            <a href="#" 
               class="flex flex-col items-center justify-center p-4 bg-white rounded-lg shadow opacity-60 cursor-not-allowed">
                <svg class="w-8 h-8 text-purple-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-sm font-semibold text-gray-700">Reports</span>
                <span class="text-xs font-medium text-gray-600">Coming Soon</span>
            </a>

            <a href="#" 
               class="flex flex-col items-center justify-center p-4 bg-white rounded-lg shadow opacity-60 cursor-not-allowed">
                <svg class="w-8 h-8 text-orange-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span class="text-sm font-semibold text-gray-700">Journal Entry</span>
                <span class="text-xs font-medium text-gray-600">Coming Soon</span>
            </a>
        </div>
    </div>
</div>
<?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/livewire/dashboard.blade.php ENDPATH**/ ?>