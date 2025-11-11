@extends('layouts.bootstrap')

@section('title', 'Bank Management')

@section('content')
<div class="container-fluid" x-data="bankManagement()">
    <!-- Header -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="card-title mb-1">
                        <i class="fas fa-university me-2 text-primary"></i>Bank Management
                    </h2>
                    <p class="text-muted mb-0">
                        <small>Master Data: ms_acc_bank</small>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="/master" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Dashboard
                    </a>
                    <button @click="openAddModal()" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Add New
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <!-- Search -->
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fas fa-search me-1"></i>Search
                    </label>
                    <input type="text" 
                           class="form-control" 
                           placeholder="Search by code, name, account..." 
                           x-model="search"
                           @input.debounce.500ms="loadData()">
                </div>

                <!-- Status Filter -->
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-filter me-1"></i>Status
                    </label>
                    <select class="form-select" x-model="status" @change="loadData()">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-list-ol me-1"></i>Per Page
                    </label>
                    <select class="form-select" x-model="perPage" @change="loadData()">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="col-md-4 d-flex align-items-end">
                    <button @click="exportData()" class="btn btn-success me-2">
                        <i class="fas fa-file-excel me-1"></i>Export Excel
                    </button>
                    <button @click="clearFilters()" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h3 class="mb-0" x-text="meta.total"></h3>
                    <small class="text-muted">Total Banks</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-success" x-text="stats.active"></h3>
                    <small class="text-muted">Active</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-danger" x-text="stats.inactive"></h3>
                    <small class="text-muted">Inactive</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h3 class="mb-0 text-info" x-text="meta.current_page + '/' + meta.last_page"></h3>
                    <small class="text-muted">Page</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Loading State -->
            <div x-show="loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <!-- Table -->
            <div x-show="!loading" class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <a href="#" @click.prevent="sortBy('bank_code')" class="text-decoration-none text-dark">
                                    Code
                                    <i class="fas" :class="getSortIcon('bank_code')"></i>
                                </a>
                            </th>
                            <th>
                                <a href="#" @click.prevent="sortBy('bank_desc')" class="text-decoration-none text-dark">
                                    Bank Name
                                    <i class="fas" :class="getSortIcon('bank_desc')"></i>
                                </a>
                            </th>
                            <th>Account Number</th>
                            <th>COA Code</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="bank in banks" :key="bank.bank_code">
                            <tr>
                                <td><code x-text="bank.bank_code"></code></td>
                                <td><strong x-text="bank.bank_desc"></strong></td>
                                <td x-text="bank.bank_norek || '-'"></td>
                                <td><code x-text="bank.bank_coa || '-'"></code></td>
                                <td>
                                    <span class="badge" 
                                          :class="bank.rec_status == '1' ? 'bg-success' : 'bg-secondary'"
                                          x-text="bank.rec_status == '1' ? 'Active' : 'Inactive'">
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted" x-text="bank.rec_usercreated"></small>
                                </td>
                                <td>
                                    <button @click="openEditModal(bank)" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="confirmDelete(bank)" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="banks.length === 0">
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No data found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="!loading && meta.last_page > 1" class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing <span x-text="((meta.current_page - 1) * meta.per_page) + 1"></span> 
                    to <span x-text="Math.min(meta.current_page * meta.per_page, meta.total)"></span> 
                    of <span x-text="meta.total"></span> entries
                </div>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item" :class="{'disabled': meta.current_page === 1}">
                            <a class="page-link" href="#" @click.prevent="changePage(meta.current_page - 1)">Previous</a>
                        </li>
                        <template x-for="page in getPageNumbers()" :key="page">
                            <li class="page-item" :class="{'active': page === meta.current_page}">
                                <a class="page-link" href="#" @click.prevent="changePage(page)" x-text="page"></a>
                            </li>
                        </template>
                        <li class="page-item" :class="{'disabled': meta.current_page === meta.last_page}">
                            <a class="page-link" href="#" @click.prevent="changePage(meta.current_page + 1)">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal" :class="{'show d-block': showModal}" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" x-text="editMode ? 'Edit Bank' : 'Add New Bank'"></h5>
                    <button type="button" class="btn-close" @click="closeModal()"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveBank()">
                        <div class="mb-3">
                            <label class="form-label">Bank Code <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   x-model="form.bank_code"
                                   :disabled="editMode"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   x-model="form.bank_desc"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Account Number</label>
                            <input type="text" 
                                   class="form-control" 
                                   x-model="form.bank_norek">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">COA Code</label>
                            <input type="text" 
                                   class="form-control" 
                                   x-model="form.bank_coa"
                                   placeholder="e.g., 10101">
                        </div>
                        <div class="mb-3" x-show="editMode">
                            <label class="form-label">Status</label>
                            <select class="form-select" x-model="form.rec_status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="saveBank()" :disabled="saving">
                        <span x-show="saving">
                            <i class="fas fa-spinner fa-spin me-1"></i>Saving...
                        </span>
                        <span x-show="!saving">
                            <i class="fas fa-save me-1"></i>Save
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" :class="{'show d-block': showDeleteModal}" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" @click="showDeleteModal = false"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this bank?</p>
                    <p><strong x-text="deleteBank ? deleteBank.bank_desc : ''"></strong></p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showDeleteModal = false">Cancel</button>
                    <button type="button" class="btn btn-danger" @click="performDelete()">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function bankManagement() {
    return {
        // State
        loading: false,
        saving: false,
        banks: [],
        meta: {
            current_page: 1,
            last_page: 1,
            per_page: 25,
            total: 0
        },
        stats: {
            active: 0,
            inactive: 0
        },

        // Filters
        search: '',
        status: '',
        perPage: 25,
        sortField: 'bank_code',
        sortDirection: 'asc',
        currentPage: 1,

        // Modal
        showModal: false,
        showDeleteModal: false,
        editMode: false,
        deleteBank: null,
        form: {
            bank_code: '',
            bank_desc: '',
            bank_norek: '',
            bank_coa: '',
            rec_status: '1'
        },

        init() {
            this.loadData();
        },

        async loadData() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    search: this.search,
                    status: this.status,
                    perPage: this.perPage,
                    page: this.currentPage,
                    sortBy: this.sortField,
                    sortDirection: this.sortDirection
                });

                const response = await fetch(`/api/master/bank/data?${params}`);
                const data = await response.json();

                if (data.success) {
                    this.banks = data.data;
                    this.meta = data.meta;
                    this.updateStats();
                }
            } catch (error) {
                console.error('Error loading data:', error);
                alert('Error loading data');
            } finally {
                this.loading = false;
            }
        },

        updateStats() {
            this.stats.active = this.banks.filter(b => b.rec_status == '1').length;
            this.stats.inactive = this.banks.filter(b => b.rec_status == '0').length;
        },

        sortBy(field) {
            if (this.sortField === field) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDirection = 'asc';
            }
            this.loadData();
        },

        getSortIcon(field) {
            if (this.sortField !== field) return 'fa-sort text-muted';
            return this.sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
        },

        changePage(page) {
            if (page >= 1 && page <= this.meta.last_page) {
                this.currentPage = page;
                this.loadData();
            }
        },

        getPageNumbers() {
            const pages = [];
            const maxPages = 5;
            let start = Math.max(1, this.meta.current_page - 2);
            let end = Math.min(this.meta.last_page, start + maxPages - 1);
            
            if (end - start < maxPages - 1) {
                start = Math.max(1, end - maxPages + 1);
            }
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        },

        clearFilters() {
            this.search = '';
            this.status = '';
            this.perPage = 25;
            this.currentPage = 1;
            this.loadData();
        },

        openAddModal() {
            this.editMode = false;
            this.form = {
                bank_code: '',
                bank_desc: '',
                bank_norek: '',
                bank_coa: '',
                rec_status: '1'
            };
            this.showModal = true;
        },

        openEditModal(bank) {
            this.editMode = true;
            this.form = {...bank};
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
        },

        async saveBank() {
            this.saving = true;
            try {
                const url = this.editMode 
                    ? `/api/master/bank/${this.form.bank_code}`
                    : '/api/master/bank/store';
                
                const method = this.editMode ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    this.closeModal();
                    this.loadData();
                } else {
                    alert(data.message || 'Error saving data');
                }
            } catch (error) {
                console.error('Error saving:', error);
                alert('Error saving data');
            } finally {
                this.saving = false;
            }
        },

        confirmDelete(bank) {
            this.deleteBank = bank;
            this.showDeleteModal = true;
        },

        async performDelete() {
            try {
                const response = await fetch(`/api/master/bank/${this.deleteBank.bank_code}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    this.showDeleteModal = false;
                    this.loadData();
                } else {
                    alert(data.message || 'Error deleting data');
                }
            } catch (error) {
                console.error('Error deleting:', error);
                alert('Error deleting data');
            }
        },

        async exportData() {
            window.location.href = '/api/master/bank/export';
        }
    }
}
</script>
@endsection
