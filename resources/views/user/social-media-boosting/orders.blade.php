@extends('layouts.app')

@section('title', 'My Social Media Orders')

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-sm font-bold text-gray-900">My SMB Orders</h1>
            <p class="text-[11px] text-gray-400 mt-0.5">Track your social media boosting orders</p>
        </div>
        <a href="{{ route('user.social-media-boosting.index') }}"
           class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold text-white bg-slate-700 hover:bg-slate-800 transition-colors">
            <i class="ri-add-line"></i> New Order
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- ── Status tabs ── --}}
        <div class="flex overflow-x-auto border-b border-gray-50 px-1">
            @php
                $tabs = [
                    ''           => ['label' => 'All',        'count' => $totalOrders],
                    'pending'    => ['label' => 'Pending',    'count' => $pendingCount],
                    'processing' => ['label' => 'Processing', 'count' => $processingCount],
                    'completed'  => ['label' => 'Completed',  'count' => $completedCount],
                    'cancelled'  => ['label' => 'Cancelled',  'count' => $cancelledCount],
                ];
            @endphp
            @foreach($tabs as $status => $tab)
            @php $active = request('status') == $status; @endphp
            <a href="{{ route('user.social-media-orders.index', $status ? ['status' => $status] : []) }}"
               class="flex items-center gap-1.5 px-3 py-3 text-xs font-semibold whitespace-nowrap border-b-2 transition-colors
                      {{ $active ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                {{ $tab['label'] }}
                @if($tab['count'] > 0)
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold
                        {{ $active ? 'bg-primary-100 text-primary-600' : 'bg-gray-100 text-gray-500' }}">
                        {{ $tab['count'] }}
                    </span>
                @endif
            </a>
            @endforeach
        </div>

        @if($orders->count() > 0)
        <div class="divide-y divide-gray-50">
            @foreach($orders as $order)
            <div class="p-4 hover:bg-gray-50/50 transition-colors">

                {{-- Top row --}}
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <p class="font-semibold text-gray-800 text-sm truncate">{{ $order->product->name }}</p>
                            <span class="px-2 py-0.5 rounded-md text-xs font-medium {{ $order->status_badge_color }} flex-shrink-0">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-400">
                            <span class="bg-gray-100 text-gray-500 font-medium px-1.5 py-0.5 rounded">{{ $order->product->category->name }}</span>
                            &nbsp;·&nbsp;{{ $order->purchased_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-bold text-sm text-gray-800">{{ $order->formatted_total_amount }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">#{{ $order->order_number }}</p>
                    </div>
                </div>

                {{-- Details grid --}}
                <div class="grid grid-cols-3 gap-2 mb-3">
                    <div class="bg-gray-50 rounded-xl p-2.5">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Qty</p>
                        <p class="text-xs font-bold text-gray-700">{{ number_format($order->quantity) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-2.5">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Unit</p>
                        <p class="text-xs font-bold text-gray-700">{{ $order->formatted_unit_price }}/1k</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-2.5 min-w-0">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Link</p>
                        <a href="{{ $order->social_media_link }}" target="_blank"
                           class="text-xs text-primary-600 hover:text-primary-800 font-medium truncate block">
                            Open <i class="ri-external-link-line"></i>
                        </a>
                    </div>
                </div>

                {{-- Progress bar --}}
                @if($order->hasExternalOrder() && in_array($order->status, ['processing','completed']) && $order->external_start_count && $order->external_remains !== null)
                @php
                    $delivered = $order->status === 'completed' ? $order->quantity : max(0, $order->quantity - $order->external_remains);
                    $progress  = min(100, max(0, ($delivered / $order->quantity) * 100));
                @endphp
                <div class="mb-3">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider">Progress</span>
                        <span class="text-[10px] font-bold text-primary-600">{{ number_format($progress, 0) }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full" style="width:{{ $progress }}%; background:linear-gradient(135deg,#475569,#1e293b)"></div>
                    </div>
                    <div class="flex justify-between text-[10px] text-gray-400 mt-0.5">
                        <span>{{ number_format($delivered) }} delivered</span>
                        <span>{{ number_format($order->external_remains) }} remaining</span>
                    </div>
                </div>
                @endif

                {{-- Admin notes --}}
                @if($order->admin_notes)
                <div class="flex items-start gap-2 bg-primary-50 border border-primary-100 rounded-xl px-3 py-2 mb-3">
                    <i class="ri-information-line text-primary-500 flex-shrink-0 text-xs mt-0.5"></i>
                    <p class="text-xs text-primary-700">{{ $order->admin_notes }}</p>
                </div>
                @endif

                {{-- Footer --}}
                <div class="flex items-center justify-between pt-2.5 border-t border-gray-50">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-primary-50 text-primary-700">
                            <i class="ri-wallet-3-line"></i> {{ ucfirst($order->payment_method) }}
                        </span>
                        <span class="px-2 py-0.5 rounded-md text-xs font-medium {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-500' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                    <a href="{{ route('user.social-media-orders.show', $order) }}"
                       class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-500 text-xs font-medium transition-colors">
                        <i class="ri-eye-line"></i> View
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <x-pagination :paginator="$orders" />

        @else
        <div class="flex flex-col items-center py-14 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                <i class="ri-shopping-bag-2-line text-gray-200 text-3xl"></i>
            </div>
            <p class="text-sm font-semibold text-gray-400">
                {{ request('status') ? 'No ' . ucfirst(request('status')) . ' orders' : 'No orders yet' }}
            </p>
            <p class="text-xs text-gray-300 mt-1">Place your first order to get started</p>
            <a href="{{ route('user.social-media-boosting.index') }}"
               class="mt-4 flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold text-white bg-slate-700 hover:bg-slate-800 transition-colors">
                <i class="ri-add-line"></i> New Order
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
