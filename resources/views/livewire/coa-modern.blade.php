<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">COA Modern Management</h2>
                <p class="text-sm text-gray-600 mt-1">Flexible Hierarchy System: H1 → H2 → H3 → H4 → H5 → H6</p>
                @if($filter_sub2)
                    <div class="mt-2 inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-lg text-sm">
                        <i class="fas fa-filter mr-2"></i>
                        Filtered by Legacy Level 3 (Sub2): <strong class="ml-1">{{ $filter_sub2 }}</strong>
                        <button wire:click="$set('filter_sub2', '')" class="ml-2 text-blue-600 hover:text-blue-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('coa.legacy') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Switch to Legacy
                </a>
                <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-plus mr-2"></i>Add New
                </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search code, description, H1-H6..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Filter Level -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hierarchy Level</label>
                <select wire:model.live="filterLevel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Levels</option>
                    <option value="1">H1 Only</option>
                    <option value="2">Up to H2</option>
                    <option value="3">Up to H3</option>
                    <option value="4">Up to H4</option>
                    <option value="5">Up to H5</option>
                    <option value="6">Up to H6</option>
                </select>
            </div>

            <!-- Filter H1 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">H1 Category</label>
                <select wire:model.live="filterH1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All H1</option>
                    @foreach($h1s as $id => $desc)
                        <option value="{{ $id }}">{{ $desc }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter H2 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">H2 Category</label>
                <select wire:model.live="filterH2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All H2</option>
                    @foreach($h2s as $id => $desc)
                        <option value="{{ $id }}">{{ $desc }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-4">
            <div class="text-white">
                <p class="text-sm font-medium opacity-90">Total Accounts</p>
                <p class="text-3xl font-bold mt-1">{{ $coas->total() }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-4">
            <div class="text-white">
                <p class="text-sm font-medium opacity-90">H1 Categories</p>
                <p class="text-3xl font-bold mt-1">{{ $h1s->count() }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-4">
            <div class="text-white">
                <p class="text-sm font-medium opacity-90">H2 Categories</p>
                <p class="text-3xl font-bold mt-1">{{ $h2s->count() }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-4">
            <div class="text-white">
                <p class="text-sm font-medium opacity-90">This Page</p>
                <p class="text-3xl font-bold mt-1">{{ $coas->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('coa_code')" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center gap-2">
                                COA Code
                                @if($sortBy === 'coa_code')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                            Level
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                            Flexible Hierarchy (H1-H6)
                        </th>
                        <th wire:click="sortBy('coa_desc')" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center gap-2">
                                Description
                                @if($sortBy === 'coa_desc')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($coas as $coa)
                        @php
                            $currentLevel = $coa->getCurrentHierarchyLevel();
                            $levelColors = [
                                1 => 'bg-blue-100 text-blue-800',
                                2 => 'bg-green-100 text-green-800',
                                3 => 'bg-purple-100 text-purple-800',
                                4 => 'bg-orange-100 text-orange-800',
                                5 => 'bg-pink-100 text-pink-800',
                                6 => 'bg-indigo-100 text-indigo-800',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono font-semibold text-gray-900">{{ $coa->coa_code }}</div>
                                <div class="text-xs text-gray-500">ID: {{ $coa->coa_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 {{ $levelColors[$currentLevel] ?? 'bg-gray-100 text-gray-800' }} rounded-full text-xs font-bold">
                                    H{{ $currentLevel }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs space-y-1">
                                    @if($coa->desc_h1)
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs font-medium">H1</span>
                                            <span class="text-gray-700">{{ $coa->desc_h1 }}</span>
                                        </div>
                                    @endif
                                    @if($coa->desc_h2)
                                        <div class="flex items-center gap-2 ml-3">
                                            <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded text-xs font-medium">H2</span>
                                            <span class="text-gray-700">{{ $coa->desc_h2 }}</span>
                                        </div>
                                    @endif
                                    @if($coa->desc_h3)
                                        <div class="flex items-center gap-2 ml-6">
                                            <span class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded text-xs font-medium">H3</span>
                                            <span class="text-gray-700">{{ $coa->desc_h3 }}</span>
                                        </div>
                                    @endif
                                    @if($coa->desc_h4)
                                        <div class="flex items-center gap-2 ml-9">
                                            <span class="px-2 py-0.5 bg-orange-100 text-orange-800 rounded text-xs font-medium">H4</span>
                                            <span class="text-gray-700">{{ $coa->desc_h4 }}</span>
                                        </div>
                                    @endif
                                    @if($coa->desc_h5)
                                        <div class="flex items-center gap-2 ml-12">
                                            <span class="px-2 py-0.5 bg-pink-100 text-pink-800 rounded text-xs font-medium">H5</span>
                                            <span class="text-gray-700">{{ $coa->desc_h5 }}</span>
                                        </div>
                                    @endif
                                    @if($coa->desc_h6)
                                        <div class="flex items-center gap-2 ml-15">
                                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded text-xs font-medium">H6</span>
                                            <span class="text-gray-700">{{ $coa->desc_h6 }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-medium">{{ $coa->coa_desc }}</div>
                                @if($coa->coa_note)
                                    <div class="text-xs text-gray-500 mt-1">{{ $coa->coa_note }}</div>
                                @endif
                                @if($coa->coa_coasub2code)
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                            <i class="fas fa-link mr-1"></i>Legacy: {{ $coa->coa_coasub2code }}
                                        </span>
                                    </div>
                                @endif
                                @if($coa->isLeafNode())
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Leaf Node
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $coa->rec_status === 'A' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $coa->rec_status === 'A' ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-yellow-600 hover:text-yellow-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="text-lg">No accounts found</p>
                                    <p class="text-sm mt-1">Try adjusting your search or filters</p>
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

    <!-- Info Alert -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-rocket text-blue-600 mt-1 mr-3"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Modern System (Flexible 1-6 Level Hierarchy)</p>
                <p>This page uses the new flexible structure: <code class="bg-blue-100 px-1 rounded">H1 → H2 → H3 → H4 → H5 → H6</code></p>
                <p class="mt-1">Each account can use 1 to 6 levels as needed. For the old 3-level system, use <a href="{{ route('coa.legacy') }}" class="underline font-semibold">COA Legacy</a></p>
            </div>
        </div>
    </div>
</div>
