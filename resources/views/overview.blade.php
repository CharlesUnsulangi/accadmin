@extends('layouts.bootstrap')

@section('title', 'System Overview')

@section('content')
<div class="container-fluid py-4" x-data="systemOverview()">
    <!-- Page Header -->
    <div class="mb-4">
        <h2 class="mb-1">
            <i class="fas fa-chart-line text-primary me-2"></i>
            System Overview
        </h2>
        <p class="text-muted mb-0">Statistics and overview of your accounting system</p>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Loading statistics...</p>
    </div>

    <!-- Main Content -->
    <div x-show="!loading" style="display: none;">
        <!-- Statistics Cards Row 1: COA -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Total COA</p>
                                <h3 class="mb-0 fw-bold" x-text="stats.coa.total">0</h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-list-alt fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Active COA</p>
                                <h3 class="mb-0 fw-bold text-success" x-text="stats.coa.active">0</h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Inactive COA</p>
                                <h3 class="mb-0 fw-bold text-warning" x-text="stats.coa.inactive">0</h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-times-circle fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Last Updated</p>
                                <p class="mb-0 small" x-text="stats.lastUpdate">-</p>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-clock fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hierarchy Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-sitemap text-primary me-2"></i>
                            COA Hierarchy Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                    <span class="text-muted">Level H1:</span>
                                    <span class="badge bg-primary" x-text="stats.hierarchy.h1">0</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                    <span class="text-muted">Level H2:</span>
                                    <span class="badge bg-primary" x-text="stats.hierarchy.h2">0</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                    <span class="text-muted">Level H3:</span>
                                    <span class="badge bg-primary" x-text="stats.hierarchy.h3">0</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                    <span class="text-muted">Level H4:</span>
                                    <span class="badge bg-primary" x-text="stats.hierarchy.h4">0</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                    <span class="text-muted">Level H5:</span>
                                    <span class="badge bg-primary" x-text="stats.hierarchy.h5">0</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                    <span class="text-muted">Level H6:</span>
                                    <span class="badge bg-primary" x-text="stats.hierarchy.h6">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-history text-primary me-2"></i>
                            Recent Activities
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <template x-for="activity in stats.activities" :key="activity.title">
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex align-items-start">
                                        <div :class="'bg-' + activity.color + ' bg-opacity-10 p-2 rounded me-3'">
                                            <i :class="'fas ' + activity.icon + ' text-' + activity.color"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1" x-text="activity.title"></h6>
                                                <small class="text-muted" x-text="activity.time"></small>
                                            </div>
                                            <p class="mb-0 small text-muted" x-text="activity.description"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt text-warning me-2"></i>
                            Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="{{ route('coa.modern') }}" class="btn btn-outline-primary w-100 py-3">
                                    <i class="fas fa-list-alt fa-2x mb-2 d-block"></i>
                                    <span>Manage COA</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('jurnal.transaksi') }}" class="btn btn-outline-success w-100 py-3">
                                    <i class="fas fa-book fa-2x mb-2 d-block"></i>
                                    <span>Journal Entry</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('master.dashboard') }}" class="btn btn-outline-info w-100 py-3">
                                    <i class="fas fa-database fa-2x mb-2 d-block"></i>
                                    <span>Master Data</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('dashboard.admin.it') }}" class="btn btn-outline-warning w-100 py-3">
                                    <i class="fas fa-cog fa-2x mb-2 d-block"></i>
                                    <span>Admin IT</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function systemOverview() {
    return {
        loading: true,
        stats: {
            coa: {
                total: 0,
                active: 0,
                inactive: 0
            },
            hierarchy: {
                h1: 0,
                h2: 0,
                h3: 0,
                h4: 0,
                h5: 0,
                h6: 0
            },
            lastUpdate: '-',
            activities: []
        },

        init() {
            this.loadStats();
        },

        async loadStats() {
            try {
                const response = await fetch('/api/dashboard/overview-stats');
                const data = await response.json();
                
                this.stats = data;
                this.stats.lastUpdate = new Date().toLocaleString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (error) {
                console.error('Failed to load stats:', error);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection