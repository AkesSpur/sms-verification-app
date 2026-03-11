@extends('layouts.app')

@section('title', 'Order Details - #' . $rental->id)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('user.sms.rental.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                            <i class="fas fa-mobile-alt mr-2"></i>
                            USA Numbers 1
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-sm font-medium text-gray-500">Order #{{ $rental->id }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Order Details</h1>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('user.sms.rental.index') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to USA Numbers
            </a>
        </div>
    </div>

    <!-- Order Status Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Order Status</h2>
            <span id="order-status" class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium
                {{ $rental->status === 'completed' ? 'bg-green-100 text-green-800' : 
                   ($rental->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                   ($rental->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                <i class="fas fa-circle mr-2 text-xs"></i>
                {{ ucfirst($rental->status) }}
            </span>
        </div>
    </div>

    <!-- Order Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Order Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Information</h3>
            <div class="space-y-4">
                <div class="flex justify-between py-2">
                    <span class="text-sm text-gray-600">Order ID</span>
                    <span class="text-sm font-medium text-gray-900">#{{ $rental->id }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-sm text-gray-600">Service</span>
                    <span class="text-sm font-medium text-gray-900">{{ $rental->service_code }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-sm text-gray-600">Country</span>
                    <span class="text-sm font-medium text-gray-900">USA</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-sm text-gray-600">Price</span>
                    <span class="text-sm font-medium text-primary-600">₦{{ number_format($rental->price, 2) }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-sm text-gray-600">Activation ID</span>
                    <span class="text-sm font-medium text-gray-900">{{ $rental->rental_id ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-sm text-gray-600">Created</span>
                    <span class="text-sm font-medium text-gray-900">{{ $rental->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-sm text-gray-600">Updated</span>
                    <span class="text-sm font-medium text-gray-900">{{ $rental->updated_at->format('M d, Y H:i') }}</span>
                </div>
            </div>
                        
                        @if($rental->sms_text)
                        <div class="mt-6">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h6 class="text-blue-800 font-semibold mb-2 flex items-center">
                                    <i class="fas fa-sms text-lg mr-2"></i>
                                    @lang('SMS Message')
                                </h6>
                                <p class="text-blue-700 mb-0 font-mono bg-white p-3 rounded border">{{ $rental->sms_text }}</p>
                            </div>
                        </div>
                        @endif
        </div>

        <!-- Phone Number & SMS -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Phone Number & SMS</h3>
            
            <!-- Phone Number -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <div class="flex items-center space-x-3">
                    <div class="flex-1 bg-gray-50 rounded-lg p-3">
                        <span class="font-mono text-lg font-semibold text-gray-900" id="phone-number">{{ $rental->phone_number ?? 'Loading...' }}</span>
                    </div>
                    @if($rental->phone_number)
                        <button onclick="copyToClipboard('{{ $rental->phone_number }}')" 
                                class="bg-gray-600 text-white px-3 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-copy"></i>
                        </button>
                    @endif
                </div>
            </div>
            
            <!-- SMS Code -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">SMS Code</label>
                <div class="flex items-center space-x-3" id="sms-code-display">
                    <div class="flex-1 bg-gray-50 rounded-lg p-3">
                        @if($rental->sms_code)
                            <span class="font-mono text-lg font-semibold text-green-600">{{ $rental->sms_code }}</span>
                        @else
                            <span class="text-gray-400 italic">Waiting for SMS...</span>
                        @endif
                    </div>
                    @if($rental->sms_code)
                        <button onclick="copyToClipboard('{{ $rental->sms_code }}')" 
                                class="bg-gray-600 text-white px-3 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-copy"></i>
                        </button>
                    @endif
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex flex-wrap gap-3">
                @if(in_array($rental->status, ['pending', 'active']))
                    <button type="button" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors" id="checkCodeBtn" data-id="{{ $rental->id }}">
                        <i class="fas fa-sync mr-2"></i>Check for SMS
                    </button>
                    <button type="button" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors" id="cancelBtn" data-id="{{ $rental->id }}">
                        <i class="fas fa-times mr-2"></i>Cancel Purchase
                    </button>
                @endif
            </div>
        </div>

    </div>
    
    <!-- How It Works Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">How It Works</h3>
        <div class="grid grid-cols-1 gap-4">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center mt-0.5">
                    <span class="text-xs font-semibold text-primary-600">1</span>
                </div>
                <p class="text-sm text-gray-600">Use the phone number to receive SMS verification codes</p>
            </div>
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center mt-0.5">
                    <span class="text-xs font-semibold text-primary-600">2</span>
                </div>
                <p class="text-sm text-gray-600">Click "Check for SMS" to see if any messages have arrived</p>
            </div>
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center mt-0.5">
                    <span class="text-xs font-semibold text-primary-600">3</span>
                </div>
                <p class="text-sm text-gray-600">Numbers expire automatically if no SMS is received within the time limit</p>
            </div>
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center mt-0.5">
                    <span class="text-xs font-semibold text-primary-600">4</span>
                </div>
                <p class="text-sm text-gray-600">You can cancel the purchase anytime before receiving an SMS for a full refund</p>
            </div>
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center mt-0.5">
                    <span class="text-xs font-semibold text-primary-600">5</span>
                </div>
                <p class="text-sm text-gray-600">This page automatically checks for SMS every 30 seconds</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function ($) {
    "use strict";
    
    // Check code button
    $('#checkCodeBtn').on('click', function() {
        const id = $(this).data('id');
        const btn = $(this);
        const originalText = btn.html();
        
        btn.html('<i class="fas fa-spinner la-spin"></i> @lang("Checking...")');
        btn.prop('disabled', true);
        
        $.ajax({
            url: `{{ route('user.sms.rental.check.code', ':id') }}`.replace(':id', id),
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    notify('success', response.message);
                    if (response.reload) {
                        // Update SMS code in place instead of full page reload
                        if (response.sms_code) {
                            // Update the SMS code display
                            const smsCodeDisplay = $('#sms-code-display');
                            if (smsCodeDisplay.length) {
                                smsCodeDisplay.html(`<span class="text-green-600 font-bold text-lg font-mono">${response.sms_code}</span>
                                    <button type="button" class="text-gray-400 hover:text-gray-600 p-1 rounded" onclick="copyToClipboard('${response.sms_code}')" title="Copy SMS code">
                                        <i class="fas fa-copy text-sm"></i>
                                    </button>`);
                            }
                            
                            // Update status badge
                            const statusBadge = $('.inline-flex.items-center.px-3.py-1.rounded-full.text-sm.font-medium');
                            statusBadge.removeClass('bg-orange-100 text-orange-800 bg-blue-100 text-blue-800').addClass('bg-green-100 text-green-800');
                            statusBadge.html('<i class="fas fa-check-circle mr-1"></i> Completed');
                            
                            // Hide action buttons
                            $('#checkCodeBtn, #cancelBtn').hide();
                            
                            // Stop auto-refresh
                            if (window.autoRefreshInterval) {
                                clearInterval(window.autoRefreshInterval);
                            }
                        } else {
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        }
                    }
                } else {
                    notify('info', response.message);
                    setTimeout(() => {
                                location.reload();
                            }, 1500);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                notify('error', response.message || '@lang("Something went wrong")');
            },
            complete: function() {
                btn.html(originalText);
                btn.prop('disabled', false);
            }
        });
    });
    
    // Cancel button with custom confirmation modal
    $('#cancelBtn').on('click', function() {
        const id = $(this).data('id');
        
        // Create custom confirmation modal
        const modalHtml = `
            <div id="cancelConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
                    <div class="p-6">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full">
                            <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 text-center mb-2">@lang('Cancel Purchase')</h3>
                        <p class="text-gray-600 text-center mb-6">@lang('Are you sure you want to cancel this purchase? You may receive a partial refund.')</p>
                        <div class="flex space-x-3">
                            <button type="button" id="cancelConfirmNo" class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-xl transition-colors duration-200">
                                @lang('No, Keep It')
                            </button>
                            <button type="button" id="cancelConfirmYes" class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition-colors duration-200">
                                @lang('Yes, Cancel')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Add modal to body
        $('body').append(modalHtml);
        
        // Handle modal actions
        $('#cancelConfirmNo').on('click', function() {
            $('#cancelConfirmModal').remove();
        });
        
        $('#cancelConfirmYes').on('click', function() {
            $('#cancelConfirmModal').remove();
            
            const btn = $('#cancelBtn');
            const originalText = btn.html();
            
            btn.html('<i class="fas fa-spinner la-spin"></i> @lang("Cancelling...")');
            btn.prop('disabled', true);
            
            $.ajax({
                url: `{{ route('user.sms.rental.cancel', ':id') }}`.replace(':id', id),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        notify('success', response.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        notify('error', response.message);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    notify('error', response.message || '@lang("Something went wrong")');
                },
                complete: function() {
                    btn.html(originalText);
                    btn.prop('disabled', false);
                }
            });
        });
        
        // Close modal when clicking outside
        $('#cancelConfirmModal').on('click', function(e) {
            if (e.target === this) {
                $(this).remove();
            }
        });
    });
    
    // Auto-refresh for active purchases
    @if(in_array($rental->status, ['pending', 'active']))
    window.autoRefreshInterval = setInterval(function() {
        $('#checkCodeBtn').trigger('click');
    }, 30000); // Check every 30 seconds
    @endif
    
    // Countdown timer functionality
    function updateCountdowns() {
        $('.countdown-timer').each(function() {
            const $timer = $(this);
            const expiresAt = new Date($timer.data('expires'));
            const now = new Date();
            const timeLeft = expiresAt - now;
            
            if (timeLeft <= 0) {
                $timer.find('.countdown-display').text('Expired');
                $timer.removeClass('text-orange-600').addClass('text-red-600');
                return;
            }
            
            const minutes = Math.floor(timeLeft / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
            
            const display = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            $timer.find('.countdown-display').text(display);
            
            // Change color when less than 5 minutes
            if (minutes < 5) {
                $timer.removeClass('text-orange-600').addClass('text-red-600');
            }
        });
    }
    
    // Update countdowns every second
    if ($('.countdown-timer').length > 0) {
        updateCountdowns();
        setInterval(updateCountdowns, 1000);
    }
    
})(jQuery);

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        notify('success', '@lang("Copied to clipboard!")');
    }, function(err) {
        notify('error', '@lang("Failed to copy")');
    });
}
</script>
@endpush