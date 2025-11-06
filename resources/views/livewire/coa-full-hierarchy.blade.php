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
                <h2 class="text-2xl font-bold text-gray-800">COA Full Hierarchy Report</h2>
                <p class="text-sm text-gray-600 mt-1">Complete 4-Level Hierarchy: Main → Sub1 → Sub2 → Detail COA</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('coa.modern') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-layer-group mr-2"></i>Modern View
                </a>
                <a href="{{ route('coa.legacy') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-sitemap mr-2"></i>Legacy View
                </a>
                <button wire:click="export" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
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
                    placeholder="Search in all levels..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Filter Main -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Main Category</label>
                <select wire:model.live="filterMain" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Main</option>
                    @foreach($mains as $code => $desc)
                        <option value="{{ $code }}">{{ $desc }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Sub1 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sub Category 1</label>
                <select wire:model.live="filterSub1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Sub1</option>
                    @foreach($sub1s as $code => $desc)
                        <option value="{{ $code }}">{{ $desc }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Per Page -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
                <select wire:model.live="perPage" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-4 text-white">
            <p class="text-sm font-medium opacity-90">Total Records</p>
            <p class="text-3xl font-bold mt-1">{{ $hierarchy->total() }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-4 text-white">
            <p class="text-sm font-medium opacity-90">Current Page</p>
            <p class="text-3xl font-bold mt-1">{{ $hierarchy->count() }} of {{ $perPage }}</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Level 1<br>Main</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Level 2<br>Sub1</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Level 3<br>Sub2</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Level 4<br>COA Detail</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Note & Arus Kas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">H1-H6 Hierarchy</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($hierarchy as $row)
                        <tr class="hover:bg-gray-50 transition">
                            <!-- Level 1 - Main -->
                            <td class="px-4 py-3">
                                <div class="text-xs">
                                    <div class="font-mono font-bold text-blue-800">{{ $row->coa_main_code }}</div>
                                    <div class="text-gray-700 mt-1">{{ $row->coa_main_desc }}</div>
                                </div>
                            </td>

                            <!-- Level 2 - Sub1 -->
                            <td class="px-4 py-3">
                                @if($row->coasub1_code)
                                    <div class="text-xs">
                                        <div class="font-mono font-semibold text-green-700">{{ $row->coasub1_code }}</div>
                                        <div class="text-gray-600 mt-1">{{ $row->coasub1_desc }}</div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Level 3 - Sub2 -->
                            <td class="px-4 py-3">
                                @if($row->coasub2_code)
                                    <div class="text-xs">
                                        <div class="font-mono font-semibold text-purple-700">{{ $row->coasub2_code }}</div>
                                        <div class="text-gray-600 mt-1">{{ $row->coasub2_desc }}</div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Level 4 - Detail -->
                            <td class="px-4 py-3">
                                @if($row->coa_code)
                                    <div class="text-xs">
                                        <div class="font-mono font-bold text-gray-900">{{ $row->coa_code }}</div>
                                        <div class="font-medium text-gray-700 mt-1">{{ $row->coa_desc }}</div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Note & Arus Kas -->
                            <td class="px-4 py-3">
                                <div class="text-xs space-y-1">
                                    @if($row->coa_note)
                                        <div class="text-gray-600">
                                            <i class="fas fa-sticky-note mr-1"></i>
                                            {{ $row->coa_note }}
                                        </div>
                                    @endif
                                    @if($row->arus_kas_code)
                                        <div class="text-blue-600">
                                            <i class="fas fa-money-bill-wave mr-1"></i>
                                            {{ $row->arus_kas_code }}
                                        </div>
                                    @endif
                                    @if(!$row->coa_note && !$row->arus_kas_code)
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>

                            <!-- H1-H6 Hierarchy -->
                            <td class="px-4 py-3">
                                <div class="text-xs space-y-1">
                                    @if($row->desc_h1)
                                        <div class="flex items-center gap-1">
                                            <span class="px-1.5 py-0.5 bg-blue-100 text-blue-800 rounded text-xs font-medium">H1</span>
                                            <span class="text-gray-600">{{ $row->desc_h1 }}</span>
                                        </div>
                                    @endif
                                    @if($row->desc_h2)
                                        <div class="flex items-center gap-1">
                                            <span class="px-1.5 py-0.5 bg-green-100 text-green-800 rounded text-xs font-medium">H2</span>
                                            <span class="text-gray-600">{{ $row->desc_h2 }}</span>
                                        </div>
                                    @endif
                                    @if($row->desc_h3)
                                        <div class="flex items-center gap-1">
                                            <span class="px-1.5 py-0.5 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">H3</span>
                                            <span class="text-gray-600">{{ $row->desc_h3 }}</span>
                                        </div>
                                    @endif
                                    @if($row->desc_h4)
                                        <div class="flex items-center gap-1">
                                            <span class="px-1.5 py-0.5 bg-purple-100 text-purple-800 rounded text-xs font-medium">H4</span>
                                            <span class="text-gray-600">{{ $row->desc_h4 }}</span>
                                        </div>
                                    @endif
                                    @if($row->desc_h5)
                                        <div class="flex items-center gap-1">
                                            <span class="px-1.5 py-0.5 bg-pink-100 text-pink-800 rounded text-xs font-medium">H5</span>
                                            <span class="text-gray-600">{{ $row->desc_h5 }}</span>
                                        </div>
                                    @endif
                                    @if($row->desc_h6)
                                        <div class="flex items-center gap-1">
                                            <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-800 rounded text-xs font-medium">H6</span>
                                            <span class="text-gray-600">{{ $row->desc_h6 }}</span>
                                        </div>
                                    @endif
                                    @if(!$row->desc_h1 && !$row->desc_h2 && !$row->desc_h3 && !$row->desc_h4 && !$row->desc_h5 && !$row->desc_h6)
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="text-lg">No data found</p>
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
            {{ $hierarchy->links() }}
        </div>
    </div>

    <!-- Info Alert -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Full Hierarchy Report - 4 Level Legacy System</p>
                <p class="mt-1"><strong>Query:</strong> LEFT JOIN dari ms_acc_coa_main → ms_acc_coasub1 → ms_acc_coasub2 → ms_acc_coa</p>
                <p class="mt-1"><strong>Level 1:</strong> Main Category (Asset, Liability, Equity, dll)</p>
                <p class="mt-1"><strong>Level 2:</strong> Sub Category 1 (Current Asset, Fixed Asset, dll)</p>
                <p class="mt-1"><strong>Level 3:</strong> Sub Category 2 (Cash & Bank, Inventory, dll)</p>
                <p class="mt-1"><strong>Level 4:</strong> Detail COA (Actual accounts untuk transaksi)</p>
                <p class="mt-2"><strong>H1-H6:</strong> Modern flexible hierarchy (jika diisi)</p>
            </div>
        </div>
    </div>
</div>
