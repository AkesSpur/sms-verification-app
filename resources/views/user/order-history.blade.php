@extends('layouts.user')

@section('title', 'Order History')

@push('styles')
<style>
    /* Custom styles for log content display */
    #logItemContent img {
        max-width: 100%;
        height: auto;
        border-radius: 0.375rem;
        margin: 0.5rem 0;
    }
    
    #logItemContent p {
        margin-bottom: 0.75rem;
    }
    
    #logItemContent ul, #logItemContent ol {
        margin: 0.5rem 0;
        padding-left: 1.5rem;
    }
    
    #logItemContent li {
        margin-bottom: 0.25rem;
    }
    
    #logItemContent h1, #logItemContent h2, #logItemContent h3, 
    #logItemContent h4, #logItemContent h5, #logItemContent h6 {
        font-weight: 600;
        margin: 1rem 0 0.5rem 0;
    }
    
    #logItemContent h1 { font-size: 1.5rem; }
    #logItemContent h2 { font-size: 1.25rem; }
    #logItemContent h3 { font-size: 1.125rem; }
    
    #logItemContent strong, #logItemContent b {
        font-weight: 600;
    }
    
    #logItemContent em, #logItemContent i {
        font-style: italic;
    }
    
    #logItemContent a {
        color: #2563eb;
        text-decoration: underline;
    }
    
    #logItemContent a:hover {
        color: #1d4ed8;
    }
    
    #logItemContent blockquote {
        border-left: 4px solid #e5e7eb;
        padding-left: 1rem;
        margin: 1rem 0;
        font-style: italic;
        color: #6b7280;
    }
    
    #logItemContent pre {
        background-color: #f3f4f6;
        padding: 1rem;
        border-radius: 0.375rem;
        overflow-x: auto;
        margin: 0.5rem 0;
    }
    
    #logItemContent code {
        background-color: #f3f4f6;
        padding: 0.125rem 0.25rem;
        border-radius: 0.25rem;
        font-family: 'Courier New', monospace;
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Order History</h1>
                <p class="text-gray-600 mt-1">View all your SMS verifications, logs, and gift orders</p>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-lg shadow-sm" id="orderHistoryContainer">
        <div class="border-b border-gray-200 px-4">
            <nav class="flex space-x-4  overflow-x-auto scrollbar-hide" aria-label="Tabs" style="scrollbar-width: none; -ms-overflow-style: none;">
                <button onclick="switchTab('sms')" id="sms-tab"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors border-primary-500 text-primary-600">
                    <i class="fas fa-sms mr-2"></i>SMS Orders
                </button>
                <button onclick="switchTab('logs')" id="logs-tab"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-list-alt mr-2"></i>Log Orders
                </button>
                <button onclick="switchTab('gifts')" id="gifts-tab"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-gift mr-2"></i>Gift Orders
                </button>
            </nav>
            <style>
                .scrollbar-hide::-webkit-scrollbar {
                    display: none;
                }
            </style>
        </div>

        <!-- SMS Orders Tab -->
        <div id="sms-content" class="p-6">
            <div class="space-y-4">
                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-4 mb-6">
                    <div class="flex-1">
                        <input type="text" placeholder="Search by number or service..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div class="flex gap-2">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option>All Status</option>
                            <option>Completed</option>
                            <option>Pending</option>
                            <option>Failed</option>
                        </select>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option>All Countries</option>
                            <option>USA</option>
                            <option>UK</option>
                            <option>Canada</option>
                        </select>
                    </div>
                </div>

                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SMS Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($smsOrders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#SMS{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->phone_number ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $order->country->flag ?? '🌍' }} {{ $order->country->name ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $order->service->name ?? 'Unknown Service' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'completed' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'active' => 'bg-blue-100 text-blue-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'expired' => 'bg-gray-100 text-gray-800',
                                            'failed' => 'bg-red-100 text-red-800'
                                        ];
                                        $statusClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                    {{ $order->sms_code ?? ($order->status === 'completed' ? 'N/A' : 'Waiting...') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${{ number_format($order->final_price ?? $order->price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($order->sms_code)
                                        <button class="text-primary-600 hover:text-primary-900 mr-3" onclick="copyToClipboard('{{ $order->sms_code }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    @endif
                                    @if($order->canBeCancelled())
                                        <button class="text-red-600 hover:text-red-900" onclick="cancelOrder({{ $order->id }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @else
                                        <button class="text-blue-600 hover:text-blue-900" onclick="viewOrder({{ $order->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                    No SMS orders found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden space-y-4">
                    @forelse($smsOrders as $order)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium text-gray-500">#SMS{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</span>
                                <span class="text-lg">{{ $order->country->flag ?? '🌍' }}</span>
                                <span class="font-medium text-gray-900">{{ $order->phone_number ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                            <div>
                                <span class="text-gray-500">Service:</span>
                                <span class="ml-1 font-medium">{{ $order->service->name ?? 'Unknown Service' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">SMS Code:</span>
                                <span class="ml-1 font-mono font-medium">{{ $order->sms_code ?? ($order->status === 'completed' ? 'N/A' : 'Waiting...') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Price:</span>
                                <span class="ml-1 font-medium">${{ number_format($order->final_price ?? $order->price, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-gray-500">Date:</span>
                                    <span class="ml-1">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                                </div>
                                @php
                                    $statusColors = [
                                        'completed' => 'bg-green-100 text-green-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'active' => 'bg-blue-100 text-blue-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'expired' => 'bg-gray-100 text-gray-800',
                                        'failed' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            @if($order->sms_code)
                                <button class="flex-1 bg-primary-100 text-primary-700 px-3 py-2 rounded-lg text-sm hover:bg-primary-200 transition-colors" onclick="copyToClipboard('{{ $order->sms_code }}')">
                                    <i class="fas fa-copy mr-1"></i>Copy Code
                                </button>
                            @endif
                            @if($order->canBeCancelled())
                                <button class="flex-1 bg-red-100 text-red-700 px-3 py-2 rounded-lg text-sm hover:bg-red-200 transition-colors" onclick="cancelOrder({{ $order->id }})">
                                    <i class="fas fa-times mr-1"></i>Cancel
                                </button>
                            @else
                                <button class="flex-1 bg-blue-100 text-blue-700 px-3 py-2 rounded-lg text-sm hover:bg-blue-200 transition-colors" onclick="viewOrder({{ $order->id }})">
                                    <i class="fas fa-eye mr-1"></i>View
                                </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="bg-gray-50 rounded-lg p-4 text-center text-gray-500">
                        No SMS orders found.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Digital Products Tab -->
        <div id="logs-content" class="p-6" style="display: none;">
            <div class="space-y-4">
                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-4 mb-6">
                    <div class="flex-1">
                        <input type="text" placeholder="Search digital products..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div class="flex gap-2">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option>All Products</option>
                            <option>Gift Cards</option>
                            <option>VPN Access</option>
                            <option>Digital Services</option>
                        </select>
                    </div>
                </div>

                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($digitalProducts as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-sm text-gray-900">#ORD{{ $product['order_id'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-box text-blue-600 text-sm"></i>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $product['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₦{{ number_format($product['amount'], 0) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product['status'] == 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                    @elseif($product['status'] == 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Failed</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product['created_at']->format('Y-m-d H:i:s') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openLogModal('{{ $product['id'] }}', '{{ addslashes($product['name']) }}', '{{ addslashes($product['details']) }}', '{{ addslashes($product['full_log_item'] ?? '') }}')" 
                                            class="text-primary-600 hover:text-primary-900 bg-primary-50 hover:bg-primary-100 px-3 py-1 rounded-md transition-colors mr-3">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    @if($product['status'] == 'completed' && $product['full_log_item'])
                                    <button onclick="copyToClipboard('{{ addslashes($product['full_log_item']) }}')" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <i class="fas fa-box-open text-gray-300 text-4xl mb-4"></i>
                                        <p class="text-lg font-medium text-gray-400">No digital product orders found</p>
                                        <p class="text-sm text-gray-400 mt-1">Your digital product purchases will appear here</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden space-y-4">
                    @forelse($digitalProducts as $product)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-box text-blue-600 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-900">{{ $product['name'] }}</span>
                            </div>
                            
                            <span class="text-sm font-medium text-gray-500">#ORD{{ $product['order_id'] }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                            <div>
                                <span class="text-gray-500">Type:</span>
                                <span class="ml-1 font-medium">{{ $product['type'] }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Amount:</span>
                                <span class="ml-1 font-medium">₦{{ number_format($product['amount'], 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center col-span-2">
                                <div>
                                    <span class="text-gray-500">Date:</span>
                                    <span class="ml-1">{{ $product['created_at']->format('Y-m-d H:i:s') }}</span>
                                </div>                                                                                                
                                @if($product['status'] == 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                            @elseif($product['status'] == 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Failed</span>
                            @endif
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="openLogModal('{{ $product['id'] }}', '{{ addslashes($product['name']) }}', '{{ addslashes($product['details']) }}', '{{ addslashes($product['full_log_item'] ?? '') }}')" 
                                    class="flex-1 bg-primary-100 text-primary-700 px-3 py-2 rounded-lg text-sm hover:bg-primary-200 transition-colors">
                                <i class="fas fa-eye mr-1"></i>View
                            </button>
                            @if($product['status'] == 'completed' && $product['full_log_item'])
                            <button onclick="copyToClipboard('{{ addslashes($product['full_log_item']) }}')" class="flex-1 bg-blue-100 text-blue-700 px-3 py-2 rounded-lg text-sm hover:bg-blue-200 transition-colors">
                                <i class="fas fa-copy mr-1"></i>Copy
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="bg-gray-50 rounded-lg p-8 text-center">
                        <i class="fas fa-box-open text-gray-300 text-4xl mb-4"></i>
                        <p class="text-lg font-medium text-gray-400">No digital product orders found</p>
                        <p class="text-sm text-gray-400 mt-1">Your digital product purchases will appear here</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Digital Products Pagination -->
            @if($digitalProducts->hasPages())
            <div class="mt-6">
                <div class="flex items-center justify-between">
                    <div class="hidden sm:block text-sm text-gray-700">
                        Showing <span class="font-medium">{{ $digitalProducts->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $digitalProducts->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $digitalProducts->total() }}</span> results
                    </div>
                    <div class="flex flex-1 justify-between sm:justify-end space-x-2">
                        {{-- Previous Page Link --}}
                        @if ($digitalProducts->onFirstPage())
                            <span class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg opacity-50 cursor-not-allowed">
                                Previous
                            </span>
                        @else
                            <a href="{{ $digitalProducts->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Previous
                            </a>
                        @endif

                        {{-- Pagination Elements - Only visible on desktop --}}
                        <div class="hidden sm:flex items-center space-x-2">
                            @php
                                $currentPage = $digitalProducts->currentPage();
                                $lastPage = $digitalProducts->lastPage();
                                $window = 2; // Number of pages to show on each side of current page
                            @endphp

                            {{-- First Page --}}
                            @if($lastPage > 5)
                                <a href="{{ $digitalProducts->url(1) }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 {{ $currentPage == 1 ? 'bg-primary-600 text-white border-transparent' : '' }}">
                                    1
                                </a>
                                
                                {{-- Left Ellipsis --}}
                                @if($currentPage > ($window + 2))
                                    <span class="px-2 py-2 text-gray-500">...</span>
                                @endif
                            @endif

                            {{-- Page Window --}}
                            @foreach(range(max(2, $currentPage - $window), min($lastPage - 1, $currentPage + $window)) as $page)
                                @if($page > 1 && $page < $lastPage)
                                    <a href="{{ $digitalProducts->url($page) }}" class="px-3 py-2 text-sm font-medium {{ $page == $currentPage ? 'text-white bg-primary-600 border-transparent' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50' }} rounded-lg">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            {{-- Last Page --}}
                            @if($lastPage > 5)
                                {{-- Right Ellipsis --}}
                                @if($currentPage < ($lastPage - $window - 1))
                                    <span class="px-2 py-2 text-gray-500">...</span>
                                @endif

                                <a href="{{ $digitalProducts->url($lastPage) }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 {{ $currentPage == $lastPage ? 'bg-primary-600 text-white border-transparent' : '' }}">
                                    {{ $lastPage }}
                                </a>
                            @endif
                        </div>

                        {{-- Next Page Link --}}
                        @if ($digitalProducts->hasMorePages())
                            <a href="{{ $digitalProducts->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Next
                            </a>
                        @else
                            <span class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg opacity-50 cursor-not-allowed">
                                Next
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Gift Orders Tab -->
        <div id="gifts-content" class="p-6" style="display: none;">
            <div class="space-y-4">
                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-4 mb-6">
                    <div class="flex-1">
                        <input type="text" placeholder="Search gift orders..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div class="flex gap-2">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option>All Status</option>
                            <option>Delivered</option>
                            <option>Processing</option>
                            <option>Shipped</option>
                        </select>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option>All Categories</option>
                            <option>Flowers</option>
                            <option>Electronics</option>
                            <option>Gift Cards</option>
                        </select>
                    </div>
                </div>

                @if($giftOrders && count($giftOrders) > 0)
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gift Item</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($giftOrders as $gift)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#GIFT{{ $gift['id'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-{{ $gift['icon'] ?? 'gift' }} text-pink-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $gift['item_name'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $gift['item_description'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $gift['recipient'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₦{{ number_format($gift['amount']) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($gift['status'] == 'delivered') bg-green-100 text-green-800
                                        @elseif($gift['status'] == 'processing') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($gift['status']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $gift['created_at']->format('M d, Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openGiftModal('{{ $gift['id'] }}', '{{ addslashes($gift['item_name']) }}', '{{ addslashes($gift['item_description']) }}', '{{ addslashes($gift['recipient']) }}', '{{ $gift['tracking_code'] ?? ($gift['status'] == 'cancelled' ? 'Order has been cancelled - no tracking available' : 'Order is still pending - tracking info will be available soon') }}', '{{ $gift['status'] }}', '{{ number_format($gift['amount']) }}', '{{ $gift['created_at']->format('M d, Y H:i') }}', '{{ addslashes($gift['notes'] ?? '') }}')" 
                                            class="text-primary-600 hover:text-primary-900 bg-primary-50 hover:bg-primary-100 px-3 py-1 rounded-md transition-colors">
                                        View
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden space-y-4">
                    @foreach($giftOrders as $gift)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-{{ $gift['icon'] ?? 'gift' }} text-pink-600 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-900">{{ $gift['item_name'] }}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-500">#GIFT{{ $gift['id'] }}</span>                                
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                            <div>
                                <span class="text-gray-500">Recipient:</span>
                                <span class="ml-1 font-medium">{{ $gift['recipient'] }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Amount:</span>
                                <span class="ml-1 font-medium">₦{{ number_format($gift['amount']) }}</span>
                            </div>
                            <div class="flex justify-between items-center col-span-2">
                                <div>
                                    <span class="text-gray-500">Date:</span>
                                    <span class="ml-1">{{ $gift['created_at']->format('M d, Y H:i') }}</span>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($gift['status'] == 'delivered') bg-green-100 text-green-800
                                    @elseif($gift['status'] == 'processing') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($gift['status']) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="openGiftModal('{{ $gift['id'] }}', '{{ addslashes($gift['item_name']) }}', '{{ addslashes($gift['item_description']) }}', '{{ addslashes($gift['recipient']) }}', '{{ $gift['tracking_code'] ?? ($gift['status'] == 'cancelled' ? 'Order has been cancelled - no tracking available' : 'Order is still pending - tracking info will be available soon') }}', '{{ $gift['status'] }}', '{{ number_format($gift['amount']) }}', '{{ $gift['created_at']->format('M d, Y H:i') }}', '{{ addslashes($gift['notes'] ?? '') }}')"
                                    class="flex-1 bg-blue-100 text-blue-700 px-3 py-2 rounded-lg text-sm hover:bg-blue-200 transition-colors">
                                <i class="fas fa-eye mr-1"></i>View
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="mb-4">
                        <i class="fas fa-gift text-gray-300" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No Gift Orders Yet</h3>
                    <p class="text-gray-500 mb-6">You haven't placed any gift orders yet. Start spreading joy by sending gifts to your loved ones!</p>
                    <a href="{{ route('all-gifts') }}" class="inline-flex items-center px-6 py-3 bg-pink-600 text-white font-medium rounded-lg hover:bg-pink-700 transition-colors">
                        <i class="fas fa-heart mr-2"></i>
                        Browse Gifts
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($smsOrders->hasPages())
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div class="hidden sm:block text-sm text-gray-700">
                Showing <span class="font-medium">{{ $smsOrders->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $smsOrders->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $smsOrders->total() }}</span> results
            </div>
            <div class="flex flex-1 justify-between sm:justify-end space-x-2">
                {{-- Previous Page Link --}}
                @if ($smsOrders->onFirstPage())
                    <span class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg opacity-50 cursor-not-allowed">
                        Previous
                    </span>
                @else
                    <a href="{{ $smsOrders->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Previous
                    </a>
                @endif

                {{-- Pagination Elements - Only visible on desktop --}}
                <div class="hidden sm:flex items-center space-x-2">
                    @php
                        $currentPage = $smsOrders->currentPage();
                        $lastPage = $smsOrders->lastPage();
                        $window = 2; // Number of pages to show on each side of current page
                    @endphp

                    {{-- First Page --}}
                    @if($lastPage > 5)
                        <a href="{{ $smsOrders->url(1) }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 {{ $currentPage == 1 ? 'bg-primary-600 text-white border-transparent' : '' }}">
                            1
                        </a>
                        
                        {{-- Left Ellipsis --}}
                        @if($currentPage > ($window + 2))
                            <span class="px-2 py-2 text-gray-500">...</span>
                        @endif
                    @endif

                    {{-- Page Window --}}
                    @foreach(range(max(2, $currentPage - $window), min($lastPage - 1, $currentPage + $window)) as $page)
                        @if($page > 1 && $page < $lastPage)
                            <a href="{{ $smsOrders->url($page) }}" class="px-3 py-2 text-sm font-medium {{ $page == $currentPage ? 'text-white bg-primary-600 border-transparent' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50' }} rounded-lg">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    {{-- Last Page --}}
                    @if($lastPage > 5)
                        {{-- Right Ellipsis --}}
                        @if($currentPage < ($lastPage - $window - 1))
                            <span class="px-2 py-2 text-gray-500">...</span>
                        @endif

                        <a href="{{ $smsOrders->url($lastPage) }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 {{ $currentPage == $lastPage ? 'bg-primary-600 text-white border-transparent' : '' }}">
                            {{ $lastPage }}
                        </a>
                    @endif
                </div>

                {{-- Next Page Link --}}
                @if ($smsOrders->hasMorePages())
                    <a href="{{ $smsOrders->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Next
                    </a>
                @else
                    <span class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg opacity-50 cursor-not-allowed">
                        Next
                    </span>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Log Modal -->
    <div id="logModal" style="display: none;" class="z-50">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 w-full h-full" style="z-index: 999;"></div>
        <div class="fixed inset-0 flex items-center justify-center" style="z-index: 1000;" onclick="closeLogModal()">
            <div class="relative mx-auto p-6 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-medium text-gray-900">Digital Product Details</h3>
                        <button onclick="closeLogModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                            <p class="text-base text-gray-900 font-medium" id="logProductName"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Details</label>
                            <p class="text-sm text-gray-900" id="logProductDetails"></p>
                        </div>
                        <div id="logItemSection">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Product Content</label>
                            <div class="border border-gray-300 rounded-lg p-4 bg-gray-50 max-h-96 overflow-y-auto">
                                <div id="logItemContent" class="prose prose-sm max-w-none"></div>
                            </div>
                            <div class="mt-3 flex justify-end">
                                 <button onclick="copyLogItemToClipboard(event)" 
                                         class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors text-sm">
                                     <i class="fas fa-copy mr-2"></i>Copy Content
                                 </button>
                             </div>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end space-x-3">
                        <button onclick="closeLogModal()" 
                                class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gift Modal -->
    <div id="giftModal" style="display: none;" class="z-50">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 w-full h-full" style="z-index: 999;"></div>
        <div class="fixed inset-0 flex items-center justify-center" style="z-index: 1000;" onclick="closeGiftModal()">
            <div class="relative mx-auto p-6 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-medium text-gray-900">Gift Order Details</h3>
                        <button onclick="closeGiftModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Gift Item</label>
                                <p class="text-base text-gray-900 font-medium" id="giftItemName"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                                <p class="text-base text-gray-900 font-medium" id="giftAmount"></p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <p class="text-sm text-gray-900" id="giftItemDescription"></p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Recipient</label>
                                <p class="text-sm text-gray-900" id="giftRecipient"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Order Date</label>
                                <p class="text-sm text-gray-900" id="giftOrderDate"></p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tracking Information</label>
                            <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Tracking Code</p>
                                        <p class="text-lg font-mono text-gray-900" id="giftTrackingCode"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 mb-2">Status</p>
                                        <span id="giftStatus" class="inline-flex px-3 py-1 text-sm font-semibold rounded-full"></span>
                                    </div>
                                </div>
                                <div class="mt-3 flex justify-end">
                                    <button onclick="copyGiftTrackingToClipboard(event)" 
                                            class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors text-sm">
                                        <i class="fas fa-copy mr-2"></i>Copy Tracking Code
                                    </button>
                                </div>

                                <div id="giftNotesSection" style="display: none;">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Order Notes</label>
                                    <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                                        <div id="giftNotes" class="text-sm text-gray-900"></div>
                                    </div>
                                </div>
                                
                                <!-- Notes Section -->
                                <div id="giftNotesContainer" style="display: none;">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Order Notes</label>
                                    <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                                        <div id="giftNotesContent" class="text-sm text-gray-900"></div>
                                    </div>
                                </div>
        
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end space-x-3">
                        <button onclick="closeGiftModal()" 
                                class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab switching functionality
    function switchTab(tabName) {
        // Hide all tab contents
        document.getElementById('sms-content').style.display = 'none';
        document.getElementById('logs-content').style.display = 'none';
        document.getElementById('gifts-content').style.display = 'none';
        
        // Remove active classes from all tabs
        document.getElementById('sms-tab').className = 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
        document.getElementById('logs-tab').className = 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
        document.getElementById('gifts-tab').className = 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
        
        // Show selected tab content and add active class
        document.getElementById(tabName + '-content').style.display = 'block';
        document.getElementById(tabName + '-tab').className = 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors border-primary-500 text-primary-600';
    }
    
    // Log modal functions
    function openLogModal(id, name, details, fullLogItem) {
        document.getElementById('logProductName').textContent = name;
        document.getElementById('logProductDetails').textContent = details || 'No additional details provided';
        
        // Display HTML content from log_item
        const logItemContent = document.getElementById('logItemContent');
        if (fullLogItem && fullLogItem.trim() != '') {
            logItemContent.innerHTML = fullLogItem;
        } else {
            logItemContent.innerHTML = '<p class="text-gray-500 italic">No content available</p>';
        }
        
        document.getElementById('logModal').style.display = 'block';
    }
    
    // Copy log item content to clipboard
    function copyLogItemToClipboard(event) {
        const logItemContent = document.getElementById('logItemContent');
        const textContent = logItemContent.innerText || logItemContent.textContent || '';
        
        if (textContent.trim() == '' || textContent == 'No content available') {
            alert('No content to copy');
            return;
        }
        
        navigator.clipboard.writeText(textContent).then(function() {
            // Show success feedback
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
            button.classList.remove('bg-primary-600', 'hover:bg-primary-700');
            button.classList.add('bg-green-600', 'hover:bg-green-700');
            
            setTimeout(function() {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-600', 'hover:bg-green-700');
                button.classList.add('bg-primary-600', 'hover:bg-primary-700');
            }, 2000);
        }).catch(function(err) {
            console.error('Could not copy text: ', err);
            alert('Failed to copy content');
        });
    }
    
    function closeLogModal() {
        document.getElementById('logModal').style.display = 'none';
    }
    
    // HTML sanitization function
    function sanitizeHtml(html) {
        if (!html) return '';
        
        // Create a temporary div to parse HTML
        const temp = document.createElement('div');
        temp.innerHTML = html;
        
        // Define allowed tags and attributes
        const allowedTags = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'span', 'div'];
        const allowedAttributes = ['class', 'style'];
        
        // Function to clean element recursively
        function cleanElement(element) {
            // Remove script tags and their content
            const scripts = element.querySelectorAll('script');
            scripts.forEach(script => script.remove());
            
            // Process all elements
            const allElements = element.querySelectorAll('*');
            allElements.forEach(el => {
                // Remove disallowed tags
                if (!allowedTags.includes(el.tagName.toLowerCase())) {
                    el.replaceWith(...el.childNodes);
                    return;
                }
                
                // Remove disallowed attributes
                Array.from(el.attributes).forEach(attr => {
                    if (!allowedAttributes.includes(attr.name.toLowerCase())) {
                        el.removeAttribute(attr.name);
                    }
                });
                
                // Remove javascript: and data: URLs
                if (el.hasAttribute('href')) {
                    const href = el.getAttribute('href');
                    if (href.startsWith('javascript:') || href.startsWith('data:')) {
                        el.removeAttribute('href');
                    }
                }
            });
        }
        
        cleanElement(temp);
        return temp.innerHTML;
    }
    
    // Gift modal functions
    function openGiftModal(id, name, description, recipient, trackingCode, status, amount, orderDate, notes) {
        document.getElementById('giftItemName').textContent = name;
        document.getElementById('giftItemDescription').textContent = description || 'No description provided';
        document.getElementById('giftRecipient').textContent = recipient;
        
        // Handle tracking code with HTML sanitization
        const trackingElement = document.getElementById('giftTrackingCode');
        trackingElement.innerHTML = sanitizeHtml(trackingCode);
        
        // Handle notes display with HTML sanitization
        const notesContainer = document.getElementById('giftNotesContainer');
        const notesContent = document.getElementById('giftNotesContent');
        
        if (notes && notes.trim() != '') {
            notesContainer.style.display = 'block';
            notesContent.innerHTML = sanitizeHtml(notes);
        } else {
            notesContainer.style.display = 'block';
            notesContent.innerHTML = '<em class="text-gray-500">No additional notes available for this order.</em>';
        }
        document.getElementById('giftAmount').textContent = '₦' + amount;
        document.getElementById('giftOrderDate').textContent = orderDate;
        
        // Set status with appropriate styling
        const statusElement = document.getElementById('giftStatus');
        statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        
        // Remove existing status classes
        statusElement.className = 'inline-flex px-3 py-1 text-sm font-semibold rounded-full';
        
        // Add appropriate status class
        if (status == 'delivered') {
            statusElement.className += ' bg-green-100 text-green-800';
        } else if (status == 'processing') {
            statusElement.className += ' bg-yellow-100 text-yellow-800';
        } else {
            statusElement.className += ' bg-red-100 text-red-800';
        }
        
        document.getElementById('giftModal').style.display = 'block';
    }
    
    // Copy gift tracking code to clipboard
    function copyGiftTrackingToClipboard(event) {
        const trackingCode = document.getElementById('giftTrackingCode').textContent;
        
        if (!trackingCode || trackingCode.trim() == '') {
            alert('No tracking code to copy');
            return;
        }
        
        navigator.clipboard.writeText(trackingCode).then(function() {
            // Show success feedback
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
            button.classList.remove('bg-primary-600', 'hover:bg-primary-700');
            button.classList.add('bg-green-600', 'hover:bg-green-700');
            
            setTimeout(function() {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-600', 'hover:bg-green-700');
                button.classList.add('bg-primary-600', 'hover:bg-primary-700');
            }, 2000);
        }).catch(function(err) {
            console.error('Could not copy tracking code: ', err);
            alert('Failed to copy tracking code');
        });
    }
    
    function closeGiftModal() {
        document.getElementById('giftModal').style.display = 'none';
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
            alert('Copied to clipboard!');
        }).catch(err => {
            console.error('Failed to copy: ', err);
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = cleanText;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            alert('Copied to clipboard!');
        });
    }
</script>
@endsection