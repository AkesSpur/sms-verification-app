@extends('layouts.user')

@section('title', 'Order Details - #' . $order->id)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('usa.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                            <i class="fas fa-mobile-alt mr-2"></i>
                            USA Numbers
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-sm font-medium text-gray-500">Order #{{ $order->id }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Order Details</h1>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button onclick="checkOrderStatus({{ $order->id }})" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-sync-alt mr-2"></i>Refresh Status
            </button>
            @if($order->status === 'pending')
                <button onclick="cancelOrder({{ $order->id }})" 
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancel Order
                </button>
            @endif
        </div>
    </div>

    <!-- Order Status Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Order Status</h2>
            <span id="order-status" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                   ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                   ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                <i class="fas fa-circle mr-2 text-xs"></i>
                {{ ucfirst($order->status) }}
            </span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center 
                    {{ $order->created_at ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">Order Placed</p>
                <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y H:i') }}</p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center 
                    {{ $order->phone_number ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">Number Assigned</p>
                <p class="text-xs text-gray-500">
                    {{ $order->phone_number ? 'Completed' : 'Pending' }}
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center 
                    {{ $order->sms_code ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                    <i class="fas fa-sms"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">SMS Received</p>
                <p class="text-xs text-gray-500">
                    {{ $order->sms_code ? 'Completed' : 'Waiting' }}
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center 
                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                    <i class="fas fa-check-circle"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">Completed</p>
                <p class="text-xs text-gray-500">
                    {{ $order->status === 'completed' ? $order->updated_at->format('M d, Y H:i') : 'Pending' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Order Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Order Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Information</h3>
            <dl class="space-y-4">
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Order ID</dt>
                    <dd class="text-sm text-gray-900">#{{ $order->id }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Service</dt>
                    <dd class="text-sm text-gray-900">{{ $order->service->name ?? 'Unknown Service' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Country</dt>
                    <dd class="text-sm text-gray-900">🇺🇸 United States</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Price</dt>
                    <dd class="text-sm text-gray-900">${{ number_format($order->price, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Activation ID</dt>
                    <dd class="text-sm text-gray-900 font-mono">{{ $order->activation_id ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                    <dd class="text-sm text-gray-900">{{ $order->created_at->format('M d, Y H:i:s') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                    <dd class="text-sm text-gray-900">{{ $order->updated_at->format('M d, Y H:i:s') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Phone Number & SMS -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Phone Number & SMS</h3>
            
            @if($order->phone_number)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <div class="flex items-center space-x-2">
                        <input type="text" value="{{ $order->phone_number }}" readonly 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-900 font-mono">
                        <button onclick="copyToClipboard('{{ $order->phone_number }}')" 
                                class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            @else
                <div class="mb-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-yellow-600 mr-2"></i>
                            <span class="text-yellow-800">Waiting for phone number assignment...</span>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($order->sms_code)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">SMS Code</label>
                    <div class="flex items-center space-x-2">
                        <input type="text" value="{{ $order->sms_code }}" readonly 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-green-50 text-green-900 font-mono text-lg font-bold">
                        <button onclick="copyToClipboard('{{ $order->sms_code }}')" 
                                class="px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <p class="text-sm text-green-600 mt-2">
                        <i class="fas fa-check-circle mr-1"></i>
                        SMS code received at {{ $order->updated_at->format('M d, Y H:i:s') }}
                    </p>
                </div>
            @else
                <div class="mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-sms text-blue-600 mr-2"></i>
                                <span class="text-blue-800">Waiting for SMS code...</span>
                            </div>
                            @if($order->phone_number && $order->status === 'pending')
                                <button onclick="requestSmsRetry({{ $order->id }})" 
                                        class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition-colors">
                                    Request Retry
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Auto-refresh notice -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-sync-alt mr-2 text-gray-400"></i>
                    <span>This page auto-refreshes every 30 seconds to check for SMS updates</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    @if($order->status === 'pending')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
            <div class="flex flex-wrap gap-3">
                <button onclick="checkOrderStatus({{ $order->id }})" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>Check Status
                </button>
                @if($order->phone_number)
                    <button onclick="requestSmsRetry({{ $order->id }})" 
                            class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-redo mr-2"></i>Request SMS Retry
                    </button>
                @endif
                <button onclick="cancelOrder({{ $order->id }})" 
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancel Order
                </button>
            </div>
        </div>
    @endif
</div>

<script>
// Auto-refresh functionality
let autoRefreshInterval;

function startAutoRefresh() {
    autoRefreshInterval = setInterval(() => {
        if (document.querySelector('#order-status').textContent.trim().toLowerCase().includes('pending')) {
            checkOrderStatus({{ $order->id }}, false);
        }
    }, 30000);
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

// Check order status
function checkOrderStatus(orderId, showNotification = true) {
    fetch(`{{ route('usa.check-status', '') }}/${orderId}`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (showNotification) {
                showNotification(data.message || 'Status updated', 'success');
            }
            
            // Update the page if there are changes
            if (data.order.sms_code || data.order.status !== '{{ $order->status }}') {
                location.reload();
            }
        } else {
            if (showNotification) {
                showNotification(data.message || 'Failed to check status', 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (showNotification) {
            showNotification('Error checking order status', 'error');
        }
    });
}

// Cancel order
function cancelOrder(orderId) {
    if (!confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
        return;
    }
    
    fetch(`{{ route('usa.cancel', '') }}/${orderId}`, {
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
            if (data.refunded) {
                showNotification(`Refund of $${data.refund_amount} processed`, 'info');
            }
            setTimeout(() => {
                window.location.href = '{{ route("usa.index") }}';
            }, 2000);
        } else {
            showNotification(data.message || 'Failed to cancel order', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error cancelling order', 'error');
    });
}

// Request SMS retry
function requestSmsRetry(orderId) {
    fetch(`{{ route('usa.check-status', '') }}/${orderId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ action: 'retry' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'SMS retry requested', 'success');
        } else {
            showNotification(data.message || 'Failed to request SMS retry', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error requesting SMS retry', 'error');
    });
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showNotification('Copied to clipboard!', 'success');
    }, function(err) {
        console.error('Could not copy text: ', err);
        showNotification('Failed to copy to clipboard', 'error');
    });
}

// Notification function
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

// Stop auto-refresh when page is not visible
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopAutoRefresh();
    } else {
        startAutoRefresh();
    }
});

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    stopAutoRefresh();
});
</script>
@endsection