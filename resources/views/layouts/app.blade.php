<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Material Control System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-blue-900 text-white flex-shrink-0 flex flex-col h-screen">
            <div class="p-4 border-b border-blue-800">
                <h1 class="text-xl font-bold">
                    <i class="fas fa-warehouse mr-2"></i>Material Control
                </h1>
                <p class="text-xs text-blue-300 mt-1">Stamping Manufacturing</p>
            </div>
            
            <nav class="flex-1 overflow-y-auto p-4 scrollbar-hide">
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center p-2 rounded hover:bg-blue-800 {{ request()->routeIs('dashboard') ? 'bg-blue-800' : '' }}">
                            <i class="fas fa-chart-line w-6"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('all-stock') }}" class="flex items-center p-2 rounded hover:bg-blue-800 {{ request()->routeIs('all-stock') ? 'bg-blue-800' : '' }}">
                            <i class="fas fa-boxes w-6"></i>
                            <span>All Data Stock</span>
                        </a>
                    </li>
                    
                    @if(auth()->user()->hasAnyPermission(['view-materials', 'view-suppliers', 'view-customers', 'view-warehouses']))
                    <li class="mt-4">
                        <button onclick="toggleDropdown('masterData')" class="w-full flex items-center justify-between p-2 rounded hover:bg-blue-800 text-left">
                            <span class="text-xs text-blue-300 uppercase font-semibold">Master Data</span>
                            <i id="masterData-icon" class="fas fa-chevron-down text-xs transition-transform"></i>
                        </button>
                        <ul id="masterData-menu" class="mt-2 space-y-1 pl-2">
                            @if(auth()->user()->hasPermission('view-materials'))
                            <li>
                                <a href="{{ route('materials.index') }}" class="flex items-center p-2 rounded hover:bg-blue-800 {{ request()->routeIs('materials.*') ? 'bg-blue-800' : '' }}">
                                    <i class="fas fa-box w-6"></i>
                                    <span>Material</span>
                                </a>
                            </li>
                            @endif
                            
                            @if(auth()->user()->hasPermission('view-suppliers'))
                            <li>
                                <a href="{{ route('suppliers.index') }}" class="flex items-center p-2 rounded hover:bg-blue-800 {{ request()->routeIs('suppliers.*') ? 'bg-blue-800' : '' }}">
                                    <i class="fas fa-truck w-6"></i>
                                    <span>Supplier</span>
                                </a>
                            </li>
                            @endif
                            
                            @if(auth()->user()->hasPermission('view-customers'))
                            <li>
                                <a href="{{ route('customers.index') }}" class="flex items-center p-2 rounded hover:bg-blue-800 {{ request()->routeIs('customers.*') ? 'bg-blue-800' : '' }}">
                                    <i class="fas fa-users w-6"></i>
                                    <span>Customer</span>
                                </a>
                            </li>
                            @endif
                            
                            @if(auth()->user()->hasPermission('view-warehouses'))
                            <li>
                                <a href="{{ route('warehouses.index') }}" class="flex items-center p-2 rounded hover:bg-blue-800 {{ request()->routeIs('warehouses.*') ? 'bg-blue-800' : '' }}">
                                    <i class="fas fa-warehouse w-6"></i>
                                    <span>Warehouse</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if(auth()->user()->hasAnyPermission(['view-transactions', 'view-production-orders', 'view-opname']))
                    <li class="mt-4">
                        <button onclick="toggleDropdown('transaksi')" class="w-full flex items-center justify-between p-2 rounded hover:bg-blue-800 text-left">
                            <span class="text-xs text-blue-300 uppercase font-semibold">Transaksi</span>
                            <i id="transaksi-icon" class="fas fa-chevron-down text-xs transition-transform"></i>
                        </button>
                        <ul id="transaksi-menu" class="mt-2 space-y-1 pl-2">
                            @if(auth()->user()->hasPermission('view-transactions'))
                            <li>
                                <a href="{{ route('transactions.index') }}" class="flex items-center p-2 rounded hover:bg-blue-800 {{ request()->routeIs('transactions.*') ? 'bg-blue-800' : '' }}">
                                    <i class="fas fa-exchange-alt w-6"></i>
                                    <span>Stock Transaction</span>
                                </a>
                            </li>
                            @endif
                            
                            @if(auth()->user()->hasPermission('view-production-orders'))
                            <li>
                                <a href="{{ route('production-orders.index') }}" class="flex items-center p-2 rounded hover:bg-blue-800 {{ request()->routeIs('production-orders.*') ? 'bg-blue-800' : '' }}">
                                    <i class="fas fa-industry w-6"></i>
                                    <span>Production Order</span>
                                </a>
                            </li>
                            @endif
                            
                            @if(auth()->user()->hasPermission('view-opname'))
                            <li>
                                <a href="{{ route('opname.index') }}" class="flex items-center p-2 rounded hover:bg-blue-800 {{ request()->routeIs('opname.*') ? 'bg-blue-800' : '' }}">
                                    <i class="fas fa-clipboard-check w-6"></i>
                                    <span>Stock Opname</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if(auth()->user()->hasAnyPermission(['view-users', 'view-roles']))
                    <li class="mt-4">
                        <button onclick="toggleDropdown('sistem')" class="w-full flex items-center justify-between p-2 rounded hover:bg-blue-800 text-left">
                            <span class="text-xs text-blue-300 uppercase font-semibold">Sistem</span>
                            <i id="sistem-icon" class="fas fa-chevron-down text-xs transition-transform"></i>
                        </button>
                        <ul id="sistem-menu" class="mt-2 space-y-1 pl-2">
                            @if(auth()->user()->hasPermission('view-users'))
                            <li>
                                <a href="{{ route('users.index') }}" class="flex items-center p-2 rounded hover:bg-blue-800 {{ request()->routeIs('users.*') ? 'bg-blue-800' : '' }}">
                                    <i class="fas fa-user-cog w-6"></i>
                                    <span>User Management</span>
                                </a>
                            </li>
                            @endif
                            
                            @if(auth()->user()->hasPermission('view-roles'))
                            <li>
                                <a href="{{ route('roles.index') }}" class="flex items-center p-2 rounded hover:bg-blue-800 {{ request()->routeIs('roles.*') ? 'bg-blue-800' : '' }}">
                                    <i class="fas fa-user-shield w-6"></i>
                                    <span>Roles & Permissions</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                </ul>
            </nav>
            
            <!-- Logout Button -->
            <div class="p-4 border-t border-blue-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center p-2 rounded hover:bg-red-600 bg-red-700 text-white transition">
                        <i class="fas fa-sign-out-alt w-6"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <style>
            /* Hide scrollbar for Chrome, Safari and Opera */
            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }

            /* Hide scrollbar for IE, Edge and Firefox */
            .scrollbar-hide {
                -ms-overflow-style: none;  /* IE and Edge */
                scrollbar-width: none;  /* Firefox */
            }
        </style>

        <script>
            function toggleDropdown(menuId) {
                const menu = document.getElementById(menuId + '-menu');
                const icon = document.getElementById(menuId + '-icon');
                
                if (menu.style.display === 'none' || menu.style.display === '') {
                    menu.style.display = 'block';
                    icon.classList.remove('fa-chevron-right');
                    icon.classList.add('fa-chevron-down');
                } else {
                    menu.style.display = 'none';
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-right');
                }
            }

            // Initialize dropdowns as closed, then open only the active section
            document.addEventListener('DOMContentLoaded', function() {
                const menuIds = ['masterData', 'transaksi', 'sistem'];
                
                // First, close all menus
                menuIds.forEach(function(menuId) {
                    const menu = document.getElementById(menuId + '-menu');
                    const icon = document.getElementById(menuId + '-icon');
                    if (menu && icon) {
                        menu.style.display = 'none';
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-right');
                    }
                });

                // Then, open only the menu that contains the active page
                menuIds.forEach(function(menuId) {
                    const menu = document.getElementById(menuId + '-menu');
                    const icon = document.getElementById(menuId + '-icon');
                    
                    if (menu && icon) {
                        // Check if any link in this menu is active
                        const activeLink = menu.querySelector('a.bg-blue-800');
                        if (activeLink) {
                            menu.style.display = 'block';
                            icon.classList.remove('fa-chevron-right');
                            icon.classList.add('fa-chevron-down');
                        }
                    }
                });
            });
        </script>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm px-6 py-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">
                            <i class="fas fa-user-circle mr-2"></i>{{ auth()->user()->name }}
                            @if(auth()->user()->isSupplier() && auth()->user()->supplier)
                                <span class="text-xs text-gray-500">({{ auth()->user()->supplier->supplier_name }})</span>
                            @endif
                        </span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
