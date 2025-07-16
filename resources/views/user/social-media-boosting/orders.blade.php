@extends('layouts.user')

@section('title', 'My Social Media Orders')

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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Order History</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">My Social Media Orders</h1>
                <p class="text-gray-600">Track and manage your social media boosting orders</p>
            </div>
            <a href="{{ route('user.social-media-boosting.index') }}" 
               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>
                New Order
            </a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('user.social-media-orders.index') }}" 
                   class="py-2 px-1 border-b-2 font-medium text-sm {{ !request('status') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    All Orders
                    @if($totalOrders > 0)
                        <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">{{ $totalOrders }}</span>
                    @endif
                </a>
                <a href="{{ route('user.social-media-orders.index', ['status' => 'pending']) }}" 
                   class="py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'pending' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Pending
                    @if($pendingCount > 0)
                        <span class="ml-2 bg-yellow-100 text-yellow-600 py-0.5 px-2 rounded-full text-xs">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('user.social-media-orders.index', ['status' => 'processing']) }}" 
                   class="py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'processing' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Processing
                    @if($processingCount > 0)
                        <span class="ml-2 bg-blue-100 text-blue-600 py-0.5 px-2 rounded-full text-xs">{{ $processingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('user.social-media-orders.index', ['status' => 'completed']) }}" 
                   class="py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'completed' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Completed
                    @if($completedCount > 0)
                        <span class="ml-2 bg-green-100 text-green-600 py-0.5 px-2 rounded-full text-xs">{{ $completedCount }}</span>
                    @endif
                </a>
                <a href="{{ route('user.social-media-orders.index', ['status' => 'cancelled']) }}" 
                   class="py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'cancelled' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Cancelled
                    @if($cancelledCount > 0)
                        <span class="ml-2 bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs">{{ $cancelledCount }}</span>
                    @endif
                </a>
            </nav>
        </div>
    </div>

    <!-- Orders List -->
    @if($orders->count() > 0)
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 mr-3">{{ $order->product->name }}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status_badge_color }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-1">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ $order->product->category->name }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Ordered on {{ $order->purchased_at->format('M d, Y \\a\\t h:i A') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">{{ $order->formatted_total_amount }}</div>
                                <div class="text-sm text-gray-500">Order #{{ $order->order_number }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-3">
                                <div class="text-sm text-blue-600 font-medium mb-1">Social Media Link</div>
                                <div class="text-sm font-medium text-gray-900 break-all">
                                    <a href="{{ $order->social_media_link }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        {{ Str::limit($order->social_media_link, 40) }}
                                        <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-3">
                                <div class="text-sm text-purple-600 font-medium mb-1">Quantity</div>
                                <div class="text-sm font-bold text-purple-900">{{ number_format($order->quantity) }}</div>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-lg p-3">
                                <div class="text-sm text-green-600 font-medium mb-1">Unit Price</div>
                                <div class="text-sm font-bold text-green-900">{{ $order->formatted_unit_price }} per 1,000</div>
                            </div>
                        </div>

                        @if($order->hasExternalOrder() && ($order->status === 'processing' || $order->status === 'completed'))
                            <!-- External Order Progress -->
                            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-semibold text-indigo-900">Order Progress</h4>
                                 
                                </div>
                                
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
                                    
                                    <div class="grid grid-cols-2 gap-4 mb-3">
                                     
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-green-600">{{ number_format($delivered) }}</div>
                                            <div class="text-xs text-green-600">Delivered</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-orange-600">{{ number_format($order->external_remains) }}</div>
                                            <div class="text-xs text-orange-600">Remaining</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Progress Bar -->
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-300" 
                                             style="width: {{ $progress }}%"></div>
                                    </div>
                                    <div class="text-center text-sm text-indigo-700 font-medium">{{ number_format($progress, 1) }}% Complete</div>
                                @endif
                                
                            </div>
                        @endif

                        @if($order->admin_notes)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-blue-500 mr-2 mt-0.5"></i>
                                    <div>
                                        <div class="text-sm font-medium text-blue-900 mb-1">Admin Notes</div>
                                        <div class="text-sm text-blue-800">{{ $order->admin_notes }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center space-x-4 text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-credit-card mr-1"></i>
                                    {{ ucfirst($order->payment_method) }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas fa-{{ $order->payment_status === 'paid' ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($order->status === 'pending')
                                    <span class="text-sm text-yellow-600">
                                        <i class="fas fa-clock mr-1"></i>
                                        Awaiting processing
                                    </span>
                                @elseif($order->status === 'processing')
                                    <span class="text-sm text-blue-600">
                                        <i class="fas fa-cog fa-spin mr-1"></i>
                                        Being processed
                                    </span>
                                @elseif($order->status === 'completed')
                                    <span class="text-sm text-green-600">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Completed
                                    </span>
                                @elseif($order->status === 'cancelled')
                                    <span class="text-sm text-red-600">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Cancelled
                                    </span>
                                @endif
                                <a href="{{ route('user.social-media-orders.show', $order) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                                    <i class="fas fa-eye mr-1"></i>
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="mt-8">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shopping-cart text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                @if(request('status'))
                    No {{ ucfirst(request('status')) }} Orders
                @else
                    No Orders Yet
                @endif
            </h3>
            <p class="text-gray-500 mb-4">
                @if(request('status'))
                    You don't have any {{ request('status') }} orders at the moment.
                @else
                    You haven't placed any social media boosting orders yet.
                @endif
            </p>
            <a href="{{ route('user.social-media-boosting.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Place Your First Order
            </a>
        </div>
    @endif
@endsection