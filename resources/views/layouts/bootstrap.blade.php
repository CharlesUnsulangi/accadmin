<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AccAdmin') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    @livewireStyles

    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin: 0.25rem 0;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            font-weight: 600;
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 0.5rem;
        }
        .sidebar .nav-link .fa-chevron-down {
            transition: transform 0.3s;
        }
        .sidebar .nav-link[aria-expanded="true"] .fa-chevron-down {
            transform: rotate(180deg);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        .dropdown-menu {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar text-white p-3" style="width: 250px;">
            <div class="mb-4">
                <h4 class="navbar-brand text-white mb-0">
                    <i class="fas fa-chart-line me-2"></i>AccAdmin
                </h4>
                <small class="text-white-50">Accounting System</small>
            </div>

            <nav class="nav flex-column">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>

                <!-- COA Menu -->
                <div class="mt-3">
                    <small class="text-white-50 px-3 text-uppercase">Chart of Accounts</small>
                    
                    <a href="{{ route('coa.modern') }}" class="nav-link {{ request()->routeIs('coa.modern') ? 'active' : '' }}">
                        <i class="fas fa-layer-group"></i>
                        <span>COA Modern</span>
                    </a>
                    
                    <a href="{{ route('coa.main') }}" class="nav-link {{ request()->routeIs('coa.main') ? 'active' : '' }}">
                        <i class="fas fa-folder"></i>
                        <span>COA Main</span>
                    </a>
                    
                    <a href="{{ route('coa.legacy') }}" class="nav-link {{ request()->routeIs('coa.legacy') ? 'active' : '' }}">
                        <i class="fas fa-sitemap"></i>
                        <span>COA Legacy</span>
                    </a>
                    
                    <a href="{{ route('coa.hierarchy') }}" class="nav-link {{ request()->routeIs('coa.hierarchy') ? 'active' : '' }}">
                        <i class="fas fa-project-diagram"></i>
                        <span>Full Hierarchy</span>
                    </a>
                </div>

                <!-- Transaksi Menu -->
                <div class="mt-3">
                    <small class="text-white-50 px-3 text-uppercase">Transaksi</small>
                    
                    <a href="{{ route('jurnal.transaksi') }}" class="nav-link {{ request()->routeIs('jurnal.transaksi') ? 'active' : '' }}">
                        <i class="fas fa-book"></i>
                        <span>Jurnal Transaksi</span>
                    </a>
                    
                    <a href="{{ route('cheque.management') }}" class="nav-link {{ request()->routeIs('cheque.management') ? 'active' : '' }}">
                        <i class="fas fa-money-check-alt"></i>
                        <span>Buku Cheque</span>
                    </a>
                    
                    <a href="{{ route('transaksi.cheque') }}" class="nav-link {{ request()->routeIs('transaksi.cheque') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Transaksi Cheque</span>
                    </a>
                </div>

                <!-- Closing Menu -->
                <div class="mt-3">
                    <small class="text-white-50 px-3 text-uppercase">Periode Closing</small>
                    
                    <a href="{{ route('closing.process') }}" class="nav-link {{ request()->routeIs('closing.*') ? 'active' : '' }}">
                        <i class="fas fa-lock"></i>
                        <span>Closing Process</span>
                    </a>
                </div>

                <!-- Master Menu -->
                <div class="mt-3">
                    <small class="text-white-50 px-3 text-uppercase">Master</small>
                    
                    <!-- Master Data Dropdown -->
                    <div class="nav-link" data-bs-toggle="collapse" data-bs-target="#masterSubmenu" 
                         style="cursor: pointer;" 
                         aria-expanded="{{ request()->routeIs('master.*') ? 'true' : 'false' }}">
                        <i class="fas fa-database"></i>
                        <span>Master Data</span>
                        <i class="fas fa-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
                    </div>
                    <div class="collapse {{ request()->routeIs('master.*') ? 'show' : '' }}" id="masterSubmenu">
                        <a href="{{ route('master.bank') }}" class="nav-link ps-5 {{ request()->routeIs('master.bank') ? 'active' : '' }}">
                            <i class="fas fa-university"></i>
                            <span>Bank</span>
                        </a>
                        <a href="{{ route('master.area') }}" class="nav-link ps-5 {{ request()->routeIs('master.area') ? 'active' : '' }}">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Area</span>
                        </a>
                        <a href="{{ route('master.vendor') }}" class="nav-link ps-5 {{ request()->routeIs('master.vendor') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>Vendor/Supplier</span>
                        </a>
                        <a href="{{ route('master.transaksi') }}" class="nav-link ps-5 {{ request()->routeIs('master.transaksi') ? 'active' : '' }}">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Jenis Transaksi</span>
                        </a>
                        <a href="{{ route('master.statuscheque') }}" class="nav-link ps-5 {{ request()->routeIs('master.statuscheque') ? 'active' : '' }}">
                            <i class="fas fa-check-square"></i>
                            <span>Status Cheque</span>
                        </a>
                        <a href="#" class="nav-link ps-5">
                            <i class="fas fa-building"></i>
                            <span>Company</span>
                        </a>
                        <a href="#" class="nav-link ps-5">
                            <i class="fas fa-user-tie"></i>
                            <span>Customer</span>
                        </a>
                        <a href="#" class="nav-link ps-5">
                            <i class="fas fa-cube"></i>
                            <span>Item/Product</span>
                        </a>
                        <a href="#" class="nav-link ps-5">
                            <i class="fas fa-tags"></i>
                            <span>Category</span>
                        </a>
                        <a href="#" class="nav-link ps-5">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Currency</span>
                        </a>
                        <a href="#" class="nav-link ps-5">
                            <i class="fas fa-percentage"></i>
                            <span>Tax</span>
                        </a>
                    </div>
                </div>

                <!-- Admin Menu -->
                <div class="mt-3">
                    <small class="text-white-50 px-3 text-uppercase">Admin</small>
                    
                    <!-- Admin IT Dropdown -->
                    <div class="nav-link" data-bs-toggle="collapse" data-bs-target="#adminItSubmenu" 
                         style="cursor: pointer;" 
                         class="{{ request()->routeIs('it.documentation') || request()->routeIs('admin.sp') ? 'active' : '' }}">
                        <i class="fas fa-tools"></i>
                        <span>Admin IT</span>
                        <i class="fas fa-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
                    </div>
                    <div class="collapse {{ request()->routeIs('it.documentation') || request()->routeIs('admin.sp') ? 'show' : '' }}" 
                         id="adminItSubmenu">
                        <a href="{{ route('it.documentation') }}" 
                           class="nav-link ps-5 {{ request()->routeIs('it.documentation') ? 'active' : '' }}">
                            <i class="fas fa-book-open"></i>
                            <span>IT Documentation</span>
                        </a>
                        <a href="{{ route('admin.sp') }}" 
                           class="nav-link ps-5 {{ request()->routeIs('admin.sp') ? 'active' : '' }}">
                            <i class="fas fa-database"></i>
                            <span>Stored Procedures</span>
                        </a>
                    </div>
                </div>

                <!-- Dokumentasi Menu -->
                <div class="mt-3">
                    <small class="text-white-50 px-3 text-uppercase">Dokumentasi</small>
                    
                    <!-- Dokumentasi Dropdown -->
                    <div class="nav-link" data-bs-toggle="collapse" data-bs-target="#docsSubmenu" 
                         style="cursor: pointer;" 
                         aria-expanded="{{ request()->routeIs('it.documentation') || request()->routeIs('docs.tables') ? 'true' : 'false' }}">
                        <i class="fas fa-book"></i>
                        <span>Dokumentasi</span>
                        <i class="fas fa-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
                    </div>
                    <div class="collapse {{ request()->routeIs('it.documentation') || request()->routeIs('docs.tables') ? 'show' : '' }}" 
                         id="docsSubmenu">
                        <a href="{{ route('it.documentation') }}" 
                           class="nav-link ps-5 {{ request()->routeIs('it.documentation') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            <span>IT Documentation</span>
                        </a>
                        <a href="{{ route('docs.tables') }}" 
                           class="nav-link ps-5 {{ request()->routeIs('docs.tables') ? 'active' : '' }}">
                            <i class="fas fa-table"></i>
                            <span>Tables</span>
                        </a>
                    </div>
                </div>

                <!-- Pure JS Versions -->
                <div class="mt-3">
                    <small class="text-white-50 px-3 text-uppercase">Pure JS Versions</small>
                    
                    <a href="{{ route('coa.bootstrap') }}" class="nav-link">
                        <i class="fab fa-bootstrap"></i>
                        <span>Bootstrap</span>
                    </a>
                    
                    <a href="{{ route('coa.alpine') }}" class="nav-link">
                        <i class="fas fa-mountain"></i>
                        <span>Alpine.js</span>
                    </a>
                    
                    <a href="{{ route('coa.jquery') }}" class="nav-link">
                        <i class="fab fa-js-square"></i>
                        <span>jQuery</span>
                    </a>
                    
                    <a href="{{ route('coa.js.index') }}" class="nav-link">
                        <i class="fas fa-code"></i>
                        <span>Vanilla JS</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-fill">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" 
                                   data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-2 fs-5"></i>
                                    <span>{{ Auth::user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="fas fa-user me-2"></i>Profile
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="p-4">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @livewireScripts
</body>
</html>
