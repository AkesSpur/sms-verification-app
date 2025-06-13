<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SMS Verification') - Dashboard</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f8fafc',
                            500: '#334155',
                            600: '#1e293b',
                            700: '#0f172a'
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">
    <!-- Mobile sidebar overlay -->
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="sidebarOpen = false"></div>
    </div>

    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 bg-white shadow-lg transform transition-all duration-300 ease-in-out lg:relative lg:translate-x-0" 
             :class="{
                'translate-x-0': sidebarOpen,
                '-translate-x-full lg:translate-x-0': !sidebarOpen,
                'w-64': !sidebarCollapsed,
                'w-16': sidebarCollapsed
             }">
            
            <!-- Logo -->
            <div class="flex items-center h-16 px-4 bg-primary-600" :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
                <h1 class="text-xl font-bold text-white" x-show="!sidebarCollapsed">SMS Verify</h1>
                <div class="flex items-center justify-center" x-show="sidebarCollapsed">
                    <i class="fas fa-sms text-white text-xl"></i>
                </div>
                <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:block text-white hover:text-gray-200">
                    <i class="fas" :class="sidebarCollapsed ? 'fa-chevron-right' : 'fa-chevron-left'"></i>
                </button>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-8" :class="sidebarCollapsed ? 'px-2' : 'px-4'">
                <div class="space-y-2">
                    <a href="{{ route('user.dashboard') }}" 
                       class="flex items-center py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.dashboard') ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-600' : '' }}"
                       :class="sidebarCollapsed ? 'px-2 justify-center' : 'px-4'">
                        <i class="fas fa-chart-line w-5 h-5" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
                        <span class="font-medium" x-show="!sidebarCollapsed">Dashboard</span>
                    </a>
                    
                    <a href="{{ route('user.usa-numbers') }}" 
                       class="flex items-center py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.usa-numbers') ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-600' : '' }}"
                       :class="sidebarCollapsed ? 'px-2 justify-center' : 'px-4'">
                        <i class="fas fa-flag-usa w-5 h-5" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
                        <span class="font-medium" x-show="!sidebarCollapsed">USA Numbers</span>
                    </a>
                    
                    <a href="{{ route('user.all-countries') }}" 
                       class="flex items-center py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.all-countries') ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-600' : '' }}"
                       :class="sidebarCollapsed ? 'px-2 justify-center' : 'px-4'">
                        <i class="fas fa-globe w-5 h-5" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
                        <span class="font-medium" x-show="!sidebarCollapsed">All Countries</span>
                    </a>
                    
                    <a href="{{ route('user.transaction') }}" 
                       class="flex items-center py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.transaction') ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-600' : '' }}"
                       :class="sidebarCollapsed ? 'px-2 justify-center' : 'px-4'">
                        <i class="fas fa-receipt w-5 h-5" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
                        <span class="font-medium" x-show="!sidebarCollapsed">Transactions</span>
                    </a>
                </div>
            </nav>
            
            <!-- User info and logout -->
            <div class="absolute bottom-0 left-0 right-0 border-t border-gray-200" :class="sidebarCollapsed ? 'p-2' : 'p-4'">
                <div class="flex items-center mb-4" :class="sidebarCollapsed ? 'justify-center' : ''">
                    <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div class="ml-3" x-show="!sidebarCollapsed">
                        <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-500">${{ number_format(auth()->user()->balance ?? 0, 2) }}</p>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('logout') }}" x-show="!sidebarCollapsed">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                        <i class="fas fa-sign-out-alt w-4 h-4 mr-3"></i>
                        <span>Logout</span>
                    </button>
                </form>
                
                <button x-show="sidebarCollapsed" class="w-full flex justify-center p-2 text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                        onclick="document.querySelector('form[action=\'{{ route('logout') }}\']').submit()">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col w-full h-full overflow-hidden">
            <!-- Top bar -->
            <header class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                            <i class="fas fa-bars w-6 h-6"></i>
                        </button>
                        <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:block p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 mr-2">
                            <i class="fas" :class="sidebarCollapsed ? 'fa-indent' : 'fa-outdent'"></i>
                        </button>
                        <h1 class="ml-4 lg:ml-0 text-2xl font-semibold text-gray-900">@yield('title', 'Dashboard')</h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Balance display -->
                        <div class="hidden sm:flex items-center px-3 py-2 bg-green-50 rounded-lg">
                            <i class="fas fa-wallet text-green-600 mr-2"></i>
                            <span class="text-sm font-medium text-green-800">${{ number_format(auth()->user()->balance ?? 0, 2) }}</span>
                        </div>
                        
                        <!-- Notifications -->
                        <button class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-bell w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Toast notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            const icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-times-circle' : 'fa-info-circle';
            
            toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 transform transition-all duration-300 translate-x-full`;
            toast.innerHTML = `
                <i class="fas ${icon}"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            document.getElementById('toast-container').appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('Copied to clipboard!', 'success');
            }).catch(() => {
                showToast('Failed to copy', 'error');
            });
        }

        // Auto-close sidebar on mobile when clicking links
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarLinks = document.querySelectorAll('nav a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 1024) {
                        Alpine.store('sidebarOpen', false);
                    }
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>