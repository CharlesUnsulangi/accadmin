

<?php $__env->startSection('title', 'Chart of Accounts'); ?>

<?php $__env->startSection('page-title', 'Chart of Accounts'); ?>
<?php $__env->startSection('page-description', 'Manage your chart of accounts'); ?>

<?php $__env->startSection('content'); ?>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('coa-management');

$__html = app('livewire')->mount($__name, $__params, 'lw-2486246642-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/coa/index.blade.php ENDPATH**/ ?>