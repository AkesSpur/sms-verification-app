@extends('layouts.app')

@section('title', 'Reseller Store')

@section('content')
@php
$waPhone = preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '');
$waLink  = 'https://wa.me/' . $waPhone . '?text=' . urlencode('Hello, I am interested in a product that is out of stock.');
@endphp

<div class="space-y-5 max-w-4xl mx-auto">

    {{-- ── Not a reseller ── --}}
    @if(!$isReseller)
    <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-2xl p-5">
        <i class="ri-user-star-line text-amber-500 flex-shrink-0 mt-0.5 text-lg"></i>
        <div class="flex-1">
            <p class="text-sm font-semibold text-amber-800 mb-1">Reseller Access Required</p>
            <p class="text-xs text-amber-700 mb-4">You are not a reseller yet. Submit a request to gain access to reseller products.</p>
            <div class="flex flex-wrap gap-2">
                <form id="resellerRequestForm" method="POST" action="{{ route('user.reseller.request') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold text-white bg-slate-700 hover:bg-slate-800 transition-colors btn-glow">
                        <i class="ri-send-plane-line"></i> Request Access
                    </button>
                </form>
                @if($waPhone)
                <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode('Hello, I want to become a reseller. My account email is ' . (auth()->user()->email ?? '')) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors">
                    <i class="ri-whatsapp-line"></i> Chat on WhatsApp
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Products (blurred preview for non-resellers) ── --}}
    @endif

    {{-- ── Products list ── --}}
    @if($isReseller)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Resellers Products</p>
        </div>

        @forelse($products as $product)
        <div class="flex items-center gap-4 p-4 border-b border-gray-50 last:border-0 hover:bg-gray-50/60 transition-colors">

            {{-- Image --}}
            @if($product->image)
                <img src="{{ asset($product->image) }}"
                     alt="{{ $product->name }}"
                     class="w-12 h-12 rounded-xl object-cover border border-gray-100 flex-shrink-0">
            @else
                <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="ri-box-3-line text-primary-300 text-xl"></i>
                </div>
            @endif

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm truncate">{{ $product->name }}</p>
                @if($product->description)
                    <p class="text-[11px] text-gray-400 truncate mt-0.5">{{ Str::limit(strip_tags($product->description), 80) }}</p>
                @endif
                <div class="flex flex-wrap items-center gap-3 mt-1">
                    <span class="text-xs text-gray-500">
                        Stock:&nbsp;
                        @if($product->available_stock > 0)
                            <span class="text-emerald-600 font-semibold">{{ $product->available_stock }} qty.</span>
                        @else
                            <span class="text-red-500 font-semibold">0 qty.</span>
                        @endif
                    </span>
                    <span class="text-xs font-bold text-gray-800">&#8358;{{ number_format($product->price) }}</span>
                </div>
            </div>

            {{-- Action --}}
            <div class="flex-shrink-0">
                @if($product->available_stock > 0)
                    <button onclick="openPurchaseModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->available_stock }})"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold text-white transition-all btn-glow"
                            style="background: linear-gradient(135deg, #475569 0%, #1e293b 100%);">
                        <i class="ri-shopping-bag-2-line"></i> Buy
                    </button>
                @else
                    <a href="{{ $waLink }}"
                       target="_blank" rel="noopener noreferrer"
                       class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors">
                        <i class="ri-whatsapp-line"></i> Request
                    </a>
                @endif
            </div>

        </div>
        @empty
        <div class="flex flex-col items-center py-14 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                <i class="ri-store-2-line text-gray-200 text-3xl"></i>
            </div>
            <p class="text-sm font-semibold text-gray-400">No products available</p>
            <p class="text-xs text-gray-300 mt-1">Check back soon for new reseller products.</p>
        </div>
        @endforelse
    </div>
    @endif

</div>

{{-- ── Purchase modal ── --}}
<div id="resellerPurchaseModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full transform transition-all duration-300 scale-95 opacity-0" id="resellerModalContent">
        <div class="p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-primary-50 flex items-center justify-center flex-shrink-0">
                    <i class="ri-shopping-bag-2-line text-primary-500"></i>
                </div>
                <h3 class="text-sm font-bold text-gray-900 flex-1">Purchase <span id="rpProductName" class="text-primary-600"></span></h3>
                <button onclick="closeResellerPurchaseModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="ri-close-line text-lg"></i>
                </button>
            </div>

            <div class="space-y-4">
                {{-- Quantity --}}
                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Quantity</label>
                    <div class="flex items-center gap-3">
                        <button id="rpDecreaseBtn"
                                class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors text-gray-600">
                            <i class="ri-subtract-line"></i>
                        </button>
                        <input type="number" id="rpQuantity" value="1" min="1"
                               class="w-16 text-center border border-gray-200 rounded-lg py-1.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-primary-200">
                        <button id="rpIncreaseBtn"
                                class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors text-gray-600">
                            <i class="ri-add-line"></i>
                        </button>
                        <span class="text-xs text-gray-400">Max: <span id="rpMaxStock" class="font-semibold text-gray-600"></span></span>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="bg-gray-50 rounded-xl p-3 space-y-2 text-xs">
                    <div class="flex justify-between text-gray-500">
                        <span>Unit Price</span>
                        <span id="rpUnitPrice" class="font-semibold text-gray-700"></span>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 pt-2 font-bold text-sm">
                        <span class="text-gray-700">Total</span>
                        <span id="rpTotalPrice" class="text-gray-900"></span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex gap-2.5">
                    <button type="button" onclick="closeResellerPurchaseModal()"
                            class="flex-1 py-2.5 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-semibold transition-colors">
                        Cancel
                    </button>
                    <button type="button" onclick="confirmResellerPurchase()" id="rpConfirmBtn"
                            class="flex-1 py-2.5 rounded-xl text-xs font-bold text-white transition-all btn-glow"
                            style="background: linear-gradient(135deg, #475569 0%, #1e293b 100%);">
                        <i class="ri-credit-card-line mr-1"></i> Purchase
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let rpProductId       = null;
let rpUnitPriceValue  = 0;
let rpMaxStockValue   = 0;

function openPurchaseModal(productId, productName, unitPrice, maxStock) {
    rpProductId      = productId;
    rpUnitPriceValue = unitPrice;
    rpMaxStockValue  = maxStock;

    document.getElementById('rpProductName').textContent = productName;
    document.getElementById('rpUnitPrice').textContent   = '₦' + unitPrice.toLocaleString();
    document.getElementById('rpMaxStock').textContent    = maxStock;
    document.getElementById('rpQuantity').value          = 1;
    updateResellerTotal();

    const modal   = document.getElementById('resellerPurchaseModal');
    const content = document.getElementById('resellerModalContent');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    });
    document.body.style.overflow = 'hidden';
    modal.onclick = function(e) { if (e.target === modal) closeResellerPurchaseModal(); };

    document.getElementById('rpDecreaseBtn').onclick = function() {
        const inp = document.getElementById('rpQuantity');
        if (parseInt(inp.value) > 1) { inp.value = parseInt(inp.value) - 1; updateResellerTotal(); }
    };
    document.getElementById('rpIncreaseBtn').onclick = function() {
        const inp = document.getElementById('rpQuantity');
        if (parseInt(inp.value) < rpMaxStockValue) { inp.value = parseInt(inp.value) + 1; updateResellerTotal(); }
    };
    document.getElementById('rpQuantity').oninput = function() {
        let v = parseInt(this.value);
        if (v < 1) this.value = 1;
        if (v > rpMaxStockValue) this.value = rpMaxStockValue;
        updateResellerTotal();
    };
}

function closeResellerPurchaseModal() {
    const modal   = document.getElementById('resellerPurchaseModal');
    const content = document.getElementById('resellerModalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function updateResellerTotal() {
    const qty = parseInt(document.getElementById('rpQuantity').value) || 1;
    document.getElementById('rpTotalPrice').textContent = '₦' + (qty * rpUnitPriceValue).toLocaleString();
}

function confirmResellerPurchase() {
    const qty = parseInt(document.getElementById('rpQuantity').value);
    const btn = document.getElementById('rpConfirmBtn');
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line animate-spin mr-1"></i> Processing…';

    fetch('{{ route('reseller.purchase') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ product_id: rpProductId, quantity: qty })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            notify('success', data.message);
            closeResellerPurchaseModal();
            setTimeout(() => showResellerPurchaseDetails(data.data), 1200);
        } else {
            notify('error', data.message || 'Purchase failed. Please try again.');
        }
    })
    .catch(() => notify('error', 'An unexpected error occurred. Please try again.'))
    .finally(() => { btn.disabled = false; btn.innerHTML = orig; });
}

function showResellerPurchaseDetails(data) {
    const quantity  = data?.order?.quantity ?? (data?.order?.logs?.length ?? 0);
    const total     = Number(data?.total_amount || 0);
    const remaining = Number(data?.remaining_balance || 0);

    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-5">
            <div class="text-center mb-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-3">
                    <i class="ri-check-line text-emerald-500 text-2xl"></i>
                </div>
                <h3 class="text-sm font-bold text-gray-900 mb-1">Purchase Successful!</h3>
                <p class="text-xs text-gray-400">Your reseller logs have been added to your account.</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 space-y-2 text-xs mb-4">
                <div class="flex justify-between"><span class="text-gray-400">Product</span><span class="font-semibold text-gray-700">${data.product_name}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Quantity</span><span class="font-semibold text-gray-700">${quantity}</span></div>
                <div class="flex justify-between border-t border-gray-200 pt-2"><span class="text-gray-400">Total Paid</span><span class="font-bold text-gray-900">₦${total.toLocaleString()}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Remaining Balance</span><span class="font-semibold text-gray-700">₦${remaining.toLocaleString()}</span></div>
            </div>
            <div class="flex gap-2.5">
                <button onclick="this.closest('.fixed').remove()" class="flex-1 py-2.5 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-semibold transition-colors">Close</button>
                <a href="{{ route('user.order-history') }}" class="flex-1 py-2.5 rounded-xl text-xs font-bold text-white text-center transition-all btn-glow" style="background:linear-gradient(135deg,#475569,#1e293b);">View Orders</a>
            </div>
        </div>`;
    document.body.appendChild(modal);
    modal.onclick = function(e) { if (e.target === modal) modal.remove(); };
}
</script>
@endpush
