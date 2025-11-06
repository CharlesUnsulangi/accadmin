<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>COA Management - Alpine.js</title>
    
    @vite(['resources/css/app.css'])
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
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
    </style>
</head>
<body class="bg-gray-100" x-data="coaApp()" x-init="init()">
    <div class="container mx-auto px-4 py-8">
        <!-- Toast Notification -->
        <div x-show="toast.show" 
             x-transition
             x-cloak
             class="fixed top-4 right-4 z-50 min-w-[300px] px-6 py-4 rounded-lg shadow-lg text-white"
             :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'">
            <div class="flex items-center gap-3">
                <i :class="toast.type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'" class="text-xl"></i>
                <span x-text="toast.message"></span>
            </div>
        </div>

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">COA Management</h1>
                    <p class="text-gray-600 mt-1">Alpine.js Version - Reactive & Lightweight</p>
                </div>
                <div class="flex gap-2">
                    <button @click="view = 'modern'" 
                            class="px-4 py-2 rounded-lg transition"
                            :class="view === 'modern' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'">
                        <i class="fas fa-layer-group mr-2"></i>Modern
                    </button>
                    <button @click="loadHierarchy()" 
                            class="px-4 py-2 rounded-lg transition"
                            :class="view === 'hierarchy' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'">
                        <i class="fas fa-sitemap mr-2"></i>Hierarchy
                    </button>
                    <button @click="openModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
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
                        x-model="filters.search"
                        @input.debounce.500ms="loadData()"
                        placeholder="Search code, description..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                </div>
                <div>
                    <select x-model="filters.perPage" @change="loadData()" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
                <div>
                    <button @click="loadData()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <p class="text-sm font-medium opacity-90">Total Records</p>
                <p class="text-4xl font-bold mt-2" x-text="pagination.total || 0"></p>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <p class="text-sm font-medium opacity-90">Current Page</p>
                <p class="text-4xl font-bold mt-2" x-text="pagination.current_page || 0"></p>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <p class="text-sm font-medium opacity-90">Showing</p>
                <p class="text-4xl font-bold mt-2" x-text="items.length || 0"></p>
            </div>
        </div>

        <!-- Loading -->
        <div x-show="loading" x-cloak class="flex justify-center items-center py-12">
            <div class="loader"></div>
        </div>

        <!-- Table -->
        <div x-show="!loading" class="bg-white rounded-lg shadow-sm overflow-hidden">
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
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-if="items.length === 0">
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="text-lg">No data found</p>
                                </td>
                            </tr>
                        </template>

                        <template x-for="item in items" :key="item.coa_code">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-mono text-sm font-semibold" x-text="item.coa_code"></td>
                                <td class="px-6 py-4 text-sm" x-text="item.coa_desc || '-'"></td>
                                <td class="px-6 py-4 text-sm">
                                    <span x-show="item.desc_h1" class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs" x-text="item.desc_h1"></span>
                                    <span x-show="!item.desc_h1">-</span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span x-show="item.desc_h2" class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs" x-text="item.desc_h2"></span>
                                    <span x-show="!item.desc_h2">-</span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex flex-wrap gap-1">
                                        <span x-show="item.desc_h3" class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs" x-text="item.desc_h3"></span>
                                        <span x-show="item.desc_h4" class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs" x-text="item.desc_h4"></span>
                                        <span x-show="item.desc_h5" class="px-2 py-1 bg-pink-100 text-pink-800 rounded text-xs" x-text="item.desc_h5"></span>
                                        <span x-show="item.desc_h6" class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded text-xs" x-text="item.desc_h6"></span>
                                        <span x-show="!item.desc_h3 && !item.desc_h4 && !item.desc_h5 && !item.desc_h6">-</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 rounded text-xs"
                                          :class="item.rec_status === '1' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                          x-text="item.rec_status === '1' ? 'Active' : 'Inactive'">
                                    </span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-700">
                        Showing <span x-text="pagination.from || 0"></span> to <span x-text="pagination.to || 0"></span> of <span x-text="pagination.total"></span> results
                    </div>
                    <div class="flex gap-1">
                        <button @click="loadData(pagination.current_page - 1)" 
                                :disabled="pagination.current_page <= 1"
                                :class="pagination.current_page <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                class="px-3 py-1 border rounded">
                            Previous
                        </button>

                        <template x-for="page in paginationPages()" :key="page">
                            <button @click="loadData(page)" 
                                    class="px-3 py-1 border rounded"
                                    :class="page === pagination.current_page ? 'bg-blue-600 text-white' : 'hover:bg-gray-100'"
                                    x-text="page">
                            </button>
                        </template>

                        <button @click="loadData(pagination.current_page + 1)" 
                                :disabled="pagination.current_page >= pagination.last_page"
                                :class="pagination.current_page >= pagination.last_page ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                                class="px-3 py-1 border rounded">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div x-show="modal.show" 
         x-cloak
         @click.self="closeModal()"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-screen overflow-y-auto"
             @click.stop>
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold" x-text="modal.title"></h3>
                    <button @click="closeModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <form @submit.prevent="handleSubmit()">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">COA Code *</label>
                                <input type="text" x-model="form.coa_code" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                                <input type="text" x-model="form.coa_desc" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">H1 Level (Required) *</label>
                            <input type="text" x-model="form.desc_h1" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">H1 ID</label>
                            <input type="text" x-model="form.ms_coa_h1_id" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">H2 Level</label>
                            <input type="text" x-model="form.desc_h2" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">H2 ID</label>
                            <input type="text" x-model="form.ms_coa_h2_id" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">H3 Level</label>
                                <input type="text" x-model="form.desc_h3" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">H4 Level</label>
                                <input type="text" x-model="form.desc_h4" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">H5 Level</label>
                                <input type="text" x-model="form.desc_h5" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">H6 Level</label>
                                <input type="text" x-model="form.desc_h6" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="closeModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" 
                                :disabled="loading"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                            <i class="fas fa-save mr-2"></i>Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function coaApp() {
            return {
                // State
                loading: false,
                view: 'modern',
                items: [],
                pagination: {},
                filters: {
                    search: '',
                    perPage: 25
                },
                modal: {
                    show: false,
                    title: 'Add New COA'
                },
                form: this.resetForm(),
                toast: {
                    show: false,
                    message: '',
                    type: 'success'
                },

                // Initialize
                init() {
                    this.loadData();
                },

                // Reset form
                resetForm() {
                    return {
                        coa_code: '',
                        coa_desc: '',
                        desc_h1: '',
                        ms_coa_h1_id: '',
                        desc_h2: '',
                        ms_coa_h2_id: '',
                        desc_h3: '',
                        desc_h4: '',
                        desc_h5: '',
                        desc_h6: ''
                    };
                },

                // Load data
                async loadData(page = 1) {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams({
                            page: page,
                            search: this.filters.search,
                            per_page: this.filters.perPage
                        });

                        const response = await fetch(`/coa-js/data?${params}`, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();
                        this.items = data.data || [];
                        this.pagination = {
                            current_page: data.current_page,
                            last_page: data.last_page,
                            from: data.from,
                            to: data.to,
                            total: data.total
                        };
                    } catch (error) {
                        this.showToast('Error loading data: ' + error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                // Load hierarchy
                async loadHierarchy() {
                    this.view = 'hierarchy';
                    this.showToast('Hierarchy view coming soon!', 'info');
                },

                // Pagination pages
                paginationPages() {
                    const pages = [];
                    const current = this.pagination.current_page;
                    const last = this.pagination.last_page;
                    
                    for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
                        pages.push(i);
                    }
                    return pages;
                },

                // Modal functions
                openModal() {
                    this.modal.show = true;
                    this.modal.title = 'Add New COA';
                    this.form = this.resetForm();
                },

                closeModal() {
                    this.modal.show = false;
                },

                // Handle form submit
                async handleSubmit() {
                    this.loading = true;
                    try {
                        const response = await fetch('/coa-js/store', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.form)
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.showToast('COA created successfully!', 'success');
                            this.closeModal();
                            this.loadData(this.pagination.current_page);
                        } else {
                            this.showToast('Error: ' + (result.message || 'Unknown error'), 'error');
                        }
                    } catch (error) {
                        this.showToast('Error: ' + error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                // Show toast
                showToast(message, type = 'success') {
                    this.toast = {
                        show: true,
                        message: message,
                        type: type
                    };

                    setTimeout(() => {
                        this.toast.show = false;
                    }, 3000);
                }
            }
        }
    </script>
</body>
</html>
