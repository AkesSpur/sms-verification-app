@extends('layouts.app')

@section('title', 'SMS Orders')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">SMS Orders</h1>
            <p class="text-sm text-gray-500 mt-0.5">Your SMS verification order history</p>
        </div>
        <a href="{{ route('user.usa-numbers') }}"
           class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-plus text-xs"></i> New Order
        </a>
    </div>

    {{-- Order History sub-nav --}}
    <div class="flex gap-2 mb-6 overflow-x-auto pb-1">
        <a href="{{ route('user.orders.sms') }}"
           class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-slate-800 text-white">
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
           class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-white border border-gray-200 text-gray-600 hover:border-slate-400 hover:text-slate-800 transition-colors">
            <i class="fas fa-tags"></i> Reseller Orders
        </a>
    </div>

    {{-- Orders table card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @if($orders->isEmpty())
            <div class="text-center py-16 text-gray-400">
                <i class="fas fa-sms text-4xl mb-3 block"></i>
                <p class="font-medium text-gray-500">No SMS orders yet</p>
                <p class="text-sm mt-1">Your SMS verification orders will appear here.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-100">
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Order</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Service</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide hidden sm:table-cell">Country</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide hidden md:table-cell">Phone</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Status</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide hidden lg:table-cell">SMS Code</th>
                            <th class="text-right px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Price</th>
                            <th class="text-right px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide hidden md:table-cell">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($orders as $order)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('order.show', $order) }}"
                                   class="text-xs font-mono text-blue-600 hover:text-blue-800 hover:underline">
                                    #{{ $order->id }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-800">{{ $order->service->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                                <span class="text-gray-600">{{ $order->country->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <span class="font-mono text-gray-700 text-xs">{{ $order->phone_number ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'pending'   => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                        'active'    => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'completed' => 'bg-green-50 text-green-700 border-green-200',
                                        'cancelled' => 'bg-gray-50 text-gray-500 border-gray-200',
                                        'expired'   => 'bg-orange-50 text-orange-600 border-orange-200',
                                        'failed'    => 'bg-red-50 text-red-600 border-red-200',
                                        'refunded'  => 'bg-purple-50 text-purple-600 border-purple-200',
                                    ];
                                    $color = $statusColors[$order->status] ?? 'bg-gray-50 text-gray-500 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium border {{ $color }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                @if($order->sms_code)
                                    <button onclick="copyToClipboard('{{ $order->sms_code }}')"
                                            class="font-mono text-xs bg-green-50 text-green-700 border border-green-200 px-2 py-1 rounded hover:bg-green-100 transition-colors">
                                        {{ $order->sms_code }} <i class="fas fa-copy ml-1 text-xs opacity-60"></i>
                                    </button>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="font-semibold text-gray-800">&#8358;{{ number_format($order->price ?? 0) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right hidden md:table-cell">
                                <span class="text-xs text-gray-400">{{ $order->created_at->format('d M Y') }}</span>
                                <br><span class="text-xs text-gray-300">{{ $order->created_at->format('H:i') }}</span>
                            </td>
                        </tr>
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
