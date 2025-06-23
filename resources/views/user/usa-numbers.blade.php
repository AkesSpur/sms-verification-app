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
                <span class="text-sm text-green-800 font-medium">Balance: ${{ number_format($stats['balance'], 2) }}</span>
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
        <form id="usaForm" class="space-y-4" x-data="{ selectedService: '', servicePrice: 0, statusChecked: false, isLoading: false }">
            @csrf
            
            <!-- Service Selection -->
            <div>
                <label for="service" class="block text-sm font-medium text-gray-700 mb-2">Select Service</label>
                <select id="service" name="service" x-model="selectedService" 
                        @change="servicePrice = 0; statusChecked = false; document.getElementById('status-result').innerHTML = '';" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Choose a service...</option>
                    @foreach($services as $service)
                        <option value="{{ $service->code }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Price Display -->
            <div x-show="servicePrice > 0" x-cloak class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Price:</span>
                    <span class="text-lg font-semibold text-gray-900" x-text="'$' + servicePrice.toFixed(2)"></span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div x-show="selectedService" x-cloak class="flex space-x-3">
                <button type="button" @click="checkAvailability()" :disabled="isLoading"
                        :class="isLoading ? 'bg-gray-300 cursor-not-allowed' : 'bg-gray-100 hover:bg-gray-200'"
                        class="flex-1 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-search mr-2" :class="isLoading ? 'fa-spin fa-spinner' : ''"></i>
                    <span x-text="isLoading ? 'Checking...' : 'Check Availability'"></span>
                </button>
                <button type="submit" :disabled="!statusChecked || isLoading" 
                        :class="(statusChecked && !isLoading) ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                        class="flex-1 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-shopping-cart mr-2"></i>Purchase
                </button>
            </div>

            <!-- Status Result -->
            <div id="status-result"></div>
        </form>
    </div>

    <!-- Active Orders -->
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
                                    <p class="text-sm font-medium text-gray-900">{{ $order->phone_number ?? 'Requesting...' }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $order->service->name ?? 'Unknown Service' }} • 
                                        @if($order->sms_code)
                                            Code: {{ $order->sms_code }}
                                        @else
                                            Waiting for SMS
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</p>
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
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak 
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
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
                                        <a href="{{ route('usa.show', $order->id) }}" 
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SMS Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Sample Data -->
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">+1 (555) 123-4567</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">WhatsApp</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center space-x-2">
                                    <span class="font-mono font-bold text-green-600">123456</span>
                                    <button onclick="copyToClipboard('123456')" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Dec 15, 2023 14:30</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-primary-600 hover:text-primary-900 mr-3" onclick="refreshNumber(1)">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <button class="text-gray-600 hover:text-gray-900" onclick="showDetails(1)">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">+1 (555) 987-6543</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Telegram</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Waiting</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Pending...</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Dec 15, 2023 15:45</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-primary-600 hover:text-primary-900 mr-3" onclick="refreshNumber(2)">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <button class="text-gray-600 hover:text-gray-900" onclick="showDetails(2)">
                                    <i class="fas fa-info-circle"></i>
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
                            <i class="fas fa-flag-usa text-blue-600"></i>
                            <span class="font-medium text-gray-900">+1 (555) 123-4567</span>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500">Service:</span>
                            <span class="ml-1 font-medium">WhatsApp</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Created:</span>
                            <span class="ml-1 text-gray-500">Dec 15, 14:30</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500">SMS Code:</span>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="font-mono font-bold text-green-600">123456</span>
                                <button onclick="copyToClipboard('123456')" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2 mt-3">
                        <button onclick="refreshNumber(1)" class="flex-1 bg-primary-100 text-primary-700 px-3 py-2 rounded-lg text-sm hover:bg-primary-200 transition-colors">
                            <i class="fas fa-sync-alt mr-1"></i>Refresh
                        </button>
                        <button onclick="showDetails(1)" class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors">
                            <i class="fas fa-info-circle mr-1"></i>Details
                        </button>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-flag-usa text-blue-600"></i>
                            <span class="font-medium text-gray-900">+1 (555) 987-6543</span>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Waiting</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500">Service:</span>
                            <span class="ml-1 font-medium">Telegram</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Created:</span>
                            <span class="ml-1 text-gray-500">Dec 15, 15:45</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500">SMS Code:</span>
                            <span class="ml-1 text-gray-500">Pending...</span>
                        </div>
                    </div>
                    <div class="flex space-x-2 mt-3">
                        <button onclick="refreshNumber(2)" class="flex-1 bg-primary-100 text-primary-700 px-3 py-2 rounded-lg text-sm hover:bg-primary-200 transition-colors">
                            <i class="fas fa-sync-alt mr-1"></i>Refresh
                        </button>
                        <button onclick="showDetails(2)" class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors">
                            <i class="fas fa-info-circle mr-1"></i>Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <!-- Uncomment this section if no numbers exist
            <div class="text-center py-12">
                <i class="fas fa-mobile-alt text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No USA Numbers Yet</h3>
                <p class="text-gray-500 mb-4">Get your first USA number using the form above.</p>
            </div>
            -->
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
                        <span class="text-green-800 font-bold">$${data.price.toFixed(2)}</span>
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
    
    fetch(`{{ route('usa.order.status', '') }}/${orderId}`, {
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
    if (!confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
        return;
    }
    
    if (!checkRateLimit()) return;
    
    const orderElement = document.querySelector(`[data-order-id="${orderId}"]`);
    if (!orderElement) return;
    
    fetch(`{{ route('usa.order.cancel', '') }}/${orderId}`, {
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
                showNotification(`Refund of $${data.refund_amount} processed`, 'info');
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
    
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Parse the response and update only the orders section
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newOrdersSection = doc.querySelector('#active-orders');
        
        if (newOrdersSection) {
            document.querySelector('#active-orders').innerHTML = newOrdersSection.innerHTML;
            showNotification('Orders refreshed', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error refreshing orders', 'error');
    })
    .finally(() => {
        refreshBtn.innerHTML = originalContent;
        refreshBtn.disabled = false;
    });
}

// Update order display helper
function updateOrderDisplay(orderElement, orderData) {
    const phoneElement = orderElement.querySelector('.text-sm.font-medium');
    const serviceElement = orderElement.querySelector('.text-xs.text-gray-500');
    const statusElement = orderElement.querySelector('.inline-flex');
    const iconElement = orderElement.querySelector('.fas.fa-mobile-alt');
    
    // Update phone number
    if (orderData.phone_number) {
        phoneElement.textContent = orderData.phone_number;
    }
    
    // Update service info
    if (orderData.sms_code) {
        serviceElement.innerHTML = `${orderData.service_name} • Code: ${orderData.sms_code}`;
    }
    
    // Update status
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
    
    statusElement.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClasses[orderData.status] || 'bg-blue-100 text-blue-800'}`;
    statusElement.textContent = orderData.status.charAt(0).toUpperCase() + orderData.status.slice(1);
    
    iconElement.className = `fas fa-mobile-alt ${iconClasses[orderData.status] || 'text-blue-600'}`;
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
    showNotification('Order details: #' + orderId, 'info');
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

// Initialize auto-refresh on page load
document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();
});

// Security: Clear sensitive data on page unload
window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
    activeStatusChecks.clear();
});
</script>
@endsection