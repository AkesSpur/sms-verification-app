@extends('layouts.app')

@section('title', 'Gift Orders')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Gift Orders</h1>
            <p class="text-sm text-gray-500 mt-0.5">Your gift purchase history</p>
        </div>
        <a href="{{ route('all-gifts') }}"
           class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-gift text-xs"></i> Browse Gifts
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
           class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-slate-800 text-white">
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
                <i class="fas fa-gift text-4xl mb-3 block"></i>
                <p class="font-medium text-gray-500">No gift orders yet</p>
                <p class="text-sm mt-1">Your gift purchases will appear here.</p>
                <a href="{{ route('all-gifts') }}"
                   class="mt-4 inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    Browse Gifts
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-100">
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Order #</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Gift</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide hidden sm:table-cell">Recipient</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Status</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide hidden lg:table-cell">Tracking</th>
                            <th class="text-right px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">Amount</th>
                            <th class="text-right px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide hidden md:table-cell">Date</th>
                            <th class="text-center px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wide">View</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($orders as $order)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="text-xs font-mono text-gray-500">{{ $order->order_number ?? '#'.$order->id }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if($order->gift && $order->gift->featured_image)
                                        <img src="{{ asset($order->gift->featured_image) }}"
                                             alt="{{ $order->gift->name }}"
                                             class="w-8 h-8 rounded object-cover border border-gray-100 flex-shrink-0">
                                    @else
                                        <div class="w-8 h-8 bg-pink-50 rounded flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-gift text-pink-400 text-sm"></i>
                                        </div>
                                    @endif
                                    <span class="font-medium text-gray-800">{{ $order->gift->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                                <p class="text-gray-700 font-medium">{{ $order->recipient_name ?? '—' }}</p>
                                @if($order->recipient_phone)
                                    <p class="text-xs text-gray-400">{{ $order->recipient_phone }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'pending'    => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                        'confirmed'  => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'processing' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'shipped'    => 'bg-orange-50 text-orange-600 border-orange-200',
                                        'delivered'  => 'bg-green-50 text-green-700 border-green-200',
                                        'cancelled'  => 'bg-gray-50 text-gray-500 border-gray-200',
                                        'failed'     => 'bg-red-50 text-red-600 border-red-200',
                                    ];
                                    $color = $statusColors[$order->status] ?? 'bg-gray-50 text-gray-500 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium border {{ $color }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                @if($order->tracking_number)
                                    <button onclick="showTrackingModal({{ $order->id }})"
                                            class="font-mono text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2 py-1 rounded hover:bg-blue-100 transition-colors text-left max-w-[150px] truncate">
                                        {{ Str::limit(strip_tags($order->tracking_number), 20) }} <i class="fas fa-eye ml-1 opacity-60"></i>
                                    </button>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="font-semibold text-gray-800">&#8358;{{ number_format($order->total_amount ?? 0) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right hidden md:table-cell">
                                <span class="text-xs text-gray-400">{{ $order->created_at->format('d M Y') }}</span>
                                <br><span class="text-xs text-gray-300">{{ $order->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="openGiftOrderModal({{ $order->id }})"
                                   class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 hover:text-slate-800 border border-gray-200 hover:border-slate-400 px-2.5 py-1 rounded-lg transition-colors">
                                    <i class="fas fa-eye text-xs"></i> View
                                </button>
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

<script>
    window.giftOrders = @json($orders->items());
</script>

{{-- ── Gift Order Details Modal ── --}}
<div id="giftOrderModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all duration-300 scale-95 opacity-0 overflow-hidden max-h-[90vh] flex flex-col" id="giftOrderModalContent">
        <div class="p-5 border-b border-gray-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <i class="ri-gift-line text-pink-500"></i> Order Details
            </h3>
            <button onclick="closeGiftOrderModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
        <div class="p-5 overflow-y-auto" id="giftOrderDetailsBody">
            {{-- Content populated by JS --}}
        </div>
        <div class="p-4 border-t border-gray-50 bg-gray-50 flex justify-end">
            <button onclick="closeGiftOrderModal()" class="px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-600 text-xs font-bold hover:bg-gray-50 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

{{-- ── Tracking Modal ── --}}
<div id="trackingModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" id="trackingModalContent">
        <div class="p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">Tracking Information</h3>
                <button onclick="closeTrackingModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="ri-close-line text-lg"></i>
                </button>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-xs text-gray-600 leading-relaxed break-words" id="trackingContent"></div>
            <div class="mt-4 flex justify-end gap-2">
                <button onclick="copyTrackingContent()" class="px-4 py-2 rounded-xl bg-primary-50 text-primary-600 text-xs font-bold hover:bg-primary-100 transition-colors">
                    <i class="ri-file-copy-line mr-1"></i> Copy
                </button>
                <button onclick="closeTrackingModal()" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-600 text-xs font-bold hover:bg-gray-200 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openGiftOrderModal(orderId) {
    const order = window.giftOrders.find(o => o.id === orderId);
    if (!order) return;

    const modal = document.getElementById('giftOrderModal');
    const content = document.getElementById('giftOrderModalContent');
    const body = document.getElementById('giftOrderDetailsBody');
    
    // Build content
    let html = `
        <div class="space-y-4 text-xs">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 rounded-xl bg-gray-100 flex-shrink-0 overflow-hidden">
                    ${order.gift && order.gift.featured_image ? 
                        `<img src="/${order.gift.featured_image}" class="w-full h-full object-cover">` : 
                        `<div class="w-full h-full flex items-center justify-center text-gray-400"><i class="ri-gift-line text-2xl"></i></div>`}
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">${order.gift ? order.gift.name : 'Unknown Gift'}</h4>
                    <p class="text-gray-500 mt-0.5">Order #${order.order_number}</p>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded bg-gray-100 text-gray-600 font-medium capitalize">${order.status}</span>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 p-3 rounded-xl">
                    <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Recipient</p>
                    <p class="font-medium text-gray-800">${order.recipient_name}</p>
                    <p class="text-gray-500">${order.recipient_phone}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-xl">
                    <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Sender</p>
                    <p class="font-medium text-gray-800">${order.sender_name}</p>
                    <p class="text-gray-500">${order.sender_phone}</p>
                </div>
            </div>

            <div class="bg-gray-50 p-3 rounded-xl">
                <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Delivery Address</p>
                <p class="text-gray-700 leading-relaxed">
                    ${order.delivery_address}<br>
                    ${order.delivery_city}, ${order.delivery_state}<br>
                    ${order.delivery_country} ${order.delivery_zip ? '- ' + order.delivery_zip : ''}
                </p>
            </div>

            ${order.tracking_number ? `
            <div class="bg-indigo-50 p-3 rounded-xl border border-indigo-100">
                <p class="text-[10px] uppercase tracking-wider text-indigo-400 font-semibold mb-1">Tracking Info</p>
                <div class="text-indigo-900 leading-relaxed tracking-content prose prose-sm max-w-none">
                    ${order.tracking_number}
                </div>
            </div>
            ` : ''}

            <div class="border-t border-gray-100 pt-3">
                <div class="flex justify-between mb-1">
                    <span class="text-gray-500">Unit Price</span>
                    <span class="font-medium">₦${parseFloat(order.unit_price).toLocaleString()}</span>
                </div>
                <div class="flex justify-between mb-1">
                    <span class="text-gray-500">Customization</span>
                    <span class="font-medium">₦${parseFloat(order.customization_cost).toLocaleString()}</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-dashed border-gray-200">
                    <span class="font-bold text-gray-900">Total</span>
                    <span class="font-bold text-primary-600">₦${parseFloat(order.total_amount).toLocaleString()}</span>
                </div>
            </div>
        </div>
    `;
    
    body.innerHTML = html;
    
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    });
}

function closeGiftOrderModal() {
    const modal = document.getElementById('giftOrderModal');
    const content = document.getElementById('giftOrderModalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 300);
}

function showTrackingModal(orderId) {
    const order = window.giftOrders.find(o => o.id === orderId);
    if (!order || !order.tracking_number) return;

    const modal = document.getElementById('trackingModal');
    const modalContent = document.getElementById('trackingModalContent');
    document.getElementById('trackingContent').innerHTML = order.tracking_number;
    
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    });
}

function closeTrackingModal() {
    const modal = document.getElementById('trackingModal');
    const modalContent = document.getElementById('trackingModalContent');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 300);
}

function copyTrackingContent() {
    const content = document.getElementById('trackingContent').innerText;
    navigator.clipboard.writeText(content).then(() => {
        if(typeof showToast === 'function') showToast('Copied!', 'success');
        else alert('Copied!');
    });
}
</script>
@endsection
