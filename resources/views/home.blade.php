<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blizzlogspot - Secure & Fast</title>

    <link rel="icon" type="image/png" href="{{asset($logoSetting->favicon)}}">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
        .secondary-nav-bg{
             background-color: rgba(255, 255, 255, 0.8);
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
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        /* Celebration Animations */
        .celebration-sparkle {
            animation: sparkle 2s ease-in-out infinite;
        }
        @keyframes sparkle {
            0%, 100% { opacity: 0.3; transform: scale(1) rotate(0deg); }
            50% { opacity: 1; transform: scale(1.2) rotate(180deg); }
        }
        
        .gift-bounce {
            animation: giftBounce 3s ease-in-out infinite;
        }
        @keyframes giftBounce {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            25% { transform: translateY(-10px) rotate(2deg); }
            75% { transform: translateY(-5px) rotate(-2deg); }
        }
        
        .confetti-fall {
            animation: confettiFall 4s linear infinite;
        }
        @keyframes confettiFall {
            0% { transform: translateY(-100px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
        }
        
        .pulse-glow {
            animation: pulseGlow 2s ease-in-out infinite;
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 5px rgba(251, 191, 36, 0.3); }
            50% { box-shadow: 0 0 20px rgba(251, 191, 36, 0.8), 0 0 30px rgba(251, 191, 36, 0.4); }
        }
        .navbar-scrolled {
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
            position: relative;
        }
        .live-indicator::before {
            content: "";
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #22c55e;
            border-radius: 50%;
            margin-right: 0.5rem;
            position: relative;
            z-index: 2;
        }
        .live-indicator::after {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            background-color: #22c55e;
            border-radius: 50%;
            opacity: 0.6;
            animation: bubble 2s infinite;
        }
        @keyframes bubble {
            0% {
                transform: translateY(-50%) scale(1);
                opacity: 0.6;
            }
            50% {
                transform: translateY(-50%) scale(1.8);
                opacity: 0.3;
            }
            100% {
                transform: translateY(-50%) scale(2.5);
                opacity: 0;
            }
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .testimonial-card {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #f1f5f9;
        }
        
        .testimonial-card.no-border {
            border: none;
        }
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .gradient-text {
            background: linear-gradient(135deg, #1e293b 0%, #4f46e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
        }
        .service-card {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #f1f5f9;
            padding: 1.5rem;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .feature-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin-bottom: 1rem;
            color: #4f46e5;
            border: 1px solid #e2e8f0;
        }
        
        /* Infinite Sliding Services Animation */
        .services-slider {
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .services-track {
            display: flex;
            width: calc(200%); /* Double width for seamless loop */
            animation: slide 20s linear infinite;
        }
        
        .service-item {
            min-width: 140px;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: flex-start;
            margin: 0 12px;
            flex-shrink: 0;
            gap: 8px;
        }
        
        .service-item .service-icon {
            width: 40px;
            height: 40px;
            margin: 0;
            padding: 8px;
            border-radius: 12px;
            background: #f8f9fa;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .service-item:hover .service-icon {
            transform: scale(1.1);
        }
        
        .service-item h3 {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin: 0;
            text-align: left;
            white-space: nowrap;
        }
        
        .service-item svg {
            width: 24px;
            height: 24px;
            filter: grayscale(1);
            transition: filter 0.3s ease;
        }
        
        .service-item:hover svg {
            filter: grayscale(0);
        }
        
        @keyframes slide {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }
        
        .services-track:hover {
            animation-play-state: paused;
        }
        
        /* Recent Orders Animation */
        .orders-slider {
            width: 100%;
            position: relative;
            overflow: hidden;
            height: 80px;
        }
        
        .orders-track {
            display: flex;
            width: calc(200%); /* Double width for seamless loop */
            animation: slideUp 25s linear infinite;
            gap: 0.75rem;
        }
        
        .order-item {
            min-width: 200px;
            flex-shrink: 0;
        }
        
        .order-card {
            background: #ffffff;
            border-radius: 8px;
            padding: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            height: 70px;
        }
        
        .order-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        
        .order-content {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.25rem;
        }
        
        .order-type-badge {
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            font-size: 0.625rem;
            font-weight: 600;
            text-transform: uppercase;
            color: white;
        }
        
        .order-type-sms {
            background: #10b981;
        }
        
        .order-type-digital {
            background: #3b82f6;
        }
        
        .order-type-gift {
            background: #f59e0b;
        }
        
        .order-time {
            color: #9ca3af;
            font-size: 0.625rem;
        }
        
        .order-info {
            flex: 1;
        }
        
        .order-user {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.75rem;
            margin: 0 0 0.125rem 0;
            line-height: 1.2;
        }
        
        .order-product {
            color: #6b7280;
            font-size: 0.625rem;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .order-price {
            color: #059669;
            font-size: 0.625rem;
            font-weight: 600;
            margin-left: 0.5rem;
            flex-shrink: 0;
        }
        
        @keyframes slideUp {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }
        
        .orders-track:hover {
            animation-play-state: paused;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .order-item {
                min-width: 160px;
            }
            
            .order-card {
                padding: 0.5rem;
                height: 60px;
            }
            
            .order-type-badge {
                font-size: 0.5rem;
                padding: 0.1rem 0.25rem;
            }
            
            .order-user {
                font-size: 0.625rem;
            }
            
            .order-product {
                font-size: 0.5rem;
            }
            
            .order-price {
                font-size: 0.5rem;
                margin-left: 0.25rem;
            }
            
            .order-time {
                font-size: 0.5rem;
            }
        }
    </style>
</head>
<body class="bg-white">
    <!-- Navigation -->
    <nav id="navbar" class="fixed w-full z-50 transition-all duration-300 bg-white bg-opacity-90 backdrop-blur-sm" data-aos="fade-down">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-2xl font-bold text-gray-900 navbar-logo">
                            {{-- <i class="fas fa-mobile-alt mr-2"></i> --}}
                            {{$settings->site_name}}
                        </h1>
                    </div>
                </div>
                <div class="flex items-center space-x-1 md:space-x-4">
                    @auth
                        <!-- User Balance Display -->
                          <div class="flex items-center px-3 py-2 bg-blue-50 rounded-lg">
                              <i class="fas fa-wallet text-blue-600 mr-2"></i>
                              <span class="text-sm font-medium text-blue-800">₦{{ number_format(auth()->user()->balance, 0) }}</span>
                          </div>
                          
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

                                <!-- USA Numbers 1 -->
                                <a href="{{ route('user.sms.rental.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-flag-usa w-4 h-4 mr-3 text-gray-400"></i>
                                    USA Numbers 1
                                </a>
                                <!-- USA Numbers 2 -->
                                <a href="{{ route('user.usa-numbers') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-flag-usa w-4 h-4 mr-3 text-gray-400"></i>
                                    USA Numbers 2
                                </a>
                                <!-- All Countries -->
                                <a href="{{ route('user.all-countries') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-globe w-4 h-4 mr-3 text-gray-400"></i>
                                    All Countries
                                </a>
                                <!-- Reseller Store -->
                                <a href="{{ route('user.reseller') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-tags w-4 h-4 mr-3 text-gray-400"></i>
                                    Reseller Store
                                </a>
                                <!-- Order History -->
                                <a href="{{ route('user.order-history') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-history w-4 h-4 mr-3 text-gray-400"></i>
                                    Order History
                                </a>
                                <!-- Logs Store -->
                                <a href="{{ route('all-categories') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-store w-4 h-4 mr-3 text-gray-400"></i>
                                    Logs Store
                                </a>
                                <!-- Boosting Of Accounts -->
                                <a href="{{ route('user.social-media-boosting.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-rocket w-4 h-4 mr-3 text-gray-400"></i>
                                    Boosting Of Accounts
                                </a>
                                <!-- Gift Store -->
                                <a href="{{ route('all-gifts') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-gift w-4 h-4 mr-3 text-gray-400"></i>
                                    Gift Store
                                </a>
                                <!-- Transactions -->
                                <a href="{{ route('user.transaction') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-receipt w-4 h-4 mr-3 text-gray-400"></i>
                                    Transactions
                                </a>
                                <!-- Profile Settings -->
                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-user-cog w-4 h-4 mr-3 text-gray-400"></i>
                                    Profile Settings
                                </a>
                                
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
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-slate-800 to-gray-900 text-white hover:from-slate-900 hover:to-black px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Secondary Navigation -->    
    <section id="secondary-nav" class="w-full secondary-nav-bg border-b border-gray-100 transition-all duration-300" data-aos="fade-down" data-aos-delay="200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-center py-2">
                <div class="flex space-x-6">                    
                    <!-- Gifts Link -->
                    <a href="{{ route('all-gifts') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 text-sm font-medium transition-colors duration-300">
                        Gifts
                    </a>
                    
                    <!-- Categories Link -->
                    <a href="{{ route('all-categories') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 text-sm font-medium transition-colors duration-300">
                        All Logs
                    </a>
                    
                    <!-- Reseller Link -->
                    <a href="{{ route('user.reseller') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 text-sm font-medium transition-colors duration-300">
                        Reseller
                    </a>
                    
                    <!-- Services Link -->
                    <a href="{{ route('services') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 text-sm font-medium transition-colors duration-300">
                        Account Boosting
                    </a>

                </div>
            </div>
        </div>
    </section>

    <!-- Reseller CTA moved to bottom -->

    <!-- Hero Section -->
    <section class="relative flex items-start justify-center overflow-hidden pt-16 pb-16">
        <!-- Celebration Background Elements -->
        <div class="absolute inset-0 pointer-events-none">
            <!-- Floating Sparkles -->
            <div class="absolute top-20 left-10 w-4 h-4 text-yellow-400 celebration-sparkle" style="animation-delay: 0s;">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 0l3.09 6.26L22 9.27l-6.91 3.01L12 24l-3.09-6.26L2 14.73l6.91-3.01L12 0z"/>
                </svg>
            </div>
            <div class="absolute top-32 right-20 w-3 h-3 text-pink-400 celebration-sparkle" style="animation-delay: 1s;">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 0l3.09 6.26L22 9.27l-6.91 3.01L12 24l-3.09-6.26L2 14.73l6.91-3.01L12 0z"/>
                </svg>
            </div>
            <div class="absolute top-16 left-1/3 w-5 h-5 text-blue-400 celebration-sparkle" style="animation-delay: 2s;">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 0l3.09 6.26L22 9.27l-6.91 3.01L12 24l-3.09-6.26L2 14.73l6.91-3.01L12 0z"/>
                </svg>
            </div>
            
            <!-- Floating Gift Icons -->
            <div class="absolute top-40 left-16 w-6 h-6 text-red-500 gift-bounce" style="animation-delay: 0.5s;">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1z"/>
                </svg>
            </div>
            <div class="absolute top-60 right-32 w-5 h-5 text-green-500 gift-bounce" style="animation-delay: 1.5s;">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1z"/>
                </svg>
            </div>
            
            <!-- Confetti Particles -->
            <div class="absolute top-24 left-1/4 w-2 h-2 bg-yellow-400 rounded-full confetti-fall" style="animation-delay: 0.3s;"></div>
            <div class="absolute top-36 right-1/4 w-3 h-3 bg-pink-500 rounded-full confetti-fall" style="animation-delay: 1.2s;"></div>
            <div class="absolute top-48 left-1/2 w-2 h-2 bg-blue-500 rounded-full confetti-fall" style="animation-delay: 2.1s;"></div>
            <div class="absolute top-28 right-1/3 w-2 h-2 bg-purple-500 rounded-full confetti-fall" style="animation-delay: 0.8s;"></div>
            
            <!-- Heart Elements -->
            <div class="absolute top-44 left-20 w-4 h-4 text-red-400 pulse-glow" style="animation-delay: 1.8s;">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </div>
            <div class="absolute top-52 right-24 w-3 h-3 text-pink-400 pulse-glow" style="animation-delay: 2.5s;">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pb-6">
            <div class="grid lg:grid-cols-2 gap-6 items-center">
                <!-- Left Content -->
                <div class="text-center lg:text-left mt-8" data-aos="fade-right" data-aos-duration="1000">
                    <!-- Premium Badge -->
                    <div class="mb-6">
                        <span class="premium-badge">
                            <i class="fas fa-crown"></i>
                            Premium Digital Services
                        </span>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-medium text-gray-900 mb-6 leading-tight">
                        Complete Digital Solutions &
                        <span class="block gradient-text">Premium Services</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-600 mb-6 max-w-2xl mx-auto lg:mx-0">
                        Get instant SMS verification, boost your social media presence, secure VPN logins, and premium gifts delivered worldwide. 
                        <span class="text-slate-700 font-semibold">Complete digital marketplace</span> for all your needs.
                    </p>
                    
                    <!-- Trust Indicators -->
                    <div class="flex flex-wrap gap-4 justify-center lg:justify-start mb-8">
                        <div class="trust-indicator">
                            <div class="w-2 h-2 bg-green-500 mr-1 rounded-full live-indicator"></div>
                            <span>Live Support</span>
                        </div>
                        <div class="trust-indicator">
                            <i class="fas fa-clock"></i>
                            <span>Instant Delivery</span>
                        </div>
                        <div class="trust-indicator">
                            <i class="fas fa-chart-line"></i>
                            <span>Account Boosting</span>
                        </div>
                        <div class="trust-indicator">
                            <i class="fas fa-gift"></i>
                            <span>Premium Gifts</span>
                        </div>
                        <div class="trust-indicator">
                            <i class="fas fa-shield-alt"></i>
                            <span>VPN Access</span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-12">
                        <a href="{{ route('user.sms.rental.index') }}" class="bg-gradient-to-r from-slate-800 to-gray-900 hover:from-slate-900 hover:to-black text-white px-8 py-4 rounded-lg text-lg font-semibold transition-all hover-scale shadow-lg">
                            <i class="fas fa-rocket mr-2"></i>Start Verifying Now
                        </a>
                        <a href="#digital-products" class="bg-white hover:bg-gray-50 text-gray-800 border-2 border-gray-200 hover:border-gray-300 px-8 py-4 rounded-lg text-lg font-semibold transition-all hover-scale shadow-lg">
                            <i class="fas fa-store mr-2"></i>Browse Store
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6 max-w-md mx-auto lg:mx-0">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-slate-700 mb-1 counter" data-target="150">0</div>
                            <div class="text-sm text-gray-500">SMS Services</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-slate-700 mb-1 counter" data-target="25">0</div>
                            <div class="text-sm text-gray-500">VPN Providers</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-slate-700 mb-1 counter" data-target="500">0</div>
                            <div class="text-sm text-gray-500">Gifts Delivered</div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Illustration -->
                <div class="hidden lg:block" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="relative">
                        <!-- Primary Gift Box Illustration -->
                        <div class="absolute top-0 right-0 z-20">
                            <svg class="w-24 h-24" viewBox="0 0 100 100" fill="none">
                                <!-- Gift Box Base -->
                                <rect x="20" y="40" width="60" height="45" rx="4" fill="#dc2626" stroke="#b91c1c" stroke-width="1"/>
                                <!-- Gift Box Lid -->
                                <rect x="18" y="35" width="64" height="12" rx="4" fill="#ef4444" stroke="#dc2626" stroke-width="1"/>
                                <!-- Ribbon Vertical -->
                                <rect x="47" y="20" width="6" height="65" fill="#fbbf24"/>
                                <!-- Ribbon Horizontal -->
                                <rect x="15" y="47" width="70" height="6" fill="#fbbf24"/>
                                <!-- Bow -->
                                <ellipse cx="45" cy="25" rx="8" ry="6" fill="#f59e0b"/>
                                <ellipse cx="55" cy="25" rx="8" ry="6" fill="#f59e0b"/>
                                <circle cx="50" cy="25" r="3" fill="#d97706"/>
                                <!-- Enhanced Sparkles -->
                                <circle cx="30" cy="20" r="1.5" fill="#fbbf24" opacity="0.8">
                                    <animate attributeName="opacity" values="0.8;0.3;0.8" dur="2s" repeatCount="indefinite"/>
                                </circle>
                                <circle cx="75" cy="30" r="1" fill="#fbbf24" opacity="0.6">
                                    <animate attributeName="opacity" values="0.6;0.2;0.6" dur="1.5s" repeatCount="indefinite"/>
                                </circle>
                                <circle cx="85" cy="15" r="1.2" fill="#f59e0b" opacity="0.7">
                                    <animate attributeName="opacity" values="0.7;0.2;0.7" dur="1.8s" repeatCount="indefinite"/>
                                </circle>
                            </svg>
                        </div>
                        
                        <!-- Secondary Gift Box (Blue Theme) -->
                        <div class="absolute top-16 right-20 z-15">
                            <svg class="w-16 h-16" viewBox="0 0 80 80" fill="none">
                                <!-- Gift Box Base -->
                                <rect x="15" y="35" width="50" height="35" rx="3" fill="#1e40af" stroke="#1d4ed8" stroke-width="1"/>
                                <!-- Gift Box Lid -->
                                <rect x="13" y="30" width="54" height="10" rx="3" fill="#3b82f6" stroke="#1e40af" stroke-width="1"/>
                                <!-- Ribbon Vertical -->
                                <rect x="37" y="18" width="5" height="52" fill="#ec4899"/>
                                <!-- Ribbon Horizontal -->
                                <rect x="10" y="37" width="60" height="5" fill="#ec4899"/>
                                <!-- Bow -->
                                <ellipse cx="35" cy="22" rx="6" ry="4" fill="#db2777"/>
                                <ellipse cx="44" cy="22" rx="6" ry="4" fill="#db2777"/>
                                <circle cx="39.5" cy="22" r="2" fill="#be185d"/>
                                <!-- Floating Animation -->
                                <animateTransform attributeName="transform" type="translate" values="0,0; 0,-5; 0,0" dur="3s" repeatCount="indefinite"/>
                            </svg>
                        </div>
                        
                        <!-- Third Gift Box (Green Theme) -->
                        <div class="absolute top-32 right-8 z-10">
                            <svg class="w-12 h-12" viewBox="0 0 60 60" fill="none">
                                <!-- Gift Box Base -->
                                <rect x="10" y="25" width="40" height="28" rx="2" fill="#059669" stroke="#047857" stroke-width="1"/>
                                <!-- Gift Box Lid -->
                                <rect x="8" y="22" width="44" height="8" rx="2" fill="#10b981" stroke="#059669" stroke-width="1"/>
                                <!-- Ribbon Vertical -->
                                <rect x="27" y="12" width="4" height="41" fill="#f97316"/>
                                <!-- Ribbon Horizontal -->
                                <rect x="6" y="27" width="48" height="4" fill="#f97316"/>
                                <!-- Bow -->
                                <ellipse cx="26" cy="16" rx="5" ry="3" fill="#ea580c"/>
                                <ellipse cx="33" cy="16" rx="5" ry="3" fill="#ea580c"/>
                                <circle cx="29.5" cy="16" r="1.5" fill="#c2410c"/>
                                <!-- Floating Animation -->
                                <animateTransform attributeName="transform" type="translate" values="0,0; 0,-3; 0,0" dur="2.5s" repeatCount="indefinite"/>
                            </svg>
                        </div>
                        
                        <!-- Confetti Elements -->
                        <div class="absolute top-5 left-10 z-25">
                            <svg class="w-8 h-8" viewBox="0 0 40 40" fill="none">
                                <rect x="15" y="15" width="4" height="4" fill="#f59e0b" transform="rotate(45 17 17)">
                                    <animateTransform attributeName="transform" type="rotate" values="45 17 17; 405 17 17" dur="4s" repeatCount="indefinite"/>
                                </rect>
                                <circle cx="25" cy="10" r="2" fill="#ec4899">
                                    <animate attributeName="cy" values="10;35;10" dur="3s" repeatCount="indefinite"/>
                                </circle>
                                <polygon points="8,20 12,16 16,20 12,24" fill="#8b5cf6">
                                    <animateTransform attributeName="transform" type="rotate" values="0 12 20; 360 12 20" dur="5s" repeatCount="indefinite"/>
                                </polygon>
                            </svg>
                        </div>
                        
                        <!-- More Confetti -->
                        <div class="absolute top-12 left-32 z-25">
                            <svg class="w-6 h-6" viewBox="0 0 30 30" fill="none">
                                <rect x="12" y="12" width="3" height="3" fill="#10b981" transform="rotate(30 13.5 13.5)">
                                    <animateTransform attributeName="transform" type="rotate" values="30 13.5 13.5; 390 13.5 13.5" dur="3.5s" repeatCount="indefinite"/>
                                </rect>
                                <circle cx="8" cy="8" r="1.5" fill="#f59e0b">
                                    <animate attributeName="cy" values="8;25;8" dur="2.8s" repeatCount="indefinite"/>
                                </circle>
                            </svg>
                        </div>
                        
                        <!-- Floating Social Media Icons -->
                        <div class="absolute top-8 right-32 z-30">
                            <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fab fa-instagram text-white text-lg"></i>
                                <animateTransform attributeName="transform" type="translate" values="0,0; 0,-8; 0,0" dur="3s" repeatCount="indefinite"/>
                            </div>
                        </div>
                        
                        <div class="absolute top-24 right-16 z-30">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fab fa-facebook-f text-white text-sm"></i>
                                <animateTransform attributeName="transform" type="translate" values="0,0; 0,-6; 0,0" dur="2.5s" repeatCount="indefinite"/>
                            </div>
                        </div>
                        
                        <div class="absolute top-40 right-24 z-30">
                            <div class="w-9 h-9 bg-gradient-to-br from-red-500 to-red-700 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fab fa-youtube text-white text-sm"></i>
                                <animateTransform attributeName="transform" type="translate" values="0,0; 0,-7; 0,0" dur="3.5s" repeatCount="indefinite"/>
                            </div>
                        </div>
                        
                        <div class="absolute top-56 right-8 z-30">
                            <div class="w-7 h-7 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fab fa-twitter text-white text-xs"></i>
                                <animateTransform attributeName="transform" type="translate" values="0,0; 0,-5; 0,0" dur="2.8s" repeatCount="indefinite"/>
                            </div>
                        </div>
                        
                        <!-- VPN Shield Illustration -->
                        <div class="absolute bottom-0 left-0 z-20">
                            <svg class="w-20 h-20" viewBox="0 0 80 80" fill="none">
                                <!-- Shield Background -->
                                <path d="M40 10 L60 20 L60 40 C60 55 40 65 40 65 C40 65 20 55 20 40 L20 20 L40 10 Z" fill="#1e40af" stroke="#1d4ed8" stroke-width="1"/>
                                <!-- Shield Highlight -->
                                <path d="M40 15 L55 22 L55 38 C55 50 40 58 40 58 C40 58 25 50 25 38 L25 22 L40 15 Z" fill="#3b82f6"/>
                                <!-- Lock Icon -->
                                <rect x="35" y="35" width="10" height="8" rx="1" fill="#ffffff"/>
                                <path d="M37 35 L37 32 C37 30 38 29 40 29 C42 29 43 30 43 32 L43 35" stroke="#ffffff" stroke-width="1.5" fill="none"/>
                                <!-- VPN Text -->
                                <text x="40" y="52" text-anchor="middle" fill="#ffffff" font-size="6" font-weight="bold">VPN</text>
                                <!-- Pulse Effect -->
                                <circle cx="40" cy="40" r="25" fill="none" stroke="#3b82f6" stroke-width="1" opacity="0.3">
                                    <animate attributeName="r" values="25;35;25" dur="2s" repeatCount="indefinite"/>
                                    <animate attributeName="opacity" values="0.3;0;0.3" dur="2s" repeatCount="indefinite"/>
                                </circle>
                            </svg>
                        </div>
                        
                        <!-- SMS Verification Interface (Repositioned) -->
                        <div class="relative z-10 ml-8">
                            <svg class="w-full max-w-sm mx-auto" viewBox="0 0 280 200" fill="none">
                                <!-- Background Card -->
                                <g transform="rotate(-8 140 100)">
                                    <rect x="20" y="30" width="240" height="140" rx="16" fill="#ffffff" stroke="#e5e7eb" stroke-width="1" filter="url(#shadow)"/>
                                    
                                    <!-- Phone Number Input -->
                                    <rect x="40" y="50" width="200" height="35" rx="8" fill="#f8f9fa" stroke="#e5e7eb" stroke-width="1"/>
                                    <text x="50" y="65" fill="#6b7280" font-size="10">Phone Number</text>
                                    <text x="50" y="78" fill="#1f2937" font-size="12" font-weight="500">+1 (555) 123-4567</text>
                                    
                                    <!-- Verification Code Section -->
                                    <text x="140" y="105" text-anchor="middle" fill="#10b981" font-size="11" font-weight="600">VERIFICATION CODE</text>
                                    
                                    <!-- Code Digits -->
                                    <g transform="translate(60, 115)">
                                        <rect x="0" y="0" width="28" height="32" rx="6" fill="#ffffff" stroke="#10b981" stroke-width="2"/>
                                        <text x="14" y="21" text-anchor="middle" fill="#10b981" font-size="18" font-weight="bold">7</text>
                                        
                                        <rect x="35" y="0" width="28" height="32" rx="6" fill="#ffffff" stroke="#10b981" stroke-width="2"/>
                                        <text x="49" y="21" text-anchor="middle" fill="#10b981" font-size="18" font-weight="bold">2</text>
                                        
                                        <rect x="70" y="0" width="28" height="32" rx="6" fill="#ffffff" stroke="#10b981" stroke-width="2"/>
                                        <text x="84" y="21" text-anchor="middle" fill="#10b981" font-size="18" font-weight="bold">5</text>
                                        
                                        <rect x="105" y="0" width="28" height="32" rx="6" fill="#ffffff" stroke="#10b981" stroke-width="2"/>
                                        <text x="119" y="21" text-anchor="middle" fill="#10b981" font-size="18" font-weight="bold">5</text>
                                        
                                        <rect x="140" y="0" width="28" height="32" rx="6" fill="#ffffff" stroke="#10b981" stroke-width="2"/>
                                        <text x="154" y="21" text-anchor="middle" fill="#10b981" font-size="18" font-weight="bold">3</text>
                                    </g>
                                    
                                    <!-- Success checkmark -->
                                    <circle cx="220" cy="60" r="12" fill="#10b981">
                                        <animate attributeName="r" values="0;12;12" dur="1s" begin="1s" fill="freeze"/>
                                    </circle>
                                    <path d="M214 60 L218 64 L226 54" stroke="white" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" opacity="0">
                                        <animate attributeName="opacity" values="0;1" dur="0.5s" begin="1.5s" fill="freeze"/>
                                    </path>
                                </g>
                                
                                <!-- Floating elements -->
                                <circle cx="30" cy="50" r="4" fill="#334155" opacity="0.4">
                                    <animate attributeName="cy" values="50;30;50" dur="3s" repeatCount="indefinite"/>
                                </circle>
                                <circle cx="250" cy="80" r="6" fill="#10b981" opacity="0.4">
                                    <animate attributeName="cy" values="80;60;80" dur="2.5s" repeatCount="indefinite"/>
                                </circle>
                                <circle cx="20" cy="150" r="3" fill="#f59e0b" opacity="0.4">
                                    <animate attributeName="cy" values="150;130;150" dur="4s" repeatCount="indefinite"/>
                                </circle>
                                
                                <!-- Shadow filter -->
                                <defs>
                                    <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
                                        <feDropShadow dx="0" dy="4" stdDeviation="8" flood-opacity="0.1"/>
                                    </filter>
                                </defs>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Infinite Sliding Services Section -->
    <section class="py-4 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16">
            <div class="text-center mb-8" data-aos="fade-up">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Popular <span class="gradient-text">Services</span></h2>
            </div>
            
            <!-- Infinite Sliding Container -->
            <div class="relative"  data-aos="fade-up">
                <div class="services-slider overflow-hidden">
                    <div class="services-track flex animate-slide">
                        <!-- First set of services -->
                        <div class="service-item">
                            <div class="service-icon hover:bg-green-100">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.488" fill="#25D366"/>
                                </svg>
                            </div>
                            <h3>WhatsApp</h3>
                        </div>
                        
                        <div class="service-item">
                            <div class="service-icon hover:bg-blue-100">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="12" fill="#0088cc"/>
                                    <path d="M5.491 11.74l11.57-4.461c.537-.194.996.131.82.983l0 0-1.97 9.281c-.146.658-.537.818-1.084.508l-3-2.211-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.135-.953z" fill="white"/>
                                </svg>
                            </div>
                            <h3>Telegram</h3>
                        </div>
                        
                        <div class="service-item">
                            <div class="service-icon hover:bg-red-100">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                </svg>
                            </div>
                            <h3>Google</h3>
                        </div>
                        
                        <div class="service-item">
                            <div class="service-icon hover:bg-blue-100">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"/>
                                </svg>
                            </div>
                            <h3>Facebook</h3>
                        </div>
                        
                        <div class="service-item">
                            <div class="service-icon hover:bg-pink-100">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient)"/>
                                    <defs>
                                        <linearGradient id="instagram-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" style="stop-color:#833ab4"/>
                                            <stop offset="50%" style="stop-color:#fd1d1d"/>
                                            <stop offset="100%" style="stop-color:#fcb045"/>
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </div>
                            <h3>Instagram</h3>
                        </div>
                        
                        <div class="service-item">
                            <div class="service-icon hover:bg-blue-100">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" fill="#1DA1F2"/>
                                </svg>
                            </div>
                            <h3>Twitter</h3>
                        </div>
                        
                        <div class="service-item">
                            <div class="service-icon hover:bg-red-100">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" fill="#FF0000"/>
                                </svg>
                            </div>
                            <h3>YouTube</h3>
                        </div>
                        
                        <div class="service-item">
                            <div class="service-icon hover:bg-purple-100">
                                <svg viewBox="0 0 16 16" fill="none">
                                    <path d="M3.857 0 1 2.857v10.286h3.429V16l2.857-2.857H9.57L14.714 8V0H3.857zm9.714 7.429-2.285 2.285H9l-2 2v-2H4.429V1.143h9.142v6.286z" fill="#9146FF"/>
                                    <path d="M11.857 3.143h-1.143V6.57h1.143V3.143zm-3.143 0H7.571V6.57h1.143V3.143z" fill="#9146FF"/>
                                </svg>
                            </div>
                            <h3>Twitch</h3>
                        </div>
                        
                        <!-- Duplicate set for seamless loop -->
                        <div class="service-item">
                            <div class="service-icon hover:bg-green-100">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.488" fill="#25D366"/>
                                </svg>
                            </div>
                            <h3>WhatsApp</h3>
                        </div>
                        
                        <div class="service-item">
                            <div class="service-icon hover:bg-blue-100">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="12" fill="#0088cc"/>
                                    <path d="M5.491 11.74l11.57-4.461c.537-.194.996.131.82.983l0 0-1.97 9.281c-.146.658-.537.818-1.084.508l-3-2.211-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.135-.953z" fill="white"/>
                                </svg>
                            </div>
                            <h3>Telegram</h3>
                        </div>
                        
                        <div class="service-item">
                            <div class="service-icon hover:bg-red-100">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                </svg>
                            </div>
                            <h3>Google</h3>
                        </div>
                        
                        <div class="service-item">
                            <div class="service-icon hover:bg-blue-100">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"/>
                                </svg>
                            </div>
                            <h3>Facebook</h3>
                        </div>
                        
                        <div class="service-item">
                            <div class="service-icon hover:bg-pink-100">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="url(#instagram-gradient2)"/>
                                    <defs>
                                        <linearGradient id="instagram-gradient2" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" style="stop-color:#833ab4"/>
                                            <stop offset="50%" style="stop-color:#fd1d1d"/>
                                            <stop offset="100%" style="stop-color:#fcb045"/>
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </div>
                            <h3>Instagram</h3>
                        </div>
                        
                        <div class="service-item">
                            <div class="service-icon hover:bg-blue-100">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" fill="#1DA1F2"/>
                                </svg>
                            </div>
                            <h3>Twitter</h3>
                        </div>
                        
                        <div class="service-item">
                            <div class="service-icon hover:bg-red-100">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" fill="#FF0000"/>
                                </svg>
                            </div>
                            <h3>YouTube</h3>
                        </div>
                        
                        <div class="service-item">
                            <div class="service-icon hover:bg-purple-100">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M11.571 4.714h.857c1.143 0 2.286.571 2.286 2.286v9.714c0 1.714-1.143 2.286-2.286 2.286h-.857c-1.143 0-2.286-.572-2.286-2.286V7c0-1.715 1.143-2.286 2.286-2.286z" fill="#9146FF"/>
                                    <path d="M20.571 4.714h.857C22.571 4.714 24 5.285 24 7v9.714c0 1.714-1.429 2.286-2.572 2.286h-.857c-1.143 0-2.571-.572-2.571-2.286V7c0-1.715 1.428-2.286 2.571-2.286z" fill="#9146FF"/>
                                    <path d="M2.571 4.714h.858C4.571 4.714 6 5.285 6 7v9.714c0 1.714-1.429 2.286-2.571 2.286H2.57C1.429 19 0 18.428 0 16.714V7c0-1.715 1.429-2.286 2.571-2.286z" fill="#9146FF"/>
                                </svg>
                            </div>
                            <h3>Twitch</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Orders Section -->
    <section class="py-8 bg-gray-50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16">
            <div class="text-center mb-6" data-aos="fade-up">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Recent Orders</h3>
            </div>
            
            <!-- Recent Orders Container -->
            <div class="relative" data-aos="fade-up" data-aos-delay="200">
                <div class="orders-slider overflow-hidden">
                    <div class="orders-track flex animate-slideUp">
                        @if($recentOrders->count() > 0)
                            @foreach($recentOrders as $order)
                            <!-- Order Item -->
                            <div class="order-item">
                                <div class="order-card">
                                    <div class="order-content">
                                        <div class="order-header">
                                            <span class="order-type-badge order-type-{{ $order['type'] }}">
                                                @if($order['type'] === 'sms')
                                                    SMS
                                                @elseif($order['type'] === 'digital')
                                                    Logs
                                                @else
                                                    Gift
                                                @endif
                                            </span>
                                            <span class="order-time">{{ $order['created_at']->diffForHumans() }}</span>
                                        </div>
                                        <div class="order-info">
                                            <h4 class="order-user">{{ $order['user_name'] }}</h4>
                                            <p class="order-product">{{ $order['product'] }} <span class="order-price">{{$settings->currency_icon}}{{ number_format($order['price'], 2) }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            
                            <!-- Duplicate for seamless loop -->
                            @foreach($recentOrders as $order)
                            <div class="order-item">
                                <div class="order-card">
                                    <div class="order-content">
                                        <div class="order-header">
                                            <span class="order-type-badge order-type-{{ $order['type'] }}">
                                                @if($order['type'] === 'sms')
                                                    SMS
                                                @elseif($order['type'] === 'digital')
                                                    Digital
                                                @else
                                                    Gift
                                                @endif
                                            </span>
                                            <span class="order-time">{{ $order['created_at']->diffForHumans() }}</span>
                                        </div>
                                        <div class="order-info">
                                            <h4 class="order-user">{{ $order['user_name'] }}</h4>
                                            <p class="order-product">{{ $order['product'] }} <span class="order-price">${{ number_format($order['price'], 2) }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <!-- Placeholder orders when no data -->
                            <div class="order-item">
                                <div class="order-card">
                                    <div class="order-content">
                                        <div class="order-header">
                                            <span class="order-type-badge order-type-sms">SMS</span>
                                            <span class="order-time">2 minutes ago</span>
                                        </div>
                                        <div class="order-info">
                                            <h4 class="order-user">John D.</h4>
                                            <p class="order-product">WhatsApp Verification <span class="order-price">$2.50</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="order-item">
                                <div class="order-card">
                                    <div class="order-content">
                                        <div class="order-header">
                                            <span class="order-type-badge order-type-digital">Digital</span>
                                            <span class="order-time">5 minutes ago</span>
                                        </div>
                                        <div class="order-info">
                                            <h4 class="order-user">Sarah M.</h4>
                                            <p class="order-product">Netflix Premium <span class="order-price">$15.99</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="order-item">
                                <div class="order-card">
                                    <div class="order-content">
                                        <div class="order-header">
                                            <span class="order-type-badge order-type-gift">Gift</span>
                                            <span class="order-time">8 minutes ago</span>
                                        </div>
                                        <div class="order-info">
                                            <h4 class="order-user">Mike R.</h4>
                                            <p class="order-product">Premium Gift Box <span class="order-price">$25.00</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Image Banner Carousel Section -->
    @if($banners->count() > 0)
    <section class="py-16 bg-gradient-to-r from-slate-50 to-gray-100 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Carousel Container -->
            <div class="relative" data-aos="fade-up">
                <div class="carousel-container overflow-hidden rounded-2xl shadow-2xl">
                    <div class="carousel-wrapper flex transition-transform duration-500 ease-in-out" id="carousel">
                        @foreach($banners as $banner)
                        <!-- Banner Slide {{ $loop->iteration }} -->
                        <div class="carousel-slide w-full flex-shrink-0 relative">
                            <div class="relative h-[155px] sm:h-[200px] md:h-[250px] lg:h-[300px] xl:h-[300px] overflow-hidden">
                                @if($banner->link_url)
                                    <a href="{{ $banner->link_url }}" target="_blank" class="block w-full h-full">
                                        <img src="{{ $banner->image_url }}" 
                                             alt="{{ $banner->title ?? 'Banner' }}" 
                                             class="w-[100%] h-full object-cover hover:scale-105 transition-transform duration-300"
                                             >
                                    </a>
                                @else
                                    <img src="{{ $banner->image_url }}" 
                                         alt="{{ $banner->title ?? 'Banner' }}" 
                                         class="w-full h-full object-cover"
                                         loading="lazy">
                                @endif
                                
                                
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                @if($banners->count() > 1)
                <!-- Navigation Arrows -->
                <button class="carousel-btn carousel-prev absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 text-gray-800 p-3 rounded-full shadow-lg z-10">
                    <i class="fas fa-chevron-left text-xl"></i>
                </button>
                <button class="carousel-btn carousel-next absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 text-gray-800 p-3 rounded-full shadow-lg z-10">
                    <i class="fas fa-chevron-right text-xl"></i>
                </button>
                
                <!-- Dots Indicator -->
                <div class="flex justify-center mt-8 space-x-2">
                    @foreach($banners as $banner)
                        <button class="carousel-dot w-3 h-3 rounded-full bg-gray-400 hover:bg-gray-600 transition-colors" data-slide="{{ $loop->index }}"></button>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 relative">
        <div class="absolute inset-0 bg-gradient-to-b from-white via-gray-50 to-white opacity-80"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="premium-badge mb-6">
                    <i class="fas fa-magic"></i>
                    Simple Process
                </span>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">How It <span class="gradient-text">Works</span></h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Get your SMS verification code in just 3 simple steps
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center testimonial-card no-border p-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon mx-auto mb-6">
                        <i class="fas fa-user-plus text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">1. Choose Service</h3>
                    <p class="text-gray-600">
                        Select from our wide range of supported services and countries. 
                        Pick the perfect number for your verification needs.
                    </p>
                </div>
                
                <div class="text-center testimonial-card no-border p-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon mx-auto mb-6">
                        <i class="fas fa-mobile-alt text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">2. Get Number</h3>
                    <p class="text-gray-600">
                        Receive your temporary phone number instantly. 
                        Use it for verification on your chosen platform.
                    </p>
                </div>
                
                <div class="text-center testimonial-card no-border p-8" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon mx-auto mb-6">
                        <i class="fas fa-comment-dots text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">3. Receive SMS</h3>
                    <p class="text-gray-600">
                        Get your verification code delivered instantly. 
                        Complete your registration process seamlessly.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Digital Products Section -->
    <section id="digital-products" class="py-20 bg-gradient-to-br from-slate-50 to-gray-100 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-white via-gray-50 to-white opacity-60"></div>
        
        <!-- Celebration Background Elements -->
        <div class="absolute inset-0 pointer-events-none">
            <!-- Floating Login Icons -->
            <div class="absolute top-20 left-10 gift-bounce" style="animation-delay: 0s;">
                <svg class="w-8 h-8 text-purple-300" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1M12 7C13.4 7 14.8 8.6 14.8 10V11.5C15.4 11.5 16 12.4 16 13V16C16 16.6 15.6 17 15 17H9C8.4 17 8 16.6 8 16V13C8 12.4 8.4 11.5 9 11.5V10C9 8.6 10.6 7 12 7M12 8.2C11.2 8.2 10.2 9.2 10.2 10V11.5H13.8V10C13.8 9.2 12.8 8.2 12 8.2Z"/>
                </svg>
            </div>
            <div class="absolute top-32 right-16 gift-bounce" style="animation-delay: 1s;">
                <svg class="w-6 h-6 text-blue-300" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1M12 7C13.4 7 14.8 8.6 14.8 10V11.5C15.4 11.5 16 12.4 16 13V16C16 16.6 15.6 17 15 17H9C8.4 17 8 16.6 8 16V13C8 12.4 8.4 11.5 9 11.5V10C9 8.6 10.6 7 12 7M12 8.2C11.2 8.2 10.2 9.2 10.2 10V11.5H13.8V10C13.8 9.2 12.8 8.2 12 8.2Z"/>
                </svg>
            </div>
            <div class="absolute bottom-20 left-1/4 gift-bounce" style="animation-delay: 2s;">
                <svg class="w-7 h-7 text-pink-300" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1M12 7C13.4 7 14.8 8.6 14.8 10V11.5C15.4 11.5 16 12.4 16 13V16C16 16.6 15.6 17 15 17H9C8.4 17 8 16.6 8 16V13C8 12.4 8.4 11.5 9 11.5V10C9 8.6 10.6 7 12 7M12 8.2C11.2 8.2 10.2 9.2 10.2 10V11.5H13.8V10C13.8 9.2 12.8 8.2 12 8.2Z"/>
                </svg>
            </div>
            
            <!-- Sparkle Elements -->
            <div class="absolute top-16 right-1/3 celebration-sparkle">
                <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
            </div>
            <div class="absolute bottom-32 right-20 celebration-sparkle" style="animation-delay: 1.5s;">
                <div class="w-2 h-2 bg-pink-400 rounded-full"></div>
            </div>
            <div class="absolute top-1/2 left-16 celebration-sparkle" style="animation-delay: 0.8s;">
                <div class="w-4 h-4 bg-purple-400 rounded-full"></div>
            </div>
            
            <!-- Confetti Elements -->
            <div class="absolute top-10 left-1/3 confetti-fall" style="animation-delay: 0s;">
                <div class="w-2 h-2 bg-green-400 transform rotate-45"></div>
            </div>
            <div class="absolute top-5 right-1/4 confetti-fall" style="animation-delay: 2s;">
                <div class="w-1 h-4 bg-yellow-400"></div>
            </div>
            <div class="absolute top-8 left-2/3 confetti-fall" style="animation-delay: 1s;">
                <div class="w-3 h-1 bg-blue-400"></div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="premium-badge mb-6">
                    <i class="fas fa-shield-alt"></i>
                    Digital Logins
                </span>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Secure <span class="gradient-text">Login Access</span></h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Access premium login credentials for your favorite platforms and services
                </p>
            </div>

            <!-- Category Header with Action Buttons -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-12" data-aos="fade-up" data-aos-delay="100">
                <div class="mb-6 md:mb-0">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Popular Login Services</h3>
                    <p class="text-gray-600">Choose from our most requested secure login credentials</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                     <a href="{{ route('all-categories') }}" class="bg-white text-slate-700 border-2 border-slate-200 hover:border-slate-300 px-6 py-3 rounded-lg font-semibold transition-all hover-scale shadow-sm">
                         <i class="fas fa-th-large mr-2"></i>View All Categories
                     </a>
                 </div>
            </div>

            <!-- Subcategories Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6" data-aos="fade-up" data-aos-delay="200">
                @foreach($activeSubcategories->take(4) as $subcategory)
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale cursor-pointer group" onclick="openProductModal('{{ $subcategory->name }}', {{ $subcategory->id }})">
                        <div class="p-6 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-xl overflow-hidden group-hover:scale-110 transition-transform">
                                @if($subcategory->image)
                                    <img src="{{ asset($subcategory->image) }}" alt="{{ $subcategory->name }}" class="w-full h-full object-contain">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-slate-100 to-gray-200 flex items-center justify-center rounded-lg">
                                        <i class="fas fa-shield-alt text-3xl text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">{{ $subcategory->name }}</h4>
                            <div class="text-xs text-green-600 font-semibold">
                                <i class="fas fa-lock mr-1"></i>Secure Access
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Hidden Additional Categories -->
            <div id="additionalCategories" class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6 hidden" data-aos="fade-up">
                @foreach($activeSubcategories->skip(4) as $subcategory)
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale cursor-pointer group" onclick="openProductModal('{{ $subcategory->name }}', {{ $subcategory->id }})">
                        <div class="p-6 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-xl overflow-hidden group-hover:scale-110 transition-transform">
                                @if($subcategory->image)
                                    <img src="{{ asset($subcategory->image) }}" alt="{{ $subcategory->name }}" class="w-full h-full object-contain">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-slate-100 to-gray-200 flex items-center justify-center rounded-lg">
                                        <i class="fas fa-shield-alt text-3xl text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">{{ $subcategory->name }}</h4>
                            <div class="text-xs text-green-600 font-semibold">
                                <i class="fas fa-lock mr-1"></i>Secure Access
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- View More/Less Button -->
            <div class="text-center mt-8 {{$activeSubcategories->count() < 5 ? 'hidden' : '' }}" data-aos="fade-up">
                <button id="toggleButton" onclick="toggleCategories()" class="bg-gradient-to-r from-slate-800 to-gray-900 hover:from-slate-900 hover:to-black text-white px-6 py-3 rounded-lg font-semibold transition-all hover-scale shadow-lg">
                    <i class="fas fa-chevron-down mr-2"></i>View All Login Services
                </button>
            </div>


        </div>
    </section>

    <!-- Social Media Boosting Section-->
    <section id="social-media-boosting" class="py-20 bg-gradient-to-br from-purple-50 to-pink-50 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-white via-purple-50 to-white opacity-60"></div>
        
        <!-- Celebration Background Elements -->
        <div class="absolute inset-0 pointer-events-none">
            <!-- Floating Social Media Icons -->
            <div class="absolute top-20 left-10 gift-bounce" style="animation-delay: 0s;">
                <svg class="w-8 h-8 text-pink-300" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.347-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                </svg>
            </div>
            <div class="absolute top-32 right-16 gift-bounce" style="animation-delay: 1s;">
                <svg class="w-6 h-6 text-blue-300" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                </svg>
            </div>
            <div class="absolute bottom-20 left-1/4 gift-bounce" style="animation-delay: 2s;">
                <svg class="w-7 h-7 text-purple-300" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                </svg>
            </div>
            
            <!-- Sparkle Elements -->
            <div class="absolute top-16 right-1/3 celebration-sparkle">
                <div class="w-3 h-3 bg-pink-400 rounded-full"></div>
            </div>
            <div class="absolute bottom-32 right-20 celebration-sparkle" style="animation-delay: 1.5s;">
                <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
            </div>
            <div class="absolute top-1/2 left-16 celebration-sparkle" style="animation-delay: 0.8s;">
                <div class="w-4 h-4 bg-blue-400 rounded-full"></div>
            </div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="premium-badge mb-6">
                    <i class="fas fa-chart-line"></i>
                    Social Media Growth
                </span>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Boost Your <span class="gradient-text">Social Media</span></h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Grow your social media presence with our professional boosting services for all major platforms
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12" data-aos="fade-up" data-aos-delay="200">
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-pink-100 to-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fab fa-instagram text-2xl text-pink-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Instagram Growth</h3>
                    <p class="text-gray-600 text-sm">Increase followers, likes, views, and engagement on your Instagram posts and stories</p>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fab fa-facebook text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Facebook Boost</h3>
                    <p class="text-gray-600 text-sm">Boost your Facebook page likes, post engagement, and reach more potential customers</p>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-100 to-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fab fa-youtube text-2xl text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">YouTube Growth</h3>
                    <p class="text-gray-600 text-sm">Grow your YouTube channel with more subscribers, views, and engagement on your videos</p>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="text-center bg-white rounded-xl shadow-lg p-8" data-aos="fade-up" data-aos-delay="300">
                <div class="mb-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-3xl text-blue-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Boost Your Social Media Presence</h3>
                    <p class="text-gray-600 mb-6">Enhance your social media accounts with our premium boosting services - followers, likes, views, and engagement across all major platforms</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('services') }}" class="bg-gradient-to-r from-slate-800 to-gray-900 hover:from-slate-900 hover:to-black text-white px-8 py-4 rounded-lg font-semibold transition-all hover-scale shadow-lg">
                        <i class="fas fa-chart-line mr-2"></i>Start Boosting Now
                    </a>
                    
                    @auth
                    <div class="flex items-center text-green-600">
                        <i class="fas fa-wallet mr-2"></i>
                        <span class="text-sm font-medium">Balance: ₦{{ number_format(auth()->user()->balance, 0) }}</span>
                    </div>
                    @endauth
                </div>
                
                <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div class="text-sm">
                        <div class="font-semibold text-gray-900">Fast Delivery</div>
                        <div class="text-gray-500">Quick results</div>
                    </div>
                    <div class="text-sm">
                        <div class="font-semibold text-gray-900">Safe & Secure</div>
                        <div class="text-gray-500">Platform compliant</div>
                    </div>
                    <div class="text-sm">
                        <div class="font-semibold text-gray-900">24/7 Support</div>
                        <div class="text-gray-500">Always available</div>
                    </div>
                    <div class="text-sm">
                        <div class="font-semibold text-gray-900">Best Prices</div>
                        <div class="text-gray-500">Competitive rates</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gifts Section -->
    <section class="py-20 bg-gradient-to-b from-white to-gray-50 relative overflow-hidden">
        <!-- Celebration Background Elements -->
        <div class="absolute inset-0 pointer-events-none">
            <!-- Floating Gift Boxes -->
            <div class="absolute top-16 left-8 gift-bounce" style="animation-delay: 0.5s;">
                <svg class="w-10 h-10 text-red-300" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.9 1 3 1.9 3 3V7C3 8.1 3.9 9 5 9H8V22H16V9H21Z"/>
                </svg>
            </div>
            <div class="absolute top-40 right-12 gift-bounce" style="animation-delay: 1.2s;">
                <svg class="w-8 h-8 text-green-300" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.9 1 3 1.9 3 3V7C3 8.1 3.9 9 5 9H8V22H16V9H21Z"/>
                </svg>
            </div>
            <div class="absolute bottom-24 left-1/3 gift-bounce" style="animation-delay: 2.1s;">
                <svg class="w-9 h-9 text-blue-300" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.9 1 3 1.9 3 3V7C3 8.1 3.9 9 5 9H8V22H16V9H21Z"/>
                </svg>
            </div>
            
            <!-- Heart Elements -->
            <div class="absolute top-24 right-1/4 celebration-sparkle" style="animation-delay: 0.3s;">
                <svg class="w-6 h-6 text-pink-400" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </div>
            <div class="absolute bottom-40 right-16 celebration-sparkle" style="animation-delay: 1.8s;">
                <svg class="w-5 h-5 text-red-400" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </div>
            
            <!-- Sparkle Dots -->
            <div class="absolute top-32 left-1/4 celebration-sparkle" style="animation-delay: 0.7s;">
                <div class="w-4 h-4 bg-yellow-400 rounded-full"></div>
            </div>
            <div class="absolute bottom-16 left-20 celebration-sparkle" style="animation-delay: 2.5s;">
                <div class="w-3 h-3 bg-purple-400 rounded-full"></div>
            </div>
            <div class="absolute top-1/2 right-8 celebration-sparkle" style="animation-delay: 1.1s;">
                <div class="w-5 h-5 bg-pink-400 rounded-full"></div>
            </div>
            
            <!-- Confetti -->
            <div class="absolute top-12 left-1/2 confetti-fall" style="animation-delay: 0.2s;">
                <div class="w-2 h-2 bg-red-400 transform rotate-45"></div>
            </div>
            <div class="absolute top-8 right-1/3 confetti-fall" style="animation-delay: 1.4s;">
                <div class="w-1 h-5 bg-green-400"></div>
            </div>
            <div class="absolute top-6 left-1/5 confetti-fall" style="animation-delay: 2.8s;">
                <div class="w-3 h-1 bg-blue-400"></div>
            </div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Section Header -->
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="premium-badge mb-6">
                    <i class="fas fa-gift"></i>
                    Perfect Gifts
                </span>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Beautiful <span class="gradient-text">Gifts</span></h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Send meaningful gifts to your loved ones with our curated collection
                </p>
            </div>

            <!-- Gifts Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-12" data-aos="fade-up" data-aos-delay="200">
                @forelse($gifts as $gift)
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale cursor-pointer group {{ $loop->first ? 'pulse-glow' : '' }}" onclick="window.location.href='{{ route('gift.show', $gift->slug) }}'">
                        <div class="aspect-square overflow-hidden rounded-t-xl relative">
                            @if($gift->main_image)
                                <img src="{{ asset($gift->main_image) }}" alt="{{ $gift->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                                <img src="https://via.placeholder.com/400x400?text=No+Image" alt="{{ $gift->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @endif
                            
                            @if($gift->customizable)
                                <div class="absolute top-2 right-2 bg-purple-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                    <i class="fas fa-magic mr-1"></i>Customizable
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h4 class="font-bold text-gray-900 mb-2 text-sm">{{ $gift->name }}</h4>
                            <p class="text-lg font-bold text-slate-700">₦{{ number_format($gift->price, 0) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-12">
                        <p class="text-gray-500">No gifts available at the moment. Please check back later.</p>
                    </div>
                @endforelse
            </div>
            </div>

            <!-- View All Gifts Button -->
            <div class="text-center" data-aos="fade-up">
                <a href="{{ route('all-gifts') }}" class="bg-gradient-to-r from-slate-800 to-gray-900 hover:from-slate-900 hover:to-black text-white px-8 py-4 rounded-lg font-semibold transition-all hover-scale shadow-lg">
                    <i class="fas fa-gift mr-2"></i>View All Gifts
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 relative">
        <div class="absolute inset-0 bg-gradient-to-b from-white via-gray-50 to-white opacity-80"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="testimonial-card py-12 px-8" data-aos="fade-up">
                <span class="premium-badge mb-6">
                    <i class="fas fa-crown"></i>
                    Premium Access
                </span>
                <h2 class="text-4xl font-bold mb-6 text-gray-900">Ready to Get <span class="gradient-text">Started?</span></h2>
                <p class="text-xl mb-8 text-gray-600">
                    Join thousands of users who rely on our secure SMS verification service
                </p>
                <div class="flex justify-center">
                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-slate-800 to-gray-900 text-white px-8 py-4 rounded-lg font-semibold hover:from-slate-900 hover:to-black transition-colors shadow-lg hover-scale">
                        Create Free Account
                    </a>
                </div>
                
                <!-- Trust Badges -->
                <div class="flex flex-wrap justify-center gap-6 mt-12">
                    <div class="flex items-center">
                        <i class="fas fa-lock text-slate-700 mr-2"></i>
                        <span class="text-sm text-gray-600">Secure Payments</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        <span class="text-sm text-gray-600">Verified Service</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-slate-700 mr-2"></i>
                        <span class="text-sm text-gray-600">Data Protection</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gradient-to-br from-gray-25 to-slate-25 relative overflow-hidden" style="background: linear-gradient(135deg, #fafafa 0%, #f8fafc 100%);">
        <!-- Celebration Background Elements -->
        <div class="absolute inset-0 pointer-events-none">
            <!-- Floating Icons -->
            <div class="absolute top-20 left-16 celebration-sparkle" style="animation-delay: 0s;">
                <div class="w-4 h-4 bg-blue-400 rounded-full"></div>
            </div>
            <div class="absolute bottom-20 right-20 celebration-sparkle" style="animation-delay: 1.5s;">
                <div class="w-3 h-3 bg-purple-400 rounded-full"></div>
            </div>
            <div class="absolute top-1/2 left-8 celebration-sparkle" style="animation-delay: 0.8s;">
                <div class="w-5 h-5 bg-green-400 rounded-full"></div>
            </div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Choose <span class="gradient-text">Our Service</span></h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Experience the best features that make us the preferred choice for SMS verification</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover-scale ">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center">
                        <i class="fas fa-bolt text-white text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-2">Instant Delivery</h4>
                    <p class="text-gray-600">Get your logs and codes delivered to you instantly after purchase</p>
                </div>
                <div class="text-center bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover-scale " style="animation-delay: 0.5s;">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-2">100% Secure</h4>
                    <p class="text-gray-600">All transactions are encrypted and protected with industry-standard security</p>
                </div>
                <div class="text-center bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover-scale " style="animation-delay: 1s;">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl flex items-center justify-center">
                        <i class="fas fa-headset text-white text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-2">24/7 Support</h4>
                    <p class="text-gray-600">Our dedicated support team is available around the clock to assist you</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Website Builder Contact -->
    <div class="bg-gradient-to-r from-gray-100 to-gray-200 py-3 text-center text-sm text-gray-700 border-t border-gray-200">
        <div class="flex items-center justify-center space-x-2 scale-90 hover:scale-100 transition-transform duration-300">
            <i class="fas fa-mobile-alt text-blue-600 animate-pulse"></i>
            <p>
                Need a custom SMS platform? <a href="https://wa.link/18c124" class="text-blue-600 hover:text-blue-800 font-medium transition-colors relative group">
                    Contact the developer
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"></span>
                </a>
            </p>
            <i class="fas fa-code text-blue-600 animate-bounce"></i>
        </div>
    </div>

   
    <!-- Reseller CTA Section (Bottom) -->
    <section class="w-full bg-gradient-to-r from-blue-50 to-indigo-50 border-t border-blue-100" data-aos="fade-up" data-aos-offset="50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="text-slate-800 mb-4 md:mb-0">
                    <h2 class="text-lg md:text-xl font-semibold">Ready to sell in bulk?</h2>
                    <p class="text-sm text-slate-600">Access wholesale pricing via our Reseller Store.</p>
                </div>
                <a href="{{ route('user.reseller') }}" class="inline-flex items-center px-6 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition-colors">
                    <i class="fas fa-store mr-2"></i>
                    Become a Reseller
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white py-12 border-t border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <!-- Logo and Company Name -->
                <div class="mb-6 md:mb-0">
                    <h3 class="text-2xl font-bold text-slate-900">
                        <i class="fas fa-mobile-alt mr-2 text-blue-600"></i>
                        {{$settings->site_name}}
                    </h3>
                </div>
                
                <!-- Contact Info -->
                <div class="flex items-center space-x-8 mb-6 md:mb-0">
                @if($settings->whatsapp_support_link)
                <a href="{{ $settings->whatsapp_support_link }}" target="_blank" class="flex items-center text-slate-600 hover:text-green-600 transition-colors">
                    <i class="fab fa-whatsapp mr-2"></i>
                    WhatsApp
                </a>
                @endif
                @if($settings->telegram_support_link)
                <a href="{{ $settings->telegram_support_link }}" target="_blank" class="flex items-center text-slate-600 hover:text-blue-500 transition-colors">
                    <i class="fab fa-telegram mr-2"></i>
                    Telegram
                    </a>
                @endif
                </div>
                
                <!-- Copyright -->
                <div class="text-slate-500 text-sm">
                    &copy; {{ date('Y') }} {{$settings->site_name}}. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <!-- Initialize AOS -->
    <script>
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
        
        // Counter animation
        function animateCounter(element) {
            const target = parseInt(element.getAttribute('data-target'));
            const duration = 2000; // 2 seconds
            const increment = target / (duration / 16); // 60fps
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current) + '+';
            }, 16);
        }
        
        // Intersection Observer for counter animation
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        // Observe all counter elements
        document.querySelectorAll('.counter').forEach(counter => {
            counterObserver.observe(counter);
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
        
        // Toggle categories function with smooth transition
        function toggleCategories() {
            const additionalCategories = document.getElementById('additionalCategories');
            const toggleButton = document.getElementById('toggleButton');
            
            if (additionalCategories.classList.contains('hidden')) {
                additionalCategories.classList.remove('hidden');
                additionalCategories.style.opacity = '0';
                additionalCategories.style.transform = 'translateY(-20px)';
                
                setTimeout(() => {
                    additionalCategories.style.transition = 'all 0.5s ease-in-out';
                    additionalCategories.style.opacity = '1';
                    additionalCategories.style.transform = 'translateY(0)';
                }, 10);
                
                toggleButton.innerHTML = '<i class="fas fa-chevron-up mr-2"></i>View Less';
            } else {
                additionalCategories.style.transition = 'all 0.5s ease-in-out';
                additionalCategories.style.opacity = '0';
                additionalCategories.style.transform = 'translateY(-20px)';
                
                setTimeout(() => {
                    additionalCategories.classList.add('hidden');
                    additionalCategories.style.opacity = '';
                    additionalCategories.style.transform = '';
                    additionalCategories.style.transition = '';
                }, 500);
                
                toggleButton.innerHTML = '<i class="fas fa-chevron-down mr-2"></i>View All Logs';
            }
        }
        
        // Digital products data from database
        const digitalProductsData = @json($digitalProductsData);
        
        // Open product modal with redesigned cards
        function openProductModal(subcategoryName, subcategoryId) {
            const subcategory = digitalProductsData.find(sub => sub.id == subcategoryId);
            if (!subcategory) return;
            
            const products = subcategory.products || [];
            const subcategoryImage = subcategory.image ? `{{ asset('') }}${subcategory.image}` : null;
            
            const modalHTML = `
                <div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[80vh] overflow-y-auto">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="text-2xl font-bold text-gray-900">${subcategoryName} Logs</h3>
                                <button onclick="closeProductModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                ${products.map(product => {
                                    const stockStatus = product.stock > 10 ? 'text-green-600' : product.stock > 5 ? 'text-yellow-600' : 'text-red-600';
                                    const stockText = product.stock > 10 ? 'In Stock' : product.stock > 0 ? `${product.stock} left` : 'Out of Stock';
                                    const stockBg = product.stock > 10 ? 'bg-green-100' : product.stock > 5 ? 'bg-yellow-100' : 'bg-red-100';
                                    const productImage = product.image ? `{{ asset('') }}${product.image}` : (subcategoryImage || 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=400&h=300&fit=crop&crop=center');
                                    const formattedPrice = `₦${parseFloat(product.price).toLocaleString()}`;
                                    
                                    return `
                                        <div class="bg-white border border-gray-200 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden cursor-pointer transform hover:-translate-y-1" onclick="redirectToProduct('${product.slug}')">
                                            <div class="relative">
                                                <img src="${productImage}" alt="${product.name}" class="w-full h-48 object-cover">
                                                <div class="absolute top-3 right-3 ${stockBg} ${stockStatus} px-2 py-1 rounded-full text-xs font-semibold">
                                                    ${stockText}
                                                </div>
                                            </div>
                                            <div class="p-4">
                                                <h4 class="font-semibold text-gray-900 mb-2 text-sm">${product.name}</h4>
                                                <div class="flex items-center justify-between mb-3">
                                                    <span class="text-xl font-bold text-slate-800">${formattedPrice}</span>
                                                    <div class="flex items-center text-green-600 text-xs">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        Instant Delivery
                                                    </div>
                                                </div>
                                                <button class="w-full bg-gradient-to-r from-slate-800 to-gray-900 hover:from-slate-900 hover:to-black text-white py-2 px-4 rounded-lg font-semibold transition-all duration-200 text-sm ${product.stock == 0 ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-lg'}" ${product.stock == 0 ? 'disabled' : ''}>
                                                    ${product.stock == 0 ? 'Sold Out' : 'Buy Now'}
                                                </button>
                                            </div>
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }
        
        // Close product modal
        function closeProductModal() {
            const modal = document.getElementById('productModal');
            if (modal) {
                modal.remove();
            }
        }
        
        // Redirect to product page
        function redirectToProduct(slug) {
            // Redirect to product page using the new route
            window.location.href = `/product/${slug}`;
        }
        
        // Redirect to individual gift page
        function redirectToGift(giftId, giftName, price) {
            // Create URL parameters for the gift page
            const params = new URLSearchParams({
                id: giftId,
                name: giftName,
                price: price
            });
            
            // Redirect to gift page
            window.location.href = `{{ route('gift.show', '') }}/${giftId}?${params.toString()}`;
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.id == 'productModal') {
                closeProductModal();
            }
        });

        // Carousel functionality
        let currentSlide = 0;
        const totalSlides = {{ $banners->count() }};
        let carouselInterval;

        function updateCarousel() {
            const carousel = document.getElementById('carousel');
            if (carousel) {
                carousel.style.transform = `translateX(-${currentSlide * 100}%)`;
                
                // Update dots
                document.querySelectorAll('.carousel-dot').forEach((dot, index) => {
                    if (index == currentSlide) {
                        dot.classList.remove('bg-gray-400');
                        dot.classList.add('bg-gray-800');
                    } else {
                        dot.classList.remove('bg-gray-800');
                        dot.classList.add('bg-gray-400');
                    }
                });
            }
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateCarousel();
        }

        function goToSlide(slideIndex) {
            currentSlide = slideIndex;
            updateCarousel();
        }

        // Auto-play carousel
        function startCarousel() {
            carouselInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
        }

        function stopCarousel() {
            if (carouselInterval) {
                clearInterval(carouselInterval);
            }
        }

        // Initialize carousel when page loads
        document.addEventListener('DOMContentLoaded', function() {
            updateCarousel();
            startCarousel();

            // Add event listeners for navigation buttons
            const prevBtn = document.querySelector('.carousel-prev');
            const nextBtn = document.querySelector('.carousel-next');
            
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    stopCarousel();
                    prevSlide();
                    startCarousel();
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    stopCarousel();
                    nextSlide();
                    startCarousel();
                });
            }

            // Add event listeners for dots
            document.querySelectorAll('.carousel-dot').forEach((dot, index) => {
                dot.addEventListener('click', function() {
                    stopCarousel();
                    goToSlide(index);
                    startCarousel();
                });
            });

            // Pause carousel on hover
            const carouselContainer = document.querySelector('.carousel-container');
            if (carouselContainer) {
                carouselContainer.addEventListener('mouseenter', stopCarousel);
                carouselContainer.addEventListener('mouseleave', startCarousel);
            }
        });
    </script>
<script>
// Secondary Navigation Sticky Behavior
window.addEventListener('scroll', function() {
    const secondaryNav = document.getElementById('secondary-nav');
    const navbar = document.querySelector('nav');
    const navbarHeight = navbar ? navbar.offsetHeight : 64;
    
    if (window.scrollY > navbarHeight) {
        secondaryNav.classList.add('fixed', 'top-16', 'z-40', 'shadow-sm');
        secondaryNav.style.transform = 'translateY(0)';
    } else {
        secondaryNav.classList.remove('fixed', 'top-16', 'z-40', 'shadow-sm');
        secondaryNav.style.transform = 'translateY(0)';
    }
});
</script>
    {{-- <script src="//code.jivosite.com/widget/ozClxHcUVj" async></script> --}}
    <!--StartofTawk.toScript-->
    <script type="text/javascript"> var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date(); (function(){ var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0]; s1.async=true; s1.src='https://embed.tawk.to/68ea1533ca0084195466fde7/default'; s1.charset='UTF-8'; s1.setAttribute('crossorigin','*'); s0.parentNode.insertBefore(s1,s0); })(); </script> <!--End of Tawk.to Script-->

</body>
</html>