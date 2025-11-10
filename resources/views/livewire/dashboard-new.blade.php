<div class="container-fluid mt-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col">
            <h2 class="h3 mb-1">Welcome to AccAdmin Dashboard</h2>
            <p class="text-muted">Accounting Administration System - Comprehensive Overview</p>
        </div>
        <div class="col-auto">
            <div class="text-end">
                <small class="text-muted d-block">{{ now()->format('l, d F Y') }}</small>
                <small class="text-muted">{{ now()->format('H:i:s') }}</small>
            </div>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Total COA Accounts</p>
                            <h3 class="mb-0 text-primary">{{ number_format($coaStats['total']) }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-list-alt fa-2x text-primary"></i>
                        </div>
                    </div>
                    <small class="text-success">
                        <i class="fas fa-check-circle me-1"></i>{{ number_format($coaStats['active']) }} Active
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Modern Hierarchy</p>
                            <h3 class="mb-0 text-success">H1-H6</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-sitemap fa-2x text-success"></i>
                        </div>
                    </div>
                    <small class="text-muted">Flexible 6-level system</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Legacy Structure</p>
                            <h3 class="mb-0 text-warning">4 Levels</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-layer-group fa-2x text-warning"></i>
                        </div>
                    </div>
                    <small class="text-muted">Main → Sub1 → Sub2 → Detail</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">System Status</p>
                            <h3 class="mb-0 text-info">Active</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-server fa-2x text-info"></i>
                        </div>
                    </div>
                    <small class="text-success">
                        <i class="fas fa-circle me-1" style="font-size: 8px;"></i>All systems operational
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Menu Cards -->
    <div class="row mb-4">
        <!-- COA Management Card -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i>Chart of Accounts Management
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Kelola struktur akun dengan sistem modern (H1-H6) atau legacy (4 level).</p>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('coa.modern') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-rocket me-2"></i>COA Modern
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('coa.legacy') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-archive me-2"></i>COA Legacy
                            </a>
                        </div>
                        <div class="col-12 mt-2">
                            <a href="{{ route('coa.index') }}" class="btn btn-sm btn-link w-100">
                                <i class="fas fa-list me-2"></i>View All Accounts
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>Total: {{ number_format($coaStats['total']) }} accounts
                    </small>
                </div>
            </div>
        </div>

        <!-- Closing & Audit Card -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-lock me-2"></i>Closing & Audit System
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Sistem closing 4 layer dengan versioning dan audit trail lengkap.</p>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('closing.balance-sheet') }}" class="btn btn-outline-success w-100 btn-sm">
                                <i class="fas fa-balance-scale me-1"></i>Balance Sheet
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('closing.process') }}" class="btn btn-outline-primary w-100 btn-sm">
                                <i class="fas fa-tasks me-1"></i>Closing Process
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('closing.version-history') }}" class="btn btn-outline-info w-100 btn-sm">
                                <i class="fas fa-history me-1"></i>Version History
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('closing.comparison') }}" class="btn btn-outline-warning w-100 btn-sm">
                                <i class="fas fa-code-compare me-1"></i>Comparison
                            </a>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('closing.rollback') }}" class="btn btn-outline-danger w-100 btn-sm">
                                <i class="fas fa-undo me-1"></i>Rollback Interface
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>Multi-layer closing with versioning
                    </small>
                </div>
            </div>
        </div>

        <!-- Reports Card -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-info text-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Financial Reports
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Laporan keuangan lengkap untuk analisis dan decision making.</p>
                    
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action border-0 px-0">
                            <i class="fas fa-file-invoice me-2 text-primary"></i>Trial Balance
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0 px-0">
                            <i class="fas fa-balance-scale me-2 text-success"></i>Balance Sheet
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0 px-0">
                            <i class="fas fa-chart-line me-2 text-warning"></i>Income Statement
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0 px-0">
                            <i class="fas fa-money-bill-wave me-2 text-info"></i>Cash Flow
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>Export to PDF & Excel available
                    </small>
                </div>
            </div>
        </div>

        <!-- Transactions Card -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-warning text-dark border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>Transaction Management
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Input dan kelola transaksi jurnal dengan mudah.</p>
                    
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-warning">
                            <i class="fas fa-plus-circle me-2"></i>New Journal Entry
                        </a>
                        <a href="#" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>View All Transactions
                        </a>
                    </div>
                    
                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Recent Activity</small>
                            <span class="badge bg-secondary">0 Today</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>Supports multi-currency transactions
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2 text-primary"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('overview') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-chart-pie me-2"></i>System Overview
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-cog me-2"></i>Settings
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-users me-2"></i>User Management
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-file-export me-2"></i>Export Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-light border">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="mb-1">
                            <i class="fas fa-info-circle text-primary me-2"></i>System Information
                        </h6>
                        <small class="text-muted">
                            AccAdmin v1.0 - Accounting Administration System with Modern & Legacy COA Support, 
                            Multi-layer Closing System, and Comprehensive Audit Trail
                        </small>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="#" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-question-circle me-1"></i>Documentation
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-life-ring me-1"></i>Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
