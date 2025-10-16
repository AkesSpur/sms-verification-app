<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Blizzlogspot') - Dashboard</title>
    
      <link rel="icon" type="image/png" href="{{asset($logoSetting->favicon)}}">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    
    <!-- Custom Styles -->
<style>
    /* Use consistent primary colors that match the sidebar theme */
    .primary-600 { color: #1e293b; }
    .primary-700 { background-color: #0f172a; }
    .primary-500 { border-color: #334155; }
    .primary-50 { background-color: #f8fafc; }
    .bg-primary-600 { background-color: #1e293b; }
    .bg-primary-700 { background-color: #0f172a; }
    .focus\:ring-primary-500:focus { --tw-ring-color: #334155; }
    .border-primary-500 { border-color: #334155; }
    .text-primary-600 { color: #1e293b; }
    .text-primary-700 { color: #0f172a; }
    .hover\:text-primary-700:hover { color: #0f172a; }
    
    /* Custom scrollbar styles */
    nav::-webkit-scrollbar {
        width: 6px;
    }
    
    nav::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }
    
    nav::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
        transition: background 0.2s ease;
    }
    
    nav::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    nav::-webkit-scrollbar-thumb:active {
        background: #64748b;
    }
</style>
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
        <div class="fixed inset-y-0 left-0 z-50 bg-white shadow-lg transform transition-all duration-300 ease-in-out lg:relative lg:translate-x-0 flex flex-col" 
             :class="{
                'translate-x-0': sidebarOpen,
                '-translate-x-full lg:translate-x-0': !sidebarOpen,
                'w-64': !sidebarCollapsed,
                'w-16': sidebarCollapsed
             }">
            
            <!-- Logo -->
            <a href="/" class="flex items-center h-16 px-4 bg-primary-600" :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
                <h1 class="text-xl font-bold text-white" x-show="!sidebarCollapsed">Blizzlogspot</h1>
                <div class="flex items-center justify-center" x-show="sidebarCollapsed">
                    <i class="fas fa-sms text-white text-xl"></i>
                </div>
                <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:block text-white hover:text-gray-200">
                    <i class="fas" :class="sidebarCollapsed ? 'fa-chevron-right' : 'fa-chevron-left'"></i>
                </button>
            </a>
            
            <!-- Navigation -->
            <nav class="mt-8 overflow-y-auto overflow-x-hidden flex-1" :class="sidebarCollapsed ? 'px-2' : 'px-4'">
                <div class="space-y-2">
                    <a href="{{ route('user.dashboard') }}" 
                       class="flex items-center py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.dashboard') ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-600' : '' }}"
                       :class="sidebarCollapsed ? 'px-2 justify-center' : 'px-4'">
                        <i class="fas fa-chart-line w-5 h-5" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
                        <span class="font-medium" x-show="!sidebarCollapsed">Dashboard</span>
                    </a>
                    
                    <a href="{{ route('user.sms.rental.index') }}" 
                       class="flex items-center py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.sms.rental.*') ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-600' : '' }}"
                       :class="sidebarCollapsed ? 'px-2 justify-center' : 'px-4'">
                        <i class="fas fa-flag-usa w-5 h-5" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
                        <span class="font-medium" x-show="!sidebarCollapsed">USA Numbers 1</span>
                    </a>

                    <a href="{{ route('user.usa-numbers') }}" 
                       class="flex items-center py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.usa-numbers') ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-600' : '' }}"
                       :class="sidebarCollapsed ? 'px-2 justify-center' : 'px-4'">
                        <i class="fas fa-flag-usa w-5 h-5" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
                        <span class="font-medium" x-show="!sidebarCollapsed">USA Numbers 2</span>
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
                    
                    <a href="{{ route('user.order-history') }}" 
                       class="flex items-center py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.order-history') ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-600' : '' }}"
                       :class="sidebarCollapsed ? 'px-2 justify-center' : 'px-4'">
                        <i class="fas fa-history w-5 h-5" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
                        <span class="font-medium" x-show="!sidebarCollapsed">Order History</span>
                    </a>
                    
                    <a href="{{ route('all-gifts') }}" 
                       class="flex items-center py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('all-gifts') ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-600' : '' }}"
                       :class="sidebarCollapsed ? 'px-2 justify-center' : 'px-4'">
                        <i class="fas fa-gift w-5 h-5" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
                        <span class="font-medium" x-show="!sidebarCollapsed">Gift Store</span>
                    </a>
                    
                    <a href="{{ route('all-categories') }}" 
                       class="flex items-center py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('all-categories') ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-600' : '' }}"
                       :class="sidebarCollapsed ? 'px-2 justify-center' : 'px-4'">
                        <i class="fas fa-store w-5 h-5" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
                        <span class="font-medium" x-show="!sidebarCollapsed">Logs Store</span>
                    </a>
                    
                    <a href="{{ route('user.social-media-boosting.index') }}" 
                       class="flex items-center py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('user.social-media-boosting.*') || request()->routeIs('user.social-media-orders.*') ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-600' : '' }}"
                       :class="sidebarCollapsed ? 'px-2 justify-center' : 'px-4'">
                        <i class="fas fa-rocket w-5 h-5" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
                        <span class="font-medium" x-show="!sidebarCollapsed">Boosting Of Accounts</span>
                    </a>
                    
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors {{ request()->routeIs('profile.edit') ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-600' : '' }}"
                       :class="sidebarCollapsed ? 'px-2 justify-center' : 'px-4'">
                        <i class="fas fa-user-cog w-5 h-5" :class="sidebarCollapsed ? '' : 'mr-3'"></i>
                        <span class="font-medium" x-show="!sidebarCollapsed">Profile Settings</span>
                    </a>
                </div>
            </nav>
            
            <!-- User info and logout -->
            <div class="border-t border-gray-200 flex-shrink-0" :class="sidebarCollapsed ? 'p-2' : 'p-4'">
                <div class="flex items-center mb-4" :class="sidebarCollapsed ? 'justify-center' : ''">
                    <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div class="ml-3" x-show="!sidebarCollapsed">
                        <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-500">₦{{ number_format(auth()->user()->balance ?? 0, 0) }}</p>
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
                        <a href="{{ route('user.transaction') }}" class="flex items-center px-3 py-2 bg-green-50 rounded-lg hover:bg-green-100 transition-colors cursor-pointer group">
                            <i class="fas fa-wallet text-green-600 mr-2 group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium text-green-800">₦{{ number_format(auth()->user()->balance ?? 0, 0) }}</span>
                            <i class="fas fa-external-link-alt text-green-500 ml-2 text-xs opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </a>
                    </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Contact Support Icons -->
    <div class="fixed bottom-4 left-4 z-50 flex flex-col space-y-3">
        @if($settings->whatsapp_support_link)
        <!-- WhatsApp -->
        <a href="{{ $settings->whatsapp_support_link }}" target="_blank" 
           class="bg-green-500 hover:bg-green-600 text-white p-3 rounded-full shadow-lg transition-all duration-300 hover:scale-110 group  md:hidden">
            <i class="fab fa-whatsapp text-xl"></i>
            <span class="absolute left-full ml-3 top-1/2 transform -translate-y-1/2 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">WhatsApp Support</span>
        </a>
        @endif
        
        @if($settings->telegram_support_link)
        <!-- Telegram -->
        <a href="{{ $settings->telegram_support_link }}" target="_blank" 
           class="bg-blue-500 hover:bg-blue-600 text-white p-3 rounded-full shadow-lg transition-all duration-300 hover:scale-110 group">
            <i class="fab fa-telegram-plane text-xl"></i>
            <span class="absolute left-full ml-3 top-1/2 transform -translate-y-1/2 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Telegram Support</span>
        </a>
        @endif
    </div>

    <!-- Toast notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            const bgColor = type == 'success' ? 'bg-green-500' : type == 'error' ? 'bg-red-500' : 'bg-blue-500';
            const icon = type == 'success' ? 'fa-check-circle' : type == 'error' ? 'fa-times-circle' : 'fa-info-circle';
            
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

        // Helper function to strip HTML tags
        function stripHtmlTags(html) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            return tempDiv.textContent || tempDiv.innerText || '';
        }
        
        // Copy to clipboard function
        function copyToClipboard(text) {
            // Strip HTML tags from the text before copying
            const cleanText = stripHtmlTags(text);
            
            navigator.clipboard.writeText(cleanText).then(() => {
                showToast('Copied to clipboard!', 'success');
            }).catch(() => {
                showToast('Failed to copy', 'error');
            });
        }

         // Notify function for displaying messages
        function notify(type, message) {
            showToast(message, type);
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

    {{-- <script src="//code.jivosite.com/widget/ozClxHcUVj" async></script> --}}
    <!--StartofTawk.toScript-->
    <script type="text/javascript"> var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date(); (function(){ var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0]; s1.async=true; s1.src='https://embed.tawk.to/68ea1533ca0084195466fde7/default'; s1.charset='UTF-8'; s1.setAttribute('crossorigin','*'); s0.parentNode.insertBefore(s1,s0); })(); </script> <!--End of Tawk.to Script-->

</body>
</html>