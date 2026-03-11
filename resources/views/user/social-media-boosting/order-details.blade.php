@extends('layouts.app')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('user.social-media-orders.index') }}"
               class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-primary-600 transition-colors mb-1">
                <i class="ri-arrow-left-line"></i> Back to Orders
            </a>
            <h1 class="text-sm font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
        </div>
        <span class="px-2.5 py-1 rounded-md text-xs font-semibold {{ $order->status_badge_color }}">
            {{ ucfirst($order->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── Main (2 cols) ── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Order info --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-4">Order Information</p>
                <div class="space-y-0 text-xs">
                    <div class="flex justify-between py-2.5 border-b border-gray-50">
                        <span class="text-gray-400">Order Number</span>
                        <span class="font-mono font-semibold text-gray-800">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between py-2.5 border-b border-gray-50">
                        <span class="text-gray-400">Order Date</span>
                        <span class="font-semibold text-gray-800">{{ $order->purchased_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex justify-between py-2.5 border-b border-gray-50">
                        <span class="text-gray-400">Status</span>
                        <span class="px-2 py-0.5 rounded-md font-medium {{ $order->status_badge_color }}">{{ ucfirst($order->status) }}</span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-gray-400">Payment</span>
                        <span class="px-2 py-0.5 rounded-md font-medium {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-500' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Product info --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-4">Product Details</p>
                <div class="space-y-0 text-xs">
                    <div class="flex justify-between py-2.5 border-b border-gray-50">
                        <span class="text-gray-400">Product</span>
                        <span class="font-semibold text-gray-800">{{ $order->product->name }}</span>
                    </div>
                    <div class="flex justify-between py-2.5 border-b border-gray-50">
                        <span class="text-gray-400">Category</span>
                        <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 font-medium">{{ $order->product->category->name }}</span>
                    </div>
                    <div class="flex justify-between py-2.5 border-b border-gray-50">
                        <span class="text-gray-400">Quantity</span>
                        <span class="font-bold text-gray-800">{{ number_format($order->quantity) }}</span>
                    </div>
                    <div class="flex justify-between py-2.5 border-b border-gray-50">
                        <span class="text-gray-400">Unit Price</span>
                        <span class="font-semibold text-gray-800">{{ $order->formatted_unit_price }} per 1,000</span>
                    </div>
                    <div class="flex justify-between py-2.5 items-center">
                        <span class="text-gray-400">Social Media Link</span>
                        <a href="{{ $order->social_media_link }}" target="_blank"
                           class="text-primary-600 hover:text-primary-800 font-medium flex items-center gap-1 max-w-[180px] truncate">
                            {{ Str::limit($order->social_media_link, 30) }}
                            <i class="ri-external-link-line flex-shrink-0"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Progress --}}
            @if($order->hasExternalOrder() && in_array($order->status, ['processing','completed']) && $order->external_start_count && $order->external_remains !== null)
            @php
                $delivered = $order->status === 'completed' ? $order->quantity : max(0, $order->quantity - $order->external_remains);
                $progress  = min(100, max(0, ($delivered / $order->quantity) * 100));
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-4">Order Progress</p>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-emerald-50 rounded-xl p-3 text-center">
                        <p class="text-lg font-bold text-emerald-600">{{ number_format($delivered) }}</p>
                        <p class="text-xs text-emerald-600 font-medium">Delivered</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-3 text-center">
                        <p class="text-lg font-bold text-amber-600">{{ number_format($order->external_remains) }}</p>
                        <p class="text-xs text-amber-600 font-medium">Remaining</p>
                    </div>
                </div>
                <div class="flex justify-between items-center mb-1.5 text-xs">
                    <span class="text-gray-400">Progress</span>
                    <span class="font-bold text-primary-600">{{ number_format($progress, 1) }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="h-2 rounded-full" style="width:{{ $progress }}%; background:linear-gradient(135deg,#475569,#1e293b)"></div>
                </div>
                <div class="mt-3 text-center">
                    @if($progress >= 100)
                        <span class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-xs font-semibold"><i class="ri-check-line mr-1"></i>Completed</span>
                    @elseif($progress > 0)
                        <span class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-semibold"><i class="ri-loader-4-line mr-1"></i>In Progress</span>
                    @else
                        <span class="px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 text-xs font-semibold"><i class="ri-time-line mr-1"></i>Starting Soon</span>
                    @endif
                </div>
            </div>
            @endif

            {{-- Admin notes --}}
            @if($order->admin_notes)
            <div class="flex items-start gap-3 bg-primary-50 border border-primary-100 rounded-2xl p-4">
                <i class="ri-information-line text-primary-500 flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-xs font-semibold text-primary-800 mb-0.5">Admin Notes</p>
                    <p class="text-xs text-primary-700">{{ $order->admin_notes }}</p>
                </div>
            </div>
            @endif

        </div>

        {{-- ── Sidebar ── --}}
        <div class="space-y-5">

            {{-- Order summary --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-4">Summary</p>
                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between text-gray-500">
                        <span>Quantity</span>
                        <span class="font-semibold text-gray-700">{{ number_format($order->quantity) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Unit Price</span>
                        <span class="font-semibold text-gray-700">{{ $order->formatted_unit_price }}</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-100 pt-2.5 font-bold text-sm">
                        <span class="text-gray-700">Total</span>
                        <span class="text-emerald-600">{{ $order->formatted_total_amount }}</span>
                    </div>
                </div>
            </div>

            {{-- Payment info --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-4">Payment</p>
                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between text-gray-500">
                        <span>Method</span>
                        <span class="px-2 py-0.5 rounded-md bg-primary-50 text-primary-700 font-medium">
                            <i class="ri-wallet-3-line mr-0.5"></i>{{ ucfirst($order->payment_method) }}
                        </span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Status</span>
                        <span class="px-2 py-0.5 rounded-md font-medium {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-500' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-2.5">
                <a href="{{ route('user.social-media-orders.index') }}"
                   class="w-full flex items-center justify-center gap-1.5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-xs font-semibold hover:bg-gray-50 transition-colors">
                    <i class="ri-list-check-2"></i> View All Orders
                </a>
                <a href="{{ route('user.social-media-boosting.index') }}"
                   class="w-full flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-xs font-bold text-white transition-all btn-glow"
                   style="background: linear-gradient(135deg, #475569 0%, #1e293b 100%);">
                    <i class="ri-add-line"></i> New Order
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
