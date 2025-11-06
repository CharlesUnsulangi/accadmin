<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>COA Management - jQuery AJAX</title>
    
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
        .jquery-badge {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body class="bg-light">
    <!-- jQuery Badge -->
    <div class="jquery-badge">
        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
            <i class="fab fa-js-square me-1"></i> Powered by jQuery AJAX
        </span>
    </div>

    <div class="container-fluid py-4">
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
                        <p class="text-muted mb-0">
                            <i class="fab fa-js-square text-warning me-1"></i>
                            jQuery AJAX Version - Classic & Reliable
                        </p>
                    </div>
                    <div class="btn-toolbar gap-2" role="toolbar">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary active">
                                <i class="fas fa-layer-group me-1"></i>Modern
                            </button>
                            <button type="button" class="btn btn-outline-primary">
                                <i class="fas fa-sitemap me-1"></i>Hierarchy
                            </button>
                        </div>
                        <button type="button" class="btn btn-success" id="btnAdd">
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
                                   placeholder="Search code, description...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="perPageSelect">
                            <option value="10">10 per page</option>
                            <option value="25" selected>25 per page</option>
                            <option value="50">50 per page</option>
                            <option value="100">100 per page</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" id="btnRefresh">
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
            <p class="text-muted mt-3">Loading data with jQuery AJAX...</p>
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
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-spinner fa-spin fa-3x mb-3 d-block"></i>
                                        <h5>Initializing jQuery AJAX...</h5>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="card-footer bg-white">
                <div id="pagination" class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">No data loaded</div>
                    <nav><ul class="pagination mb-0"></ul></nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="coaModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>Add New COA
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="coaForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">COA Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="coa_code" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="coa_desc" required>
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info d-flex align-items-center" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <div>Using <strong>jQuery AJAX</strong> for asynchronous data submission</div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-semibold">H1 Level <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="desc_h1" required 
                                       placeholder="e.g., Assets, Liabilities, Equity">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">H1 ID</label>
                                <input type="text" class="form-control" name="ms_coa_h1_id">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-semibold">H2 Level</label>
                                <input type="text" class="form-control" name="desc_h2" 
                                       placeholder="e.g., Current Assets, Fixed Assets">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">H2 ID</label>
                                <input type="text" class="form-control" name="ms_coa_h2_id">
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
                                                        <input type="text" class="form-control" name="desc_h3">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">H4 Level</label>
                                                        <input type="text" class="form-control" name="desc_h4">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">H5 Level</label>
                                                        <input type="text" class="form-control" name="desc_h5">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">H6 Level</label>
                                                        <input type="text" class="form-control" name="desc_h6">
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
                    <button type="button" class="btn btn-primary" id="btnSave">
                        <i class="fas fa-save me-1"></i>Save with jQuery AJAX
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery 3.7.1 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Configuration
            const API_BASE = '/coa-js';
            const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            let currentPage = 1;
            let searchTimeout = null;
            let coaModal = null;

            // Setup AJAX defaults
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                }
            });

            // Initialize
            coaModal = new bootstrap.Modal($('#coaModal')[0]);
            loadData();

            // Event handlers
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    currentPage = 1;
                    loadData();
                }, 500);
            });

            $('#perPageSelect').on('change', function() {
                loadData();
            });

            $('#btnRefresh').on('click', function() {
                loadData();
            });

            $('#btnAdd').on('click', function() {
                $('#coaForm')[0].reset();
                coaModal.show();
            });

            $('#btnSave').on('click', function() {
                handleSubmit();
            });

            // Load data with jQuery AJAX
            function loadData(page = 1) {
                currentPage = page;
                showLoading();

                const search = $('#searchInput').val();
                const perPage = $('#perPageSelect').val();

                // jQuery AJAX GET request
                $.ajax({
                    url: `${API_BASE}/data`,
                    type: 'GET',
                    data: {
                        page: page,
                        search: search,
                        per_page: perPage
                    },
                    dataType: 'json',
                    success: function(data) {
                        renderTable(data.data);
                        renderPagination(data);
                        updateStats(data);
                    },
                    error: function(xhr, status, error) {
                        showToast('Error loading data: ' + error, 'danger');
                        console.error('AJAX Error:', xhr.responseText);
                    },
                    complete: function() {
                        hideLoading();
                    }
                });
            }

            // Render table
            function renderTable(items) {
                const $tbody = $('#tableBody');
                
                if (!items || items.length === 0) {
                    $tbody.html(`
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    <h5>No data found</h5>
                                    <p class="mb-0">Try adjusting your search or filters</p>
                                </div>
                            </td>
                        </tr>
                    `);
                    return;
                }

                let html = '';
                $.each(items, function(index, item) {
                    html += `
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
                    `;
                });

                $tbody.html(html);
            }

            // Render pagination
            function renderPagination(data) {
                const $container = $('#pagination');
                const { current_page, last_page, from, to, total } = data;

                let html = '<div class="text-muted">Showing ' + (from || 0) + ' to ' + (to || 0) + ' of ' + total + ' results</div>';
                html += '<nav><ul class="pagination mb-0">';

                // Previous
                html += `<li class="page-item ${current_page <= 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${current_page - 1}">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>`;

                // Pages
                for (let i = Math.max(1, current_page - 2); i <= Math.min(last_page, current_page + 2); i++) {
                    html += `<li class="page-item ${i === current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>`;
                }

                // Next
                html += `<li class="page-item ${current_page >= last_page ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${current_page + 1}">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>`;

                html += '</ul></nav>';
                $container.html(html);

                // Bind pagination click events
                $('.pagination a').on('click', function(e) {
                    e.preventDefault();
                    const page = parseInt($(this).data('page'));
                    if (page > 0 && page <= last_page) {
                        loadData(page);
                    }
                });
            }

            // Update stats
            function updateStats(data) {
                $('#totalRecords').text(data.total || 0);
                $('#currentPage').text(data.current_page || 0);
                $('#showingCount').text(data.data.length || 0);
            }

            // Handle form submit with jQuery AJAX
            function handleSubmit() {
                const formData = {
                    coa_code: $('input[name="coa_code"]').val(),
                    coa_desc: $('input[name="coa_desc"]').val(),
                    desc_h1: $('input[name="desc_h1"]').val(),
                    ms_coa_h1_id: $('input[name="ms_coa_h1_id"]').val(),
                    desc_h2: $('input[name="desc_h2"]').val(),
                    ms_coa_h2_id: $('input[name="ms_coa_h2_id"]').val(),
                    desc_h3: $('input[name="desc_h3"]').val(),
                    desc_h4: $('input[name="desc_h4"]').val(),
                    desc_h5: $('input[name="desc_h5"]').val(),
                    desc_h6: $('input[name="desc_h6"]').val()
                };

                // jQuery AJAX POST request
                $.ajax({
                    url: `${API_BASE}/store`,
                    type: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            showToast('COA created successfully with jQuery AJAX!', 'success');
                            coaModal.hide();
                            loadData(currentPage);
                        } else {
                            showToast('Error: ' + (result.message || 'Unknown error'), 'danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        showToast('Error: ' + error, 'danger');
                        console.error('AJAX Error:', xhr.responseText);
                    }
                });
            }

            // Loading functions
            function showLoading() {
                $('#loading').removeClass('d-none');
                $('#tableContainer').css('opacity', '0.5');
            }

            function hideLoading() {
                $('#loading').addClass('d-none');
                $('#tableContainer').css('opacity', '1');
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

                const $toast = $(toastHtml).appendTo('#toastContainer');
                const toast = new bootstrap.Toast($toast[0], { delay: 3000 });
                toast.show();

                $toast.on('hidden.bs.toast', function() {
                    $(this).remove();
                });
            }
        });
    </script>
</body>
</html>
