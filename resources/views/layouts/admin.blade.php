<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AccAdmin') }} - @yield('title', 'Accounting System')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100">
    <div x-data="{ sidebarOpen: true }" class="flex h-screen">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" 
               class="bg-gray-800 text-white transition-all duration-300 ease-in-out flex-shrink-0">
            <div class="flex items-center justify-between p-4 border-b border-gray-700">
                <h1 x-show="sidebarOpen" class="text-xl font-bold">AccAdmin</h1>
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="text-gray-400 hover:text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              :d="sidebarOpen ? 'M11 19l-7-7 7-7m8 14l-7-7 7-7' : 'M13 5l7 7-7 7M5 5l7 7-7 7'"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="mt-4">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3">Dashboard</span>
                </a>

                <!-- COA Management -->
                <div x-data="{ open: {{ request()->routeIs('coa.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-3 hover:bg-gray-700">
                        <div class="flex items-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span x-show="sidebarOpen" class="ml-3">Chart of Accounts</span>
                        </div>
                        <svg x-show="sidebarOpen" 
                             :class="open ? 'rotate-180' : ''" 
                             class="w-4 h-4 transition-transform" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="open" x-transition class="bg-gray-900">
                        <a href="{{ route('coa.modern') }}" 
                           class="flex items-center px-4 py-2 pl-12 hover:bg-gray-700 {{ request()->routeIs('coa.modern') ? 'bg-gray-800 border-l-4 border-blue-400' : '' }}">
                            <i class="fas fa-rocket w-4 h-4"></i>
                            <span x-show="sidebarOpen" class="ml-2">COA Modern (H1-H6)</span>
                        </a>
                        <a href="{{ route('coa.legacy') }}" 
                           class="flex items-center px-4 py-2 pl-12 hover:bg-gray-700 {{ request()->routeIs('coa.legacy') ? 'bg-gray-800 border-l-4 border-yellow-400' : '' }}">
                            <i class="fas fa-archive w-4 h-4"></i>
                            <span x-show="sidebarOpen" class="ml-2">COA Legacy (3 Level)</span>
                        </a>
                        <a href="{{ route('coa.index') }}" 
                           class="flex items-center px-4 py-2 pl-12 hover:bg-gray-700 {{ request()->routeIs('coa.index') ? 'bg-gray-800 border-l-4 border-gray-400' : '' }}">
                            <i class="fas fa-list w-4 h-4"></i>
                            <span x-show="sidebarOpen" class="ml-2">COA List (Old)</span>
                        </a>
                    </div>
                </div>

                <!-- Journal Entry -->
                <a href="#" class="flex items-center px-4 py-3 hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3">Journal Entry</span>
                </a>

                <!-- Reports -->
                <div x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-3 hover:bg-gray-700">
                        <div class="flex items-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span x-show="sidebarOpen" class="ml-3">Reports</span>
                        </div>
                        <svg x-show="sidebarOpen" 
                             :class="open ? 'rotate-180' : ''" 
                             class="w-4 h-4 transition-transform" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="open" x-transition class="bg-gray-900">
                        <a href="#" class="flex items-center px-4 py-2 pl-12 hover:bg-gray-700">
                            <span x-show="sidebarOpen">Trial Balance</span>
                        </a>
                        <a href="#" class="flex items-center px-4 py-2 pl-12 hover:bg-gray-700">
                            <span x-show="sidebarOpen">Balance Sheet</span>
                        </a>
                        <a href="#" class="flex items-center px-4 py-2 pl-12 hover:bg-gray-700">
                            <span x-show="sidebarOpen">Income Statement</span>
                        </a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">
                            @yield('page-title', 'Dashboard')
                        </h2>
                        <p class="text-sm text-gray-600">
                            @yield('page-description', 'Welcome to AccAdmin')
                        </p>
                    </div>

                    <!-- User Menu -->
                    <div x-data="{ userMenuOpen: false }" class="relative">
                        <button @click="userMenuOpen = !userMenuOpen" 
                                class="flex items-center space-x-3 focus:outline-none">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-700">{{ Auth::user()->name ?? 'Guest' }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email ?? '' }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr(Auth::user()->name ?? 'G', 0, 1)) }}
                            </div>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="userMenuOpen" 
                             @click.away="userMenuOpen = false"
                             x-transition
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="{{ route('profile.edit') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                <!-- Flash Messages -->
                @if (session('success'))
                <div x-data="{ show: true }" 
                     x-show="show"
                     x-transition
                     x-init="setTimeout(() => show = false, 5000)"
                     class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </button>
                </div>
                @endif

                @if (session('error'))
                <div x-data="{ show: true }" 
                     x-show="show"
                     x-transition
                     x-init="setTimeout(() => show = false, 5000)"
                     class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </button>
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
