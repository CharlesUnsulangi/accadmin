@extends('layouts.bootstrap')

@section('title', 'Admin IT Dashboard')

@section('content')
<div class="container-fluid" x-data="adminItDashboard()">
    <!-- Header -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="card-title mb-1">
                        <i class="fas fa-tools me-2 text-danger"></i>Admin IT Dashboard
                    </h2>
                    <p class="text-muted mb-0">
                        <small>Database Management, Documentation & System Monitoring</small>
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
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-1" style="font-size: 0.75rem;">
                                Database Tables
                            </h6>
                            <h2 class="mb-0" x-text="stats.tables"></h2>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <i class="fas fa-table fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-white-50">
                        <i class="fas fa-database me-1"></i>
                        AccAdmin DB
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
                                Stored Procedures
                            </h6>
                            <h2 class="mb-0" x-text="stats.stored_procs"></h2>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <i class="fas fa-cogs fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-white-50">
                        <i class="fas fa-check me-1"></i>
                        Registered
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-1" style="font-size: 0.75rem;">
                                Database Size
                            </h6>
                            <h2 class="mb-0" style="font-size: 1.5rem;" x-text="stats.db_size"></h2>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <i class="fas fa-hdd fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-white-50">
                        <i class="fas fa-info-circle me-1"></i>
                        Total Size
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-1" style="font-size: 0.75rem;">
                                System Uptime
                            </h6>
                            <h2 class="mb-0" style="font-size: 1.5rem;" x-text="stats.uptime"></h2>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <i class="fas fa-server fa-2x"></i>
                        </div>
                    </div>
                    <small class="text-white-50">
                        <i class="fas fa-check-circle me-1"></i>
                        Healthy
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Database Management Section -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-database me-2 text-primary"></i>Database Management
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card h-100 border-start border-primary border-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                            <i class="fas fa-table fa-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Table Browser</h6>
                                            <p class="text-muted small mb-2">Browse and manage database tables</p>
                                            <a href="/database-tables" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-arrow-right me-1"></i>Open
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-start border-success border-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                                            <i class="fas fa-cogs fa-2x text-success"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Stored Procedures</h6>
                                            <p class="text-muted small mb-2">Manage SP registry</p>
                                            <a href="/admin-sp" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-arrow-right me-1"></i>Manage
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-start border-warning border-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                                            <i class="fas fa-cubes fa-2x text-warning"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Applications</h6>
                                            <p class="text-muted small mb-2">Manage application registry</p>
                                            <a href="/applications" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-arrow-right me-1"></i>Manage
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <div class="card h-100 border-start border-info border-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                                            <i class="fas fa-chart-bar fa-2x text-info"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Table Access Stats</h6>
                                            <p class="text-muted small mb-2">Monitor table usage</p>
                                            <a href="/table-access-stats" class="btn btn-sm btn-outline-info">
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
        </div>
    </div>

    <!-- Documentation Section -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2 text-info"></i>Documentation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card h-100 border border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-book-open fa-3x text-info mb-3"></i>
                                    <h5>IT Documentation</h5>
                                    <p class="text-muted small">Table structures, schemas, and technical docs</p>
                                    <a href="/it-documentation" class="btn btn-info btn-sm w-100">
                                        <i class="fas fa-arrow-right me-1"></i>View Docs
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border border-secondary">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-alt fa-3x text-secondary mb-3"></i>
                                    <h5>Table Browser Docs</h5>
                                    <p class="text-muted small">Comprehensive table documentation</p>
                                    <a href="/docs-tables" class="btn btn-secondary btn-sm w-100">
                                        <i class="fas fa-arrow-right me-1"></i>Browse
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-code fa-3x text-primary mb-3"></i>
                                    <h5>API Documentation</h5>
                                    <p class="text-muted small">REST API endpoints & usage</p>
                                    <button class="btn btn-primary btn-sm w-100" disabled>
                                        <i class="fas fa-clock me-1"></i>Coming Soon
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2 text-success"></i>Recent Database Activities
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item" x-show="activities.length === 0">
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <p>No recent activities</p>
                            </div>
                        </div>
                        <template x-for="activity in activities" :key="activity.id">
                            <div class="d-flex mb-3">
                                <div class="me-3">
                                    <span class="badge bg-primary rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-database"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <strong x-text="activity.title"></strong>
                                        <small class="text-muted" x-text="activity.time"></small>
                                    </div>
                                    <p class="text-muted small mb-0" x-text="activity.description"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function adminItDashboard() {
    return {
        stats: {
            tables: 0,
            stored_procs: 0,
            db_size: '0 MB',
            uptime: '99.9%'
        },
        activities: [],

        init() {
            this.loadStats();
            this.loadActivities();
        },

        async loadStats() {
            try {
                const response = await fetch('/api/dashboard/admin-it-stats');
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.stats = data.data;
                    }
                }
            } catch (error) {
                console.error('Error loading stats:', error);
                // Default values
                this.stats = {
                    tables: 45,
                    stored_procs: 12,
                    db_size: '500 MB',
                    uptime: '99.9%'
                };
            }
        },

        async loadActivities() {
            // Placeholder for recent activities
            this.activities = [
                {
                    id: 1,
                    title: 'Table metadata updated',
                    description: 'ms_acc_bank - 12 records updated',
                    time: '10 minutes ago'
                },
                {
                    id: 2,
                    title: 'Stored procedure executed',
                    description: 'sp_calculate_monthly completed successfully',
                    time: '1 hour ago'
                },
                {
                    id: 3,
                    title: 'Table browser accessed',
                    description: 'tr_acc_transaksi viewed by Admin',
                    time: '2 hours ago'
                }
            ];
        }
    }
}
</script>
@endsection
