<div x-data="{
    showDeleteModal: false,
    deleteId: null,
    showFilters: false
}">
    <!-- Header dengan Search dan Actions -->
    <div class="mb-6 flex justify-between items-center">
        <div class="flex-1 max-w-lg">
            <input type="text" 
                   wire:model.live="search" 
                   placeholder="Search COA code, description, or note..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        
        <div class="flex items-center space-x-2">
            <button @click="showFilters = !showFilters"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                <span x-show="!showFilters">Show Filters</span>
                <span x-show="showFilters">Hide Filters</span>
            </button>
            
            <button wire:click="create" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                + Add New COA
            </button>
        </div>
    </div>

    <!-- Filters Panel -->
    <div x-show="showFilters" 
         x-transition
         class="mb-4 p-4 bg-white rounded-lg shadow">
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="filterStatus" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Status</option>
                    <option value="A">Active</option>
                    <option value="D">Deleted</option>
                    <option value="I">Inactive</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent COA Sub2</label>
                <select wire:model.live="filterParent" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Parents</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->coa_main2_code }}">
                            {{ $parent->coa_main2_code }} - {{ $parent->coa_main2_desc }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
                <select wire:model.live="perPage" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>

    <!-- COA Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        COA Code
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Hierarchy (4 Levels)
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($coas as $coa)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                        {{ $coa->coa_code }}
                        <div class="text-xs text-gray-500 font-normal">ID: {{ $coa->coa_id }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <div class="font-medium">{{ $coa->coa_desc }}</div>
                        @if($coa->coa_note)
                        <div class="text-xs text-gray-500 mt-1">{{ $coa->coa_note }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($coa->coaSub2)
                            <div class="space-y-1">
                                <!-- Level 1: COA Main -->
                                @if($coa->coaSub2->coaSub1 && $coa->coaSub2->coaSub1->coaMain)
                                <div class="flex items-center text-xs">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-medium">
                                        L1
                                    </span>
                                    <span class="ml-2 text-gray-700">
                                        {{ $coa->coaSub2->coaSub1->coaMain->coa_main_code }} - 
                                        {{ Str::limit($coa->coaSub2->coaSub1->coaMain->coa_main_desc, 20) }}
                                    </span>
                                </div>
                                @endif
                                
                                <!-- Level 2: COA Sub1 -->
                                @if($coa->coaSub2->coaSub1)
                                <div class="flex items-center text-xs pl-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-green-100 text-green-800 font-medium">
                                        L2
                                    </span>
                                    <span class="ml-2 text-gray-700">
                                        {{ $coa->coaSub2->coaSub1->coa_main1_code }} - 
                                        {{ Str::limit($coa->coaSub2->coaSub1->coa_main1_desc, 20) }}
                                    </span>
                                </div>
                                @endif
                                
                                <!-- Level 3: COA Sub2 -->
                                <div class="flex items-center text-xs pl-8">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-purple-100 text-purple-800 font-medium">
                                        L3
                                    </span>
                                    <span class="ml-2 text-gray-700">
                                        {{ $coa->coaSub2->coa_main2_code }} - 
                                        {{ Str::limit($coa->coaSub2->coa_main2_desc, 20) }}
                                    </span>
                                </div>
                                
                                <!-- Level 4: COA Detail (current) -->
                                <div class="flex items-center text-xs pl-12">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-orange-100 text-orange-800 font-medium">
                                        L4
                                    </span>
                                    <span class="ml-2 text-gray-900 font-medium">
                                        Current Account
                                    </span>
                                </div>
                            </div>
                        @else
                            <span class="text-gray-400 text-xs">No parent</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 text-xs rounded-full font-semibold
                            {{ $coa->account_type === 'Asset' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $coa->account_type === 'Liability' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $coa->account_type === 'Equity' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $coa->account_type === 'Revenue' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $coa->account_type === 'Expense' ? 'bg-orange-100 text-orange-800' : '' }}
                            {{ $coa->account_type === 'Other' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ $coa->account_type }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($coa->rec_status === 'A')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div x-data="{ open: false }" class="relative inline-block text-left">
                            <button @click="open = !open" 
                                    class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                </svg>
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                <div class="py-1">
                                    <button wire:click="edit('{{ $coa->coa_code }}')" 
                                            @click="open = false"
                                            class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Edit
                                    </button>
                                    <button @click="deleteId = '{{ $coa->coa_code }}'; showDeleteModal = true; open = false"
                                            class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="mt-2">No COA found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $coas->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto m-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ $editMode ? 'Edit COA' : 'Create New COA' }}
                </h3>
            </div>

            <form wire:submit.prevent="save" class="px-6 py-4">
                <div class="space-y-4">
                    <!-- COA Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            COA Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               wire:model="coa_code" 
                               {{ $editMode ? 'readonly' : '' }}
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg {{ $editMode ? 'bg-gray-100' : '' }}"
                               placeholder="e.g., 10001">
                        @error('coa_code') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- COA ID -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            COA ID <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               wire:model="coa_id" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                               placeholder="Unique identifier">
                        @error('coa_id') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Parent COA Sub2 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Parent COA Sub2 <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="coa_coasub2code" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">Select Parent</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->coa_main2_code }}">
                                    {{ $parent->coa_main2_code }} - {{ $parent->coa_main2_desc }}
                                </option>
                            @endforeach
                        </select>
                        @error('coa_coasub2code') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <input type="text" 
                               wire:model="coa_desc" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                               placeholder="Account description">
                        @error('coa_desc') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Note -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Note
                        </label>
                        <textarea wire:model="coa_note" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                  placeholder="Additional notes"></textarea>
                        @error('coa_note') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Cash Flow Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Cash Flow Code
                        </label>
                        <input type="text" 
                               wire:model="arus_kas_code" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                               placeholder="Cash flow category">
                        @error('arus_kas_code') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" 
                            wire:click="closeModal"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        {{ $editMode ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" 
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full m-4">
            <div class="px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Delete</h3>
                <p class="text-gray-600 mb-6">
                    Are you sure you want to delete this COA? This action cannot be undone.
                </p>
                
                <div class="flex justify-end space-x-2">
                    <button @click="showDeleteModal = false" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                    <button @click="$wire.delete(deleteId); showDeleteModal = false" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
