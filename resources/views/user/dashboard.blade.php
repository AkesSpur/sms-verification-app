@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Balance Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Balance</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($balance, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wallet text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Numbers Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Numbers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $activeNumbers }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-phone text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Numbers Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Numbers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalNumbers }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Completed Orders Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $completedOrders }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- USA Numbers CTA -->
                <div class="group relative bg-white rounded-xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 hover:border-primary-300">
                    <div class="absolute top-4 right-4">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <span class="text-red-600 text-xs font-bold">🇺🇸</span>
                        </div>
                    </div>
                    <div class="flex items-start mb-4">
                        <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center mr-4 shadow-lg border-2 border-gray-200">
                            <svg class="w-10 h-7" viewBox="0 0 60 40" xmlns="http://www.w3.org/2000/svg">
                                <!-- Red stripes -->
                                <rect width="60" height="40" fill="#B22234"/>
                                <!-- White stripes -->
                                <rect y="3" width="60" height="3" fill="white"/>
                                <rect y="9" width="60" height="3" fill="white"/>
                                <rect y="15" width="60" height="3" fill="white"/>
                                <rect y="21" width="60" height="3" fill="white"/>
                                <rect y="27" width="60" height="3" fill="white"/>
                                <rect y="33" width="60" height="3" fill="white"/>
                                <!-- Blue canton -->
                                <rect width="24" height="21" fill="#3C3B6E"/>
                                <!-- Stars (simplified pattern) -->
                                <g fill="white">
                                    <circle cx="3" cy="3" r="0.8"/>
                                    <circle cx="7" cy="3" r="0.8"/>
                                    <circle cx="11" cy="3" r="0.8"/>
                                    <circle cx="15" cy="3" r="0.8"/>
                                    <circle cx="19" cy="3" r="0.8"/>
                                    <circle cx="5" cy="6" r="0.8"/>
                                    <circle cx="9" cy="6" r="0.8"/>
                                    <circle cx="13" cy="6" r="0.8"/>
                                    <circle cx="17" cy="6" r="0.8"/>
                                    <circle cx="21" cy="6" r="0.8"/>
                                    <circle cx="3" cy="9" r="0.8"/>
                                    <circle cx="7" cy="9" r="0.8"/>
                                    <circle cx="11" cy="9" r="0.8"/>
                                    <circle cx="15" cy="9" r="0.8"/>
                                    <circle cx="19" cy="9" r="0.8"/>
                                    <circle cx="5" cy="12" r="0.8"/>
                                    <circle cx="9" cy="12" r="0.8"/>
                                    <circle cx="13" cy="12" r="0.8"/>
                                    <circle cx="17" cy="12" r="0.8"/>
                                    <circle cx="21" cy="12" r="0.8"/>
                                    <circle cx="3" cy="15" r="0.8"/>
                                    <circle cx="7" cy="15" r="0.8"/>
                                    <circle cx="11" cy="15" r="0.8"/>
                                    <circle cx="15" cy="15" r="0.8"/>
                                    <circle cx="19" cy="15" r="0.8"/>
                                    <circle cx="5" cy="18" r="0.8"/>
                                    <circle cx="9" cy="18" r="0.8"/>
                                    <circle cx="13" cy="18" r="0.8"/>
                                    <circle cx="17" cy="18" r="0.8"/>
                                    <circle cx="21" cy="18" r="0.8"/>
                                </g>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900 mb-1">USA Numbers</h4>
                            <p class="text-sm text-gray-600 mb-3">Premium US phone numbers for verification</p>
                            <div class="flex items-center text-xs text-gray-500">
                                <i class="fas fa-shield-alt mr-1"></i>
                                <span>High success rate</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-700 text-sm mb-4 leading-relaxed">Get instant access to premium US phone numbers for WhatsApp, Telegram, Discord, Instagram, and other popular services.</p>
                    <a href="{{ route('user.usa-numbers') }}" class="inline-flex items-center justify-center w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 group-hover:shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        Browse USA Numbers
                    </a>
                </div>

                <!-- All Countries CTA -->
                <div class="group relative bg-white rounded-xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 hover:border-primary-300">
                    <div class="absolute top-4 right-4">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-green-600 text-xs font-bold">🌍</span>
                        </div>
                    </div>
                    <div class="flex items-start mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                            <i class="fas fa-globe-americas text-white text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900 mb-1">International Numbers</h4>
                            <p class="text-sm text-gray-600 mb-3">Global phone numbers from 150+ countries</p>
                            <div class="flex items-center text-xs text-gray-500">
                                <i class="fas fa-globe mr-1"></i>
                                <span>Worldwide coverage</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-700 text-sm mb-4 leading-relaxed">Access phone numbers from countries worldwide including UK, Canada, Germany, France, Australia, and many more.</p>
                    <a href="{{ route('user.all-countries') }}" class="inline-flex items-center justify-center w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 group-hover:shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        Browse All Countries
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

    <!-- Website Builder Contact -->
    <div class="py-3 text-center text-sm text-gray-700 border-t border-gray-200 mt-6">
        <div class="flex items-center justify-center space-x-2 scale-90 hover:scale-100 transition-transform duration-300">
            <i class="fas fa-mobile-alt text-blue-600 animate-pulse"></i>
            <p>
                Need a custom website? <a href="https://wa.link/18c124" class="text-blue-600 hover:text-blue-800 font-medium transition-colors relative group">
                    Contact the developer
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"></span>
                </a>
            </p>
            <i class="fas fa-code text-blue-600 animate-bounce"></i>
        </div>
    </div>

</div>


@endsection