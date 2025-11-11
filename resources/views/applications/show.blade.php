@extends('layouts.bootstrap')

@section('title', 'Application Detail - ' . $application->apps_desc)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.admin-it') }}">Admin IT Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('applications.index') }}">Applications</a></li>
                    <li class="breadcrumb-item active">{{ $application->apps_desc }}</li>
                </ol>
            </nav>
            <h2 class="mb-0">
                <i class="fas fa-cube text-primary"></i> 
                {{ $application->apps_desc }}
            </h2>
        </div>
        <div>
            <a href="{{ route('applications.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Application Info Card -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Application Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted mb-1"><i class="fas fa-key"></i> Application ID</label>
                            <p class="mb-0"><code>{{ $application->ms_admin_it_aplikasi_id }}</code></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted mb-1"><i class="fas fa-tag"></i> Application Name</label>
                            <p class="mb-0"><strong>{{ $application->apps_desc }}</strong></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted mb-1"><i class="fas fa-circle"></i> Status</label>
                            <p class="mb-0">
                                @if($application->cek_non_aktif == 0 || $application->cek_non_aktif === null)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($application->framework)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="text-muted mb-1"><i class="fas fa-code"></i> Framework/Technology</label>
                            <p class="mb-0"><span class="badge bg-info">{{ $application->framework }}</span></p>
                        </div>
                    </div>
                    @endif

                    @if($application->aplikasi_note)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="text-muted mb-1"><i class="fas fa-sticky-note"></i> Description/Notes</label>
                            <p class="mb-0 text-muted">{{ $application->aplikasi_note }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted mb-1"><i class="fas fa-user"></i> Created By</label>
                            <p class="mb-0">{{ $application->user_created ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted mb-1"><i class="fas fa-clock"></i> Created Time</label>
                            <p class="mb-0">{{ $application->time_created ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentation Topics -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Documentation Topics</h5>
                    <span class="badge bg-white text-info">{{ count($topics) }} Topics</span>
                </div>
                <div class="card-body">
                    @if(count($topics) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topics as $index => $topic)
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <span class="badge bg-info rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                            {{ $topic->value_priority ?? $index + 1 }}
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $topic->topic_desc }}</h6>
                                        <small class="text-muted">Topic ID: {{ $topic->ms_admin_it_topic }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fs-1 mb-3 d-block opacity-50 text-muted"></i>
                            <p class="text-muted mb-0">No documentation topics yet.</p>
                            <small class="text-muted">Go back to <a href="{{ route('applications.index') }}">Applications List</a> to manage topics.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats Sidebar -->
        <div class="col-lg-4">
            <!-- Statistics -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted d-block">Total Topics</small>
                            <h4 class="mb-0 text-primary">{{ count($topics) }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-list fa-2x text-primary"></i>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted d-block">Application Status</small>
                            <h5 class="mb-0">
                                @if($application->cek_non_aktif == 0 || $application->cek_non_aktif === null)
                                    <span class="text-success">Active</span>
                                @else
                                    <span class="text-secondary">Inactive</span>
                                @endif
                            </h5>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>

                    @if($application->framework)
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block">Technology</small>
                            <h6 class="mb-0">{{ $application->framework }}</h6>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-code fa-2x text-info"></i>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('applications.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit Application
                        </a>
                        <a href="{{ route('applications.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-list"></i> Manage Topics
                        </a>
                        <a href="{{ route('applications.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
