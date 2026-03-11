@extends('layouts.app')

@section('title', 'Reseller Orders')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Reseller Orders</h1>
            <p class="text-sm text-gray-500 mt-0.5">Your reseller product purchase history</p>
        </div>
        <a href="{{ route('user.reseller') }}"
           class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-tags text-xs"></i> Reseller Store
        </a>
    </div>

    {{-- Order History sub-nav --}}
    <div class="flex gap-2 mb-6 overflow-x-auto pb-1">
        <a href="{{ route('user.orders.sms') }}"
           class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-white border border-gray-200 text-gray-600 hover:border-slate-400 hover:text-slate-800 transition-colors">
            <i class="fas fa-sms"></i> SMS Orders
        </a>
        <a href="{{ route('user.orders.logs') }}"
           class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-white border border-gray-200 text-gray-600 hover:border-slate-400 hover:text-slate-800 transition-colors">
            <i class="fas fa-box"></i> Log Orders
        </a>
        <a href="{{ route('user.orders.gifts') }}"
           class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-white border border-gray-200 text-gray-600 hover:border-slate-400 hover:text-slate-800 transition-colors">
            <i class="fas fa-gift"></i> Gift Orders
        </a>
        <a href="{{ route('user.orders.reseller') }}"
           class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-slate-800 text-white">
            <i class="fas fa-tags"></i> Reseller Orders
        </a>
    </div>

    {{-- Orders table card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($orders->isEmpty())
            <div class="text-center py-16 text-gray-400">
                <i class="fas fa-tags text-4xl mb-3 block"></i>
                <p class="font-medium text-gray-500">No reseller orders yet</p>
                <p class="text-sm mt-1">Your reseller product purchases will appear here.</p>
                <a href="{{ route('user.reseller') }}"
                   class="mt-4 inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Reseller Store
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-100">
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Order #</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Product</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide hidden sm:table-cell">Qty</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Status</th>
                            <th class="text-right px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Amount</th>
                            <th class="text-right px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide hidden md:table-cell">Date</th>
                            <th class="text-center px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Logs</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" x-data="{ expandedOrder: null }">
                        @foreach($orders as $order)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="text-xs font-mono text-gray-500">{{ $order->order_number ?? '#'.$order->id }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-800">{{ $order->product->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                                <span class="inline-flex items-center px-2 py-0.5 bg-slate-100 text-slate-700 rounded-md text-xs font-medium">
                                    x{{ $order->quantity ?? 1 }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'pending'   => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                        'completed' => 'bg-green-50 text-green-700 border-green-200',
                                        'failed'    => 'bg-red-50 text-red-600 border-red-200',
                                        'cancelled' => 'bg-gray-50 text-gray-500 border-gray-200',
                                    ];
                                    $color = $statusColors[$order->status] ?? 'bg-gray-50 text-gray-500 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium border {{ $color }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="font-semibold text-gray-800">&#8358;{{ number_format($order->total_amount ?? 0) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right hidden md:table-cell">
                                <span class="text-xs text-gray-400">{{ $order->created_at->format('d M Y') }}</span>
                                <br><span class="text-xs text-gray-300">{{ $order->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($order->logs && $order->logs->count() > 0 && $order->status === 'completed')
                                    <button @click="expandedOrder = expandedOrder === {{ $order->id }} ? null : {{ $order->id }}"
                                            class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-800 border border-blue-200 hover:border-blue-400 px-2.5 py-1 rounded-lg transition-colors">
                                        <i class="fas fa-list text-xs"></i>
                                        <span x-text="expandedOrder === {{ $order->id }} ? 'Hide' : 'View ({{ $order->logs->count() }})'">View ({{ $order->logs->count() }})</span>
                                    </button>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                        </tr>

                        {{-- Expanded logs row --}}
                        @if($order->logs && $order->logs->count() > 0 && $order->status === 'completed')
                        <tr x-show="expandedOrder === {{ $order->id }}" x-cloak>
                            <td colspan="7" class="px-4 py-4 bg-slate-50 border-t border-dashed border-slate-200">
                                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide mb-3">
                                    Log Items ({{ $order->logs->count() }})
                                </p>
                                <div class="space-y-2 max-h-64 overflow-y-auto">
                                    @foreach($order->logs as $index => $log)
                                    <div class="bg-white border border-gray-100 rounded-lg p-3 flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <span class="text-xs text-gray-400 font-mono">{{ $index + 1 }}.</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm text-gray-700 break-words">{!! $log->log_item !!}</div>
                                        </div>
                                        <button onclick="copyToClipboard(this.closest('.bg-white').querySelector('.text-gray-700').innerText)"
                                                class="flex-shrink-0 text-xs text-gray-400 hover:text-slate-700 border border-gray-200 hover:border-slate-400 px-2 py-1 rounded transition-colors">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($orders->hasPages())
                <div class="px-4 py-4 border-t border-gray-100">
                    {{ $orders->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
