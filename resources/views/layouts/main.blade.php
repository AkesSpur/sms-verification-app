<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SMS Verification - Secure & Fast')</title>
    
      <link rel="icon" type="image/png" href="{{asset($logoSetting->favicon)}}">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom Styles -->
    <style>
        .hero-bg {
            background-color: #ffffff;
            position: relative;
            overflow: hidden;
        }
        .hero-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.03) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(255, 206, 84, 0.03) 0%, transparent 50%),
                        radial-gradient(circle at 40% 40%, rgba(120, 119, 198, 0.02) 0%, transparent 50%);
            pointer-events: none;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .hover-scale {
            transition: transform 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.05);
        }
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .navbar-scrolled {
            position: fixed !important;
            background-color: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .counter {
            font-variant-numeric: tabular-nums;
        }
        .hero-illustration {
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect width="400" height="300" fill="%23f8fafc"/><rect x="50" y="50" width="120" height="200" rx="20" fill="%23e2e8f0"/><rect x="60" y="60" width="100" height="180" rx="15" fill="%23ffffff"/><circle cx="120" cy="80" r="8" fill="%2364748b"/><rect x="80" y="100" width="80" height="4" rx="2" fill="%2364748b"/><rect x="80" y="110" width="60" height="4" rx="2" fill="%2394a3b8"/><rect x="80" y="130" width="80" height="30" rx="4" fill="%2306b6d4"/><rect x="80" y="170" width="80" height="4" rx="2" fill="%2364748b"/><rect x="80" y="180" width="50" height="4" rx="2" fill="%2394a3b8"/><path d="M200 100 L350 100 L350 200 L200 200 Z" fill="none" stroke="%2306b6d4" stroke-width="2" stroke-dasharray="5,5"/><circle cx="275" cy="150" r="30" fill="%2306b6d4" opacity="0.2"/><path d="M260 150 L270 160 L290 140" stroke="%2306b6d4" stroke-width="3" fill="none"/></svg>') no-repeat center center;
            background-size: contain;
        }
        #navbar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .premium-badge {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #f0f4ff 0%, #e2e8ff 100%);
            color: #4f46e5;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.875rem;
            box-shadow: 0 2px 4px rgba(79, 70, 229, 0.1);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(79, 70, 229, 0.2);
        }
        .premium-badge i {
            margin-right: 0.5rem;
            color: #4f46e5;
        }
        .trust-indicator {
            display: flex;
            align-items: center;
            background-color: #f8fafc;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
        .trust-indicator i {
            margin-right: 0.5rem;
            color: #0f172a;
        }
        .live-indicator {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            color: #64748b;
        }
        .live-indicator::before {
            content: "";
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #22c55e;
            border-radius: 50%;
            margin-right: 0.5rem;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
        @yield('styles')
</head>
<body class="bg-white">
    <!-- Navigation -->
    <nav id="navbar" class="absolute w-full z-50 transition-all duration-300 bg-transparent" data-aos="fade-down">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <h1 class="text-2xl font-bold text-gray-900 navbar-logo transition-colors duration-200">
                                <i class="fas fa-mobile-alt mr-2"></i>BlizzSMS
                            </h1>
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <!-- User Balance Display -->
                        {{-- <div class="flex items-center px-3 py-2 bg-blue-50 rounded-lg"> --}}
                        <a href="{{ route('user.transaction') }}" class="flex items-center px-3 py-2 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors cursor-pointer group">
                            <i class="fas fa-wallet text-blue-600 mr-2 group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium text-blue-800">₦{{ number_format(auth()->user()->balance ?? 0, 0) }}</span>
                            <i class="fas fa-external-link-alt text-blue-500 ml-2 text-xs opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </a>
                            {{-- <i class="fas fa-wallet text-blue-600 mr-2"></i>
                            <span class="text-sm font-medium text-blue-800">₦{{ number_format(auth()->user()->balance, 0) }}</span>
                        </div> --}}
                        
                        <!-- User Dropdown -->
                        <div class="relative ml-1" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors navbar-link">
                                <i class="fas fa-user-circle mr-2 text-lg"></i>
                                <span>{{ auth()->user()->name }}</span>
                                <i class="fas fa-caret-down ml-1" :class="{ 'rotate-180': open }"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                                <!-- Dashboard Link -->
                                <a href="{{ route('user.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-tachometer-alt w-4 h-4 mr-3 text-gray-400"></i>
                                    Dashboard
                                </a>
                                
                                @if(auth()->user()->is_admin)
                                    <!-- Admin Panel Link -->
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-cog w-4 h-4 mr-3 text-gray-400"></i>
                                        Admin Panel
                                    </a>
                                @endif
                                
                                <!-- Divider -->
                                <div class="border-t border-gray-100 my-1"></div>
                                
                                <!-- Logout Link -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt w-4 h-4 mr-3"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                    @else
                        <!-- Login and Register Links for guests -->
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors navbar-link">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>


    <!-- Footer -->
<footer class="bg-white py-12 border-t border-gray-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <!-- Logo and Company Name -->
            <div class="mb-6 md:mb-0">
                <h3 class="text-2xl font-bold text-slate-900">
                    <i class="fas fa-mobile-alt mr-2 text-blue-600"></i>
                    BlizzSMS
                </h3>
            </div>
            
            <!-- Contact Info -->
            <div class="flex items-center space-x-8 mb-6 md:mb-0">
                <a href="https://wa.me/+2347011780974" target="_blank" class="flex items-center text-slate-600 hover:text-green-600 transition-colors">
                    <i class="fab fa-whatsapp mr-2"></i>
                    WhatsApp
                </a>
                <a href="https://t.me/blizzsms" target="_blank" class="flex items-center text-slate-600 hover:text-blue-500 transition-colors">
                    <i class="fab fa-telegram mr-2"></i>
                    Telegram
                </a>
            </div>
            
            <!-- Copyright -->
            <div class="text-slate-500 text-sm mt-auto">
                &copy; {{ date('Y') }} BlizzSMS. All rights reserved.
            </div>
        </div>
    </div>
</footer>
    <!-- Scripts -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>