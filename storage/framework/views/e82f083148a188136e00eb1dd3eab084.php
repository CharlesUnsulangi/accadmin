

<?php $__env->startSection('content'); ?>
<style>
    [x-cloak] {
        display: none !important;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.08);
    }
</style>

<div x-data="databaseTables()" x-init="loadTables()" class="container-fluid py-4">
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Total Tables</h6>
                            <h3 class="mb-0" x-text="formatNumber(stats.total_tables)">0</h3>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-table fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Total Records</h6>
                            <h3 class="mb-0" x-text="formatNumber(stats.total_records)">0</h3>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-database fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Last Updated</h6>
                            <h6 class="mb-0" x-text="formatDate(stats.latest_update)">-</h6>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Actions -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Database Tables</h5>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button @click="updateAllMetadata()" 
                                :disabled="updating"
                                class="btn btn-success btn-sm">
                            <span x-show="!updating">
                                <i class="fas fa-sync-alt me-1"></i>Update All Metadata
                            </span>
                            <span x-show="updating">
                                <i class="fas fa-spinner fa-spin me-1"></i>Updating...
                            </span>
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" 
                               class="form-control border-start-0 bg-light" 
                               placeholder="Search tables..."
                               x-model="search"
                               @input.debounce.300ms="loadTables()">
                        <button class="btn btn-outline-secondary" 
                                @click="search = ''; loadTables()"
                                x-show="search">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <div x-show="alert.show" 
         :class="'alert alert-' + alert.type + ' alert-dismissible fade show'" 
         role="alert"
         x-transition>
        <span x-text="alert.message"></span>
        <button type="button" class="btn-close" @click="alert.show = false"></button>
    </div>

    <!-- Messages Modal - Bootstrap Native -->
    <div class="modal fade" 
         id="messagesModal" 
         tabindex="-1" 
         aria-labelledby="messagesModalLabel" 
         aria-hidden="true"
         data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="messagesModalLabel">
                        <i class="fas fa-comment-dots me-2"></i>
                        Messages for <span x-text="messagesModal.tableName || ''"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Add Message Form -->
                    <div class="card mb-3 border-primary">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-plus-circle me-2"></i>Add New Message</h6>
                            <div class="mb-3">
                                <textarea class="form-control" 
                                          rows="3" 
                                          placeholder="Enter your message or note about this table..."
                                          x-model="messagesModal.newMessage"
                                          :disabled="messagesModal.submitting"></textarea>
                            </div>
                            <button @click="addMessage()" 
                                    :disabled="messagesModal.submitting || !messagesModal.newMessage.trim()"
                                    class="btn btn-primary btn-sm">
                                <span x-show="!messagesModal.submitting">
                                    <i class="fas fa-paper-plane me-1"></i>Add Message
                                </span>
                                <span x-show="messagesModal.submitting">
                                    <i class="fas fa-spinner fa-spin me-1"></i>Adding...
                                </span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Messages List -->
                    <div x-show="messagesModal.loading" class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                        <p class="text-muted mt-2">Loading messages...</p>
                    </div>
                    
                    <div x-show="!messagesModal.loading && messagesModal.messages.length === 0" class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted opacity-50 mb-3"></i>
                        <p class="text-muted">No messages yet. Be the first to add one!</p>
                    </div>
                    
                    <div x-show="!messagesModal.loading && messagesModal.messages.length > 0">
                        <h6 class="mb-3"><i class="fas fa-list me-2"></i>Messages (<span x-text="messagesModal.messages.length"></span>)</h6>
                        <template x-for="message in messagesModal.messages" :key="message.tr_admin_it_aplikasi_table_msg_id">
                            <div class="card mb-2 border-start border-4 border-info">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                <strong x-text="message.user_created"></strong>
                                            </small>
                                            <small class="text-muted ms-3">
                                                <i class="fas fa-calendar me-1"></i>
                                                <span x-text="formatDate(message.date_created)"></span>
                                            </small>
                                        </div>
                                        <button @click="deleteMessage(message.tr_admin_it_aplikasi_table_msg_id)" 
                                                class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <p class="mb-0" x-text="message.msg_desc"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Detail Modal - Bootstrap Native -->
    <div class="modal fade" 
         id="detailModal" 
         tabindex="-1" 
         aria-labelledby="detailModalLabel" 
         aria-hidden="true"
         data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="modal-title text-white" id="detailModalLabel">
                        <i class="fas fa-info-circle me-2"></i>
                        Table Details: <span x-text="detailModal.table?.table_name || ''"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Left Column: Table Information -->
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-database me-2"></i>Table Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="text-muted" style="width: 40%;">Table ID:</td>
                                            <td><code x-text="detailModal.table?.tr_aplikasi_table_id"></code></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Table Name:</td>
                                            <td><strong x-text="detailModal.table?.table_name"></strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Table Type:</td>
                                            <td><span x-text="detailModal.table?.table_type || '-'"></span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Schema Description:</td>
                                            <td><span x-text="detailModal.table?.note_schema || '-'"></span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">User Note:</td>
                                            <td>
                                                <template x-if="detailModal.table?.table_note">
                                                    <div class="alert alert-info mb-0 py-2 px-3">
                                                        <i class="fas fa-sticky-note me-2"></i>
                                                        <span x-text="detailModal.table?.table_note"></span>
                                                    </div>
                                                </template>
                                                <template x-if="!detailModal.table?.table_note">
                                                    <span class="text-muted fst-italic">No user note yet</span>
                                                </template>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Total Records:</td>
                                            <td><span class="badge bg-info" x-text="formatNumber(detailModal.table?.record)"></span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Date Range:</td>
                                            <td>
                                                <span x-text="formatDate(detailModal.table?.record_date_start)"></span>
                                                <i class="fas fa-arrow-right mx-2"></i>
                                                <span x-text="formatDate(detailModal.table?.record_date_last)"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Last Updated:</td>
                                            <td><span x-text="formatDate(detailModal.table?.date_updated)"></span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column: Quick Add Message -->
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm border-primary h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Quick Add Message</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info alert-sm">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <small>Add a note or comment about this table. The latest message will be displayed in the table list.</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Message / Note:</label>
                                        <textarea class="form-control" 
                                                  rows="5" 
                                                  placeholder="Enter your message or note about this table..."
                                                  x-model="detailModal.quickMessage"
                                                  :disabled="detailModal.submitting"></textarea>
                                        <small class="text-muted">
                                            <i class="fas fa-key me-1"></i>
                                            Primary Key: <code x-text="detailModal.table?.tr_aplikasi_table_id"></code>
                                        </small>
                                    </div>
                                    
                                    <button @click="quickAddMessage()" 
                                            :disabled="detailModal.submitting || !detailModal.quickMessage?.trim()"
                                            class="btn btn-primary w-100">
                                        <span x-show="!detailModal.submitting">
                                            <i class="fas fa-paper-plane me-2"></i>Add Message
                                        </span>
                                        <span x-show="detailModal.submitting">
                                            <i class="fas fa-spinner fa-spin me-2"></i>Adding...
                                        </span>
                                    </button>
                                    
                                    <div class="mt-3" x-show="detailModal.table?.table_note">
                                        <label class="form-label fw-bold text-muted">Current Note:</label>
                                        <div class="alert alert-secondary mb-0">
                                            <i class="fas fa-sticky-note me-2"></i>
                                            <span x-text="detailModal.table?.table_note"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Full Width: Message History -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-history me-2"></i>Message History 
                                            (<span x-text="detailModal.messages.length"></span>)
                                        </h6>
                                        <button @click="loadDetailMessages()" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                    <div x-show="detailModal.loadingMessages" class="text-center py-4">
                                        <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                                        <p class="text-muted mt-2">Loading messages...</p>
                                    </div>
                                    
                                    <div x-show="!detailModal.loadingMessages && detailModal.messages.length === 0" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted opacity-50 mb-3"></i>
                                        <p class="text-muted">No messages yet. Add the first one!</p>
                                    </div>
                                    
                                    <div x-show="!detailModal.loadingMessages && detailModal.messages.length > 0">
                                        <template x-for="(message, index) in detailModal.messages" :key="message.tr_admin_it_aplikasi_table_msg_id">
                                            <div class="card mb-2 border-start border-4" 
                                                 :class="index === 0 ? 'border-success' : 'border-info'">
                                                <div class="card-body py-2">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <span x-show="index === 0" class="badge bg-success me-2">Latest</span>
                                                            <small class="text-muted">
                                                                <i class="fas fa-user me-1"></i>
                                                                <strong x-text="message.user_created"></strong>
                                                            </small>
                                                            <small class="text-muted ms-3">
                                                                <i class="fas fa-calendar me-1"></i>
                                                                <span x-text="formatDate(message.date_created)"></span>
                                                            </small>
                                                        </div>
                                                        <button @click="deleteMessageFromDetail(message.tr_admin_it_aplikasi_table_msg_id)" 
                                                                class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    <p class="mb-0" x-text="message.msg_desc"></p>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="openMessagesModal(detailModal.table?.tr_aplikasi_table_id, detailModal.table?.table_name)">
                        <i class="fas fa-comment-dots me-2"></i>View All Messages
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 text-center" style="width: 80px;">
                                <a href="#" @click.prevent="sortByColumn('cek_priority')" class="text-decoration-none text-dark">
                                    Priority
                                    <i class="fas" :class="getSortIcon('cek_priority')"></i>
                                </a>
                            </th>
                            <th class="border-0">
                                <a href="#" @click.prevent="sortByColumn('table_name')" class="text-decoration-none text-dark">
                                    Table Name
                                    <i class="fas" :class="getSortIcon('table_name')"></i>
                                </a>
                            </th>
                            <th class="border-0">Schema Description</th>
                            <th class="border-0">User Note</th>
                            <th class="border-0 text-end">
                                <a href="#" @click.prevent="sortByColumn('record')" class="text-decoration-none text-dark">
                                    Records
                                    <i class="fas" :class="getSortIcon('record')"></i>
                                </a>
                            </th>
                            <th class="border-0">
                                <a href="#" @click.prevent="sortByColumn('record_date_start')" class="text-decoration-none text-dark">
                                    First Record
                                    <i class="fas" :class="getSortIcon('record_date_start')"></i>
                                </a>
                            </th>
                            <th class="border-0">
                                <a href="#" @click.prevent="sortByColumn('record_date_last')" class="text-decoration-none text-dark">
                                    Last Record
                                    <i class="fas" :class="getSortIcon('record_date_last')"></i>
                                </a>
                            </th>
                            <th class="border-0">Updated</th>
                            <th class="border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loading">
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Loading tables...</p>
                                </td>
                            </tr>
                        </template>
                        
                        <template x-if="!loading && tables.length === 0">
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-inbox fs-1 mb-3 d-block opacity-50 text-muted"></i>
                                    <p class="mb-0 text-muted">No tables found</p>
                                </td>
                            </tr>
                        </template>
                        
                        <template x-for="table in tables" :key="table.tr_aplikasi_table_id">
                            <tr @dblclick="goToTableDetail(table.table_name)" style="cursor: pointer;">
                                <td class="text-center" @click.stop>
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               role="switch"
                                               :id="'priority-' + table.tr_aplikasi_table_id"
                                               :checked="table.cek_priority == 1 || table.cek_priority == true"
                                               @change="togglePriority(table)"
                                               @click.stop
                                               style="cursor: pointer;">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-2">
                                            <i class="fas fa-table"></i>
                                        </div>
                                        <div>
                                            <strong class="text-primary" x-text="table.table_name"></strong>
                                            <br>
                                            <small class="text-muted" x-text="'ID: ' + table.tr_aplikasi_table_id"></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted" style="font-size: 0.9rem;" x-text="table.note_schema || 'No description'"></span>
                                </td>
                                <td>
                                    <template x-if="table.table_note">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-sticky-note text-info me-2"></i>
                                            <span class="text-dark" style="font-size: 0.9rem;" x-text="table.table_note"></span>
                                        </div>
                                    </template>
                                    <template x-if="!table.table_note">
                                        <span class="text-muted fst-italic" style="font-size: 0.85rem;">-</span>
                                    </template>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-info" x-text="formatNumber(table.record)"></span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="far fa-calendar me-1"></i>
                                        <span x-text="formatDate(table.record_date_start)"></span>
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="far fa-calendar me-1"></i>
                                        <span x-text="formatDate(table.record_date_last)"></span>
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted" x-text="formatDateAgo(table.date_updated)"></small>
                                </td>
                                <td class="text-center no-click" @click.stop>
                                    <div class="btn-group" role="group">
                                        <button @click="viewTableDetail(table)"
                                                class="btn btn-sm btn-outline-success"
                                                title="View Details">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                        <button @click="updateTableMetadata(table.table_name)" 
                                                :disabled="updatingTable === table.table_name"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Sync Metadata">
                                            <span x-show="updatingTable !== table.table_name">
                                                <i class="fas fa-sync-alt"></i>
                                            </span>
                                            <span x-show="updatingTable === table.table_name">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </span>
                                        </button>
                                        <button @click="openMessagesModal(table.tr_aplikasi_table_id, table.table_name)"
                                                class="btn btn-sm btn-outline-info"
                                                title="View Messages">
                                            <i class="fas fa-comment-dots"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="card-footer bg-white border-top">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <select class="form-select form-select-sm" style="width: auto;" x-model="perPage" @change="loadTables()">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
                <div class="col-md-4 text-center">
                    <span class="text-muted" x-text="`Page ${pagination.current_page} of ${pagination.last_page}`"></span>
                </div>
                <div class="col-md-4">
                    <nav>
                        <ul class="pagination pagination-sm justify-content-end mb-0">
                            <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <template x-for="page in getPageNumbers()" :key="page">
                                <li class="page-item" :class="{ active: page === pagination.current_page }">
                                    <a class="page-link" href="#" @click.prevent="changePage(page)" x-text="page"></a>
                                </li>
                            </template>
                            <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function databaseTables() {
    return {
        tables: [],
        stats: {
            total_tables: 0,
            total_records: 0,
            latest_update: null
        },
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 25,
            total: 0
        },
        search: '',
        sortBy: 'table_name',
        sortDirection: 'asc',
        perPage: 25,
        loading: false,
        updating: false,
        updatingTable: null,
        alert: {
            show: false,
            type: 'success',
            message: ''
        },
        
        // Messages modal state
        messagesModal: {
            tableId: null,
            tableName: '',
            messages: [],
            newMessage: '',
            loading: false,
            submitting: false
        },
        
        // Detail modal state
        detailModal: {
            table: null,
            messages: [],
            quickMessage: '',
            submitting: false,
            loadingMessages: false
        },
        
        loadTables() {
            console.log('loadTables called, detailModal.show:', this.detailModal.show);
            this.loading = true;
            
            fetch('/api/database-tables/data?' + new URLSearchParams({
                search: this.search,
                sortBy: this.sortBy,
                sortDirection: this.sortDirection,
                perPage: this.perPage,
                page: this.pagination.current_page
            }))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.tables = data.data;
                    this.stats = data.stats;
                    this.pagination = {
                        current_page: data.current_page,
                        last_page: data.last_page,
                        per_page: data.per_page,
                        total: data.total
                    };
                } else {
                    this.showAlert('error', data.message || 'Error loading tables');
                }
            })
            .catch(error => {
                this.showAlert('danger', 'Error: ' + error.message);
            })
            .finally(() => {
                this.loading = false;
            });
        },
        
        async togglePriority(table) {
            const newValue = table.cek_priority == 1 ? 0 : 1;
            
            try {
                const response = await fetch(`/api/database-tables/toggle-priority/${table.tr_aplikasi_table_id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        cek_priority: newValue
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    // Update local state
                    table.cek_priority = newValue;
                    this.showAlert('success', `Priority ${newValue == 1 ? 'enabled' : 'disabled'} for ${table.table_name}`);
                } else {
                    this.showAlert('danger', data.message || 'Failed to update priority');
                }
            } catch (error) {
                this.showAlert('danger', 'Error updating priority: ' + error.message);
            }
        },
        
        goToTableDetail(tableName) {
            window.location.href = `/table-detail/${encodeURIComponent(tableName)}`;
        },
        
        sortByColumn(column) {
            if (this.sortBy === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortBy = column;
                this.sortDirection = 'asc';
            }
            this.loadTables();
        },
        
        getSortIcon(column) {
            if (this.sortBy !== column) return 'fa-sort text-muted';
            return this.sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
        },
        
        changePage(page) {
            if (page < 1 || page > this.pagination.last_page) return;
            this.pagination.current_page = page;
            this.loadTables();
        },
        
        getPageNumbers() {
            const pages = [];
            const current = this.pagination.current_page;
            const last = this.pagination.last_page;
            
            if (last <= 7) {
                for (let i = 1; i <= last; i++) {
                    pages.push(i);
                }
            } else {
                if (current <= 4) {
                    for (let i = 1; i <= 5; i++) pages.push(i);
                    pages.push('...');
                    pages.push(last);
                } else if (current >= last - 3) {
                    pages.push(1);
                    pages.push('...');
                    for (let i = last - 4; i <= last; i++) pages.push(i);
                } else {
                    pages.push(1);
                    pages.push('...');
                    for (let i = current - 1; i <= current + 1; i++) pages.push(i);
                    pages.push('...');
                    pages.push(last);
                }
            }
            
            return pages.filter(p => p !== '...');
        },
        
        updateTableMetadata(tableName) {
            this.updatingTable = tableName;
            
            fetch('/api/database-tables/update-metadata', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ table: tableName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showAlert('success', data.message);
                    this.loadTables();
                } else {
                    this.showAlert('danger', data.message);
                }
            })
            .catch(error => {
                this.showAlert('danger', 'Error: ' + error.message);
            })
            .finally(() => {
                this.updatingTable = null;
            });
        },
        
        updateAllMetadata() {
            if (!confirm('Update metadata for all tables? This may take a while.')) return;
            
            this.updating = true;
            
            fetch('/api/database-tables/update-all-metadata', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showAlert('success', data.message);
                    this.loadTables();
                } else {
                    this.showAlert('danger', data.message);
                }
            })
            .catch(error => {
                this.showAlert('danger', 'Error: ' + error.message);
            })
            .finally(() => {
                this.updating = false;
            });
        },
        
        showAlert(type, message) {
            this.alert = { show: true, type, message };
            setTimeout(() => { this.alert.show = false; }, 5000);
        },
        
        // Detail modal functions
        viewTableDetail(table) {
            console.log('viewTableDetail called for:', table.table_name);
            this.detailModal.table = table;
            this.detailModal.quickMessage = '';
            this.loadDetailMessages();
            
            // Use Bootstrap modal API
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        },
        
        closeDetailModal() {
            console.log('closeDetailModal called');
            // Use Bootstrap modal API
            const modalElement = document.getElementById('detailModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
            this.detailModal.table = null;
            this.detailModal.messages = [];
            this.detailModal.quickMessage = '';
        },
        
        async loadDetailMessages() {
            if (!this.detailModal.table) return;
            
            this.detailModal.loadingMessages = true;
            try {
                const response = await fetch(`/api/database-tables/messages/${this.detailModal.table.tr_aplikasi_table_id}`);
                const data = await response.json();
                if (data.success) {
                    this.detailModal.messages = data.data;
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            } finally {
                this.detailModal.loadingMessages = false;
            }
        },
        
        async quickAddMessage() {
            if (!this.detailModal.quickMessage.trim()) {
                this.showAlert('warning', 'Please enter a message');
                return;
            }
            
            this.detailModal.submitting = true;
            try {
                const response = await fetch('/api/database-tables/add-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        tr_aplikasi_table_id: this.detailModal.table.tr_aplikasi_table_id,
                        msg_desc: this.detailModal.quickMessage
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    this.detailModal.quickMessage = '';
                    await this.loadDetailMessages();
                    this.loadTables(); // Reload table list to update table_note
                    
                    // Update current detail modal table data
                    const updatedTable = this.tables.find(t => t.tr_aplikasi_table_id === this.detailModal.table.tr_aplikasi_table_id);
                    if (updatedTable) {
                        this.detailModal.table = updatedTable;
                    }
                    
                    this.showAlert('success', '✅ Message added! Table note updated.');
                } else {
                    this.showAlert('danger', data.message);
                }
            } catch (error) {
                this.showAlert('danger', 'Error adding message: ' + error.message);
            } finally {
                this.detailModal.submitting = false;
            }
        },
        
        async deleteMessageFromDetail(messageId) {
            if (!confirm('Delete this message?')) return;
            
            try {
                const response = await fetch(`/api/database-tables/delete-message/${messageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    await this.loadDetailMessages();
                    this.loadTables(); // Reload table list to update table_note
                    
                    // Update current detail modal table data
                    const updatedTable = this.tables.find(t => t.tr_aplikasi_table_id === this.detailModal.table.tr_aplikasi_table_id);
                    if (updatedTable) {
                        this.detailModal.table = updatedTable;
                    }
                    
                    this.showAlert('success', '✅ Message deleted! Table note updated.');
                } else {
                    this.showAlert('danger', data.message);
                }
            } catch (error) {
                this.showAlert('danger', 'Error deleting message: ' + error.message);
            }
        },
        
        // Messages modal functions
        async openMessagesModal(tableId, tableName) {
            this.messagesModal.tableId = tableId;
            this.messagesModal.tableName = tableName;
            this.messagesModal.newMessage = '';
            await this.loadMessages();
            
            // Use Bootstrap modal API
            const modal = new bootstrap.Modal(document.getElementById('messagesModal'));
            modal.show();
        },
        
        closeMessagesModal() {
            // Use Bootstrap modal API
            const modalElement = document.getElementById('messagesModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
            this.messagesModal.messages = [];
        },
        
        async loadMessages() {
            this.messagesModal.loading = true;
            try {
                const response = await fetch(`/api/database-tables/messages/${this.messagesModal.tableId}`);
                const data = await response.json();
                if (data.success) {
                    this.messagesModal.messages = data.data;
                } else {
                    this.showAlert('danger', 'Failed to load messages');
                }
            } catch (error) {
                this.showAlert('danger', 'Error loading messages: ' + error.message);
            } finally {
                this.messagesModal.loading = false;
            }
        },
        
        async addMessage() {
            if (!this.messagesModal.newMessage.trim()) {
                this.showAlert('warning', 'Please enter a message');
                return;
            }
            
            this.messagesModal.submitting = true;
            try {
                const response = await fetch('/api/database-tables/add-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        tr_aplikasi_table_id: this.messagesModal.tableId,
                        msg_desc: this.messagesModal.newMessage
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    this.messagesModal.newMessage = '';
                    await this.loadMessages();
                    this.loadTables(); // Reload table list to update table_note
                    this.showAlert('success', 'Message added successfully. Table note updated.');
                } else {
                    this.showAlert('danger', data.message);
                }
            } catch (error) {
                this.showAlert('danger', 'Error adding message: ' + error.message);
            } finally {
                this.messagesModal.submitting = false;
            }
        },
        
        async deleteMessage(messageId) {
            if (!confirm('Delete this message?')) return;
            
            try {
                const response = await fetch(`/api/database-tables/delete-message/${messageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    await this.loadMessages();
                    this.loadTables(); // Reload table list to update table_note
                    this.showAlert('success', 'Message deleted successfully. Table note updated.');
                } else {
                    this.showAlert('danger', data.message);
                }
            } catch (error) {
                this.showAlert('danger', 'Error deleting message: ' + error.message);
            }
        },
        
        formatNumber(num) {
            if (!num) return '0';
            return new Intl.NumberFormat().format(num);
        },
        
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('en-GB', { 
                day: '2-digit', 
                month: 'short', 
                year: 'numeric' 
            });
        },
        
        formatDateAgo(date) {
            if (!date) return '-';
            const now = new Date();
            const then = new Date(date);
            const seconds = Math.floor((now - then) / 1000);
            
            if (seconds < 60) return 'just now';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            if (seconds < 2592000) return Math.floor(seconds / 86400) + ' days ago';
            return this.formatDate(date);
        }
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/database-tables-alpine.blade.php ENDPATH**/ ?>