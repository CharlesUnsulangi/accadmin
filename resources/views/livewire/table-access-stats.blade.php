<div>
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1"><i class="fas fa-chart-line me-2 text-primary"></i>Table Access Statistics</h4>
                <p class="text-muted mb-0">Monitor database table access patterns and usage</p>
            </div>
            <div>
                <select class="form-select" wire:model.live="timeRange">
                    <option value="24h">Last 24 Hours</option>
                    <option value="7d">Last 7 Days</option>
                    <option value="30d">Last 30 Days</option>
                    <option value="all">All Time</option>
                </select>
            </div>
        </div>

        <!-- Overall Stats Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Total Access</h6>
                                <h3 class="mb-0">{{ number_format($stats['total_access']) }}</h3>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fas fa-eye fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Unique Tables</h6>
                                <h3 class="mb-0">{{ number_format($stats['unique_tables']) }}</h3>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fas fa-table fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Unique Users</h6>
                                <h3 class="mb-0">{{ number_format($stats['unique_users']) }}</h3>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-2">Frontend Types</h6>
                                <h3 class="mb-0">{{ number_format($stats['unique_frontends']) }}</h3>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fas fa-desktop fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Most Accessed Tables -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0"><i class="fas fa-fire text-danger me-2"></i>Most Accessed Tables</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0">#</th>
                                        <th class="border-0">Table Name</th>
                                        <th class="border-0 text-center">Access Count</th>
                                        <th class="border-0 text-center">Users</th>
                                        <th class="border-0">Last Access</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($mostAccessed as $index => $item)
                                    <tr>
                                        <td>
                                            <span class="badge 
                                                @if($index === 0) bg-warning
                                                @elseif($index === 1) bg-secondary
                                                @elseif($index === 2) bg-danger
                                                @else bg-light text-dark
                                                @endif
                                            ">{{ $index + 1 }}</span>
                                        </td>
                                        <td><strong class="text-primary">{{ $item->table_name }}</strong></td>
                                        <td class="text-center"><span class="badge bg-info">{{ number_format($item->access_count) }}</span></td>
                                        <td class="text-center"><span class="badge bg-success">{{ number_format($item->unique_users) }}</span></td>
                                        <td><small class="text-muted">{{ \Carbon\Carbon::parse($item->last_accessed)->diffForHumans() }}</small></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-info-circle me-2"></i>No access data available
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Access by Frontend -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0"><i class="fas fa-globe text-primary me-2"></i>Access by Frontend</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0">Frontend Type</th>
                                        <th class="border-0 text-center">Access</th>
                                        <th class="border-0 text-end">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalAccess = $accessByFrontend->sum('access_count'); @endphp
                                    @forelse($accessByFrontend as $item)
                                    <tr>
                                        <td><strong>{{ $item->frontend_type }}</strong></td>
                                        <td class="text-center"><span class="badge bg-primary">{{ number_format($item->access_count) }}</span></td>
                                        <td class="text-end">
                                            @php $percentage = $totalAccess > 0 ? ($item->access_count / $totalAccess * 100) : 0; @endphp
                                            {{ number_format($percentage, 1) }}%
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No data</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Access -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0"><i class="fas fa-history text-success me-2"></i>Recent Access</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">Time</th>
                                <th class="border-0">Table</th>
                                <th class="border-0">Type</th>
                                <th class="border-0">User</th>
                                <th class="border-0">Frontend</th>
                                <th class="border-0">IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAccess as $access)
                            <tr>
                                <td><small class="text-muted">{{ \Carbon\Carbon::parse($access->accessed_at)->format('M d, H:i') }}</small></td>
                                <td><strong class="text-primary">{{ $access->table_name }}</strong></td>
                                <td><span class="badge bg-info">{{ $access->access_type }}</span></td>
                                <td>{{ $access->user_name }}</td>
                                <td><span class="badge bg-secondary">{{ $access->frontend_type }}</span></td>
                                <td><small class="text-muted">{{ $access->ip_address }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No recent activity</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
