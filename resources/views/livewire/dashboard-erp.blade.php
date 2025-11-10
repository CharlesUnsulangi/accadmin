<style>
    /* Menggunakan font Inter */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    .dashboard-container {
        font-family: 'Inter', sans-serif;
    }

    /* KPI Card styling */
    .kpi-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        background-color: #fff;
    }
    
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .kpi-card .card-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem;
    }
    
    .kpi-card .kpi-icon {
        font-size: 2.5rem;
        padding: 15px;
        border-radius: 10px;
        color: #fff;
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .kpi-card .text-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #212529;
    }
    
    .kpi-card .text-label {
        font-size: 0.9rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    /* Chart container styling */
    .chart-container {
        background-color: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        height: 400px;
    }
    
    .chart-title {
        font-weight: 600;
        color: #343a40;
        margin-bottom: 15px;
        font-size: 1.1rem;
    }

    /* Table styling */
    .table-container {
        background-color: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .table-responsive {
        max-height: 350px;
        overflow-y: auto;
    }
    
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 0.875rem;
        border-bottom: 2px solid #dee2e6;
    }
    
    .table .badge {
        font-weight: 500;
        padding: 5px 10px;
        font-size: 0.75rem;
    }

    .top-navbar {
        border-radius: 12px;
        background-color: #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 15px 20px;
        margin-bottom: 1.5rem;
    }
</style>

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
            <div class="card kpi-card">
                <div class="card-body">
                    <div>
                        <div class="text-value">{{ number_format($coaStats['total']) }}</div>
                        <div class="text-label">Total COA Accounts</div>
                    </div>
                    <div class="kpi-icon bg-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card">
                <div class="card-body">
                    <div>
                        <div class="text-value">{{ number_format($coaStats['active']) }}</div>
                        <div class="text-label">Active Accounts</div>
                    </div>
                    <div class="kpi-icon bg-primary">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card">
                <div class="card-body">
                    <div>
                        <div class="text-value">4 Layers</div>
                        <div class="text-label">Closing System</div>
                    </div>
                    <div class="kpi-icon bg-warning">
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card">
                <div class="card-body">
                    <div>
                        <div class="text-value">Online</div>
                        <div class="text-label">System Status</div>
                    </div>
                    <div class="kpi-icon bg-info">
                        <i class="fas fa-server"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Cards Row -->
    <div class="row g-4 mb-4">
        <!-- COA Management -->
        <div class="col-lg-6">
            <div class="table-container" style="height: 100%;">
                <h5 class="chart-title">
                    <i class="fas fa-book text-primary me-2"></i>Chart of Accounts Management
                </h5>
                <p class="text-muted small mb-3">Kelola struktur akun dengan sistem modern atau legacy</p>
                
                <div class="list-group">
                    <a href="{{ route('coa.modern') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-rocket text-primary me-2"></i>
                            <strong>COA Modern (H1-H6)</strong>
                            <br>
                            <small class="text-muted">Flexible 6-level hierarchy system</small>
                        </div>
                        <i class="fas fa-arrow-right text-muted"></i>
                    </a>
                    <a href="{{ route('coa.legacy') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-archive text-warning me-2"></i>
                            <strong>COA Legacy (4 Level)</strong>
                            <br>
                            <small class="text-muted">Main → Sub1 → Sub2 → Detail</small>
                        </div>
                        <i class="fas fa-arrow-right text-muted"></i>
                    </a>
                    <a href="{{ route('coa.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-list text-secondary me-2"></i>
                            <strong>View All Accounts</strong>
                            <br>
                            <small class="text-muted">Complete COA listing</small>
                        </div>
                        <i class="fas fa-arrow-right text-muted"></i>
                    </a>
                </div>
                
                <div class="mt-3 pt-3 border-top">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Total: <strong>{{ number_format($coaStats['total']) }}</strong> accounts
                    </small>
                </div>
            </div>
        </div>

        <!-- Closing & Audit -->
        <div class="col-lg-6">
            <div class="table-container" style="height: 100%;">
                <h5 class="chart-title">
                    <i class="fas fa-lock text-success me-2"></i>Closing & Audit System
                </h5>
                <p class="text-muted small mb-3">Multi-layer closing dengan versioning lengkap</p>
                
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('closing.balance-sheet') }}" class="btn btn-outline-success w-100 text-start">
                            <i class="fas fa-balance-scale me-2"></i>Balance Sheet
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('closing.process') }}" class="btn btn-outline-primary w-100 text-start">
                            <i class="fas fa-tasks me-2"></i>Closing Process
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('closing.version-history') }}" class="btn btn-outline-info w-100 text-start">
                            <i class="fas fa-history me-2"></i>Version History
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('closing.comparison') }}" class="btn btn-outline-warning w-100 text-start">
                            <i class="fas fa-code-compare me-2"></i>Comparison
                        </a>
                    </div>
                    <div class="col-12">
                        <a href="{{ route('closing.rollback') }}" class="btn btn-outline-danger w-100 text-start">
                            <i class="fas fa-undo me-2"></i>Rollback Interface
                        </a>
                    </div>
                </div>
                
                <div class="mt-3 pt-3 border-top">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        4 closing layers with audit trail
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4 mb-4">
        <!-- Financial Reports -->
        <div class="col-md-6">
            <div class="table-container">
                <h5 class="chart-title">
                    <i class="fas fa-chart-bar text-info me-2"></i>Financial Reports
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Laporan</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <i class="fas fa-file-invoice text-primary me-2"></i>
                                    <strong>Trial Balance</strong>
                                </td>
                                <td><span class="badge bg-success">Available</span></td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fas fa-balance-scale text-success me-2"></i>
                                    <strong>Balance Sheet</strong>
                                </td>
                                <td><span class="badge bg-success">Available</span></td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fas fa-chart-line text-warning me-2"></i>
                                    <strong>Income Statement</strong>
                                </td>
                                <td><span class="badge bg-warning text-dark">In Progress</span></td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-outline-secondary disabled">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fas fa-money-bill-wave text-info me-2"></i>
                                    <strong>Cash Flow</strong>
                                </td>
                                <td><span class="badge bg-success">Available</span></td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-md-6">
            <div class="table-container">
                <h5 class="chart-title">
                    <i class="fas fa-clock text-warning me-2"></i>Recent Activities
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Activity</th>
                                <th scope="col">User</th>
                                <th scope="col">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <i class="fas fa-plus-circle text-success me-2"></i>
                                    COA Account Created
                                </td>
                                <td>{{ Auth::user()->name ?? 'Admin' }}</td>
                                <td><small class="text-muted">2 hours ago</small></td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fas fa-edit text-primary me-2"></i>
                                    Balance Sheet Updated
                                </td>
                                <td>{{ Auth::user()->name ?? 'Admin' }}</td>
                                <td><small class="text-muted">5 hours ago</small></td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fas fa-lock text-warning me-2"></i>
                                    Monthly Closing Initiated
                                </td>
                                <td>{{ Auth::user()->name ?? 'Admin' }}</td>
                                <td><small class="text-muted">1 day ago</small></td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fas fa-file-export text-info me-2"></i>
                                    Report Exported
                                </td>
                                <td>{{ Auth::user()->name ?? 'Admin' }}</td>
                                <td><small class="text-muted">2 days ago</small></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="table-container">
                <h5 class="chart-title">
                    <i class="fas fa-bolt text-danger me-2"></i>Quick Actions
                </h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('overview') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-chart-pie d-block fs-3 mb-2"></i>
                            System Overview
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-plus-circle d-block fs-3 mb-2"></i>
                            New Transaction
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-file-export d-block fs-3 mb-2"></i>
                            Export Data
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-cog d-block fs-3 mb-2"></i>
                            Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
