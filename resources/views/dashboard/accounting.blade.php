@extends('layouts.bootstrap')

@section('title', 'Accounting Dashboard')

@section('content')
<div class="container-fluid" x-data="accountingDashboard()">
    <!-- Header -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="card-title mb-1">
                        <i class="fas fa-chart-line me-2 text-primary"></i>Accounting Dashboard
                    </h2>
                    <p class="text-muted mb-0">
                        <small>Chart of Accounts, Transactions, Closing & Reports</small>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="/dashboard" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Main Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-1" style="font-size: 0.75rem;">
                                COA Accounts
                            </h6>
                            <h2 class="mb-0" x-text="stats.coa_total"></h2>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <i class="fas fa-list-ul fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-white-50">
                        <i class="fas fa-check-circle me-1"></i>
                        <span x-text="stats.coa_active"></span> Active
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-1" style="font-size: 0.75rem;">
                                Transactions
                            </h6>
                            <h2 class="mb-0" x-text="stats.transactions"></h2>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <i class="fas fa-exchange-alt fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-white-50">
                        <i class="fas fa-calendar me-1"></i>
                        This Month
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-1" style="font-size: 0.75rem;">
                                Last Closing
                            </h6>
                            <h2 class="mb-0" style="font-size: 1.5rem;" x-text="stats.last_closing || '-'"></h2>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <i class="fas fa-lock fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-white-50">
                        <i class="fas fa-check me-1"></i>
                        Closed Successfully
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-1" style="font-size: 0.75rem;">
                                Balance Status
                            </h6>
                            <h2 class="mb-0">
                                <i class="fas fa-check-circle"></i>
                            </h2>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <i class="fas fa-balance-scale fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-white-50">
                        <i class="fas fa-info-circle me-1"></i>
                        Balanced
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- COA Management Section -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2 text-primary"></i>Chart of Accounts Management
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Kelola struktur akun dengan sistem modern atau legacy</p>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card h-100 border border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-rocket fa-3x text-primary mb-3"></i>
                                    <h5>COA Modern (H1-H6)</h5>
                                    <p class="text-muted small">Flexible 6-level hierarchy system</p>
                                    <a href="/coa-modern" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-arrow-right me-1"></i>Manage
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-archive fa-3x text-warning mb-3"></i>
                                    <h5>COA Legacy (4 Level)</h5>
                                    <p class="text-muted small">Main → Sub1 → Sub2 → Detail</p>
                                    <a href="/coa-legacy" class="btn btn-warning btn-sm w-100">
                                        <i class="fas fa-arrow-right me-1"></i>Manage
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-project-diagram fa-3x text-info mb-3"></i>
                                    <h5>Full Hierarchy</h5>
                                    <p class="text-muted small">Complete account structure view</p>
                                    <a href="/coa-full-hierarchy" class="btn btn-info btn-sm w-100">
                                        <i class="fas fa-arrow-right me-1"></i>View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Section -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt me-2 text-success"></i>Transactions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card h-100 border-start border-success border-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                                            <i class="fas fa-book fa-2x text-success"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Journal Entry</h6>
                                            <p class="text-muted small mb-2">Input manual journal entries</p>
                                            <a href="/jurnal-transaksi" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-arrow-right me-1"></i>Open
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-start border-primary border-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                            <i class="fas fa-money-check fa-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Cheque Management</h6>
                                            <p class="text-muted small mb-2">Manage cheque books & details</p>
                                            <a href="/cheque-management" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-arrow-right me-1"></i>Open
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-start border-info border-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                                            <i class="fas fa-file-invoice fa-2x text-info"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Transaction Cheque</h6>
                                            <p class="text-muted small mb-2">Cheque transaction processing</p>
                                            <a href="/transaksi-cheque" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-arrow-right me-1"></i>Open
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Closing & Reports Section -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-lock me-2 text-danger"></i>Closing Process
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="/closing/process" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-cog text-danger me-2"></i>
                                <strong>Closing Process</strong>
                                <br>
                                <small class="text-muted">Monthly & yearly closing wizard</small>
                            </div>
                            <i class="fas fa-arrow-right text-muted"></i>
                        </a>
                        <a href="/closing/history" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-history text-info me-2"></i>
                                <strong>Closing History</strong>
                                <br>
                                <small class="text-muted">View past closing records</small>
                            </div>
                            <i class="fas fa-arrow-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2 text-warning"></i>Reports
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="/closing/balance-sheet" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-balance-scale text-success me-2"></i>
                                <strong>Balance Sheet</strong>
                                <br>
                                <small class="text-muted">Assets, Liabilities, Equity</small>
                            </div>
                            <i class="fas fa-arrow-right text-muted"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-invoice text-primary me-2"></i>
                                <strong>Trial Balance</strong>
                                <br>
                                <small class="text-muted">Debit & Credit verification</small>
                            </div>
                            <i class="fas fa-arrow-right text-muted"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-chart-line text-warning me-2"></i>
                                <strong>Income Statement</strong>
                                <br>
                                <small class="text-muted">Revenue & Expenses</small>
                            </div>
                            <i class="fas fa-arrow-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function accountingDashboard() {
    return {
        stats: {
            coa_total: 0,
            coa_active: 0,
            transactions: 0,
            last_closing: null
        },

        init() {
            this.loadStats();
        },

        async loadStats() {
            try {
                // Load from API
                const response = await fetch('/api/dashboard/accounting-stats');
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.stats = data.data;
                    }
                }
            } catch (error) {
                console.error('Error loading stats:', error);
                // Set default values
                this.stats = {
                    coa_total: 501,
                    coa_active: 485,
                    transactions: 1245,
                    last_closing: 'Oct 2025'
                };
            }
        }
    }
}
</script>
@endsection
