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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-list-alt me-2"></i>Total COA
                    </div>
                    <div class="card-body">
                        <h3 class="card-title text-primary">{{ $coaStats['total'] }}</h3>
                        <p class="card-text text-muted small">All accounts in system</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-check-circle me-2"></i>Active Accounts
                    </div>
                    <div class="card-body">
                        <h3 class="card-title text-success">{{ $coaStats['active'] }}</h3>
                        <p class="card-text text-muted small">Ready to use</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-pause-circle me-2"></i>Inactive Accounts
                    </div>
                    <div class="card-body">
                        <h3 class="card-title text-warning">{{ $coaStats['inactive'] }}</h3>
                        <p class="card-text text-muted small">Not in use</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-server me-2"></i>System Status
                    </div>
                    <div class="card-body">
                        <h3 class="card-title text-info">OK</h3>
                        <p class="card-text text-muted small">All systems operational</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Charts and Details Row -->
        <div class="row mb-4">
            <!-- Hierarchy Breakdown -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <i class="fas fa-sitemap me-2"></i>COA Hierarchy Breakdown (H1-H6)
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">H1 - Level 1</span>
                                        <span class="fw-bold text-primary">{{ $hierarchyStats['h1'] }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $hierarchyStats['h1'] > 0 ? ($hierarchyStats['h1'] / max(array_values($hierarchyStats)) * 100) : 0 }}%" aria-valuenow="{{ $hierarchyStats['h1'] }}" aria-valuemin="0" aria-valuemax="{{ max(array_values($hierarchyStats)) }}"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">H2 - Level 2</span>
                                        <span class="fw-bold text-success">{{ $hierarchyStats['h2'] }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $hierarchyStats['h2'] > 0 ? ($hierarchyStats['h2'] / max(array_values($hierarchyStats)) * 100) : 0 }}%" aria-valuenow="{{ $hierarchyStats['h2'] }}" aria-valuemin="0" aria-valuemax="{{ max(array_values($hierarchyStats)) }}"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">H3 - Level 3</span>
                                        <span class="fw-bold text-warning">{{ $hierarchyStats['h3'] }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $hierarchyStats['h3'] > 0 ? ($hierarchyStats['h3'] / max(array_values($hierarchyStats)) * 100) : 0 }}%" aria-valuenow="{{ $hierarchyStats['h3'] }}" aria-valuemin="0" aria-valuemax="{{ max(array_values($hierarchyStats)) }}"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">H4 - Level 4</span>
                                        <span class="fw-bold text-info">{{ $hierarchyStats['h4'] }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $hierarchyStats['h4'] > 0 ? ($hierarchyStats['h4'] / max(array_values($hierarchyStats)) * 100) : 0 }}%" aria-valuenow="{{ $hierarchyStats['h4'] }}" aria-valuemin="0" aria-valuemax="{{ max(array_values($hierarchyStats)) }}"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">H5 - Level 5</span>
                                        <span class="fw-bold text-danger">{{ $hierarchyStats['h5'] }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $hierarchyStats['h5'] > 0 ? ($hierarchyStats['h5'] / max(array_values($hierarchyStats)) * 100) : 0 }}%" aria-valuenow="{{ $hierarchyStats['h5'] }}" aria-valuemin="0" aria-valuemax="{{ max(array_values($hierarchyStats)) }}"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">H6 - Level 6</span>
                                        <span class="fw-bold text-secondary">{{ $hierarchyStats['h6'] }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $hierarchyStats['h6'] > 0 ? ($hierarchyStats['h6'] / max(array_values($hierarchyStats)) * 100) : 0 }}%" aria-valuenow="{{ $hierarchyStats['h6'] }}" aria-valuemin="0" aria-valuemax="{{ max(array_values($hierarchyStats)) }}"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Total Accounts</span>
                                <span class="fw-bold">{{ $hierarchyStats['total'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Type Distribution -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <i class="fas fa-chart-pie me-2"></i>Account Type Distribution
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($accountTypes as $type => $count)
                                @if($count > 0)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle me-3 
                                            {{ $type === 'Asset' ? 'bg-primary' : '' }}
                                            {{ $type === 'Liability' ? 'bg-danger' : '' }}
                                            {{ $type === 'Equity' ? 'bg-success' : '' }}
                                            {{ $type === 'Revenue' ? 'bg-info' : '' }}
                                            {{ $type === 'Expense' ? 'bg-warning' : '' }}
                                            {{ $type === 'Other' ? 'bg-secondary' : '' }}" style="width: 20px; height: 20px;">
                                        </div>
                                        <span class="text-muted">{{ $type }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="font-weight-semibold me-2">{{ $count }}</span>
                                        <span class="text-muted">
                                            ({{ $coaStats['total'] > 0 ? round($count / $coaStats['total'] * 100, 1) : 0 }}%)
                                        </span>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <!-- Recently Created COAs -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        Recently Created
                        <a href="{{ route('coa.index') }}" class="btn btn-link btn-sm float-end">View All →</a>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse($recentCoas as $coa)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <p class="mb-0">{{ $coa->coa_code }}</p>
                                    <small class="text-muted">{{ $coa->coa_desc }}</small>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">{{ $coa->rec_datecreated->diffForHumans() }}</small>
                                    <span class="badge bg-success rounded-pill">
                                        {{ $coa->account_type }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted">
                                <svg class="mx-auto" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-text">
                                    <path d="M17 0H7a2 2 0 00-2 2v20a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2zM7 2h10a1 1 0 011 1v1H6V3a1 1 0 011-1zm11 4H6v2h13V6zm0 3H6v2h13V9zm0 3H6v2h13v-2zm-7 3H6v2h5v-2zm7 0h-5v2h5v-2z"/>
                                </svg>
                                <p class="mt-2">No recent COAs</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recently Updated COAs -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        Recently Updated
                        <a href="{{ route('coa.index') }}" class="btn btn-link btn-sm float-end">View All →</a>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse($recentUpdates as $coa)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <p class="mb-0">{{ $coa->coa_code }}</p>
                                    <small class="text-muted">{{ $coa->coa_desc }}</small>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">{{ $coa->rec_dateupdate->diffForHumans() }}</small>
                                    <p class="mb-0 text-muted" style="font-size: 0.875rem;">by {{ $coa->rec_userupdate }}</p>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted">
                                <svg class="mx-auto" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-text">
                                    <path d="M17 0H7a2 2 0 00-2 2v20a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2zM7 2h10a1 1 0 011 1v1H6V3a1 1 0 011-1zm11 4H6v2h13V6zm0 3H6v2h13V9zm0 3H6v2h13v-2zm-7 3H6v2h5v-2zm7 0h-5v2h5v-2z"/>
                                </svg>
                                <p class="mt-2">No recent updates</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col">
                <div class="card bg-light shadow-sm">
                    <div class="card-header">
                        Quick Actions
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <a href="{{ route('coa.index') }}" 
                                   class="btn btn-primary w-100">
                                    <svg class="bi bi-plus-circle" width="24" height="24" fill="currentColor">
                                        <path d="M12 2a10 10 0 1010 10A10.011 10.011 0 0012 2zm5 11H13v4a1 1 0 01-2 0v-4H7a1 1 0 010-2h4V7a1 1 0 012 0v4h4a1 1 0 010 2z"/>
                                    </svg>
                                    <span class="ms-2">Add COA</span>
                                </a>
                            </div>

                            <div class="col-6 col-md-3">
                                <a href="{{ route('coa.index') }}" 
                                   class="btn btn-success w-100">
                                    <svg class="bi bi-eye" width="24" height="24" fill="currentColor">
                                        <path d="M12 4.5a7.5 7.5 0 100 15 7.5 7.5 0 000-15zm0 13a5.5 5.5 0 110-11 5.5 5.5 0 010 11z"/>
                                    </svg>
                                    <span class="ms-2">View COA</span>
                                </a>
                            </div>

                            <div class="col-6 col-md-3">
                                <a href="#" 
                                   class="btn btn-secondary w-100 disabled">
                                    <svg class="bi bi-file-earmark-text" width="24" height="24" fill="currentColor">
                                        <path d="M17 0H7a2 2 0 00-2 2v20a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2zM7 2h10a1 1 0 011 1v1H6V3a1 1 0 011-1zm11 4H6v2h13V6zm0 3H6v2h13V9zm0 3H6v2h13v-2zm-7 3H6v2h5v-2zm7 0h-5v2h5v-2z"/>
                                    </svg>
                                    <span class="ms-2">Reports</span>
                                    <span class="d-block text-muted" style="font-size: 0.875rem;">Coming Soon</span>
                                </a>
                            </div>

                            <div class="col-6 col-md-3">
                                <a href="#" 
                                   class="btn btn-warning w-100 disabled">
                                    <svg class="bi bi-journal-plus" width="24" height="24" fill="currentColor">
                                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2V7a2 2 0 00-2-2h-5.586a1 1 0 01-.707-.293l-5.414-5.414A1 1 0 006 2zm1 2a1 1 0 011 1v3a1 1 0 01-2 0V5a1 1 0 011-1zm-1 8a1 1 0 102 0 1 1 0 00-2 0zm8-8a1 1 0 00-1 1v12a1 1 0 001 1h2a1 1 0 001-1V4a1 1 0 00-1-1h-2z"/>
                                    </svg>
                                    <span class="ms-2">Journal Entry</span>
                                    <span class="d-block text-muted" style="font-size: 0.875rem;">Coming Soon</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
