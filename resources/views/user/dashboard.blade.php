@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Virtual Account (PaymentPoint) -->
    <div id="virtualAccountWidget" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Virtual Account</h3>
                <p class="text-sm text-gray-500">Create a PaymentPoint virtual bank account to fund your wallet</p>
            </div>
            <div id="vaActionArea" class="mt-3 md:mt-0">
                <button id="createVaBtn" class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-university mr-2"></i>
                    Create Virtual Account
                </button>
            </div>
        </div>
        <div id="vaDetails" class="mt-4 hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="text-xs text-gray-500">Account Number</p>
                    <p class="text-lg font-semibold text-gray-900" id="vaAccountNumber">-</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="text-xs text-gray-500">Account Name</p>
                    <p class="text-lg font-semibold text-gray-900" id="vaAccountName">-</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="text-xs text-gray-500">Bank</p>
                    <p class="text-lg font-semibold text-gray-900" id="vaBankName">-</p>
                </div>
            </div>
        </div>
    </div>
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- USA Numbers 1 CTA -->
                <div class="group relative bg-white rounded-xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 hover:border-primary-300">
                    <div class="absolute top-4 right-4">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 text-xs font-bold">1</span>
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
                            <h4 class="text-lg font-semibold text-gray-900 mb-1">USA Numbers 1</h4>
                            <p class="text-sm text-gray-600 mb-3">Server 1 - Premium US phone numbers</p>
                            <div class="flex items-center text-xs text-gray-500">
                                <i class="fas fa-server mr-1"></i>
                                <span>Primary server</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-700 text-sm mb-4 leading-relaxed">Access our primary server with premium US phone numbers for WhatsApp, Telegram, Discord, Instagram, and other services.</p>
                    <a href="{{ route('user.sms.rental.index') }}" class="inline-flex items-center justify-center w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 group-hover:shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        Browse USA Numbers 1
                    </a>
                </div>

                <!-- USA Numbers 2 CTA -->
                <div class="group relative bg-white rounded-xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 hover:border-primary-300">
                    <div class="absolute top-4 right-4">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <span class="text-red-600 text-xs font-bold">2</span>
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
                            <h4 class="text-lg font-semibold text-gray-900 mb-1">USA Numbers 2</h4>
                            <p class="text-sm text-gray-600 mb-3">Server 2 - Premium US phone numbers</p>
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
                        Browse USA Numbers 2
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

                <!-- Reseller Store CTA -->
                <div class="group relative bg-white rounded-xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 hover:border-primary-300">
                    <div class="absolute top-4 right-4">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-tags text-yellow-600 text-xs"></i>
                        </div>
                    </div>
                    <div class="flex items-start mb-4">
                        <div class="w-14 h-14 bg-yellow-50 rounded-xl flex items-center justify-center mr-4 shadow-lg border-2 border-yellow-200">
                            <i class="fas fa-tags text-yellow-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900 mb-1">Reseller Store</h4>
                            <p class="text-sm text-gray-600 mb-3">Exclusive products for verified resellers</p>
                            <div class="flex items-center text-xs text-gray-500">
                                <i class="fas fa-user-tag mr-1"></i>
                                <span>Request access if not a reseller</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('user.reseller') }}" class="inline-flex items-center justify-center w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 group-hover:shadow-lg">
                        <i class="fas fa-tags w-4 h-4 mr-2"></i>
                        Go to Reseller Store
                    </a>
                </div>

                <!-- Logs Store CTA -->
                <div class="group relative bg-white rounded-xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 hover:border-primary-300">
                    <div class="absolute top-4 right-4">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-store text-blue-600 text-xs"></i>
                        </div>
                    </div>
                    <div class="flex items-start mb-4">
                        <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center mr-4 shadow-lg border-2 border-blue-200">
                            <i class="fas fa-store text-blue-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900 mb-1">Logs Store</h4>
                            <p class="text-sm text-gray-600 mb-3">Browse categories of logs and services</p>
                            <div class="flex items-center text-xs text-gray-500">
                                <i class="fas fa-list mr-1"></i>
                                <span>Organized by categories</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('all-categories') }}" class="inline-flex items-center justify-center w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 group-hover:shadow-lg">
                        <i class="fas fa-store w-4 h-4 mr-2"></i>
                        Browse Logs Store
                    </a>
                </div>

                <!-- Gift Store CTA -->
                <div class="group relative bg-white rounded-xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 hover:border-primary-300">
                    <div class="absolute top-4 right-4">
                        <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-gift text-pink-600 text-xs"></i>
                        </div>
                    </div>
                    <div class="flex items-start mb-4">
                        <div class="w-14 h-14 bg-pink-50 rounded-xl flex items-center justify-center mr-4 shadow-lg border-2 border-pink-200">
                            <i class="fas fa-gift text-pink-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900 mb-1">Gift Store</h4>
                            <p class="text-sm text-gray-600 mb-3">Curated gifts and customizations</p>
                            <div class="flex items-center text-xs text-gray-500">
                                <i class="fas fa-gem mr-1"></i>
                                <span>Unique items and bundles</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('all-gifts') }}" class="inline-flex items-center justify-center w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 group-hover:shadow-lg">
                        <i class="fas fa-gift w-4 h-4 mr-2"></i>
                        Browse Gift Store
                    </a>
                </div>

                <!-- Social Media Boosting CTA -->
                <div class="group relative bg-white rounded-xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 hover:border-primary-300">
                    <div class="absolute top-4 right-4">
                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-rocket text-indigo-600 text-xs"></i>
                        </div>
                    </div>
                    <div class="flex items-start mb-4">
                        <div class="w-14 h-14 bg-indigo-50 rounded-xl flex items-center justify-center mr-4 shadow-lg border-2 border-indigo-200">
                            <i class="fas fa-rocket text-indigo-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900 mb-1">Boosting Of Accounts</h4>
                            <p class="text-sm text-gray-600 mb-3">Social media boosting and growth services</p>
                            <div class="flex items-center text-xs text-gray-500">
                                <i class="fas fa-shield-alt mr-1"></i>
                                <span>High success rate</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('user.social-media-boosting.index') }}" class="inline-flex items-center justify-center w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 group-hover:shadow-lg">
                        <i class="fas fa-rocket w-4 h-4 mr-2"></i>
                        Go to Boosting
                    </a>
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
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const widget = document.getElementById('virtualAccountWidget');
    const actionArea = document.getElementById('vaActionArea');
    const details = document.getElementById('vaDetails');
    const createBtn = document.getElementById('createVaBtn');

    function showCard(account) {
        document.getElementById('vaAccountNumber').textContent = account.account_number;
        document.getElementById('vaAccountName').textContent = account.account_name;
        document.getElementById('vaBankName').textContent = account.bank_name;
        details.classList.remove('hidden');
        actionArea.innerHTML = '<span class="inline-flex items-center px-3 py-1 text-xs rounded-full bg-green-100 text-green-700"><i class="fas fa-check mr-1"></i> Active</span>';
    }

    function loadVA() {
        fetch('{{ route('api.user.virtual-account.get') }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.has_account) {
                showCard(data.account);
            }
        })
        .catch(() => {/* ignore */});
    }

    createBtn && createBtn.addEventListener('click', function() {
        createBtn.disabled = true;
        createBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
        fetch('{{ route('api.user.virtual-account.create') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showCard(data.account);
            } else {
                notify('error', data.message || 'Failed to create virtual account');
                createBtn.disabled = false;
                createBtn.innerHTML = '<i class="fas fa-university mr-2"></i>Create Virtual Account';
            }
        })
        .catch(err => {
            console.error(err);
            notify('error', 'An error occurred');
            createBtn.disabled = false;
            createBtn.innerHTML = '<i class="fas fa-university mr-2"></i>Create Virtual Account';
        });
    });

    loadVA();
});
</script>
@endpush
</div>


@endsection