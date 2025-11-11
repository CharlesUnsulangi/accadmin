@extends('layouts.bootstrap')

@section('title', 'Application Management')

@section('content')
<style>
.table-row-hover:hover {
    background-color: #f8f9fa !important;
}
.table-row-hover:hover td {
    transition: background-color 0.2s ease;
}
.list-group-sm .list-group-item {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>

<div class="container-fluid py-4" x-data="applicationManagement()">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-cubes text-primary"></i> Application Management</h2>
            <p class="text-muted mb-0">
                Kelola informasi aplikasi yang mengakses database <code>RCM_DEV_HGS_SB</code>. 
                <small class="text-info"><i class="fas fa-info-circle"></i> Click <i class="fas fa-chevron-right"></i> to expand topics & actions</small>
            </p>
        </div>
        <button class="btn btn-primary" @click="openCreateModal">
            <i class="fas fa-plus"></i> Add Application
        </button>
    </div>

    <!-- Alert -->
    <div x-show="alert.show" 
         x-transition
         :class="'alert alert-' + alert.type + ' alert-dismissible fade show'" 
         role="alert"
         style="display: none;">
        <i :class="alert.type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle'"></i>
        <span x-text="alert.message"></span>
        <button type="button" class="btn-close" @click="alert.show = false"></button>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-cubes fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Applications</h6>
                            <h3 class="mb-0" x-text="stats.total"></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Active</h6>
                            <h3 class="mb-0" x-text="stats.active"></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-pause-circle fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Inactive</h6>
                            <h3 class="mb-0" x-text="stats.inactive"></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label"><i class="fas fa-search"></i> Search</label>
                    <input type="text" 
                           class="form-control" 
                           placeholder="Search by application name or notes..."
                           x-model="filters.search"
                           @input.debounce.500ms="loadApplications">
                </div>
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-filter"></i> Status</label>
                    <select class="form-select" x-model="filters.status" @change="loadApplications">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-outline-secondary w-100" @click="resetFilters">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 3%"></th>
                            <th style="width: 5%">#</th>
                            <th style="width: 25%">Application Name</th>
                            <th style="width: 12%">Framework</th>
                            <th style="width: 10%">Created By</th>
                            <th style="width: 8%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loading">
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Loading applications...</p>
                                </td>
                            </tr>
                        </template>

                        <template x-if="!loading && applications.length === 0">
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-inbox fs-1 mb-3 d-block opacity-50 text-muted"></i>
                                    <p class="mb-0 text-muted">No applications found</p>
                                </td>
                            </tr>
                        </template>

                        <template x-for="(app, index) in applications" :key="app.ms_admin_it_aplikasi_id">
                            <>
                                <!-- Main Row -->
                                <tr class="table-row-hover">
                                    <td>
                                        <button class="btn btn-sm btn-link p-0" 
                                                @click="toggleExpand(app.ms_admin_it_aplikasi_id)"
                                                :title="expandedRows.includes(app.ms_admin_it_aplikasi_id) ? 'Collapse' : 'Expand'">
                                            <i class="fas" 
                                               :class="expandedRows.includes(app.ms_admin_it_aplikasi_id) ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                        </button>
                                    </td>
                                    <td x-text="index + 1"></td>
                                    <td>
                                        <strong x-text="app.apps_desc"></strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-key"></i>
                                            <code x-text="app.ms_admin_it_aplikasi_id"></code>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info" x-show="app.framework" x-text="app.framework"></span>
                                        <small class="text-muted" x-show="!app.framework">-</small>
                                    </td>
                                    <td>
                                        <small x-text="app.user_created || '-'"></small>
                                    </td>
                                    <td>
                                        <span class="badge" 
                                              :class="(app.cek_non_aktif === 0 || app.cek_non_aktif === null) ? 'bg-success' : 'bg-secondary'"
                                              x-text="(app.cek_non_aktif === 0 || app.cek_non_aktif === null) ? 'Active' : 'Inactive'"></span>
                                    </td>
                                </tr>

                                <!-- Expanded Row -->
                                <tr x-show="expandedRows.includes(app.ms_admin_it_aplikasi_id)" 
                                    x-transition
                                    class="bg-light">
                                    <td colspan="6" class="p-0">
                                        <div class="p-3">
                                            <div class="row">
                                                <!-- Left: Description & Topics -->
                                                <div class="col-md-8">
                                                    <!-- Description -->
                                                    <div class="mb-3" x-show="app.aplikasi_note">
                                                        <h6 class="text-muted mb-2"><i class="fas fa-sticky-note"></i> Description</h6>
                                                        <p class="mb-0 small" x-text="app.aplikasi_note"></p>
                                                    </div>

                                                    <!-- Topics -->
                                                    <div>
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <h6 class="text-muted mb-0">
                                                                <i class="fas fa-list"></i> Documentation Topics
                                                                <span class="badge bg-info ms-2" x-text="(app.topics || []).length"></span>
                                                            </h6>
                                                            <button class="btn btn-sm btn-outline-success" 
                                                                    @click="startAddTopicInline(app)"
                                                                    x-show="app.topicsLoaded && !app.addingTopic"
                                                                    title="Add new topic">
                                                                <i class="fas fa-plus"></i> Add Topic
                                                            </button>
                                                        </div>
                                                        
                                                        <div x-show="!app.topicsLoaded" class="text-center py-2">
                                                            <div class="spinner-border spinner-border-sm text-primary"></div>
                                                            <small class="ms-2 text-muted">Loading topics...</small>
                                                        </div>

                                                        <!-- Inline Add Topic Form -->
                                                        <div x-show="app.addingTopic" 
                                                             x-transition
                                                             class="card card-body bg-white mb-2 p-2">
                                                            <form @submit.prevent="saveTopicInline(app)">
                                                                <div class="row g-2">
                                                                    <div class="col-md-7">
                                                                        <input type="text" 
                                                                               class="form-control form-control-sm" 
                                                                               x-model="app.newTopicDesc"
                                                                               placeholder="Topic description (max 50 chars)"
                                                                               maxlength="50"
                                                                               required>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="number" 
                                                                               class="form-control form-control-sm" 
                                                                               x-model="app.newTopicPriority"
                                                                               placeholder="Priority"
                                                                               min="0">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="btn-group btn-group-sm w-100">
                                                                            <button type="submit" 
                                                                                    class="btn btn-success"
                                                                                    :disabled="app.savingTopic">
                                                                                <i class="fas fa-check"></i>
                                                                            </button>
                                                                            <button type="button" 
                                                                                    class="btn btn-secondary"
                                                                                    @click="cancelAddTopicInline(app)">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>

                                                        <div x-show="app.topicsLoaded && (!app.topics || app.topics.length === 0) && !app.addingTopic" 
                                                             class="text-muted small py-2">
                                                            <i class="fas fa-info-circle"></i> No topics yet
                                                        </div>

                                                        <div x-show="app.topicsLoaded && app.topics && app.topics.length > 0" 
                                                             class="list-group list-group-sm">
                                                            <template x-for="(topic, idx) in (app.topics || [])" :key="topic.ms_admin_it_topic">
                                                                <div class="list-group-item list-group-item-action py-1 px-2 small">
                                                                    <span class="badge bg-info me-2" x-text="topic.value_priority || idx + 1"></span>
                                                                    <span x-text="topic.topic_desc"></span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Right: Actions -->
                                                <div class="col-md-4">
                                                    <h6 class="text-muted mb-3"><i class="fas fa-bolt"></i> Actions</h6>
                                                    <div class="d-grid gap-2">
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                @click="viewDetail(app.ms_admin_it_aplikasi_id)">
                                                            <i class="fas fa-eye"></i> View Details
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-info" 
                                                                @click="openTopicsModal(app)">
                                                            <i class="fas fa-list"></i> Manage Topics
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-success" 
                                                                @click="openEditModal(app)">
                                                            <i class="fas fa-edit"></i> Edit Application
                                                        </button>
                                                        <button class="btn btn-sm"
                                                                :class="(app.cek_non_aktif === 0 || app.cek_non_aktif === null) ? 'btn-outline-warning' : 'btn-outline-secondary'"
                                                                @click="toggleStatus(app.ms_admin_it_aplikasi_id)">
                                                            <i :class="(app.cek_non_aktif === 0 || app.cek_non_aktif === null) ? 'fas fa-toggle-on' : 'fas fa-toggle-off'"></i>
                                                            <span x-text="(app.cek_non_aktif === 0 || app.cek_non_aktif === null) ? 'Deactivate' : 'Activate'"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" 
         :class="{ 'show d-block': modal.show }" 
         tabindex="-1" 
         style="background: rgba(0,0,0,0.5)"
         x-show="modal.show"
         x-transition>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i :class="modal.mode === 'create' ? 'fas fa-plus' : 'fas fa-edit'"></i>
                        <span x-text="modal.mode === 'create' ? 'Add New Application' : 'Edit Application'"></span>
                    </h5>
                    <button type="button" class="btn-close" @click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="submitForm">
                        <div class="mb-3">
                            <label class="form-label">Application Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   x-model="modal.form.apps_desc"
                                   placeholder="e.g., AccAdmin - Accounting Administration"
                                   maxlength="50"
                                   required>
                            <small class="text-muted">Maximum 50 characters</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Framework</label>
                            <input type="text" 
                                   class="form-control" 
                                   x-model="modal.form.framework"
                                   placeholder="e.g., Laravel 11, ASP.NET Core, React"
                                   maxlength="50">
                            <small class="text-muted">Optional - Technology framework used (max 50 chars)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes/Description</label>
                            <textarea class="form-control" 
                                      x-model="modal.form.aplikasi_note"
                                      rows="4"
                                      placeholder="Describe the purpose and features of this application"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeModal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" 
                            class="btn btn-primary" 
                            @click="submitForm"
                            :disabled="modal.submitting">
                        <span x-show="modal.submitting">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Saving...
                        </span>
                        <span x-show="!modal.submitting">
                            <i class="fas fa-save"></i>
                            <span x-text="modal.mode === 'create' ? 'Create' : 'Update'"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Topics Modal -->
    <div class="modal fade" 
         :class="{ 'show d-block': topicsModal.show }" 
         x-show="topicsModal.show"
         x-transition
         style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-list"></i>
                        Documentation Topics
                        <small class="d-block mt-1" x-text="topicsModal.appName"></small>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" @click="closeTopicsModal"></button>
                </div>
                <div class="modal-body">
                    <!-- Add Topic Form -->
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="fas fa-plus"></i> Add New Topic</h6>
                            <form @submit.prevent="addTopic">
                                <div class="row">
                                    <div class="col-md-7">
                                        <input type="text" 
                                               class="form-control" 
                                               x-model="topicsModal.newTopic.topic_desc"
                                               placeholder="Topic description (max 50 chars)"
                                               maxlength="50"
                                               required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" 
                                               class="form-control" 
                                               x-model="topicsModal.newTopic.value_priority"
                                               placeholder="Priority">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" 
                                                class="btn btn-info w-100"
                                                :disabled="topicsModal.submitting">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Topics List -->
                    <div x-show="topicsModal.loading" class="text-center py-4">
                        <div class="spinner-border text-info"></div>
                        <p class="mt-2 text-muted">Loading topics...</p>
                    </div>

                    <div x-show="!topicsModal.loading && topicsModal.topics.length === 0" class="text-center py-4">
                        <i class="fas fa-inbox fs-1 mb-3 d-block opacity-50 text-muted"></i>
                        <p class="mb-0 text-muted">No topics yet. Add your first topic above.</p>
                    </div>

                    <div x-show="!topicsModal.loading && topicsModal.topics.length > 0">
                        <div class="list-group">
                            <template x-for="(topic, index) in topicsModal.topics" :key="topic.ms_admin_it_topic">
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="flex-grow-1" x-show="topicsModal.editingId !== topic.ms_admin_it_topic">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-info me-2" x-text="topic.value_priority || 0"></span>
                                                <strong x-text="topic.topic_desc"></strong>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1" x-show="topicsModal.editingId === topic.ms_admin_it_topic">
                                            <div class="row g-2">
                                                <div class="col-md-8">
                                                    <input type="text" 
                                                           class="form-control form-control-sm" 
                                                           x-model="topicsModal.editForm.topic_desc"
                                                           maxlength="50">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="number" 
                                                           class="form-control form-control-sm" 
                                                           x-model="topicsModal.editForm.value_priority"
                                                           placeholder="Priority">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="btn-group btn-group-sm ms-2">
                                            <template x-if="topicsModal.editingId !== topic.ms_admin_it_topic">
                                                <button class="btn btn-outline-primary" 
                                                        @click="editTopic(topic)"
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </template>
                                            <template x-if="topicsModal.editingId === topic.ms_admin_it_topic">
                                                <>
                                                    <button class="btn btn-outline-success" 
                                                            @click="saveTopic(topic.ms_admin_it_topic)"
                                                            title="Save">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button class="btn btn-outline-secondary" 
                                                            @click="cancelEditTopic()"
                                                            title="Cancel">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </>
                                            </template>
                                            <button class="btn btn-outline-danger" 
                                                    @click="deleteTopic(topic.ms_admin_it_topic, topic.topic_desc)"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeTopicsModal">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function applicationManagement() {
    return {
        applications: [],
        expandedRows: [],
        loading: false,
        filters: {
            search: '',
            status: ''
        },
        stats: {
            total: 0,
            active: 0,
            inactive: 0
        },
        modal: {
            show: false,
            mode: 'create',
            submitting: false,
            editId: null,
            form: {
                apps_desc: '',
                framework: '',
                aplikasi_note: ''
            }
        },
        alert: {
            show: false,
            type: 'success',
            message: ''
        },
        topicsModal: {
            show: false,
            loading: false,
            submitting: false,
            appId: null,
            appName: '',
            topics: [],
            newTopic: {
                topic_desc: '',
                value_priority: 0
            },
            editingId: null,
            editForm: {
                topic_desc: '',
                value_priority: 0
            }
        },

        init() {
            this.loadApplications();
            this.loadStats();
        },

        getEmptyForm() {
            return {
                apps_desc: '',
                framework: '',
                aplikasi_note: ''
            };
        },

        async loadApplications() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    search: this.filters.search,
                    status: this.filters.status
                });

                const response = await fetch(`/api/applications/data?${params}`);
                const data = await response.json();

                if (data.success) {
                    this.applications = data.data;
                } else {
                    this.showAlert('danger', data.message);
                }
            } catch (error) {
                this.showAlert('danger', 'Error loading applications: ' + error.message);
            } finally {
                this.loading = false;
            }
        },

        async loadStats() {
            try {
                const response = await fetch('/api/applications/stats');
                const data = await response.json();

                if (data.success) {
                    this.stats = data.data;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },

        resetFilters() {
            this.filters = {
                search: '',
                status: ''
            };
            this.loadApplications();
        },

        openCreateModal() {
            this.modal = {
                show: true,
                mode: 'create',
                submitting: false,
                editId: null,
                form: this.getEmptyForm()
            };
        },

        openEditModal(app) {
            this.modal = {
                show: true,
                mode: 'edit',
                submitting: false,
                editId: app.ms_admin_it_aplikasi_id,
                form: {
                    apps_desc: app.apps_desc,
                    framework: app.framework || '',
                    aplikasi_note: app.aplikasi_note || ''
                }
            };
        },

        closeModal() {
            this.modal.show = false;
        },

        async submitForm() {
            if (this.modal.submitting) return;

            this.modal.submitting = true;
            try {
                const url = this.modal.mode === 'create' 
                    ? '/api/applications/store'
                    : `/api/applications/update/${this.modal.editId}`;

                const method = this.modal.mode === 'create' ? 'POST' : 'PUT';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.modal.form)
                });

                const data = await response.json();

                if (data.success) {
                    this.showAlert('success', data.message);
                    this.closeModal();
                    await this.loadApplications();
                    await this.loadStats();
                } else {
                    this.showAlert('danger', data.message);
                }
            } catch (error) {
                this.showAlert('danger', 'Error: ' + error.message);
            } finally {
                this.modal.submitting = false;
            }
        },

        async toggleStatus(id) {
            if (!confirm('Toggle status aplikasi ini?')) return;

            try {
                const response = await fetch(`/api/applications/toggle-status/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.showAlert('success', data.message);
                    await this.loadApplications();
                    await this.loadStats();
                } else {
                    this.showAlert('danger', data.message);
                }
            } catch (error) {
                this.showAlert('danger', 'Error: ' + error.message);
            }
        },

        showAlert(type, message) {
            this.alert = {
                show: true,
                type: type,
                message: message
            };

            setTimeout(() => {
                this.alert.show = false;
            }, 5000);
        },

        // Topics Management Methods
        async openTopicsModal(app) {
            this.topicsModal = {
                show: true,
                loading: true,
                submitting: false,
                appId: app.ms_admin_it_aplikasi_id,
                appName: app.apps_desc,
                topics: [],
                newTopic: {
                    topic_desc: '',
                    value_priority: 0
                },
                editingId: null,
                editForm: {
                    topic_desc: '',
                    value_priority: 0
                }
            };

            await this.loadTopics();
        },

        closeTopicsModal() {
            this.topicsModal.show = false;
        },

        async loadTopics() {
            this.topicsModal.loading = true;
            try {
                const response = await fetch(`/api/applications/${this.topicsModal.appId}/topics`);
                const data = await response.json();

                if (data.success) {
                    this.topicsModal.topics = data.data;
                } else {
                    this.showAlert('danger', data.message);
                }
            } catch (error) {
                this.showAlert('danger', 'Error loading topics: ' + error.message);
            } finally {
                this.topicsModal.loading = false;
            }
        },

        async addTopic() {
            if (!this.topicsModal.newTopic.topic_desc.trim()) {
                this.showAlert('warning', 'Please enter topic description');
                return;
            }

            this.topicsModal.submitting = true;
            try {
                const response = await fetch(`/api/applications/${this.topicsModal.appId}/topics`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.topicsModal.newTopic)
                });

                const data = await response.json();

                if (data.success) {
                    this.showAlert('success', data.message);
                    this.topicsModal.newTopic = { topic_desc: '', value_priority: 0 };
                    await this.loadTopics();
                } else {
                    this.showAlert('danger', data.message);
                }
            } catch (error) {
                this.showAlert('danger', 'Error adding topic: ' + error.message);
            } finally {
                this.topicsModal.submitting = false;
            }
        },

        editTopic(topic) {
            this.topicsModal.editingId = topic.ms_admin_it_topic;
            this.topicsModal.editForm = {
                topic_desc: topic.topic_desc,
                value_priority: topic.value_priority || 0
            };
        },

        cancelEditTopic() {
            this.topicsModal.editingId = null;
            this.topicsModal.editForm = {
                topic_desc: '',
                value_priority: 0
            };
        },

        async saveTopic(topicId) {
            this.topicsModal.submitting = true;
            try {
                const response = await fetch(`/api/applications/${this.topicsModal.appId}/topics/${topicId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.topicsModal.editForm)
                });

                const data = await response.json();

                if (data.success) {
                    this.showAlert('success', data.message);
                    this.cancelEditTopic();
                    await this.loadTopics();
                } else {
                    this.showAlert('danger', data.message);
                }
            } catch (error) {
                this.showAlert('danger', 'Error updating topic: ' + error.message);
            } finally {
                this.topicsModal.submitting = false;
            }
        },

        async deleteTopic(topicId, topicDesc) {
            if (!confirm(`Delete topic "${topicDesc}"?`)) {
                return;
            }

            try {
                const response = await fetch(`/api/applications/${this.topicsModal.appId}/topics/${topicId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.showAlert('success', data.message);
                    await this.loadTopics();
                } else {
                    this.showAlert('danger', data.message);
                }
            } catch (error) {
                this.showAlert('danger', 'Error deleting topic: ' + error.message);
            }
        },

        // Navigate to detail page
        viewDetail(appId) {
            window.location.href = `/applications/${appId}`;
        },

        // Toggle expand/collapse row
        async toggleExpand(appId) {
            const index = this.expandedRows.indexOf(appId);
            
            if (index > -1) {
                // Collapse
                this.expandedRows.splice(index, 1);
            } else {
                // Expand
                this.expandedRows.push(appId);
                
                // Load topics if not loaded yet
                const app = this.applications.find(a => a.ms_admin_it_aplikasi_id === appId);
                if (app && !app.topicsLoaded) {
                    await this.loadTopicsForApp(app);
                }
            }
        },

        // Load topics for specific application
        async loadTopicsForApp(app) {
            try {
                const response = await fetch(`/api/applications/${app.ms_admin_it_aplikasi_id}/topics`);
                const data = await response.json();

                if (data.success) {
                    app.topics = data.data;
                    app.topicsLoaded = true;
                    app.addingTopic = false;
                    app.savingTopic = false;
                }
            } catch (error) {
                console.error('Error loading topics:', error);
                app.topics = [];
                app.topicsLoaded = true;
            }
        },

        // Start adding topic inline
        startAddTopicInline(app) {
            app.addingTopic = true;
            app.newTopicDesc = '';
            app.newTopicPriority = (app.topics || []).length + 1;
        },

        // Cancel adding topic inline
        cancelAddTopicInline(app) {
            app.addingTopic = false;
            app.newTopicDesc = '';
            app.newTopicPriority = 0;
        },

        // Save topic inline
        async saveTopicInline(app) {
            if (!app.newTopicDesc || !app.newTopicDesc.trim()) {
                this.showAlert('warning', 'Please enter topic description');
                return;
            }

            app.savingTopic = true;
            try {
                const response = await fetch(`/api/applications/${app.ms_admin_it_aplikasi_id}/topics`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        topic_desc: app.newTopicDesc,
                        value_priority: app.newTopicPriority || 0
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showAlert('success', 'Topic added successfully');
                    // Reload topics for this app
                    app.topicsLoaded = false;
                    await this.loadTopicsForApp(app);
                } else {
                    this.showAlert('danger', data.message);
                    app.savingTopic = false;
                }
            } catch (error) {
                this.showAlert('danger', 'Error adding topic: ' + error.message);
                app.savingTopic = false;
            }
        }
    }
}
</script>
@endsection
