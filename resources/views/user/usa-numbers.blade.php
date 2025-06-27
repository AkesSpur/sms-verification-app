@extends('layouts.user')

@section('title', 'USA Numbers')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">USA Numbers</h1>
            <p class="mt-1 text-sm text-gray-500">Get and manage your USA phone numbers</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-2">
                <span class="text-sm text-green-800 font-medium">Balance: ₦{{ number_format($stats['balance'], 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-mobile-alt text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_orders'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Completed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_orders'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Get New USA Number</h2>
        <form id="usaForm" class="space-y-4">
            @csrf
            
            <!-- Service Selection -->
            <div>
                <label for="service" class="block text-sm font-medium text-gray-700 mb-2">Select Service</label>
                <!-- Custom Searchable Dropdown -->
                <div class="relative" id="searchable-dropdown">
                    <!-- Hidden select for form submission -->
                    <select id="service" name="service" class="hidden">
                        <option value="">Choose a service...</option>
                        @foreach($services as $service)
                            <option value="{{ $service->code }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                    
                    <!-- Custom searchable dropdown -->
                    <div class="relative">
                        <button type="button" id="dropdown-button" 
                                class="w-full px-3 py-2 text-left border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white flex items-center justify-between">
                            <span id="dropdown-text" class="block truncate text-gray-900">Choose a service...</span>
                            <svg id="dropdown-arrow" class="w-5 h-5 text-gray-400 transition-transform duration-200" 
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <!-- Dropdown panel -->
                        <div id="dropdown-panel" 
                             class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm hidden">
                            
                            <!-- Search input -->
                            <div class="sticky top-0 bg-white px-3 py-2 border-b border-gray-200">
                                <div class="relative">
                                    <input type="text" id="search-input" 
                                           placeholder="Search services..." 
                                           class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Options list -->
                            <div id="options-container" class="max-h-48 overflow-y-auto">
                                <!-- Options will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Price Display -->
            <div id="price-display" class="bg-gray-50 rounded-lg p-4 hidden">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Price:</span>
                    <span id="price-text" class="text-lg font-semibold text-gray-900">₦0</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div id="action-buttons" class="flex space-x-3 hidden">
                <button type="button" id="check-availability-btn"
                        class="flex-1 text-gray-700 px-4 py-2 rounded-lg transition-colors bg-gray-100 hover:bg-gray-200">
                    <i id="check-icon" class="fas fa-search mr-2"></i>
                    <span id="check-text">Check Availability</span>
                </button>
                <button type="submit" id="purchase-btn" disabled
                        class="flex-1 px-4 py-2 rounded-lg transition-colors bg-gray-300 text-gray-500 cursor-not-allowed">
                    <i class="fas fa-shopping-cart mr-2"></i>Purchase
                </button>
            </div>

            <!-- Status Result -->
            <div id="status-result"></div>
        </form>
    </div>

    <!-- Information Section -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600 text-lg mt-0.5"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">How USA Numbers Work</h3>
                <div class="text-sm text-blue-800 space-y-1">
                    <p><strong>Timer:</strong> Each order has a countdown timer showing time remaining to receive SMS.</p>
                    <p><strong>Auto-Refund:</strong> If no SMS is received within the time limit, your order will be automatically cancelled and your account will be refunded.</p>
                    <p><strong>Real-time Updates:</strong> Order status and timers update automatically every 30 seconds.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Orders Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Active Orders</h2>
                <button onclick="refreshOrders()" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                    <i class="fas fa-sync-alt mr-1"></i>Refresh
                </button>
            </div>
        </div>
        <div class="p-6">
            <div id="active-orders" class="space-y-4">
                @forelse($activeOrders as $order)
                    <div class="order-item flex items-center justify-between p-4 bg-gray-50 rounded-lg" data-order-id="{{ $order->id }}">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-mobile-alt {{ $order->status === 'completed' ? 'text-green-600' : ($order->status === 'pending' ? 'text-yellow-600' : 'text-blue-600') }}"></i>
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900">{{ $order->phone_number ?? 'Requesting...' }}</p>
                                        @if($order->phone_number)
                                            <button onclick="copyToClipboard('{{ $order->phone_number }}', this)" 
                                                    class="text-gray-400 hover:text-blue-600 transition-colors duration-200" 
                                                    title="Copy phone number">
                                                <i class="fas fa-copy text-xs"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        {{ $order->service->name ?? 'Unknown Service' }} • 
                                        @if($order->sms_code)
                                            Code: {{ $order->sms_code }}
                                        @else
                                            Waiting for SMS
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</p>
                                    @if($order->status === 'pending' && $order->sms_window_expires_at)
                                        <p class="text-xs text-orange-600 font-medium">
                                            <i class="fas fa-clock mr-1"></i>
                                            <span id="timer-{{ $order->id }}" data-expires="{{ $order->sms_window_expires_at->toISOString() }}">Loading...</span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            <div class="relative">
                                <button onclick="toggleOrderMenu({{ $order->id }})" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div id="order-menu-{{ $order->id }}" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200 hidden">
                                    <div class="py-1">
                                        <button onclick="checkOrderStatus({{ $order->id }})" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sync mr-2"></i>Check Status
                                        </button>
                                        @if($order->status === 'pending')
                                            <button onclick="cancelOrder({{ $order->id }})" 
                                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <i class="fas fa-times mr-2"></i>Cancel Order
                                            </button>
                                        @endif
                                        <a href="{{ route('usa.order.show', $order->id) }}" 
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-eye mr-2"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-mobile-alt text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500">No active orders found</p>
                        <p class="text-sm text-gray-400">Purchase a number to get started</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Numbers History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Your USA Numbers</h2>
        </div>
        
        <div class="p-6">
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SMS Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            function formatPhoneNumber($phone) {
                                // Remove any non-digit characters
                                $cleaned = preg_replace('/[^0-9]/', '', $phone);
                                
                                // If it starts with 1 and has 11 digits, format as US number
                                if (strlen($cleaned) == 11 && substr($cleaned, 0, 1) == '1') {
                                    $area = substr($cleaned, 1, 3);
                                    $exchange = substr($cleaned, 4, 3);
                                    $number = substr($cleaned, 7, 4);
                                    return "+1 ({$area}) {$exchange}-{$number}";
                                }
                                // If it has 10 digits, assume US number without country code
                                elseif (strlen($cleaned) == 10) {
                                    $area = substr($cleaned, 0, 3);
                                    $exchange = substr($cleaned, 3, 3);
                                    $number = substr($cleaned, 6, 4);
                                    return "+1 ({$area}) {$exchange}-{$number}";
                                }
                                // Otherwise return as-is with + prefix if not present
                                else {
                                    return strpos($phone, '+') === 0 ? $phone : '+' . $phone;
                                }
                            }
                            
                            function getStatusBadge($status) {
                                switch(strtolower($status)) {
                                    case 'active':
                                        return 'bg-green-100 text-green-800';
                                    case 'pending':
                                        return 'bg-yellow-100 text-yellow-800';
                                    case 'completed':
                                        return 'bg-blue-100 text-blue-800';
                                    case 'cancelled':
                                        return 'bg-red-100 text-red-800';
                                    case 'expired':
                                        return 'bg-gray-100 text-gray-800';
                                    case 'failed':
                                        return 'bg-red-100 text-red-800';
                                    default:
                                        return 'bg-gray-100 text-gray-800';
                                }
                            }
                        @endphp
                        
                        @forelse($allOrders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <div class="flex items-center space-x-2">
                                    <span>{{ formatPhoneNumber($order->phone_number) }}</span>
                                    <button onclick="copyToClipboard('{{ $order->phone_number }}')"
                                            class="text-gray-400 hover:text-gray-600 transition-colors"
                                            title="Copy phone number">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                #{{ $order->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $order->service->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class=" px-2.5 py-0.5 rounded-full text-xs font-medium {{ getStatusBadge($order->status) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    @if($order->status === 'cancelled' && $order->refunded)
                                        <span class="text-xs text-green-600 mt-1">
                                            <i class="fas fa-check-circle mr-1"></i>Refunded
                                        </span>
                                    @elseif($order->status === 'cancelled' && !$order->refunded)
                                        <span class="text-xs text-red-600 mt-1">
                                            <i class="fas fa-times-circle mr-1"></i>No Refund
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($order->sms_code)
                                    <div class="flex items-center space-x-2">
                                        <span class="font-mono font-bold text-green-600">{{ $order->sms_code }}</span>
                                        <button onclick="copyToClipboard('{{ $order->sms_code }}')" class="text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                @elseif($order->status === 'cancelled')
                                    <span class="text-red-500">Cancelled</span>
                                @else
                                    <span class="text-gray-500">Pending...</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('M j, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex justify-center space-x-2">
                                    @if(in_array($order->status, ['pending', 'active']))
                                        <button class="text-primary-600 hover:text-primary-900" onclick="refreshNumber({{ $order->id }})">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('usa.order.show', $order->id) }}" class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <i class="fas fa-mobile-alt text-gray-400 text-4xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No USA Numbers Yet</h3>
                                <p class="text-gray-500">Get your first USA number using the form above.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-4">
                @forelse($allOrders as $order)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-flag-usa text-blue-600"></i>
                            <span class="font-medium text-gray-900">{{ formatPhoneNumber($order->phone_number) }}</span>
                            <button onclick="copyToClipboard('{{ $order->phone_number }}')"
                                    class="text-gray-400 hover:text-gray-600 transition-colors"
                                    title="Copy phone number">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ getStatusBadge($order->status) }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            @if($order->status === 'cancelled' && $order->refunded)
                                <span class="text-xs text-green-600 mt-1">
                                    <i class="fas fa-check-circle mr-1"></i>Refunded
                                </span>
                            @elseif($order->status === 'cancelled' && !$order->refunded)
                                <span class="text-xs text-red-600 mt-1">
                                    <i class="fas fa-times-circle mr-1"></i>No Refund
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500">Order ID:</span>
                            <span class="ml-1 font-medium">#{{ $order->id }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Service:</span>
                            <span class="ml-1 font-medium">{{ $order->service->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Created:</span>
                            <span class="ml-1 text-gray-500">{{ $order->created_at->format('M j, H:i') }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500">SMS Code:</span>
                            <div class="mobile-sms-code mt-1">
                                @if($order->sms_code)
                                    <div class="flex items-center space-x-2">
                                        <span class="font-mono font-bold text-green-600">{{ $order->sms_code }}</span>
                                        <button onclick="copyToClipboard('{{ $order->sms_code }}')" class="text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                @elseif($order->status === 'cancelled')
                                    <span class="text-red-500">Cancelled</span>
                                @else
                                    <span class="text-gray-500">Pending...</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2 mt-3">
                        @if(in_array($order->status, ['pending', 'active']))
                            <button onclick="refreshNumber({{ $order->id }})" class="flex-1 bg-primary-100 text-primary-700 px-3 py-2 rounded-lg text-sm hover:bg-primary-200 transition-colors">
                                <i class="fas fa-sync-alt mr-1"></i>Refresh
                            </button>
                        @endif
                        <a href="{{ route('usa.order.show', $order->id) }}" class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors text-center">
                            <i class="fas fa-info-circle mr-1"></i>Details
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-12">
                    <i class="fas fa-mobile-alt text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No USA Numbers Yet</h3>
                    <p class="text-gray-500 mb-4">Get your first USA number using the form above.</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($allOrders->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 mt-6 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if($allOrders->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $allOrders->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    Previous
                                </a>
                            @endif
                            
                            @if($allOrders->hasMorePages())
                                <a href="{{ $allOrders->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    Next
                                </a>
                            @else
                                <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                    Next
                                </span>
                            @endif
                        </div>
                        
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium">{{ $allOrders->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $allOrders->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $allOrders->total() }}</span> results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    @if($allOrders->onFirstPage())
                                        <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    @else
                                        <a href="{{ $allOrders->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    @endif
                                    
                                    @foreach($allOrders->getUrlRange(1, $allOrders->lastPage()) as $page => $url)
                                        @if($page == $allOrders->currentPage())
                                            <span class="relative inline-flex items-center px-4 py-2 border border-primary-500 bg-primary-600 text-sm font-medium text-white">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach
                                    
                                    @if($allOrders->hasMorePages())
                                        <a href="{{ $allOrders->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    @else
                                        <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    @endif
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div id="cancelOrderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="fixed inset-0 flex items-center justify-center p-4" onclick="hideCancelOrderModal()">
        <div class="relative mx-auto border w-full max-w-md shadow-lg rounded-xl bg-white" onclick="event.stopPropagation()">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-medium text-gray-900">Cancel Order</h3>
                    <button onclick="hideCancelOrderModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        Are you sure you want to cancel this order? This action cannot be undone and any refund will be processed according to our policy.
                    </p>
                </div>
                
                <div class="flex space-x-3">
                    <button id="cancelOrderCancel" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>Keep Order
                    </button>
                    <button id="cancelOrderConfirm" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-medium">
                        <i class="fas fa-times mr-2"></i>Cancel Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables for security and rate limiting
let lastRequestTime = 0;
let requestCount = 0;
const RATE_LIMIT_WINDOW = 60000; // 1 minute
const MAX_REQUESTS_PER_WINDOW = 10;
let activeStatusChecks = new Set();

// Security: Rate limiting check
function checkRateLimit() {
    const now = Date.now();
    if (now - lastRequestTime > RATE_LIMIT_WINDOW) {
        requestCount = 0;
        lastRequestTime = now;
    }
    
    if (requestCount >= MAX_REQUESTS_PER_WINDOW) {
        showNotification('Too many requests. Please wait a moment.', 'warning');
        return false;
    }
    
    requestCount++;
    return true;
}

// Enhanced form handling with security
document.getElementById('usaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!checkRateLimit()) return;
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const service = document.getElementById('service').value;
    
    if (!service) {
        showNotification('Please select a service first', 'warning');
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    
    fetch('{{ route("usa.purchase") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        // Handle both success and error responses
        return response.json().then(data => {
            return { data, status: response.status, ok: response.ok };
        });
    })
    .then(({ data, status, ok }) => {
        if (ok && data.success) {
            showNotification(data.message || 'Number purchased successfully!', 'success');
            // Reset form
            document.getElementById('service').value = '';
            document.getElementById('status-result').innerHTML = '';
            // Refresh orders after a short delay
            setTimeout(() => {
                refreshOrders();
            }, 1000);
        } else {
            // Handle validation errors (422) and other errors
            if (status === 422 && data.errors) {
                // Show validation errors
                Object.values(data.errors).forEach(errorArray => {
                    if (Array.isArray(errorArray)) {
                        errorArray.forEach(error => {
                            showNotification(error, 'error');
                        });
                    } else {
                        showNotification(errorArray, 'error');
                    }
                });
            } else {
                // Show general error message
                showNotification(data.message || 'Purchase failed', 'error');
            }
        }
    })
    .catch(error => {
        console.error('Network Error:', error);
        showNotification('Network error. Please check your connection.', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-shopping-cart mr-2"></i>Purchase';
    });
});

// Enhanced availability checking with Alpine.js integration
function checkAvailability() {
    const service = document.getElementById('service').value;
    if (!service) {
        showNotification('Please select a service first', 'warning');
        return;
    }
    
    if (!checkRateLimit()) return;
    
    // Prevent duplicate requests
    if (activeStatusChecks.has(service)) {
        showNotification('Already checking this service...', 'info');
        return;
    }
    
    activeStatusChecks.add(service);
    const statusResult = document.getElementById('status-result');
    statusResult.innerHTML = '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4"><i class="fas fa-spinner fa-spin mr-2"></i>Checking availability...</div>';
    
    fetch(`{{ route('usa.check-availability') }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            service: service
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.available) {
            statusResult.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            <span class="text-green-800 font-medium">Service available!</span>
                        </div>
                        <span class="text-green-800 font-bold">₦${data.price.toLocaleString()}</span>
                    </div>
                    <p class="text-sm text-green-700 mt-2">Available numbers: ${data.count || 'Multiple'}</p>
                </div>
            `;
            // Update Alpine.js data
            const formElement = document.getElementById('usaForm');
            formElement._x_dataStack[0].servicePrice = data.price;
            formElement._x_dataStack[0].statusChecked = true;
        } else {
            statusResult.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-times-circle text-red-600 mr-2"></i>
                        <span class="text-red-800 font-medium">${data.message || 'Service currently unavailable'}</span>
                    </div>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        statusResult.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                    <span class="text-red-800 font-medium">Error checking availability</span>
                </div>
            </div>
        `;
    })
    .finally(() => {
        activeStatusChecks.delete(service);
    });
}

// Real-time order status checking
function checkOrderStatus(orderId) {
    if (!checkRateLimit()) return;
    
    const orderElement = document.querySelector(`[data-order-id="${orderId}"]`);
    if (!orderElement) return;
    
    const statusSpan = orderElement.querySelector('.inline-flex');
    const originalStatus = statusSpan.textContent;
    statusSpan.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Checking...';
    
    fetch(`/user/usa/order/${orderId}/status`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update order display
            updateOrderDisplay(orderElement, data.order);
            showNotification(data.message || 'Status updated', 'success');
        } else {
            statusSpan.textContent = originalStatus;
            showNotification(data.message || 'Failed to check status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        statusSpan.textContent = originalStatus;
        showNotification('Error checking order status', 'error');
    });
}

// Cancel order function
function cancelOrder(orderId) {
    // Show custom modal instead of browser confirm
    showCancelOrderModal(orderId);
}

// Show cancel order modal
function showCancelOrderModal(orderId) {
    const modal = document.getElementById('cancelOrderModal');
    const confirmBtn = document.getElementById('cancelOrderConfirm');
    const cancelBtn = document.getElementById('cancelOrderCancel');
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Handle confirm button click
    confirmBtn.onclick = function() {
        hideCancelOrderModal();
        processCancelOrder(orderId);
    };
    
    // Handle cancel button click
    cancelBtn.onclick = function() {
        hideCancelOrderModal();
    };
    
    // Handle click outside modal to close
    modal.onclick = function(event) {
        if (event.target === modal) {
            hideCancelOrderModal();
        }
    };
    
    // Handle escape key to close
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideCancelOrderModal();
        }
    });
}

// Hide cancel order modal
function hideCancelOrderModal() {
    const modal = document.getElementById('cancelOrderModal');
    modal.classList.add('hidden');
    
    // Remove event listeners
    document.removeEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideCancelOrderModal();
        }
    });
}

// Process the actual order cancellation
function processCancelOrder(orderId) {
    if (!checkRateLimit()) return;
    
    const orderElement = document.querySelector(`[data-order-id="${orderId}"]`);
    if (!orderElement) return;
    
    // Show loading state
    showNotification('Cancelling order...', 'info', 2000);
    
    fetch(`/user/usa/order/${orderId}/cancel`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Order cancelled successfully', 'success');
            // Remove or update the order element
            if (data.refunded) {
                showNotification(`Refund of ₦${data.refund_amount} processed`, 'info');
            }
            refreshOrders();
        } else {
            showNotification(data.message || 'Failed to cancel order', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error cancelling order', 'error');
    });
}

// Refresh orders function
function refreshOrders() {
    const refreshBtn = document.querySelector('button[onclick="refreshOrders()"]');
    const originalContent = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Refreshing...';
    refreshBtn.disabled = true;
    
    // First, fetch the latest active orders to ensure we have all new orders
    fetch('/user/usa-numbers', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Create a temporary element to parse the HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        
        // Get the active orders section from the response
        const newActiveOrders = tempDiv.querySelector('#active-orders');
        
        if (newActiveOrders) {
            // Replace the current active orders with the new ones
            document.querySelector('#active-orders').innerHTML = newActiveOrders.innerHTML;
            
            // Now check status for each order to get real-time updates
            const activeOrderElements = document.querySelectorAll('.order-item[data-order-id]');
            
            if (activeOrderElements.length === 0) {
                refreshBtn.innerHTML = originalContent;
                refreshBtn.disabled = false;
                return;
            }
            
            let completedChecks = 0;
            const totalChecks = activeOrderElements.length;
            
            activeOrderElements.forEach(orderElement => {
                const orderId = orderElement.getAttribute('data-order-id');
                
                fetch(`/user/usa/order/${orderId}/status`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateOrderDisplay(orderElement, data.order);
                    }
                })
                .catch(error => {
                    console.error('Error checking order status:', error);
                })
                .finally(() => {
                    completedChecks++;
                    if (completedChecks === totalChecks) {
                        refreshBtn.innerHTML = originalContent;
                        refreshBtn.disabled = false;
                        showNotification('Orders refreshed', 'success');
                        
                        // Restart all timers after refresh
                        startAllTimers();
                    }
                });
            });
        } else {
            refreshBtn.innerHTML = originalContent;
            refreshBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error refreshing orders:', error);
        refreshBtn.innerHTML = originalContent;
        refreshBtn.disabled = false;
        showNotification('Error refreshing orders', 'error');
    });}


// Update order display helper
function updateOrderDisplay(orderElement, orderData) {
    // Update status badge
    const statusElement = orderElement.querySelector('.inline-flex');
    const iconElement = orderElement.querySelector('.fas.fa-mobile-alt');
    
    const statusClasses = {
        'completed': 'bg-green-100 text-green-800',
        'pending': 'bg-yellow-100 text-yellow-800',
        'cancelled': 'bg-red-100 text-red-800',
        'expired': 'bg-gray-100 text-gray-800'
    };
    
    const iconClasses = {
        'completed': 'text-green-600',
        'pending': 'text-yellow-600',
        'cancelled': 'text-red-600',
        'expired': 'text-gray-600'
    };
    
    if (statusElement) {
        statusElement.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClasses[orderData.status] || 'bg-blue-100 text-blue-800'}`;
        statusElement.textContent = orderData.status.charAt(0).toUpperCase() + orderData.status.slice(1);
    }
    
    if (iconElement) {
        iconElement.className = `fas fa-mobile-alt ${iconClasses[orderData.status] || 'text-blue-600'}`;
    }
    
    // Update timer if present and order is pending
    const orderId = orderElement.getAttribute('data-order-id');
    const timerElement = document.getElementById(`timer-${orderId}`);
    
    if (timerElement && orderData.status === 'pending' && orderData.expires_at) {
        // Update the expires attribute with the new expiration time
        timerElement.setAttribute('data-expires', orderData.expires_at);
        
        // Update the timer display
        updateTimer(timerElement, orderData.expires_at);
    } else if (timerElement && orderData.status !== 'pending') {
        // If order is no longer pending, remove the timer
        timerElement.parentElement.remove();
    }
    
    // Update SMS code display
    const smsCodeCell = orderElement.querySelector('td:nth-child(4)');
    if (smsCodeCell) {
        if (orderData.sms_code) {
            smsCodeCell.innerHTML = `
                <div class="flex items-center space-x-2">
                    <span class="font-mono text-sm">${orderData.sms_code}</span>
                    <button onclick="copyToClipboard('${orderData.sms_code}')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            `;
        } else if (orderData.status === 'cancelled') {
            smsCodeCell.innerHTML = '<span class="text-red-500">Cancelled</span>';
        } else {
            smsCodeCell.innerHTML = '<span class="text-gray-500">Pending...</span>';
        }
    }
    
    // Update mobile card SMS code display
    const mobileSmsElement = orderElement.querySelector('.mobile-sms-code');
    if (mobileSmsElement) {
        if (orderData.sms_code) {
            mobileSmsElement.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="font-mono text-sm">${orderData.sms_code}</span>
                    <button onclick="copyToClipboard('${orderData.sms_code}')" class="text-gray-400 hover:text-gray-600 ml-2">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            `;
        } else if (orderData.status === 'cancelled') {
            mobileSmsElement.innerHTML = '<span class="text-red-500">Cancelled</span>';
        } else {
            mobileSmsElement.innerHTML = '<span class="text-gray-500">Pending...</span>';
        }
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showNotification('SMS code copied to clipboard!', 'success');
    }, function(err) {
        console.error('Could not copy text: ', err);
        showNotification('Failed to copy SMS code', 'error');
    });
}

function refreshNumber(orderId) {
    checkOrderStatus(orderId);
}

function showDetails(orderId) {
    // Redirect to order details page
    window.location.href = `/user/usa/order/${orderId}`;
}

// Timer functionality
let timerIntervals = new Map();

function updateTimer(timerElement, expiresAt) {
    const orderId = timerElement.id.replace('timer-', '');
    
    // Clear existing interval if any
    if (timerIntervals.has(orderId)) {
        clearInterval(timerIntervals.get(orderId));
    }
    
    const interval = setInterval(() => {
        const now = new Date().getTime();
        const expiry = new Date(expiresAt).getTime();
        const distance = expiry - now;
        
        if (distance < 0) {
            clearInterval(interval);
            timerIntervals.delete(orderId);
            timerElement.innerHTML = 'EXPIRED';
            timerElement.className = 'text-red-600 font-bold';
            return;
        }
        
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        timerElement.innerHTML = `${minutes}m ${seconds}s remaining`;
    }, 1000);
    
    timerIntervals.set(orderId, interval);
}

function startAllTimers() {
    const timerElements = document.querySelectorAll('[id^="timer-"]');
    timerElements.forEach(timerElement => {
        const expiresAt = timerElement.getAttribute('data-expires');
        if (expiresAt) {
            updateTimer(timerElement, expiresAt);
        }
    });
}

// Auto-refresh active orders every 30 seconds
let autoRefreshInterval;
function startAutoRefresh() {
    autoRefreshInterval = setInterval(() => {
        const activeOrderElements = document.querySelectorAll('.order-item');
        if (activeOrderElements.length > 0) {
            refreshOrders();
        }
    }, 30000);
}

// Stop auto-refresh when page is not visible
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
    } else {
        startAutoRefresh();
    }
});

// Pure JavaScript Searchable Dropdown
class SearchableDropdown {
    constructor() {
        this.services = @js($services->toArray());
        this.filteredServices = [...this.services];
        this.selectedService = null;
        this.isOpen = false;
        
        this.dropdownButton = document.getElementById('dropdown-button');
        this.dropdownPanel = document.getElementById('dropdown-panel');
        this.dropdownText = document.getElementById('dropdown-text');
        this.dropdownArrow = document.getElementById('dropdown-arrow');
        this.searchInput = document.getElementById('search-input');
        this.optionsContainer = document.getElementById('options-container');
        this.hiddenSelect = document.getElementById('service');
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.renderOptions();
    }
    
    bindEvents() {
        // Toggle dropdown
        this.dropdownButton.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggleDropdown();
        });
        
        // Search functionality
        this.searchInput.addEventListener('input', (e) => {
            this.filterServices(e.target.value);
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!document.getElementById('searchable-dropdown').contains(e.target)) {
                this.closeDropdown();
            }
        });
        
        // Keyboard navigation
        this.searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeDropdown();
            }
        });
    }
    
    toggleDropdown() {
        if (this.isOpen) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }
    
    openDropdown() {
        this.isOpen = true;
        this.dropdownPanel.classList.remove('hidden');
        this.dropdownArrow.style.transform = 'rotate(180deg)';
        this.searchInput.value = '';
        this.filteredServices = [...this.services];
        this.renderOptions();
        setTimeout(() => this.searchInput.focus(), 100);
    }
    
    closeDropdown() {
        this.isOpen = false;
        this.dropdownPanel.classList.add('hidden');
        this.dropdownArrow.style.transform = 'rotate(0deg)';
    }
    
    filterServices(searchTerm) {
        const term = searchTerm.toLowerCase().trim();
        if (term === '') {
            this.filteredServices = [...this.services];
        } else {
            this.filteredServices = this.services.filter(service => 
                service.name.toLowerCase().includes(term)
            );
        }
        this.renderOptions();
    }
    
    selectService(service) {
        this.selectedService = service;
        this.dropdownText.textContent = service.name;
        this.hiddenSelect.value = service.code;
        
        // Trigger change event on hidden select
        const changeEvent = new Event('change', { bubbles: true });
        this.hiddenSelect.dispatchEvent(changeEvent);
        
        // Reset form state
        if (window.selectedService !== undefined) {
            window.selectedService = service.code;
        }
        if (window.servicePrice !== undefined) {
            window.servicePrice = 0;
        }
        if (window.statusChecked !== undefined) {
            window.statusChecked = false;
        }
        
        const statusResult = document.getElementById('status-result');
        if (statusResult) {
            statusResult.innerHTML = '';
        }
        
        this.closeDropdown();
    }
    
    renderOptions() {
        this.optionsContainer.innerHTML = '';
        
        if (this.filteredServices.length === 0) {
            const noResults = document.createElement('div');
            noResults.className = 'px-3 py-2 text-gray-500 text-sm';
            noResults.textContent = this.services.length === 0 ? 'No services available' : 'No services found matching your search';
            this.optionsContainer.appendChild(noResults);
            return;
        }
        
        this.filteredServices.forEach(service => {
            const option = document.createElement('button');
            option.type = 'button';
            option.className = `w-full px-3 py-2 text-left hover:bg-gray-100 focus:bg-gray-100 focus:outline-none block ${
                this.selectedService && this.selectedService.code === service.code 
                    ? 'bg-primary-50 text-primary-700' 
                    : 'text-gray-900'
            }`;
            
            const span = document.createElement('span');
            span.className = 'block truncate';
            span.textContent = service.name;
            option.appendChild(span);
            
            option.addEventListener('click', () => {
                this.selectService(service);
            });
            
            this.optionsContainer.appendChild(option);
        });
    }
}

// Global variables for form state
let selectedService = null;
let servicePrice = 0;
let statusChecked = false;
let isLoading = false;

// Initialize dropdown when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('searchable-dropdown')) {
        new SearchableDropdown();
    }
    
    // Initialize form event listeners
    initializeFormHandlers();
});

// Initialize form handlers
function initializeFormHandlers() {
    const hiddenSelect = document.getElementById('service');
    if (hiddenSelect) {
        hiddenSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.value) {
                selectedService = selectedOption.value;
                servicePrice = parseFloat(selectedOption.dataset.price) || 0;
                statusChecked = false;
                
                updatePriceDisplay();
                updateActionButtons();
                clearStatusResult();
            } else {
                selectedService = null;
                servicePrice = 0;
                statusChecked = false;
                
                hidePriceDisplay();
                hideActionButtons();
                clearStatusResult();
            }
        });
    }
    
    // Check availability button
    const checkBtn = document.getElementById('check-availability-btn');
    if (checkBtn) {
        checkBtn.addEventListener('click', checkAvailability);
    }
    
    // Form submission
    const form = document.getElementById('purchase-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!statusChecked) {
                e.preventDefault();
                showNotification('Please check availability first', 'warning');
            }
        });
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        // Close order menus
        const orderMenus = document.querySelectorAll('[id^="order-menu-"]');
        orderMenus.forEach(menu => {
            if (!menu.contains(e.target) && !e.target.closest('button[onclick^="toggleOrderMenu"]')) {
                menu.classList.add('hidden');
            }
        });
    });
}

// Update price display
function updatePriceDisplay() {
    const priceDisplay = document.getElementById('price-display');
    const priceText = document.getElementById('price-text');
    
    if (servicePrice > 0) {
        priceText.textContent = '₦' + servicePrice.toLocaleString();
        priceDisplay.classList.remove('hidden');
    } else {
        hidePriceDisplay();
    }
}

// Hide price display
function hidePriceDisplay() {
    const priceDisplay = document.getElementById('price-display');
    priceDisplay.classList.add('hidden');
}

// Update action buttons
function updateActionButtons() {
    const actionButtons = document.getElementById('action-buttons');
    const purchaseBtn = document.getElementById('purchase-btn');
    
    if (selectedService) {
        actionButtons.classList.remove('hidden');
        updatePurchaseButton();
    } else {
        hideActionButtons();
    }
}

// Hide action buttons
function hideActionButtons() {
    const actionButtons = document.getElementById('action-buttons');
    actionButtons.classList.add('hidden');
}

// Update purchase button state
function updatePurchaseButton() {
    const purchaseBtn = document.getElementById('purchase-btn');
    
    if (statusChecked && !isLoading) {
        purchaseBtn.disabled = false;
        purchaseBtn.className = 'flex-1 px-4 py-2 rounded-lg transition-colors bg-primary-600 hover:bg-primary-700 text-white';
    } else {
        purchaseBtn.disabled = true;
        purchaseBtn.className = 'flex-1 px-4 py-2 rounded-lg transition-colors bg-gray-300 text-gray-500 cursor-not-allowed';
    }
}

// Update loading state
function updateLoadingState(loading) {
    isLoading = loading;
    const checkBtn = document.getElementById('check-availability-btn');
    const checkIcon = document.getElementById('check-icon');
    const checkText = document.getElementById('check-text');
    
    if (loading) {
        checkBtn.disabled = true;
        checkBtn.className = 'flex-1 text-gray-700 px-4 py-2 rounded-lg transition-colors bg-gray-300 cursor-not-allowed';
        checkIcon.className = 'fas fa-spinner fa-spin mr-2';
        checkText.textContent = 'Checking...';
    } else {
        checkBtn.disabled = false;
        checkBtn.className = 'flex-1 text-gray-700 px-4 py-2 rounded-lg transition-colors bg-gray-100 hover:bg-gray-200';
        checkIcon.className = 'fas fa-search mr-2';
        checkText.textContent = 'Check Availability';
    }
    
    updatePurchaseButton();
}

// Clear status result
function clearStatusResult() {
    const statusResult = document.getElementById('status-result');
    if (statusResult) {
        statusResult.innerHTML = '';
    }
}

// Check availability function
function checkAvailability() {
    if (!selectedService || isLoading) return;
    
    updateLoadingState(true);
    statusChecked = false;
    clearStatusResult();
    
    fetch('{{ route("usa.check-availability") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            service: selectedService
        })
    })
    .then(response => {
        return response.json();
    })
    .then(data => {
        updateLoadingState(false);
        
        const statusResult = document.getElementById('status-result');
        if (data.available) {
            statusChecked = true;
            statusResult.innerHTML = `
                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span class="text-green-800 font-medium">Available</span>
                    </div>
                    <p class="text-green-700 text-sm mt-1">${data.message}</p>
                    ${data.price ? `<p class="text-green-600 text-xs mt-1">Price: ₦${data.price.toLocaleString()}</p>` : ''}
                </div>
            `;
            showNotification('Service is available!', 'success');
        } else {
            statusChecked = false;
            statusResult.innerHTML = `
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-times-circle text-red-500 mr-2"></i>
                        <span class="text-red-800 font-medium">Not Available</span>
                    </div>
                    <p class="text-red-700 text-sm mt-1">${data.message}</p>
                </div>
            `;
            showNotification('Service is not available', 'error');
        }
        
        updatePurchaseButton();
    })
    .catch(error => {
        updateLoadingState(false);
        
        const statusResult = document.getElementById('status-result');
        statusResult.innerHTML = `
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    <span class="text-red-800 font-medium">Error</span>
                </div>
                <p class="text-red-700 text-sm mt-1">Failed to check availability. Please try again.</p>
            </div>
        `;
        showNotification('Failed to check availability', 'error');
    });
}

// Toggle order menu
function toggleOrderMenu(orderId) {
    const menu = document.getElementById(`order-menu-${orderId}`);
    const allMenus = document.querySelectorAll('[id^="order-menu-"]');
    
    // Close all other menus
    allMenus.forEach(otherMenu => {
        if (otherMenu.id !== `order-menu-${orderId}`) {
            otherMenu.classList.add('hidden');
        }
    });
    
    // Toggle current menu
    menu.classList.toggle('hidden');
}

// Enhanced notification function
function showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full ${
        type === 'success' ? 'bg-green-100 border border-green-200 text-green-800' :
        type === 'error' ? 'bg-red-100 border border-red-200 text-red-800' :
        type === 'warning' ? 'bg-yellow-100 border border-yellow-200 text-yellow-800' :
        'bg-blue-100 border border-blue-200 text-blue-800'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
                type === 'success' ? 'fa-check-circle' :
                type === 'error' ? 'fa-times-circle' :
                type === 'warning' ? 'fa-exclamation-triangle' :
                'fa-info-circle'
            } mr-2"></i>
            <span class="flex-1">${message}</span>

            
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, duration);
}

// Initialize auto-refresh and timers on page load
document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();
    startAllTimers();
});

// Security: Clear sensitive data on page unload
window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
    
    // Clear all timer intervals
    timerIntervals.forEach(interval => clearInterval(interval));
    timerIntervals.clear();
    
    activeStatusChecks.clear();
});
</script>
@endsection