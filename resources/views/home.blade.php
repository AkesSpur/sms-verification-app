<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Verification - Secure & Fast</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
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
                            <i class="fas fa-mobile-alt mr-2"></i>SMS Verify
                        </h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <!-- Dashboard Link for authenticated users -->
                        <a href="{{ route('user.dashboard') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors navbar-link">
                            <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                        </a>
                        
                        @if(auth()->user()->is_admin)
                            <!-- Admin Panel Link for admin users -->
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors navbar-link">
                                <i class="fas fa-cog mr-1"></i>Admin 
                            </a>
                        @endif
                        
                        <!-- Logout Link -->
                        {{-- <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-gradient-to-r from-red-600 to-red-700 text-white hover:from-red-700 hover:to-red-800 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-sign-out-alt mr-1"></i>Logout
                            </button>
                        </form> --}}
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

    <!-- Hero Section -->
    <section class="hero-bg min-h-screen flex items-center justify-center relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="text-center lg:text-left mt-40" data-aos="fade-right" data-aos-duration="1000">
                    <!-- Premium Badge -->
                    <div class="mb-6">
                        <span class="premium-badge">
                            <i class="fas fa-crown"></i>
                            Premium SMS Service
                        </span>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-medium text-gray-900 mb-6 leading-tight">
                        Skip the wait. Get verified
                        <span class="block gradient-text">instantly</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-600 mb-6 max-w-2xl mx-auto lg:mx-0">
                        Access any platform instantly with our reliable SMS verification service. 
                        <span class="text-slate-700 font-semibold">150+ platforms</span> supported worldwide.
                    </p>
                    
                    <!-- Trust Indicators -->
                    <div class="flex flex-wrap gap-4 justify-center lg:justify-start mb-8">
                        <div class="trust-indicator">
                            <div class="w-2 h-2 bg-green-500 rounded-full live-indicator"></div>
                            <span>Live Support</span>
                        </div>
                        <div class="trust-indicator">
                            <i class="fas fa-shield-check"></i>
                            <span>SSL Secured</span>
                        </div>
                        <div class="trust-indicator">
                            <i class="fas fa-clock"></i>
                            <span>Instant Delivery</span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-12">
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-slate-800 to-gray-900 hover:from-slate-900 hover:to-black text-white px-8 py-4 rounded-lg text-lg font-semibold transition-all hover-scale shadow-lg">
                            <i class="fas fa-rocket mr-2"></i>Start Verifying Now
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6 max-w-md mx-auto lg:mx-0">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-slate-700 mb-1 counter" data-target="150">0</div>
                            <div class="text-sm text-gray-500">Services</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-slate-700 mb-1 counter" data-target="50">0</div>
                            <div class="text-sm text-gray-500">Countries</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-slate-700 mb-1">99.9%</div>
                            <div class="text-sm text-gray-500">Uptime</div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Illustration -->
                <div class="hidden lg:block" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="relative">
                        <!-- Slanted Verification Interface -->
                        <div class="relative z-10">
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
                                        <text x="14" y="21" text-anchor="middle" fill="#10b981" font-size="18" font-weight="bold">1</text>
                                        
                                        <rect x="35" y="0" width="28" height="32" rx="6" fill="#ffffff" stroke="#10b981" stroke-width="2"/>
                                        <text x="49" y="21" text-anchor="middle" fill="#10b981" font-size="18" font-weight="bold">2</text>
                                        
                                        <rect x="70" y="0" width="28" height="32" rx="6" fill="#ffffff" stroke="#10b981" stroke-width="2"/>
                                        <text x="84" y="21" text-anchor="middle" fill="#10b981" font-size="18" font-weight="bold">3</text>
                                        
                                        <rect x="105" y="0" width="28" height="32" rx="6" fill="#ffffff" stroke="#10b981" stroke-width="2"/>
                                        <text x="119" y="21" text-anchor="middle" fill="#10b981" font-size="18" font-weight="bold">4</text>
                                        
                                        <rect x="140" y="0" width="28" height="32" rx="6" fill="#ffffff" stroke="#10b981" stroke-width="2"/>
                                        <text x="154" y="21" text-anchor="middle" fill="#10b981" font-size="18" font-weight="bold">5</text>
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

    <!-- Services Section -->
    <section class="py-20 relative">
        <div class="absolute inset-0 bg-gradient-to-b from-white via-gray-50 to-white opacity-80"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="premium-badge mb-6">
                    <i class="fas fa-check-circle"></i>
                    Verified Services
                </span>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Popular <span class="gradient-text">Services</span></h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Get verification codes for the most popular platforms and services
                </p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center p-6 rounded-lg testimonial-card">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.488" fill="#25D366"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">WhatsApp</h3>
                </div>
                
                <div class="text-center p-6 rounded-lg testimonial-card">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="12" fill="#0088cc"/>
                            <path d="M5.491 11.74l11.57-4.461c.537-.194.996.131.82.983l0 0-1.97 9.281c-.146.658-.537.818-1.084.508l-3-2.211-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.135-.953z" fill="white"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Telegram</h3>
                </div>
                
                <div class="text-center p-6 rounded-lg testimonial-card">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Google</h3>
                </div>
                
                <div class="text-center p-6 rounded-lg testimonial-card">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Facebook</h3>
                </div>
                
                <div class="text-center p-6 rounded-lg testimonial-card">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="#E4405F"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Instagram</h3>
                </div>
                
                <div class="text-center p-6 rounded-lg testimonial-card">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" fill="#1DA1F2"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Twitter</h3>
                </div>
            </div>
            
            <div class="text-center mt-12" data-aos="fade-up">
                <a href="{{ route('register') }}" class="bg-gradient-to-r from-slate-800 to-gray-900 hover:from-slate-900 hover:to-black text-white px-8 py-4 rounded-lg font-semibold transition-colors shadow-lg hover-scale">
                    View All Services
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

    <!-- Footer -->
    <footer class="bg-slate-800 text-white py-12 relative">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-800 via-slate-900 to-slate-800 opacity-80"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <!-- Company Info -->
            <div class="mb-8">
                <h3 class="text-2xl font-bold mb-4 gradient-text">SMS Verify</h3>
                <p class="text-gray-300 mb-6">The most reliable SMS verification service for your online accounts.</p>
                
                <!-- Email Contact -->
                <div class="flex items-center justify-center mb-6">
                    <i class="fas fa-envelope mr-2 text-gray-300"></i>
                    <a href="mailto:support@smsverify.com" class="text-gray-300 hover:text-white transition-colors">support@smsverify.com</a>
                </div>
                
                <!-- Social Media Links -->
                <div class="flex justify-center space-x-6 mb-8">
                    <a href="#" class="text-gray-300 hover:text-white transition-colors text-xl">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors text-xl">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors text-xl">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors text-xl">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="border-t border-slate-700 pt-8">
                <p class="text-gray-300 text-sm">&copy; {{ date('Y') }} SMS Verify. All rights reserved.</p>
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
    </script>
</body>
</html>