@push('styles')
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

    /* Chart container styling */
    .chart-container {
        background: #fff;
        padding: 28px;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        height: 400px;
        transition: all 0.3s ease;
    }

    .chart-container:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }
    
    .chart-title {
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 18px;
        font-size: 1.15rem;
    }

    /* Table styling */
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
    
    .table-responsive {
        max-height: 350px;
        overflow-y: auto;
    }
    
    .table th {
        background-color: #f8f9fa;
        color: #2d3748;
        font-weight: 600;
        font-size: 0.875rem;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px;
    }

    .table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f1f5f9;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.005);
    }
    
    .table .badge {
        font-weight: 500;
        padding: 6px 12px;
        font-size: 0.75rem;
        border-radius: 20px;
    }

    .top-navbar {
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 20px 28px;
        margin-bottom: 1.5rem;
    }

    .list-group-item {
        border: 1px solid #e2e8f0;
        border-radius: 12px !important;
        margin-bottom: 10px;
        transition: all 0.3s ease;
        background: #fff;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
        border-color: #cbd5e0;
        transform: translateX(8px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
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

    .btn-outline-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(13, 202, 240, 0.3);
    }

    .btn-outline-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 193, 7, 0.3);
    }

    .btn-outline-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
    }

    .input-group-text {
        background-color: #f8f9fa;
        border: 1px solid #e2e8f0;
    }

    .form-control {
        border: 1px solid #e2e8f0;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }
</style>
@endpush

<div class="dashboard-container">

    <!-- Top Navbar -->
    <div class="top-navbar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 fw-bold text-dark">Dashboard</h1>
                <small class="text-muted">Selamat datang, {{ Auth::user()->name ?? 'Admin' }}</small>
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
                        <div class="text-value">{{ number_format($coaStats['total']) }}</div>
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
                        <div class="text-value">{{ number_format($coaStats['active']) }}</div>
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

    <!-- Main Content Row -->
    <div class="row g-4 mb-4">
        <!-- Left Column - COA Management & Closing -->
        <div class="col-lg-8">
            <!-- COA Management Group -->
            <div class="table-container mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="mb-1" style="color: white; font-weight: 700;">
                    <i class="fas fa-book me-2"></i>Chart of Accounts Management
                </h5>
                <p class="mb-3" style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">Kelola struktur akun dengan sistem modern atau legacy</p>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('coa.modern') }}" class="btn btn-light w-100 text-start" style="padding: 1rem; border-radius: 12px;">
                            <i class="fas fa-rocket text-primary fs-4 d-block mb-2"></i>
                            <strong class="d-block">COA Modern</strong>
                            <small class="text-muted">H1-H6 System</small>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('coa.legacy') }}" class="btn btn-light w-100 text-start" style="padding: 1rem; border-radius: 12px;">
                            <i class="fas fa-archive text-warning fs-4 d-block mb-2"></i>
                            <strong class="d-block">COA Legacy</strong>
                            <small class="text-muted">4 Level</small>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('coa.hierarchy') }}" class="btn btn-light w-100 text-start" style="padding: 1rem; border-radius: 12px;">
                            <i class="fas fa-list text-info fs-4 d-block mb-2"></i>
                            <strong class="d-block">View All</strong>
                            <small class="text-muted">Complete List</small>
                        </a>
                    </div>
                </div>
                
                <div class="mt-3 pt-3 border-top border-white border-opacity-25">
                    <div class="row text-center">
                        <div class="col-4">
                            <div style="color: rgba(255,255,255,0.9);">
                                <div class="fs-4 fw-bold">{{ number_format($coaStats['total']) }}</div>
                                <small>Total Accounts</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div style="color: rgba(255,255,255,0.9);">
                                <div class="fs-4 fw-bold">{{ number_format($coaStats['active']) }}</div>
                                <small>Active</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div style="color: rgba(255,255,255,0.9);">
                                <div class="fs-4 fw-bold">{{ number_format($coaStats['inactive']) }}</div>
                                <small>Inactive</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Closing & Reports Group - Combined Card -->
            <div class="table-container" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                <div class="row">
                    <!-- Closing Section -->
                    <div class="col-md-6 border-end border-white border-opacity-25">
                        <h6 class="mb-2" style="color: white; font-weight: 700;">
                            <i class="fas fa-lock me-2"></i>Closing & Audit
                        </h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('closing.balance-sheet') }}" class="btn btn-light btn-sm w-100 text-start">
                                    <i class="fas fa-balance-scale me-1"></i>Balance
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('closing.process') }}" class="btn btn-light btn-sm w-100 text-start">
                                    <i class="fas fa-tasks me-1"></i>Process
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('closing.version-history') }}" class="btn btn-light btn-sm w-100 text-start">
                                    <i class="fas fa-history me-1"></i>History
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('closing.rollback') }}" class="btn btn-light btn-sm w-100 text-start">
                                    <i class="fas fa-undo me-1"></i>Rollback
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Reports Section -->
                    <div class="col-md-6">
                        <h6 class="mb-2" style="color: white; font-weight: 700;">
                            <i class="fas fa-chart-bar me-2"></i>Financial Reports
                        </h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="#" class="btn btn-light btn-sm w-100 text-start">
                                    <i class="fas fa-file-invoice me-1"></i>Trial
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="#" class="btn btn-light btn-sm w-100 text-start">
                                    <i class="fas fa-balance-scale me-1"></i>Balance
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="#" class="btn btn-light btn-sm w-100 text-start">
                                    <i class="fas fa-chart-line me-1"></i>Income
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="#" class="btn btn-light btn-sm w-100 text-start">
                                    <i class="fas fa-money-bill-wave me-1"></i>Cash Flow
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <!-- Right Column - Stats & Quick Actions Combined -->
        <div class="col-lg-4">
            <div class="table-container" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                <!-- Quick Stats Section -->
                <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                    <h6 class="mb-3" style="color: white; font-weight: 700;">
                        <i class="fas fa-chart-pie me-2"></i>Quick Stats
                    </h6>
                    <div class="mb-2 pb-2 border-bottom border-white border-opacity-25">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Active</span>
                            <strong class="fs-5">{{ number_format($coaStats['active']) }}</strong>
                        </div>
                    </div>
                    <div class="mb-2 pb-2 border-bottom border-white border-opacity-25">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Inactive</span>
                            <strong class="fs-5">{{ number_format($coaStats['inactive']) }}</strong>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Total</span>
                            <strong class="fs-5">{{ number_format($coaStats['total']) }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Section -->
                <div>
                    <h6 class="mb-3" style="color: white; font-weight: 700;">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('coa.modern') }}" class="btn btn-light text-start">
                            <i class="fas fa-plus-circle me-2"></i>Add New Account
                        </a>
                        <button class="btn btn-light text-start">
                            <i class="fas fa-file-import me-2"></i>Import Data
                        </button>
                        <button class="btn btn-light text-start">
                            <i class="fas fa-file-export me-2"></i>Export Report
                        </button>
                        <a href="{{ route('overview') }}" class="btn btn-light text-start">
                            <i class="fas fa-chart-pie me-2"></i>System Overview
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activities & System Info - Combined Card -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="table-container" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                <div class="row">
                    <!-- Recent Activities Section -->
                    <div class="col-md-8 border-end border-white border-opacity-25">
                        <h6 class="mb-3" style="color: white; font-weight: 700;">
                            <i class="fas fa-history me-2"></i>Recent Activities
                        </h6>
                        <div class="row g-2">
                            <div class="col-3 text-center">
                                <div style="background: rgba(255,255,255,0.2); padding: 0.75rem; border-radius: 10px; backdrop-filter: blur(10px);">
                                    <i class="fas fa-plus-circle fs-3"></i>
                                    <div class="mt-1"><strong>5</strong></div>
                                    <small>New Accounts</small>
                                </div>
                            </div>
                            <div class="col-3 text-center">
                                <div style="background: rgba(255,255,255,0.2); padding: 0.75rem; border-radius: 10px; backdrop-filter: blur(10px);">
                                    <i class="fas fa-edit fs-3"></i>
                                    <div class="mt-1"><strong>12</strong></div>
                                    <small>Updates</small>
                                </div>
                            </div>
                            <div class="col-3 text-center">
                                <div style="background: rgba(255,255,255,0.2); padding: 0.75rem; border-radius: 10px; backdrop-filter: blur(10px);">
                                    <i class="fas fa-lock fs-3"></i>
                                    <div class="mt-1"><strong>2</strong></div>
                                    <small>Closings</small>
                                </div>
                            </div>
                            <div class="col-3 text-center">
                                <div style="background: rgba(255,255,255,0.2); padding: 0.75rem; border-radius: 10px; backdrop-filter: blur(10px);">
                                    <i class="fas fa-file-alt fs-3"></i>
                                    <div class="mt-1"><strong>45</strong></div>
                                    <small>Reports</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Info Section -->
                    <div class="col-md-4">
                        <h6 class="mb-3" style="color: white; font-weight: 700;">
                            <i class="fas fa-info-circle me-2"></i>System Info
                        </h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <div style="background: rgba(255,255,255,0.3); padding: 0.5rem; border-radius: 8px; text-align: center;">
                                    <small class="d-block">Database</small>
                                    <strong>MySQL 8.0</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="background: rgba(255,255,255,0.3); padding: 0.5rem; border-radius: 8px; text-align: center;">
                                    <small class="d-block">Laravel</small>
                                    <strong>11</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="background: rgba(255,255,255,0.3); padding: 0.5rem; border-radius: 8px; text-align: center;">
                                    <small class="d-block">PHP</small>
                                    <strong>8.2</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="background: rgba(255,255,255,0.3); padding: 0.5rem; border-radius: 8px; text-align: center;">
                                    <small class="d-block">Users</small>
                                    <strong>248</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>