@extends('layouts.user')

@section('title', 'Order Details - #' . $order->order_number)

@section('content')
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('user.social-media-boosting.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="fas fa-rocket mr-2"></i>
                    Social Media Boosting
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('user.social-media-orders.index') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">
                        Order History
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Order #{{ $order->order_number }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Details</h1>
                <p class="text-gray-600">Order #{{ $order->order_number }}</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">{{ $order->formatted_total_amount }}</div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $order->status_badge_color }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Information -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Order Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Order Number</label>
                            <p class="text-sm text-gray-900 font-mono bg-gray-100 px-3 py-2 rounded">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Order Date</label>
                            <p class="text-sm text-gray-900">{{ $order->purchased_at->format('M d, Y \\a\\t h:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status_badge_color }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas fa-{{ $order->payment_status === 'paid' ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Information -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-box text-purple-600 mr-2"></i>
                        Product Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                            <p class="text-sm text-gray-900 font-medium">{{ $order->product->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $order->product->category->name }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                            <p class="text-sm text-gray-900 font-bold">{{ number_format($order->quantity) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price</label>
                            <p class="text-sm text-gray-900 font-medium">{{ $order->formatted_unit_price }} per 1,000</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Social Media Link</label>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <a href="{{ $order->social_media_link }}" target="_blank" class="text-blue-600 hover:text-blue-800 break-all">
                                    {{ $order->social_media_link }}
                                    <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Progress (only show progress bar, hide external details) -->
            @if($order->hasExternalOrder() && ($order->status === 'processing' || $order->status === 'completed'))
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
                            Order Progress
                        </h2>
                    </div>
                    <div class="p-6">
                        @if($order->external_start_count && $order->external_remains !== null)
                            @php
                                // Calculate delivered based on order status and remains
                                if ($order->status === 'processing' && $order->external_remains == 0) {
                                    $delivered = 0;
                                } elseif ($order->status === 'processing' && $order->external_remains > 0) {
                                    $delivered = $order->quantity - $order->external_remains;
                                } elseif ($order->status === 'completed') {
                                    $delivered = $order->quantity;
                                } else {
                                    $delivered = $order->external_start_count - $order->external_remains;
                                }
                                $progress = min(100, max(0, ($delivered / $order->quantity) * 100));
                            @endphp
                            
                            <div class="grid grid-cols-2 gap-6 mb-6">
                                <div class="text-center bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
                                    <div class="text-2xl font-bold text-green-600">{{ number_format($delivered) }}</div>
                                    <div class="text-sm text-green-600 font-medium">Delivered</div>
                                </div>
                                <div class="text-center bg-gradient-to-br from-orange-50 to-red-50 border border-orange-200 rounded-lg p-4">
                                    <div class="text-2xl font-bold text-orange-600">{{ number_format($order->external_remains) }}</div>
                                    <div class="text-sm text-orange-600 font-medium">Remaining</div>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Progress</span>
                                    <span class="text-sm font-medium text-indigo-600">{{ number_format($progress, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-500" 
                                         style="width: {{ $progress }}%"></div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                @if($progress >= 100)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Order Completed
                                    </span>
                                @elseif($progress > 0)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-cog fa-spin mr-1"></i>
                                        In Progress
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Starting Soon
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Admin Notes -->
            @if($order->admin_notes)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-sticky-note text-blue-600 mr-2"></i>
                            Admin Notes
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">{{ $order->admin_notes }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-receipt text-green-600 mr-2"></i>
                        Order Summary
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Quantity:</span>
                            <span class="text-sm font-medium text-gray-900">{{ number_format($order->quantity) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Unit Price:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $order->formatted_unit_price }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between">
                                <span class="text-base font-medium text-gray-900">Total Amount:</span>
                                <span class="text-base font-bold text-green-600">{{ $order->formatted_total_amount }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-credit-card text-indigo-600 mr-2"></i>
                        Payment Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Payment Method:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                <i class="fas fa-wallet mr-1"></i>
                                {{ ucfirst($order->payment_method) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Payment Status:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas fa-{{ $order->payment_status === 'paid' ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-slate-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-bolt text-gray-600 mr-2"></i>
                        Quick Actions
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <a href="{{ route('user.social-media-orders.index') }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <i class="fas fa-list mr-2"></i>
                            View All Orders
                        </a>
                        <a href="{{ route('user.social-media-boosting.index') }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Place New Order
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection