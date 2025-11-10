<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>
<?php $__env->startSection('page-description', 'Accounting Administration System Overview'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .dashboard-container {
        font-family: 'Inter', sans-serif;
        background-color: #f8f9fa;
        padding: 0;
        margin: 0;
    }

    /* KPI Card styling with gradients */
    .kpi-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        overflow: hidden;
        position: relative;
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        transition: all 0.5s ease;
    }

    .kpi-card:hover::before {
        top: -60%;
        right: -60%;
    }
    
    .kpi-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .kpi-card.gradient-1 {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .kpi-card.gradient-2 {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .kpi-card.gradient-3 {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .kpi-card.gradient-4 {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .kpi-card .card-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.75rem;
        position: relative;
        z-index: 1;
    }
    
    .kpi-card .kpi-icon {
        font-size: 2.5rem;
        padding: 18px;
        border-radius: 14px;
        color: #fff;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        width: 75px;
        height: 75px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .kpi-card .text-value {
        font-size: 2rem;
        font-weight: 700;
        color: #fff;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    .kpi-card .text-label {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.95);
        margin-top: 0.35rem;
    }

    .table-container {
        background: #fff;
        padding: 28px;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .table-container:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .top-navbar {
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 20px 28px;
        margin-bottom: 1.5rem;
    }

    .btn-outline-primary, .btn-outline-success, .btn-outline-info, 
    .btn-outline-warning, .btn-outline-secondary, .btn-outline-danger {
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        border-width: 2px;
        padding: 10px 20px;
    }

    .btn-outline-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(13, 110, 253, 0.3);
    }

    .btn-outline-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(25, 135, 84, 0.3);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="dashboard-container">
    <?php echo $__env->make('dashboard-content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/dashboard.blade.php ENDPATH**/ ?>