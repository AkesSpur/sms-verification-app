@extends('layouts.user')

@section('title', 'Order Details - #' . $order->id)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inlinae-flex items-center">
                        <a href="{{ route('user.usa-numbers') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
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
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('user.usa-numbers') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to USA Numbers
            </a>
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
                    {{ $order->sms_code ? 'bg-green-100 text-green-600' : ($order->status === 'cancelled' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-400') }}">
                    <i class="fas {{ $order->status === 'cancelled' ? 'fa-times' : 'fa-sms' }}"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">SMS Received</p>
                <p class="text-xs {{ $order->status === 'cancelled' ? 'text-red-500' : 'text-gray-500' }}">
                    {{ $order->sms_code ? 'Completed' : ($order->status === 'cancelled' ? 'Cancelled' : 'Waiting') }}
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center 
                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-600' : ($order->status === 'cancelled' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-400') }}">
                    <i class="fas {{ $order->status === 'cancelled' ? 'fa-times' : 'fa-check-circle' }}"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">Completed</p>
                <p class="text-xs {{ $order->status === 'cancelled' ? 'text-red-500' : 'text-gray-500' }}">
                    {{ $order->status === 'completed' ? $order->updated_at->format('M d, Y H:i') : ($order->status === 'cancelled' ? 'Cancelled' : 'Pending') }}
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
                    <dd class="text-sm text-gray-900">₦{{ number_format($order->price, 2) }}</dd>
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
                    <input type="text" value="{{ $order->phone_number }}" readonly 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-900 font-mono">
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
                    <input type="text" value="{{ $order->sms_code }}" readonly 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-green-50 text-green-900 font-mono text-lg font-bold">
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
                            @if($order->status === 'pending' && $order->sms_window_expires_at && !$order->sms_window_expires_at->isPast())
                                <button onclick="resendSms({{ $order->id }})" 
                                        class="bg-orange-100 text-orange-700 px-4 py-2 rounded-lg text-sm hover:bg-orange-200 transition-colors">
                                    <i class="fas fa-redo mr-1"></i>Resend SMS
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


</div>

<script>
// Resend SMS function
function resendSms(orderId) {
    // Show loading state
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Sending...';
    
    fetch(`/user/usa/order/${orderId}/resend-sms`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification(data.message, 'success');
            // Hide the resend button after successful request
            button.style.display = 'none';
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to resend SMS. Please try again.', 'error');
    })
    .finally(() => {
        // Restore button state
        button.disabled = false;
        button.innerHTML = originalContent;
    });
}

// Simple notification function
function showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full ${
        type === 'success' ? 'bg-green-100 border border-green-200 text-green-800' :
        type === 'error' ? 'bg-red-100 border border-red-200 text-red-800' :
        'bg-blue-100 border border-blue-200 text-blue-800'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
                type === 'success' ? 'fa-check-circle' :
                type === 'error' ? 'fa-times-circle' :
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
</script>

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

@endsection