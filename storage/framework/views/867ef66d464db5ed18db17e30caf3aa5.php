    <!-- Top Navbar -->
    <div class="top-navbar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 fw-bold text-dark">Dashboard</h1>
                <small class="text-muted">Selamat datang, <?php echo e(Auth::user()->name ?? 'Admin'); ?></small>
            </div>
            <div class="d-flex align-items-center">
                <div class="input-group me-3" style="width: 300px;">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                    <input class="form-control bg-light border-0" type="search" placeholder="Cari transaksi, laporan...">
                </div>
                <div class="position-relative me-3">
                    <i class="fas fa-bell fs-5 text-secondary"></i>
                    <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size: 0.6rem;">3</span>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card gradient-1">
                <div class="card-body">
                    <div>
                        <div class="text-value"><?php echo e(number_format($coaStats['total'])); ?></div>
                        <div class="text-label">Total COA Accounts</div>
                    </div>
                    <div class="kpi-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card gradient-2">
                <div class="card-body">
                    <div>
                        <div class="text-value"><?php echo e(number_format($coaStats['active'])); ?></div>
                        <div class="text-label">Active Accounts</div>
                    </div>
                    <div class="kpi-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card gradient-3">
                <div class="card-body">
                    <div>
                        <div class="text-value">4 Layers</div>
                        <div class="text-label">Closing System</div>
                    </div>
                    <div class="kpi-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card gradient-4">
                <div class="card-body">
                    <div>
                        <div class="text-value">Online</div>
                        <div class="text-label">System Status</div>
                    </div>
                    <div class="kpi-icon">
                        <i class="fas fa-server"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- COA Management Cards - Baris Pertama -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="table-container" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; height: 100%;">
                <div class="text-center">
                    <i class="fas fa-rocket fs-1 mb-3" style="color: rgba(255,255,255,0.9);"></i>
                    <h5 class="mb-2" style="color: white; font-weight: 700;">COA Modern</h5>
                    <p class="mb-3" style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">H1-H6 Flexible System</p>
                    <div class="mb-3">
                        <div class="fs-2 fw-bold"><?php echo e(number_format($hierarchyStats['h1'] ?? 0)); ?></div>
                        <small>Main Categories</small>
                    </div>
                    <a href="<?php echo e(route('coa.modern')); ?>" class="btn btn-light w-100">
                        <i class="fas fa-arrow-right me-2"></i>Open Modern COA
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="table-container" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; height: 100%;">
                <div class="text-center">
                    <i class="fas fa-archive fs-1 mb-3" style="color: rgba(255,255,255,0.9);"></i>
                    <h5 class="mb-2" style="color: white; font-weight: 700;">COA Legacy</h5>
                    <p class="mb-3" style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">4 Level Classic System</p>
                    <div class="mb-3">
                        <div class="fs-2 fw-bold"><?php echo e(number_format($coaStats['total'])); ?></div>
                        <small>Total Accounts</small>
                    </div>
                    <a href="<?php echo e(route('coa.legacy')); ?>" class="btn btn-light w-100">
                        <i class="fas fa-arrow-right me-2"></i>Open Legacy COA
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="table-container" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; height: 100%;">
                <div class="text-center">
                    <i class="fas fa-list fs-1 mb-3" style="color: rgba(255,255,255,0.9);"></i>
                    <h5 class="mb-2" style="color: white; font-weight: 700;">Full Hierarchy</h5>
                    <p class="mb-3" style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">Complete Account List</p>
                    <div class="mb-3">
                        <div class="fs-2 fw-bold"><?php echo e(number_format($coaStats['active'])); ?></div>
                        <small>Active Accounts</small>
                    </div>
                    <a href="<?php echo e(route('coa.hierarchy')); ?>" class="btn btn-light w-100">
                        <i class="fas fa-arrow-right me-2"></i>View All Accounts
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Closing & Reports - Baris Kedua -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="table-container" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                <h5 class="mb-1" style="color: white; font-weight: 700;">
                    <i class="fas fa-lock me-2"></i>Closing & Audit
                </h5>
                <p class="mb-3" style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">Multi-layer closing system with version control</p>
                
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <a href="<?php echo e(route('closing.balance-sheet')); ?>" class="btn btn-light w-100 text-start" style="padding: 0.75rem;">
                            <i class="fas fa-balance-scale text-success d-block fs-3 mb-2"></i>
                            <strong class="d-block">Balance Sheet</strong>
                            <small class="text-muted">Initial Balance</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?php echo e(route('closing.process')); ?>" class="btn btn-light w-100 text-start" style="padding: 0.75rem;">
                            <i class="fas fa-tasks text-primary d-block fs-3 mb-2"></i>
                            <strong class="d-block">Process</strong>
                            <small class="text-muted">Run Closing</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?php echo e(route('closing.version-history')); ?>" class="btn btn-light w-100 text-start" style="padding: 0.75rem;">
                            <i class="fas fa-history text-info d-block fs-3 mb-2"></i>
                            <strong class="d-block">History</strong>
                            <small class="text-muted">View Versions</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?php echo e(route('closing.rollback')); ?>" class="btn btn-light w-100 text-start" style="padding: 0.75rem;">
                            <i class="fas fa-undo text-warning d-block fs-3 mb-2"></i>
                            <strong class="d-block">Rollback</strong>
                            <small class="text-muted">Restore Data</small>
                        </a>
                    </div>
                </div>

                <div class="pt-2 border-top border-white border-opacity-25">
                    <div class="row text-center">
                        <div class="col-4">
                            <div style="color: rgba(255,255,255,0.9);">
                                <div class="fs-5 fw-bold">4</div>
                                <small>Layers</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div style="color: rgba(255,255,255,0.9);">
                                <div class="fs-5 fw-bold">12</div>
                                <small>Versions</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div style="color: rgba(255,255,255,0.9);">
                                <div class="fs-5 fw-bold">Oct</div>
                                <small>Last Close</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="table-container" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                <h5 class="mb-1" style="color: white; font-weight: 700;">
                    <i class="fas fa-chart-bar me-2"></i>Financial Reports
                </h5>
                <p class="mb-3" style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">Generate comprehensive financial statements</p>
                
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <a href="#" class="btn btn-light w-100 text-start" style="padding: 0.75rem;">
                            <i class="fas fa-file-invoice text-primary d-block fs-3 mb-2"></i>
                            <strong class="d-block">Trial Balance</strong>
                            <small class="text-muted">Debit & Credit</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="btn btn-light w-100 text-start" style="padding: 0.75rem;">
                            <i class="fas fa-balance-scale text-success d-block fs-3 mb-2"></i>
                            <strong class="d-block">Balance Sheet</strong>
                            <small class="text-muted">Assets & Liabilities</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="btn btn-light w-100 text-start" style="padding: 0.75rem;">
                            <i class="fas fa-chart-line text-info d-block fs-3 mb-2"></i>
                            <strong class="d-block">Income Statement</strong>
                            <small class="text-muted">P&L Report</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="btn btn-light w-100 text-start" style="padding: 0.75rem;">
                            <i class="fas fa-money-bill-wave text-warning d-block fs-3 mb-2"></i>
                            <strong class="d-block">Cash Flow</strong>
                            <small class="text-muted">Flow Analysis</small>
                        </a>
                    </div>
                </div>

                <div class="pt-2 border-top border-white border-opacity-25">
                    <div class="row text-center">
                        <div class="col-4">
                            <div style="color: rgba(255,255,255,0.9);">
                                <div class="fs-5 fw-bold">45</div>
                                <small>Generated</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div style="color: rgba(255,255,255,0.9);">
                                <div class="fs-5 fw-bold">PDF</div>
                                <small>Format</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div style="color: rgba(255,255,255,0.9);">
                                <div class="fs-5 fw-bold">Today</div>
                                <small>Latest</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & System - Baris Ketiga -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="table-container" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                <h5 class="mb-3 fw-bold text-dark">
                    <i class="fas fa-bolt me-2 text-warning"></i>Quick Actions
                </h5>
                
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <a href="<?php echo e(route('coa.modern')); ?>" class="btn btn-outline-primary w-100 text-start" style="padding: 1rem; border-width: 2px;">
                            <i class="fas fa-plus-circle fs-3 d-block mb-2"></i>
                            <strong class="d-block">Add Account</strong>
                            <small>Create new COA</small>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <button class="btn btn-outline-success w-100 text-start" style="padding: 1rem; border-width: 2px;">
                            <i class="fas fa-file-import fs-3 d-block mb-2"></i>
                            <strong class="d-block">Import</strong>
                            <small>Upload Excel/CSV</small>
                        </button>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <button class="btn btn-outline-info w-100 text-start" style="padding: 1rem; border-width: 2px;">
                            <i class="fas fa-file-export fs-3 d-block mb-2"></i>
                            <strong class="d-block">Export</strong>
                            <small>Download Reports</small>
                        </button>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="<?php echo e(route('overview')); ?>" class="btn btn-outline-warning w-100 text-start" style="padding: 1rem; border-width: 2px;">
                            <i class="fas fa-chart-pie fs-3 d-block mb-2"></i>
                            <strong class="d-block">Overview</strong>
                            <small>System Status</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="table-container" style="background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);">
                <h5 class="mb-3 fw-bold text-dark">
                    <i class="fas fa-info-circle me-2 text-primary"></i>System Info
                </h5>
                
                <div class="mb-2 pb-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-database me-2 text-success"></i>Database</span>
                        <strong>SQL Server</strong>
                    </div>
                </div>
                <div class="mb-2 pb-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fab fa-laravel me-2 text-danger"></i>Laravel</span>
                        <strong>11.x</strong>
                    </div>
                </div>
                <div class="mb-2 pb-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fab fa-php me-2 text-primary"></i>PHP</span>
                        <strong>8.2</strong>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle me-2 text-success"></i>Status</span>
                        <span class="badge bg-success">Online</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities - Baris Keempat -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="table-container" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="mb-3 fw-bold" style="color: white;">
                    <i class="fas fa-history me-2"></i>Recent Activities
                </h5>
                
                <div class="row g-3">
                    <?php $__currentLoopData = $recentActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-3 col-md-6">
                        <div style="background: rgba(255,255,255,0.15); padding: 1.25rem; border-radius: 12px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.25); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas <?php echo e($activity['icon']); ?> fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" style="font-weight: 600; color: white;"><?php echo e($activity['title']); ?></h6>
                                    <p class="mb-1" style="font-size: 0.85rem; color: rgba(255,255,255,0.9);"><?php echo e($activity['description']); ?></p>
                                    <small style="color: rgba(255,255,255,0.75);">
                                        <i class="fas fa-clock me-1"></i><?php echo e($activity['time']); ?>

                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
<?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/dashboard-content.blade.php ENDPATH**/ ?>