<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
             class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('message') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
             class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">COA Main Management</h2>
                <p class="text-sm text-gray-600 mt-1">Legacy Level 1 - Main Categories (ms_acc_coa_main)</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('coa.legacy') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-list mr-2"></i>View Legacy Hierarchy
                </a>
                <button wire:click="create" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Add New Main
                </button>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex gap-4">
            <div class="flex-1">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search Code, ID, Description..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
            <select wire:model.live="perPage" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-4 text-white">
            <p class="text-sm font-medium opacity-90">Total Main Categories</p>
            <p class="text-3xl font-bold mt-1">{{ $coaMains->total() }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-4 text-white">
            <p class="text-sm font-medium opacity-90">Current Page</p>
            <p class="text-3xl font-bold mt-1">{{ $coaMains->count() }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-4 text-white">
            <p class="text-sm font-medium opacity-90">Per Page</p>
            <p class="text-3xl font-bold mt-1">{{ $perPage }}</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Main Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Main ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Reference Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Children (Sub1)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Audit</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($coaMains as $main)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono font-bold text-gray-900">{{ $main->coa_main_code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $main->coa_main_id ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $main->coa_main_desc }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ $main->coa_main_coamain2code ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $main->coa_sub1s_count > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $main->coa_sub1s_count }} Sub1
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs space-y-1">
                                    <div class="text-gray-600">
                                        <i class="fas fa-user-plus mr-1"></i>
                                        {{ $main->rec_usercreated ?? '-' }}
                                    </div>
                                    <div class="text-gray-500">
                                        {{ $main->rec_datecreated ? $main->rec_datecreated->format('d/m/Y H:i') : '-' }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="text-lg">Belum ada data COA Main</p>
                                    <p class="text-sm mt-1">Klik tombol "Add New Main" untuk menambahkan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $coaMains->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-800">
                            {{ $editMode ? 'Edit COA Main' : 'Add New COA Main' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-4">
                        <form wire:submit.prevent="{{ $editMode ? 'update' : 'store' }}">
                            <div class="space-y-4">
                                <!-- Main Code -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Main Code <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="coa_main_code" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('coa_main_code') border-red-500 @enderror"
                                           placeholder="e.g., 10000">
                                    @error('coa_main_code') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>

                                <!-- Main ID -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Main ID
                                    </label>
                                    <input type="text" wire:model="coa_main_id" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                           placeholder="Optional">
                                    @error('coa_main_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>

                                <!-- Description -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Description <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="coa_main_desc" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('coa_main_desc') border-red-500 @enderror"
                                           placeholder="e.g., Asset, Liability, Equity">
                                    @error('coa_main_desc') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>

                                <!-- Reference Code -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Reference Code (coa_main_coamain2code)
                                    </label>
                                    <input type="text" wire:model="coa_main_coamain2code" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                           placeholder="Optional reference">
                                    @error('coa_main_coamain2code') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    <p class="text-xs text-gray-500 mt-1">Field untuk referensi tambahan jika diperlukan</p>
                                </div>

                                <!-- Hierarchy ID -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Hierarchy ID (id_h)
                                    </label>
                                    <input type="number" wire:model="id_h" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                           placeholder="e.g., 1, 2, 3">
                                    @error('id_h') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    <p class="text-xs text-gray-500 mt-1">Nomor urut hierarchy</p>
                                </div>

                                <!-- Check Active -->
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="cek_aktif" 
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label class="ml-2 block text-sm font-medium text-gray-700">
                                        Active (cek_aktif)
                                    </label>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                                <button type="button" wire:click="closeModal" 
                                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-save mr-2"></i>
                                    {{ $editMode ? 'Update' : 'Save' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Info Alert -->
    <div class="mt-6 space-y-4">
        <!-- Structure Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                <div class="text-sm text-blue-800">
                    <p class="font-semibold mb-1">COA Main - Level 1 (Legacy System)</p>
                    <p><strong>Table:</strong> <code class="bg-blue-100 px-1 rounded">ms_acc_coa_main</code></p>
                    <p class="mt-1"><strong>Primary Key:</strong> coa_main_code (varchar 50)</p>
                    <p class="mt-1"><strong>Hierarchy:</strong> Level 1 → Level 2 (ms_acc_coasub1) → Level 3 (ms_acc_coasub2) → Level 4 (ms_acc_coa)</p>
                    <p class="mt-1"><strong>Examples:</strong> Asset (10000), Liability (20000), Equity (30000)</p>
                </div>
            </div>
        </div>

        <!-- Database Fields -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-database text-green-600 mt-1 mr-3"></i>
                <div class="text-sm text-green-800">
                    <p class="font-semibold mb-2">Database Structure</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <p class="font-medium">Main Fields:</p>
                            <ul class="list-disc list-inside ml-2">
                                <li>coa_main_code (PK) <span class="text-red-600">*required</span></li>
                                <li>coa_main_id (optional)</li>
                                <li>coa_main_desc <span class="text-red-600">*required</span></li>
                                <li>coa_main_coamain2code (optional ref)</li>
                            </ul>
                        </div>
                        <div>
                            <p class="font-medium">Audit Trail:</p>
                            <ul class="list-disc list-inside ml-2">
                                <li>rec_usercreated, rec_datecreated</li>
                                <li>rec_userupdate, rec_dateupdate</li>
                                <li>rec_status ('1' = Active, '0' = Deleted)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
