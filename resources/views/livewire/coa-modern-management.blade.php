<div>
    <!-- Flash Message -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
             class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('message') }}
        </div>
    @endif

    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">COA Modern Management</h2>
                <p class="text-sm text-gray-600 mt-1">Flexible H1-H6 Hierarchy System dalam 1 Tabel</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('coa.legacy') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Legacy View
                </a>
                <button wire:click="create" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Add New COA
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
                    placeholder="Search COA Code, Description, Hierarchy..."
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
            <p class="text-sm font-medium opacity-90">Total COA Active</p>
            <p class="text-3xl font-bold mt-1">{{ $coas->total() }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-4 text-white">
            <p class="text-sm font-medium opacity-90">Current Page</p>
            <p class="text-3xl font-bold mt-1">{{ $coas->count() }}</p>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">COA Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Hierarchy (H1-H6)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Legacy Link</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Audit</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($coas as $coa)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono font-bold text-gray-900">{{ $coa->coa_code }}</div>
                                <div class="text-xs text-gray-500">ID: {{ $coa->coa_id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $coa->coa_desc }}</div>
                                @if($coa->coa_note)
                                    <div class="text-xs text-gray-500 mt-1">{{ $coa->coa_note }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs space-y-1">
                                    @if($coa->desc_h1)
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded font-medium">H1</span>
                                            <span class="text-gray-700">{{ $coa->desc_h1 }}</span>
                                        </div>
                                    @endif
                                    @if($coa->desc_h2)
                                        <div class="flex items-center gap-2 ml-4">
                                            <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded font-medium">H2</span>
                                            <span class="text-gray-700">{{ $coa->desc_h2 }}</span>
                                        </div>
                                    @endif
                                    @if($coa->desc_h3)
                                        <div class="flex items-center gap-2 ml-8">
                                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded font-medium">H3</span>
                                            <span class="text-gray-700">{{ $coa->desc_h3 }}</span>
                                        </div>
                                    @endif
                                    @if($coa->desc_h4)
                                        <div class="flex items-center gap-2 ml-12">
                                            <span class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded font-medium">H4</span>
                                            <span class="text-gray-700">{{ $coa->desc_h4 }}</span>
                                        </div>
                                    @endif
                                    @if($coa->desc_h5)
                                        <div class="flex items-center gap-2 ml-16">
                                            <span class="px-2 py-0.5 bg-pink-100 text-pink-800 rounded font-medium">H5</span>
                                            <span class="text-gray-700">{{ $coa->desc_h5 }}</span>
                                        </div>
                                    @endif
                                    @if($coa->desc_h6)
                                        <div class="flex items-center gap-2 ml-20">
                                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded font-medium">H6</span>
                                            <span class="text-gray-700">{{ $coa->desc_h6 }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($coa->coa_coasub2code)
                                    <div class="text-xs">
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded font-medium">
                                            {{ $coa->coa_coasub2code }}
                                        </span>
                                        @if($coa->sub2_desc)
                                            <div class="text-gray-600 mt-1">{{ $coa->sub2_desc }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs space-y-1">
                                    <div class="text-gray-600">
                                        <i class="fas fa-user-plus mr-1"></i>
                                        {{ $coa->rec_usercreated ?? '-' }}
                                    </div>
                                    <div class="text-gray-500">
                                        {{ $coa->rec_datecreated ? $coa->rec_datecreated->format('d/m/Y H:i') : '-' }}
                                    </div>
                                    @if($coa->rec_userupdate && $coa->rec_userupdate != $coa->rec_usercreated)
                                        <div class="text-gray-600 mt-2">
                                            <i class="fas fa-user-edit mr-1"></i>
                                            {{ $coa->rec_userupdate }}
                                        </div>
                                        <div class="text-gray-500">
                                            {{ $coa->rec_dateupdate ? $coa->rec_dateupdate->format('d/m/Y H:i') : '-' }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="text-lg">Belum ada data COA</p>
                                    <p class="text-sm mt-1">Klik tombol "Add New COA" untuk menambahkan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $coas->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-800">
                            {{ $editMode ? 'Edit COA' : 'Add New COA' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-4">
                        <form wire:submit.prevent="{{ $editMode ? 'update' : 'store' }}">
                            <!-- Basic Info -->
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-700 mb-3">Basic Information</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            COA Code <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" wire:model="coa_code" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('coa_code') border-red-500 @enderror">
                                        @error('coa_code') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            COA ID <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" wire:model="coa_id" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('coa_id') border-red-500 @enderror">
                                        @error('coa_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Description <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" wire:model="coa_desc" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('coa_desc') border-red-500 @enderror">
                                        @error('coa_desc') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Hierarchy H1 (Required) -->
                            <div class="mb-6 border-l-4 border-blue-500 pl-4">
                                <h4 class="text-lg font-semibold text-gray-700 mb-3">
                                    Level 1 (H1) <span class="text-red-500">*</span>
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">H1 ID <span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="ms_coa_h1_id" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('ms_coa_h1_id') border-red-500 @enderror">
                                        @error('ms_coa_h1_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">H1 Integer ID</label>
                                        <input type="number" wire:model="id_h1" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">H1 Description <span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="desc_h1" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('desc_h1') border-red-500 @enderror">
                                        @error('desc_h1') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Hierarchy H2 (Optional) -->
                            <div class="mb-6 border-l-4 border-green-500 pl-4">
                                <h4 class="text-lg font-semibold text-gray-700 mb-3">Level 2 (H2) <span class="text-gray-400 text-sm">Optional</span></h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">H2 ID</label>
                                        <input type="text" wire:model="ms_coa_h2_id" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">H2 Integer ID</label>
                                        <input type="number" wire:model="id_h2" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">H2 Description</label>
                                        <input type="text" wire:model="desc_h2" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Hierarchy H3-H6 (Collapsible Optional) -->
                            <div x-data="{ showMore: false }" class="mb-6">
                                <button type="button" @click="showMore = !showMore" 
                                        class="flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-800 mb-3">
                                    <i class="fas" :class="showMore ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                    <span x-text="showMore ? 'Hide' : 'Show'"></span> Additional Levels (H3-H6)
                                </button>

                                <div x-show="showMore" x-collapse>
                                    <!-- H3 -->
                                    <div class="mb-4 border-l-4 border-yellow-500 pl-4">
                                        <h4 class="text-md font-semibold text-gray-700 mb-2">Level 3 (H3)</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">H3 ID</label>
                                                <input type="text" wire:model="ms_coa_h3_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">H3 Integer ID</label>
                                                <input type="number" wire:model="id_h3" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">H3 Description</label>
                                                <input type="text" wire:model="desc_h3" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- H4 -->
                                    <div class="mb-4 border-l-4 border-purple-500 pl-4">
                                        <h4 class="text-md font-semibold text-gray-700 mb-2">Level 4 (H4)</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">H4 ID</label>
                                                <input type="text" wire:model="ms_coa_h4_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">H4 Integer ID</label>
                                                <input type="number" wire:model="id_h4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">H4 Description</label>
                                                <input type="text" wire:model="desc_h4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- H5 -->
                                    <div class="mb-4 border-l-4 border-pink-500 pl-4">
                                        <h4 class="text-md font-semibold text-gray-700 mb-2">Level 5 (H5)</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">H5 ID</label>
                                                <input type="text" wire:model="ms_coa_h5_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">H5 Integer ID</label>
                                                <input type="number" wire:model="id_h5" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">H5 Description</label>
                                                <input type="text" wire:model="desc_h5" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- H6 -->
                                    <div class="mb-4 border-l-4 border-indigo-500 pl-4">
                                        <h4 class="text-md font-semibold text-gray-700 mb-2">Level 6 (H6)</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">H6 ID</label>
                                                <input type="text" wire:model="ms_coa_h6_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">H6 Integer ID</label>
                                                <input type="number" wire:model="id_h6" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">H6 Description</label>
                                                <input type="text" wire:model="desc_h6" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Fields -->
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-700 mb-3">Additional Information</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                                        <input type="text" wire:model="coa_note" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Arus Kas Code</label>
                                        <input type="text" wire:model="arus_kas_code" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">COA Header</label>
                                        <input type="text" wire:model="ms_acc_coa_h" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Legacy System Reference (Backward Compatibility) -->
                            <div x-data="{ showLegacy: false }" class="mb-6">
                                <button type="button" @click="showLegacy = !showLegacy" 
                                        class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800 mb-3">
                                    <i class="fas" :class="showLegacy ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                    <span x-text="showLegacy ? 'Hide' : 'Show'"></span> Legacy System Reference (Old 4-Level Structure)
                                </button>

                                <div x-show="showLegacy" x-collapse class="border-l-4 border-orange-500 pl-4 bg-orange-50 p-4 rounded">
                                    <p class="text-sm text-orange-800 mb-3">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Fields ini untuk backward compatibility dengan sistem lama (ms_acc_coa_main, ms_acc_coasub1, ms_acc_coasub2)
                                    </p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Legacy CoaSub2 Code</label>
                                            <input type="text" wire:model="coa_coasub2code" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            <p class="text-xs text-gray-500 mt-1">FK ke ms_acc_coasub2.coasub2_code</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Old Sub2 ID</label>
                                            <input type="text" wire:model="id_old_sub_2" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Old Sub1 ID</label>
                                            <input type="text" wire:model="id_old_sub1" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Old Main ID</label>
                                            <input type="text" wire:model="id_old_main" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Sub2 Description (Old)</label>
                                            <input type="text" wire:model="sub2_desc" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Sub1 Description (Old)</label>
                                            <input type="text" wire:model="sub1_desc" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Main Description (Old)</label>
                                            <input type="text" wire:model="main_desc" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
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
        <!-- Modern System Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                <div class="text-sm text-blue-800">
                    <p class="font-semibold mb-1">Flexible H1-H6 Hierarchy System (Modern)</p>
                    <p><strong>Minimum:</strong> H1 (1 level hierarchy) - Wajib diisi</p>
                    <p class="mt-1"><strong>Maximum:</strong> H1 → H2 → H3 → H4 → H5 → H6 (6 levels hierarchy)</p>
                    <p class="mt-1"><strong>Flexible:</strong> Bisa create COA di level berapa saja sesuai kebutuhan</p>
                    <p class="mt-1"><strong>Contoh:</strong> Asset (H1) → Current Asset (H2) → Cash & Bank (H3) → Bank BCA (H4)</p>
                </div>
            </div>
        </div>

        <!-- Database Structure Info -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-database text-green-600 mt-1 mr-3"></i>
                <div class="text-sm text-green-800">
                    <p class="font-semibold mb-2">Database Structure - Table: ms_acc_coa</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <p class="font-medium">Basic Fields:</p>
                            <ul class="list-disc list-inside ml-2">
                                <li>coa_code (PK)</li>
                                <li>coa_id, coa_desc, coa_note</li>
                                <li>arus_kas_code, ms_acc_coa_h</li>
                            </ul>
                        </div>
                        <div>
                            <p class="font-medium">Hierarchy Fields (H1-H6):</p>
                            <ul class="list-disc list-inside ml-2">
                                <li>ms_coa_h1_id, desc_h1, id_h1</li>
                                <li>ms_coa_h2_id, desc_h2, id_h2</li>
                                <li>... sampai H6</li>
                            </ul>
                        </div>
                        <div>
                            <p class="font-medium">Legacy Reference:</p>
                            <ul class="list-disc list-inside ml-2">
                                <li>coa_coasub2code (FK)</li>
                                <li>id_old_sub_2, id_old_sub1, id_old_main</li>
                                <li>sub2_desc, sub1_desc, main_desc</li>
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

        <!-- Legacy System Info -->
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-link text-orange-600 mt-1 mr-3"></i>
                <div class="text-sm text-orange-800">
                    <p class="font-semibold mb-1">Legacy System (4 Separate Tables)</p>
                    <p class="mt-1"><strong>Level 1:</strong> <code class="bg-orange-100 px-1 rounded">ms_acc_coa_main</code> → Main Categories</p>
                    <p class="mt-1"><strong>Level 2:</strong> <code class="bg-orange-100 px-1 rounded">ms_acc_coasub1</code> → Sub Category 1</p>
                    <p class="mt-1"><strong>Level 3:</strong> <code class="bg-orange-100 px-1 rounded">ms_acc_coasub2</code> → Sub Category 2</p>
                    <p class="mt-1"><strong>Level 4:</strong> <code class="bg-orange-100 px-1 rounded">ms_acc_coa</code> → Detail COA (this table)</p>
                    <p class="mt-2"><strong>Backward Compatibility:</strong> Field <code class="bg-orange-100 px-1 rounded">coa_coasub2code</code> untuk link ke sistem lama</p>
                    <p class="mt-1">Field legacy lainnya: id_old_*, sub2_desc, sub1_desc, main_desc untuk migrasi data</p>
                </div>
            </div>
        </div>
    </div>
</div>
