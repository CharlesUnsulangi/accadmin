<style>
    .gradient-card {
        background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary-rgb) 100%);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .gradient-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
    .stat-card {
        border-radius: 15px;
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.2) !important;
    }
    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }
    .menu-card {
        border-radius: 20px;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        overflow: hidden;
        border: none;
    }
    .menu-card:hover {
        transform: scale(1.03);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2) !important;
    }
    .btn-modern {
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .gradient-bg-1 {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .gradient-bg-2 {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .gradient-bg-3 {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .gradient-bg-4 {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    .pulse {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
</style>

<div class="container-fluid mt-4">
    <!-- Welcome Header with Gradient -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
                <div class="card-body text-white p-4">
                    <h2 class="h3 mb-2 fw-bold">
                        <i class="fas fa-chart-line me-2"></i>Welcome to AccAdmin Dashboard
                    </h2>
                    <p class="mb-0 opacity-75">Accounting Administration System - Modern & Powerful</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 20px;">
                <div class="card-body text-white text-center p-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-calendar-day fa-2x me-3"></i>
                        <div class="text-start">
                            <div class="fw-bold">{{ now()->format('d M Y') }}</div>
                            <small class="opacity-75">{{ now()->format('l') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Overview with Gradients -->
    <div class="row mb-4 g-4">
    <!-- Statistics Overview with Gradients -->
    <div class="row mb-4 g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-lg stat-card gradient-bg-1">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="mb-1 opacity-75">Total COA Accounts</p>
                            <h2 class="mb-0 fw-bold">{{ number_format($coaStats['total']) }}</h2>
                        </div>
                        <div class="stat-icon bg-white bg-opacity-25">
                            <i class="fas fa-list-alt"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2"></i>
                        <small>{{ number_format($coaStats['active']) }} Active Accounts</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-lg stat-card gradient-bg-2">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="mb-1 opacity-75">Modern System</p>
                            <h2 class="mb-0 fw-bold">H1-H6</h2>
                        </div>
                        <div class="stat-icon bg-white bg-opacity-25">
                            <i class="fas fa-sitemap"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-layer-group me-2"></i>
                        <small>Flexible 6-Level Hierarchy</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-lg stat-card gradient-bg-3">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="mb-1 opacity-75">Legacy Structure</p>
                            <h2 class="mb-0 fw-bold">4 Levels</h2>
                        </div>
                        <div class="stat-icon bg-white bg-opacity-25">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-arrow-right me-2"></i>
                        <small>Main → Sub1 → Sub2 → Detail</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-lg stat-card gradient-bg-4">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="mb-1 opacity-75">System Status</p>
                            <h2 class="mb-0 fw-bold">Online</h2>
                        </div>
                        <div class="stat-icon bg-white bg-opacity-25 pulse">
                            <i class="fas fa-server"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-circle me-2" style="font-size: 8px;"></i>
                        <small>All Systems Operational</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Menu Cards -->
    <div class="row mb-4 g-4">
        <!-- COA Management Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-lg menu-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon bg-white bg-opacity-25 me-3">
                            <i class="fas fa-book"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Chart of Accounts</h5>
                            <small class="opacity-75">Management System</small>
                        </div>
                    </div>
                    <p class="mb-4 opacity-90">Kelola struktur akun dengan sistem modern (H1-H6) atau legacy (4 level).</p>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('coa.modern') }}" class="btn btn-light btn-modern w-100">
                                <i class="fas fa-rocket me-2"></i>COA Modern
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('coa.legacy') }}" class="btn btn-outline-light btn-modern w-100">
                                <i class="fas fa-archive me-2"></i>COA Legacy
                            </a>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('coa.index') }}" class="btn btn-sm btn-link text-white w-100 mt-2">
                                <i class="fas fa-list me-2"></i>View All Accounts →
                            </a>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top border-white border-opacity-25">
                        <small class="opacity-75">
                            <i class="fas fa-check-circle me-1"></i>{{ number_format($coaStats['total']) }} total accounts
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Closing & Audit Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-lg menu-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon bg-white bg-opacity-25 me-3">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Closing & Audit</h5>
                            <small class="opacity-75">Multi-Layer System</small>
                        </div>
                    </div>
                    <p class="mb-4 opacity-90">Sistem closing 4 layer dengan versioning dan audit trail lengkap.</p>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('closing.balance-sheet') }}" class="btn btn-light btn-modern w-100 btn-sm">
                                <i class="fas fa-balance-scale me-1"></i>Balance Sheet
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('closing.process') }}" class="btn btn-outline-light btn-modern w-100 btn-sm">
                                <i class="fas fa-tasks me-1"></i>Process
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('closing.version-history') }}" class="btn btn-outline-light btn-modern w-100 btn-sm">
                                <i class="fas fa-history me-1"></i>History
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('closing.comparison') }}" class="btn btn-outline-light btn-modern w-100 btn-sm">
                                <i class="fas fa-code-compare me-1"></i>Compare
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('closing.rollback') }}" class="btn btn-outline-light btn-modern w-100 btn-sm">
                                <i class="fas fa-undo me-1"></i>Rollback
                            </a>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top border-white border-opacity-25">
                        <small class="opacity-75">
                            <i class="fas fa-check-circle me-1"></i>Multi-layer with versioning
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-lg menu-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon bg-white bg-opacity-25 me-3">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Financial Reports</h5>
                            <small class="opacity-75">Comprehensive Analysis</small>
                        </div>
                    </div>
                    <p class="mb-4 opacity-90">Laporan keuangan lengkap untuk analisis dan decision making.</p>
                    
                    <div class="list-group bg-transparent">
                        <a href="#" class="list-group-item list-group-item-action bg-white bg-opacity-10 text-white border-0 rounded mb-2">
                            <i class="fas fa-file-invoice me-2"></i>Trial Balance
                            <i class="fas fa-arrow-right float-end"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action bg-white bg-opacity-10 text-white border-0 rounded mb-2">
                            <i class="fas fa-balance-scale me-2"></i>Balance Sheet
                            <i class="fas fa-arrow-right float-end"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action bg-white bg-opacity-10 text-white border-0 rounded mb-2">
                            <i class="fas fa-chart-line me-2"></i>Income Statement
                            <i class="fas fa-arrow-right float-end"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action bg-white bg-opacity-10 text-white border-0 rounded">
                            <i class="fas fa-money-bill-wave me-2"></i>Cash Flow
                            <i class="fas fa-arrow-right float-end"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-lg menu-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon bg-white bg-opacity-25 me-3">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Transactions</h5>
                            <small class="opacity-75">Journal Management</small>
                        </div>
                    </div>
                    <p class="mb-4 opacity-90">Input dan kelola transaksi jurnal dengan mudah dan cepat.</p>
                    
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-light btn-modern">
                            <i class="fas fa-plus-circle me-2"></i>New Journal Entry
                        </a>
                        <a href="#" class="btn btn-outline-light btn-modern">
                            <i class="fas fa-list me-2"></i>View All Transactions
                        </a>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top border-white border-opacity-25">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="opacity-75">Recent Activity</small>
                            <span class="badge bg-white text-dark">0 Today</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg" style="border-radius: 20px; background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                <div class="card-body p-4">
                    <h6 class="mb-3 fw-bold text-dark">
                        <i class="fas fa-bolt me-2 text-danger"></i>Quick Actions
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('overview') }}" class="btn btn-dark btn-modern w-100">
                                <i class="fas fa-chart-pie me-2"></i>System Overview
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="btn btn-outline-dark btn-modern w-100">
                                <i class="fas fa-cog me-2"></i>Settings
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="btn btn-outline-dark btn-modern w-100">
                                <i class="fas fa-users me-2"></i>User Management
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="btn btn-outline-dark btn-modern w-100">
                                <i class="fas fa-file-export me-2"></i>Export Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow" style="border-radius: 15px; background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-2 fw-bold text-dark">
                                <i class="fas fa-info-circle me-2"></i>AccAdmin v1.0 - Accounting Administration System
                            </h6>
                            <p class="mb-0 text-dark opacity-75 small">
                                Modern & Legacy COA Support • Multi-layer Closing System • Comprehensive Audit Trail
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="#" class="btn btn-dark btn-sm btn-modern me-2">
                                <i class="fas fa-question-circle me-1"></i>Documentation
                            </a>
                            <a href="#" class="btn btn-outline-dark btn-sm btn-modern">
                                <i class="fas fa-life-ring me-1"></i>Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
