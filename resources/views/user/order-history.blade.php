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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#SMS001</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">+1 555 123 4567</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">🇺🇸 USA</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">WhatsApp</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">123456</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-01-15 14:30</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-primary-600 hover:text-primary-900 mr-3" onclick="copyToClipboard('123456')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#SMS002</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">+44 7700 900123</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">🇬🇧 UK</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Telegram</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Waiting...</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-01-15 15:45</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-primary-600 hover:text-primary-900 mr-3">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium text-gray-500">#SMS001</span>
                                <span class="text-lg">🇺🇸</span>
                                <span class="font-medium text-gray-900">+1 555 123 4567</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                            <div>
                                <span class="text-gray-500">Service:</span>
                                <span class="ml-1 font-medium">WhatsApp</span>
                            </div>
                            <div>
                                <span class="text-gray-500">SMS Code:</span>
                                <span class="ml-1 font-mono font-medium">123456</span>
                            </div>
                            <div class="flex justify-between items-center col-span-2">
                                <div>
                                    <span class="text-gray-500">Date:</span>
                                    <span class="ml-1">2024-01-15 14:30</span>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="flex-1 bg-primary-100 text-primary-700 px-3 py-2 rounded-lg text-sm hover:bg-primary-200 transition-colors" onclick="copyToClipboard('123456')">
                                <i class="fas fa-copy mr-1"></i>Copy Code
                            </button>
                            <button class="flex-1 bg-blue-100 text-blue-700 px-3 py-2 rounded-lg text-sm hover:bg-blue-200 transition-colors">
                                <i class="fas fa-eye mr-1"></i>View
                            </button>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium text-gray-500">#SMS002 </span>
                                <span class="text-lg">🇬🇧</span>
                                <span class="font-medium text-gray-900">+44 7700 900123</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                            <div>
                                <span class="text-gray-500">Service:</span>
                                <span class="ml-1 font-medium">Telegram</span>
                            </div>
                            <div>
                                <span class="text-gray-500">SMS Code:</span>
                                <span class="ml-1 text-gray-500">Waiting...</span>
                            </div>
                            <div class="flex justify-between items-center col-span-2">
                                <div>
                                    <span class="text-gray-500">Date:</span>
                                    <span class="ml-1">2024-01-15 15:45</span>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="flex-1 bg-primary-100 text-primary-700 px-3 py-2 rounded-lg text-sm hover:bg-primary-200 transition-colors">
                                <i class="fas fa-sync-alt mr-1"></i>Refresh
                            </button>
                            <button class="flex-1 bg-red-100 text-red-700 px-3 py-2 rounded-lg text-sm hover:bg-red-200 transition-colors">
                                <i class="fas fa-trash mr-1"></i>Cancel
                            </button>
                        </div>
                    </div>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $gift['id'] }}</td>
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
                                    <button onclick="openGiftModal('{{ $gift['id'] }}', '{{ $gift['item_name'] }}', '{{ $gift['item_description'] }}', '{{ $gift['recipient'] }}', '{{ $gift['tracking_code'] ?? 'TRK' . strtoupper(substr(md5($gift['id']), 0, 8)) }}', '{{ $gift['status'] }}')" 
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
                                <span class="text-sm font-medium text-gray-500">#{{ str_pad($gift['id'], 3, '0', STR_PAD_LEFT) }}🎁</span>                                
                                <span class="font-medium text-gray-900">{{ $gift['item_name'] }}</span>
                            </div>
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
                            <button onclick="openGiftModal('{{ $gift['id'] }}', '{{ $gift['item_name'] }}', '{{ $gift['item_description'] }}', '{{ $gift['recipient'] }}', '{{ $gift['tracking_code'] ?? 'TRK' . strtoupper(substr(md5($gift['id']), 0, 8)) }}', '{{ $gift['status'] }}')" 
                                    class="flex-1 bg-blue-100 text-blue-700 px-3 py-2 rounded-lg text-sm hover:bg-blue-200 transition-colors">
                                <i class="fas fa-eye mr-1"></i>View
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">97</span> results
            </div>
            <div class="flex items-center space-x-2">
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50" disabled>
                    Previous
                </button>
                <button class="px-3 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700">
                    1
                </button>
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    2
                </button>
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    3
                </button>
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Next
                </button>
            </div>
        </div>
    </div>

    <!-- Log Modal -->
    <div id="logModal" style="display: none;" class="z-50">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 w-full h-full" style="z-index: 49;"></div>
        <div class="fixed inset-0 flex items-center justify-center z-50" onclick="closeLogModal()">
            <div class="relative mx-auto p-6 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white" onclick="event.stopPropagation()">
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
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 w-full h-full" style="z-index: 49;"></div>
        <div class="fixed inset-0 flex items-center justify-center z-50" onclick="closeGiftModal()">
            <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" onclick="event.stopPropagation()">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Gift Details</h3>
                        <button onclick="closeGiftModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gift Item</label>
                            <p class="text-sm text-gray-900" id="giftItemName"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <p class="text-sm text-gray-900" id="giftItemDescription"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Recipient</label>
                            <p class="text-sm text-gray-900" id="giftRecipient"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tracking Code</label>
                            <div class="flex items-center space-x-2">
                                <input type="text" id="giftTrackingCode" readonly 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm">
                                <button onclick="copyToClipboard(document.getElementById('giftTrackingCode').value)" 
                                        class="px-3 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <span id="giftStatus" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"></span>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button onclick="closeGiftModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
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
    
    // Gift modal functions
    function openGiftModal(id, name, description, recipient, trackingCode, status) {
        document.getElementById('giftItemName').textContent = name;
        document.getElementById('giftItemDescription').textContent = description;
        document.getElementById('giftRecipient').textContent = recipient;
        document.getElementById('giftTrackingCode').value = trackingCode;
        
        // Set status with appropriate styling
        const statusElement = document.getElementById('giftStatus');
        statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        
        // Remove existing status classes
        statusElement.className = 'inline-flex px-2 py-1 text-xs font-semibold rounded-full';
        
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