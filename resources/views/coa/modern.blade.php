<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>COA Management - Pure JavaScript</title>
    
    @vite(['resources/css/app.css'])
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <style>
        .loader {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(400px); }
            to { transform: translateX(0); }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">COA Management</h1>
                    <p class="text-gray-600 mt-1">Pure JavaScript - No Livewire</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="switchView('modern')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-layer-group mr-2"></i>Modern
                    </button>
                    <button onclick="switchView('hierarchy')" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        <i class="fas fa-sitemap mr-2"></i>Hierarchy
                    </button>
                    <button onclick="openAddModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>Add New
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <input 
                        type="text" 
                        id="searchInput"
                        placeholder="Search code, description..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        onkeyup="debounceSearch()"
                    >
                </div>
                <div>
                    <select id="perPageSelect" onchange="loadData()" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="10">10 per page</option>
                        <option value="25" selected>25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
                <div>
                    <button onclick="loadData()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <p class="text-sm font-medium opacity-90">Total Records</p>
                <p class="text-4xl font-bold mt-2" id="totalRecords">-</p>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <p class="text-sm font-medium opacity-90">Current Page</p>
                <p class="text-4xl font-bold mt-2" id="currentPage">-</p>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <p class="text-sm font-medium opacity-90">Showing</p>
                <p class="text-4xl font-bold mt-2" id="showingCount">-</p>
            </div>
        </div>

        <!-- Loading -->
        <div id="loading" class="hidden flex justify-center items-center py-12">
            <div class="loader"></div>
        </div>

        <!-- Table -->
        <div id="tableContainer" class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">H1</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">H2</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">H3-H6</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Data will be inserted here -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="pagination" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <!-- Pagination will be inserted here -->
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold" id="modalTitle">Add New COA</h3>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <form id="coaForm" onsubmit="handleSubmit(event)">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">COA Code *</label>
                                <input type="text" id="coa_code" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                                <input type="text" id="coa_desc" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">H1 Level (Required) *</label>
                            <input type="text" id="desc_h1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">H1 ID</label>
                            <input type="text" id="ms_coa_h1_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">H2 Level</label>
                            <input type="text" id="desc_h2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">H2 ID</label>
                            <input type="text" id="ms_coa_h2_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">H3 Level</label>
                                <input type="text" id="desc_h3" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">H4 Level</label>
                                <input type="text" id="desc_h4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">H5 Level</label>
                                <input type="text" id="desc_h5" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">H6 Level</label>
                                <input type="text" id="desc_h6" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i>Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '/coa-js';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
        let currentPage = 1;
        let searchTimeout = null;

        // Load data on page load
        document.addEventListener('DOMContentLoaded', () => {
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

        // Load data from API
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
                showToast('Error loading data: ' + error.message, 'error');
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
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p class="text-lg">No data found</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = items.map(item => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-mono text-sm font-semibold">${item.coa_code || '-'}</td>
                    <td class="px-6 py-4 text-sm">${item.coa_desc || '-'}</td>
                    <td class="px-6 py-4 text-sm">
                        ${item.desc_h1 ? `<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">${item.desc_h1}</span>` : '-'}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        ${item.desc_h2 ? `<span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">${item.desc_h2}</span>` : '-'}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        ${item.desc_h3 ? `<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs mr-1">${item.desc_h3}</span>` : ''}
                        ${item.desc_h4 ? `<span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs mr-1">${item.desc_h4}</span>` : ''}
                        ${item.desc_h5 ? `<span class="px-2 py-1 bg-pink-100 text-pink-800 rounded text-xs mr-1">${item.desc_h5}</span>` : ''}
                        ${item.desc_h6 ? `<span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded text-xs">${item.desc_h6}</span>` : ''}
                        ${!item.desc_h3 && !item.desc_h4 && !item.desc_h5 && !item.desc_h6 ? '-' : ''}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-2 py-1 ${item.rec_status === '1' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} rounded text-xs">
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

            let html = '<div class="flex justify-between items-center">';
            html += `<div class="text-sm text-gray-700">Showing ${from || 0} to ${to || 0} of ${total} results</div>`;
            html += '<div class="flex gap-1">';

            // Previous
            if (current_page > 1) {
                html += `<button onclick="loadData(${current_page - 1})" class="px-3 py-1 border rounded hover:bg-gray-100">Previous</button>`;
            }

            // Pages
            for (let i = Math.max(1, current_page - 2); i <= Math.min(last_page, current_page + 2); i++) {
                html += `<button onclick="loadData(${i})" class="px-3 py-1 border rounded ${i === current_page ? 'bg-blue-600 text-white' : 'hover:bg-gray-100'}">${i}</button>`;
            }

            // Next
            if (current_page < last_page) {
                html += `<button onclick="loadData(${current_page + 1})" class="px-3 py-1 border rounded hover:bg-gray-100">Next</button>`;
            }

            html += '</div></div>';
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
            document.getElementById('modalTitle').textContent = 'Add New COA';
            document.getElementById('coaForm').reset();
            document.getElementById('modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
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
                    closeModal();
                    loadData(currentPage);
                } else {
                    showToast('Error: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                showToast('Error: ' + error.message, 'error');
            }
        }

        // Switch view
        function switchView(view) {
            if (view === 'hierarchy') {
                window.location.href = '/coa-full-hierarchy';
            }
        }

        // Loading
        function showLoading() {
            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('tableContainer').classList.add('opacity-50');
        }

        function hideLoading() {
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('tableContainer').classList.remove('opacity-50');
        }

        // Toast notification
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white px-6 py-4 rounded-lg shadow-lg`;
            toast.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} text-xl"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    </script>
</body>
</html>
