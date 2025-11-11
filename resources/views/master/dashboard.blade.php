@extends('layouts.bootstrap')

@section('title', 'Master Data Dashboard')

@section('content')
<div class="container-fluid" x-data="masterDashboard()">
    <!-- Header -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="card-title mb-1">
                        <i class="fas fa-database me-2 text-primary"></i>Master Data Dashboard
                    </h2>
                    <p class="text-muted mb-0">
                        <small>Overview and Quick Access to All Master Data</small>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <button @click="refreshAll()" class="btn btn-primary" :disabled="loading">
                        <i class="fas fa-sync-alt me-1" :class="{'fa-spin': loading}"></i>
                        Refresh All
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="text-muted mt-2">Loading master data statistics...</p>
    </div>

    <!-- Statistics Cards -->
    <div x-show="!loading" class="row g-4 mb-4">
        <!-- COA Card -->
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">
                                Chart of Accounts
                            </h6>
                            <h2 class="mb-0" x-text="stats.coa.total"></h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-list-ul fa-2x text-primary"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-success">
                            <i class="fas fa-check-circle me-1"></i>
                            <span x-text="stats.coa.active"></span> Active
                        </small>
                        <a href="/coa-alpine" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-arrow-right me-1"></i>View
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Card -->
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">
                                Banks
                            </h6>
                            <h2 class="mb-0" x-text="stats.bank.total"></h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-university fa-2x text-info"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-success">
                            <i class="fas fa-check-circle me-1"></i>
                            <span x-text="stats.bank.active"></span> Active
                        </small>
                        <a href="/master/bank" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-arrow-right me-1"></i>View
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Area Card -->
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">
                                Areas / Branches
                            </h6>
                            <h2 class="mb-0" x-text="stats.area.total"></h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-map-marker-alt fa-2x text-success"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-success">
                            <i class="fas fa-check-circle me-1"></i>
                            <span x-text="stats.area.active"></span> Active
                        </small>
                        <a href="/master/area" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-arrow-right me-1"></i>View
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Card -->
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">
                                Vendors / Suppliers
                            </h6>
                            <h2 class="mb-0" x-text="stats.vendor.total"></h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-users fa-2x text-warning"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-success">
                            <i class="fas fa-check-circle me-1"></i>
                            <span x-text="stats.vendor.active"></span> Active
                        </small>
                        <a href="/master/vendor" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-arrow-right me-1"></i>View
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Grid -->
    <div x-show="!loading" class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-th-large me-2"></i>Quick Access
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- COA Section -->
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="mb-3">
                                    <i class="fas fa-list-ul text-primary me-2"></i>Chart of Accounts
                                </h6>
                                <div class="d-grid gap-2">
                                    <a href="/coa-alpine" class="btn btn-sm btn-outline-primary text-start">
                                        <i class="fas fa-layer-group me-2"></i>COA Modern View
                                    </a>
                                    <a href="/coa-legacy" class="btn btn-sm btn-outline-primary text-start">
                                        <i class="fas fa-sitemap me-2"></i>COA Legacy (4-Level)
                                    </a>
                                    <a href="/coa-full-hierarchy" class="btn btn-sm btn-outline-primary text-start">
                                        <i class="fas fa-project-diagram me-2"></i>Full Hierarchy
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Other Masters Section -->
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="mb-3">
                                    <i class="fas fa-database text-info me-2"></i>Master Data
                                </h6>
                                <div class="d-grid gap-2">
                                    <a href="/master/bank" class="btn btn-sm btn-outline-info text-start">
                                        <i class="fas fa-university me-2"></i>Bank Management
                                    </a>
                                    <a href="/master/area" class="btn btn-sm btn-outline-success text-start">
                                        <i class="fas fa-map-marker-alt me-2"></i>Area / Branch Management
                                    </a>
                                    <a href="/master/vendor" class="btn btn-sm btn-outline-warning text-start">
                                        <i class="fas fa-users me-2"></i>Vendor / Supplier Management
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Last Updated Info -->
    <div x-show="!loading && stats.lastUpdated" class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-info-circle text-muted me-2"></i>
                            <small class="text-muted">
                                Last updated: <span x-text="stats.lastUpdated"></span>
                            </small>
                        </div>
                        <div>
                            <small class="text-muted">
                                System: AccAdmin v1.0 | Powered by Alpine.js
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function masterDashboard() {
    return {
        loading: true,
        stats: {
            coa: { total: 0, active: 0 },
            bank: { total: 0, active: 0 },
            area: { total: 0, active: 0 },
            vendor: { total: 0, active: 0 },
            lastUpdated: null
        },

        init() {
            this.loadStats();
        },

        async loadStats() {
            this.loading = true;
            try {
                const response = await fetch('/api/master/stats');
                const data = await response.json();
                
                if (data.success) {
                    this.stats = data.data;
                    this.stats.lastUpdated = new Date().toLocaleString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            } finally {
                this.loading = false;
            }
        },

        refreshAll() {
            this.loadStats();
        }
    }
}
</script>
@endsection
