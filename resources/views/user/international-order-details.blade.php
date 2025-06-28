@extends('layouts.user')

@section('title', 'International Order Details - #' . $order->id)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('user.all-countries') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                            <i class="fas fa-globe mr-2"></i>
                            International Numbers
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
            <h1 class="text-2xl font-bold text-gray-900 mt-2">International Order Details</h1>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('user.all-countries') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to International Numbers
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
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Order ID:</span>
                    <span class="text-sm font-medium text-gray-900">#{{ $order->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Country:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $order->country_name ?? 'Unknown' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Service:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $order->service->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Phone Number:</span>
                    <span class="text-sm font-medium text-gray-900">
                        @if($order->phone_number)
                            <span id="phone-number">{{ $order->phone_number }}</span>
                        @else
                            <span class="text-gray-400">Not assigned yet</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">SMS Code:</span>
                    <span class="text-sm font-medium text-gray-900">
                        @if($order->sms_code)
                            <span id="sms-code" class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $order->sms_code }}</span>
                        @else
                            <span class="text-gray-400">Waiting for SMS...</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Amount:</span>
                    <span class="text-sm font-medium text-gray-900">${{ number_format($order->amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Timing Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Timing Information</h3>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Created:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $order->created_at->format('M d, Y H:i:s') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Expires:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $order->expires_at->format('M d, Y H:i:s') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Time Remaining:</span>
                    <span id="countdown" class="text-sm font-medium text-orange-600" data-expires="{{ $order->expires_at->toISOString() }}">
                        Calculating...
                    </span>
                </div>
                @if($order->status === 'completed')
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Completed:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $order->updated_at->format('M d, Y H:i:s') }}</span>
                    </div>
                @endif
                @if($order->refund_status)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Refund Status:</span>
                        <span class="text-sm font-medium {{ $order->refund_status === 'refunded' ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ ucfirst($order->refund_status) }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
            <div>
                <h4 class="text-sm font-medium text-blue-900 mb-2">Instructions</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Use the phone number provided to receive SMS verification codes</li>
                    <li>• The number will expire automatically after 20 minutes if no SMS is received</li>
                    <li>• You will be automatically refunded if the number expires without receiving an SMS</li>
                    <li>• Click "Refresh Status" to check for new SMS codes</li>
                    <li>• You can cancel the order before receiving an SMS for a full refund</li>
                </ul>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    // Countdown timer only - no interactive functions
    function updateCountdown() {
        const countdownElement = document.getElementById('countdown');
        if (!countdownElement) return;
        
        const expiresAt = new Date(countdownElement.dataset.expires);
        const now = new Date();
        const timeLeft = expiresAt - now;
        
        if (timeLeft <= 0) {
            countdownElement.textContent = 'Expired';
            countdownElement.className = 'text-sm font-medium text-red-600';
            return;
        }
        
        const minutes = Math.floor(timeLeft / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
        
        countdownElement.textContent = `${minutes}m ${seconds}s`;
        
        if (minutes < 5) {
            countdownElement.className = 'text-sm font-medium text-red-600';
        } else if (minutes < 10) {
            countdownElement.className = 'text-sm font-medium text-yellow-600';
        } else {
            countdownElement.className = 'text-sm font-medium text-orange-600';
        }
    }
     
     // Start countdown timer
     updateCountdown();
     setInterval(updateCountdown, 1000);
</script>

<!-- Website Builder Contact -->
<div class="py-3 text-center text-sm text-gray-700 border-t border-gray-200 mt-6">
    <div class="flex items-center justify-center space-x-2 scale-90 hover:scale-100 transition-transform duration-300">
        <i class="fas fa-mobile-alt text-blue-600 animate-pulse"></i>
        <p>
            Need a custom website? <a href="mailto:dev@blizzsms.com" class="text-blue-600 hover:text-blue-800 font-medium transition-colors relative group">
                Contact the developer
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"></span>
            </a>
        </p>
        <i class="fas fa-code text-blue-600 animate-bounce"></i>
    </div>
</div>

@endpush