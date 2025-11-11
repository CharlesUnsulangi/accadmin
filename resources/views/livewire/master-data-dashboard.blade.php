<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="card-title mb-1">
                        <i class="fas fa-database me-2 text-primary"></i>Master Data Dashboard
                    </h2>
                    <p class="text-muted mb-0">
                        <small>Overview of all master data tables in the system</small>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <button wire:click="refresh" class="btn btn-primary">
                        <i class="fas fa-sync-alt me-1"></i>Refresh Statistics
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if(session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h5 class="text-uppercase text-muted">
                <i class="fas fa-chart-line me-2"></i>Chart of Accounts
            </h5>
        </div>
        
        <!-- COA Cards -->
        @foreach(['coa', 'coa_main', 'coa_sub1', 'coa_sub2'] as $key)
        @if(isset($statistics[$key]))
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-{{ $statistics[$key]['color'] }} shadow-sm h-100">
                <div class="card-header bg-{{ $statistics[$key]['color'] }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas {{ $statistics[$key]['icon'] }} fa-lg"></i>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0">{{ $statistics[$key]['total'] }}</h3>
                            <small>Active Records</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="card-title">{{ $statistics[$key]['label'] }}</h6>
                    <p class="card-text text-muted small">{{ $statistics[$key]['description'] }}</p>
                    @if($statistics[$key]['inactive'] > 0)
                    <p class="text-muted small mb-2">
                        <i class="fas fa-ban text-danger"></i> {{ $statistics[$key]['inactive'] }} inactive
                    </p>
                    @endif
                    <a href="{{ route($statistics[$key]['route']) }}" class="btn btn-sm btn-outline-{{ $statistics[$key]['color'] }} w-100">
                        <i class="fas fa-arrow-right me-1"></i>Manage
                    </a>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>

    <!-- Other Master Data -->
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h5 class="text-uppercase text-muted">
                <i class="fas fa-folder-open me-2"></i>Other Master Data
            </h5>
        </div>

        @foreach(['banks', 'areas', 'vendors', 'status_cheque'] as $key)
        @if(isset($statistics[$key]))
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-{{ $statistics[$key]['color'] }} shadow-sm h-100">
                <div class="card-header bg-{{ $statistics[$key]['color'] }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas {{ $statistics[$key]['icon'] }} fa-lg"></i>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0">{{ $statistics[$key]['total'] }}</h3>
                            <small>Active Records</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="card-title">{{ $statistics[$key]['label'] }}</h6>
                    <p class="card-text text-muted small">{{ $statistics[$key]['description'] }}</p>
                    @if($statistics[$key]['inactive'] > 0)
                    <p class="text-muted small mb-2">
                        <i class="fas fa-ban text-danger"></i> {{ $statistics[$key]['inactive'] }} inactive
                    </p>
                    @endif
                    <a href="{{ route($statistics[$key]['route']) }}" class="btn btn-sm btn-outline-{{ $statistics[$key]['color'] }} w-100">
                        <i class="fas fa-arrow-right me-1"></i>Manage
                    </a>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>

    <!-- Last Updated Information -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Last Updated Information
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($lastUpdated) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Master Table</th>
                                    <th>Last Updated</th>
                                    <th>Updated By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lastUpdated as $table => $info)
                                <tr>
                                    <td><code>{{ $table }}</code></td>
                                    <td>
                                        <i class="fas fa-calendar-alt text-muted me-1"></i>
                                        {{ \Carbon\Carbon::parse($info['date'])->format('Y-m-d H:i:s') }}
                                        <small class="text-muted">({{ \Carbon\Carbon::parse($info['date'])->diffForHumans() }})</small>
                                    </td>
                                    <td>
                                        <i class="fas fa-user text-muted me-1"></i>
                                        {{ $info['user'] }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted mb-0">No update information available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Info Panel -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-info shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle text-info me-3 mt-1 fa-2x"></i>
                        <div>
                            <h6 class="mb-2">Master Data Management Guidelines</h6>
                            <ul class="small mb-0">
                                <li>All master data follows standard audit trail with created/updated user and timestamp</li>
                                <li>Use soft delete (rec_status = '0') instead of permanent deletion for data integrity</li>
                                <li>COA hierarchy: Main (L1) → Sub1 (L2) → Sub2 (L3) → COA Detail (L4)</li>
                                <li>Export to Excel available for all master tables</li>
                                <li>Filter by status (Active/Inactive) available on all pages</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('coa.modern') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-plus-circle me-2"></i>Add New COA
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('master.bank') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-university me-2"></i>Manage Banks
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('master.vendor') }}" class="btn btn-outline-danger w-100">
                                <i class="fas fa-users me-2"></i>Manage Vendors
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('coa.hierarchy') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-sitemap me-2"></i>View Full COA Hierarchy
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('database.tables') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-database me-2"></i>Database Tables Metadata
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-dark w-100">
                                <i class="fas fa-home me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
