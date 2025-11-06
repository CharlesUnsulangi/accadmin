<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>COA Management - Bootstrap</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <style>
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #0d6efd;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .badge-h1 { background-color: #0d6efd; }
        .badge-h2 { background-color: #198754; }
        .badge-h3 { background-color: #ffc107; color: #000; }
        .badge-h4 { background-color: #6f42c1; }
        .badge-h5 { background-color: #d63384; }
        .badge-h6 { background-color: #6610f2; }
        .stats-card {
            border-radius: 10px;
            padding: 1.5rem;
            color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stats-card-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stats-card-success { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stats-card-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .card-hover:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            transform: translateY(-2px);
            transition: all 0.3s;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4" id="app">
        <!-- Toast Container -->
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
            <div id="toastContainer"></div>
        </div>

        <!-- Header -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-1 fw-bold text-primary">
                            <i class="fas fa-chart-line me-2"></i>COA Management
                        </h1>
                        <p class="text-muted mb-0">Bootstrap 5 Version - Modern & Responsive</p>
                    </div>
                    <div class="btn-toolbar gap-2" role="toolbar">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary active" id="btnModern">
                                <i class="fas fa-layer-group me-1"></i>Modern
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="btnHierarchy">
                                <i class="fas fa-sitemap me-1"></i>Hierarchy
                            </button>
                        </div>
                        <button type="button" class="btn btn-success" onclick="openAddModal()">
                            <i class="fas fa-plus me-1"></i>Add New
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="searchInput" 
                                   placeholder="Search code, description..." 
                                   onkeyup="debounceSearch()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="perPageSelect" onchange="loadData()">
                            <option value="10">10 per page</option>
                            <option value="25" selected>25 per page</option>
                            <option value="50">50 per page</option>
                            <option value="100">100 per page</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" onclick="loadData()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stats-card stats-card-primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Total Records</p>
                            <h2 class="mb-0 fw-bold" id="totalRecords">-</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card stats-card-success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Current Page</p>
                            <h2 class="mb-0 fw-bold" id="currentPage">-</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card stats-card-info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Showing</p>
                            <h2 class="mb-0 fw-bold" id="showingCount">-</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading -->
        <div id="loading" class="d-none text-center py-5">
            <div class="loader"></div>
            <p class="text-muted mt-3">Loading data...</p>
        </div>

        <!-- Table -->
        <div id="tableContainer" class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold">Code</th>
                                <th class="fw-semibold">Description</th>
                                <th class="fw-semibold">H1</th>
                                <th class="fw-semibold">H2</th>
                                <th class="fw-semibold">H3-H6</th>
                                <th class="fw-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <!-- Data will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="card-footer bg-white">
                <div id="pagination" class="d-flex justify-content-between align-items-center">
                    <!-- Pagination will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="coaModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-plus-circle me-2"></i>Add New COA
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="coaForm" onsubmit="handleSubmit(event)">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">COA Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="coa_code" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="coa_desc" required>
                            </div>

                            <div class="col-12">
                                <div class="alert alert-primary d-flex align-items-center" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <div><strong>Required:</strong> H1 Level must be filled (minimum 1 level)</div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-semibold">H1 Level <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="desc_h1" required 
                                       placeholder="e.g., Assets, Liabilities, Equity">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">H1 ID</label>
                                <input type="text" class="form-control" id="ms_coa_h1_id">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-semibold">H2 Level</label>
                                <input type="text" class="form-control" id="desc_h2" 
                                       placeholder="e.g., Current Assets, Fixed Assets">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">H2 ID</label>
                                <input type="text" class="form-control" id="ms_coa_h2_id">
                            </div>

                            <div class="col-12">
                                <div class="accordion" id="additionalLevels">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" 
                                                    data-bs-toggle="collapse" data-bs-target="#levelH3H6">
                                                <i class="fas fa-layer-group me-2"></i>Additional Levels (H3-H6) - Optional
                                            </button>
                                        </h2>
                                        <div id="levelH3H6" class="accordion-collapse collapse" 
                                             data-bs-parent="#additionalLevels">
                                            <div class="accordion-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">H3 Level</label>
                                                        <input type="text" class="form-control" id="desc_h3">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">H4 Level</label>
                                                        <input type="text" class="form-control" id="desc_h4">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">H5 Level</label>
                                                        <input type="text" class="form-control" id="desc_h5">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">H6 Level</label>
                                                        <input type="text" class="form-control" id="desc_h6">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('coaForm').requestSubmit()">
                        <i class="fas fa-save me-1"></i>Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const API_BASE = '/coa-js';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
        let currentPage = 1;
        let searchTimeout = null;
        let coaModal = null;

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            coaModal = new bootstrap.Modal(document.getElementById('coaModal'));
            loadData();
        });

        // Debounce search
        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                loadData();
            }, 500);
        }

        // Load data
        async function loadData(page = 1) {
            currentPage = page;
            showLoading();

            const search = document.getElementById('searchInput').value;
            const perPage = document.getElementById('perPageSelect').value;

            try {
                const response = await fetch(`${API_BASE}/data?page=${page}&search=${search}&per_page=${perPage}`, {
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                renderTable(data.data);
                renderPagination(data);
                updateStats(data);
            } catch (error) {
                showToast('Error loading data: ' + error.message, 'danger');
            } finally {
                hideLoading();
            }
        }

        // Render table
        function renderTable(items) {
            const tbody = document.getElementById('tableBody');
            
            if (!items || items.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                <h5>No data found</h5>
                                <p class="mb-0">Try adjusting your search or filters</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = items.map(item => `
                <tr>
                    <td><code class="fw-bold">${item.coa_code || '-'}</code></td>
                    <td>${item.coa_desc || '-'}</td>
                    <td>
                        ${item.desc_h1 ? `<span class="badge badge-h1">${item.desc_h1}</span>` : '<span class="text-muted">-</span>'}
                    </td>
                    <td>
                        ${item.desc_h2 ? `<span class="badge badge-h2">${item.desc_h2}</span>` : '<span class="text-muted">-</span>'}
                    </td>
                    <td>
                        ${item.desc_h3 ? `<span class="badge badge-h3 me-1">${item.desc_h3}</span>` : ''}
                        ${item.desc_h4 ? `<span class="badge badge-h4 me-1">${item.desc_h4}</span>` : ''}
                        ${item.desc_h5 ? `<span class="badge badge-h5 me-1">${item.desc_h5}</span>` : ''}
                        ${item.desc_h6 ? `<span class="badge badge-h6">${item.desc_h6}</span>` : ''}
                        ${!item.desc_h3 && !item.desc_h4 && !item.desc_h5 && !item.desc_h6 ? '<span class="text-muted">-</span>' : ''}
                    </td>
                    <td>
                        <span class="badge ${item.rec_status === '1' ? 'bg-success' : 'bg-danger'}">
                            ${item.rec_status === '1' ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                </tr>
            `).join('');
        }

        // Render pagination
        function renderPagination(data) {
            const container = document.getElementById('pagination');
            const { current_page, last_page, from, to, total } = data;

            let html = '<div class="text-muted">Showing ' + (from || 0) + ' to ' + (to || 0) + ' of ' + total + ' results</div>';
            html += '<nav><ul class="pagination mb-0">';

            // Previous
            html += `<li class="page-item ${current_page <= 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="event.preventDefault(); ${current_page > 1 ? 'loadData(' + (current_page - 1) + ')' : ''}">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>`;

            // Pages
            for (let i = Math.max(1, current_page - 2); i <= Math.min(last_page, current_page + 2); i++) {
                html += `<li class="page-item ${i === current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="event.preventDefault(); loadData(${i})">${i}</a>
                </li>`;
            }

            // Next
            html += `<li class="page-item ${current_page >= last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="event.preventDefault(); ${current_page < last_page ? 'loadData(' + (current_page + 1) + ')' : ''}">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>`;

            html += '</ul></nav>';
            container.innerHTML = html;
        }

        // Update stats
        function updateStats(data) {
            document.getElementById('totalRecords').textContent = data.total || 0;
            document.getElementById('currentPage').textContent = data.current_page || 0;
            document.getElementById('showingCount').textContent = data.data.length || 0;
        }

        // Modal functions
        function openAddModal() {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Add New COA';
            document.getElementById('coaForm').reset();
            coaModal.show();
        }

        // Handle form submit
        async function handleSubmit(e) {
            e.preventDefault();

            const formData = {
                coa_code: document.getElementById('coa_code').value,
                coa_desc: document.getElementById('coa_desc').value,
                desc_h1: document.getElementById('desc_h1').value,
                ms_coa_h1_id: document.getElementById('ms_coa_h1_id').value,
                desc_h2: document.getElementById('desc_h2').value,
                ms_coa_h2_id: document.getElementById('ms_coa_h2_id').value,
                desc_h3: document.getElementById('desc_h3').value,
                desc_h4: document.getElementById('desc_h4').value,
                desc_h5: document.getElementById('desc_h5').value,
                desc_h6: document.getElementById('desc_h6').value,
            };

            try {
                const response = await fetch(`${API_BASE}/store`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (result.success) {
                    showToast('COA created successfully!', 'success');
                    coaModal.hide();
                    loadData(currentPage);
                } else {
                    showToast('Error: ' + (result.message || 'Unknown error'), 'danger');
                }
            } catch (error) {
                showToast('Error: ' + error.message, 'danger');
            }
        }

        // Loading
        function showLoading() {
            document.getElementById('loading').classList.remove('d-none');
            document.getElementById('tableContainer').style.opacity = '0.5';
        }

        function hideLoading() {
            document.getElementById('loading').classList.add('d-none');
            document.getElementById('tableContainer').style.opacity = '1';
        }

        // Toast notification
        function showToast(message, type = 'success') {
            const toastHtml = `
                <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;

            const container = document.getElementById('toastContainer');
            const div = document.createElement('div');
            div.innerHTML = toastHtml;
            container.appendChild(div.firstElementChild);

            const toastElement = container.lastElementChild;
            const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
            toast.show();

            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }
    </script>
</body>
</html>
<?php /**PATH C:\ProjectSoftwareCWU\laravel\AccAdmin\resources\views/coa/bootstrap.blade.php ENDPATH**/ ?>