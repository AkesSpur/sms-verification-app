@extends('layouts.app')

@section('title', 'Log Orders')

@section('styles')
<style>
    .log-content img { max-width: 100%; height: auto; border-radius: 0.375rem; margin: 0.5rem 0; }
    .log-content p { margin-bottom: 0.5rem; }
    .log-content ul, .log-content ol { margin: 0.5rem 0; padding-left: 1.5rem; }
    .log-content strong, .log-content b { font-weight: 600; }
    .log-content a { color: #2563eb; text-decoration: underline; }
    .log-content pre { background: #f3f4f6; padding: 0.75rem; border-radius: 0.375rem; overflow-x: auto; font-size: 0.75rem; }
    .log-content code { background: #f3f4f6; padding: 0.1rem 0.3rem; border-radius: 0.25rem; font-size: 0.75rem; }
</style>
@endsection

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Log Orders</h1>
            <p class="text-sm text-gray-500 mt-0.5">Your digital product order history</p>
        </div>
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-store text-xs"></i> Browse Store
        </a>
    </div>

    {{-- Order History sub-nav --}}
    <div class="flex gap-2 mb-6 overflow-x-auto pb-1">
        <a href="{{ route('user.orders.sms') }}"
           class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-white border border-gray-200 text-gray-600 hover:border-slate-400 hover:text-slate-800 transition-colors">
            <i class="fas fa-sms"></i> SMS Orders
        </a>
        <a href="{{ route('user.orders.logs') }}"
           class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-slate-800 text-white">
            <i class="fas fa-box"></i> Log Orders
        </a>
        <a href="{{ route('user.orders.gifts') }}"
           class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-white border border-gray-200 text-gray-600 hover:border-slate-400 hover:text-slate-800 transition-colors">
            <i class="fas fa-gift"></i> Gift Orders
        </a>
        <a href="{{ route('user.orders.reseller') }}"
           class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-white border border-gray-200 text-gray-600 hover:border-slate-400 hover:text-slate-800 transition-colors">
            <i class="fas fa-tags"></i> Reseller Orders
        </a>
    </div>

    {{-- Orders table card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($orders->isEmpty())
            <div class="text-center py-16 text-gray-400">
                <i class="fas fa-box text-4xl mb-3 block"></i>
                <p class="font-medium text-gray-500">No log orders yet</p>
                <p class="text-sm mt-1">Your digital product purchases will appear here.</p>
                <a href="{{ route('home') }}"
                   class="mt-4 inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Browse Products
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-100">
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Order #</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Product</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide hidden sm:table-cell">Category</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Status</th>
                            <th class="text-right px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Amount</th>
                            <th class="text-right px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide hidden md:table-cell">Date</th>
                            <th class="text-center px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Log</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" x-data="{ expandedLog: null }">
                        @foreach($orders as $order)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="text-xs font-mono text-gray-500">{{ $order->order_number ?? '#'.$order->id }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-800">{{ $order->product->name ?? 'N/A' }}</span>
                                @if($order->quantity > 1)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600 ml-1">
                                        x{{ $order->quantity }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                                <span class="text-gray-500 text-xs">{{ $order->product->subcategory->category->name ?? '—' }}</span>
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
                                @if($order->status === 'completed' && $order->purchased_logs->isNotEmpty())
                                    <button @click="expandedLog = expandedLog === {{ $order->id }} ? null : {{ $order->id }}"
                                            class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-800 border border-blue-200 hover:border-blue-400 px-2.5 py-1 rounded-lg transition-colors">
                                        <i class="fas fa-eye text-xs"></i>
                                        <span x-text="expandedLog === {{ $order->id }} ? 'Hide' : 'View'">View</span>
                                    </button>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                        {{-- Log content row --}}
                        @if($order->status === 'completed' && $order->purchased_logs->isNotEmpty())
                        <tr x-show="expandedLog === {{ $order->id }}" x-cloak>
                            <td colspan="7" class="px-4 py-4 bg-slate-50 border-t border-dashed border-slate-200">
                                <div class="space-y-4">
                                    @foreach($order->purchased_logs as $index => $log)
                                        <div class="bg-white border border-gray-100 rounded-lg p-4">
                                            <div class="flex items-start justify-between mb-2">
                                                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">
                                                    Log Item {{ $order->purchased_logs->count() > 1 ? '#' . ($index + 1) : '' }}
                                                </p>
                                                <button onclick="copyToClipboard(document.getElementById('log-{{ $order->id }}-{{ $index }}').innerText)"
                                                        class="text-xs text-gray-500 hover:text-slate-800 flex items-center gap-1 border border-gray-200 hover:border-slate-400 px-2 py-1 rounded transition-colors">
                                                    <i class="fas fa-copy"></i> Copy
                                                </button>
                                            </div>
                                            <div id="log-{{ $order->id }}-{{ $index }}" class="log-content text-sm text-gray-700 max-h-48 overflow-y-auto">
                                                {!! $log->log_item !!}
                                            </div>
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
