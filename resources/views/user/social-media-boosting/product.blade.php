@extends('layouts.app')

@section('title', $product->name . ' - Social Media Boosting')

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">

    {{-- ── Back / Header ── --}}
    <div>
        <a href="{{ route('user.social-media-boosting.category', $category->slug) }}"
           class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-indigo-600 transition-colors mb-1">
            <i class="ri-arrow-left-line"></i> Back to {{ $category->name }}
        </a>
        <h1 class="text-sm font-bold text-gray-900">{{ $product->name }}</h1>
        <span class="px-2 py-0.5 text-xs rounded-md bg-indigo-50 text-indigo-700 font-medium">{{ $category->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- ── Product info ── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-4">Product Details</p>

            @if($product->description)
            <p class="text-xs text-gray-500 mb-4 leading-relaxed">{{ $product->description }}</p>
            @endif

            <div class="bg-gray-50 rounded-xl p-3 space-y-2.5 text-xs mb-4">
                <div class="flex justify-between text-gray-500">
                    <span>Price per 1,000</span>
                    <span class="font-bold text-indigo-600 text-sm">₦{{ number_format($product->price_per_1000, 0) }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>Min Quantity</span>
                    <span class="font-semibold text-gray-700">{{ number_format($product->min_quantity) }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>Max Quantity</span>
                    <span class="font-semibold text-gray-700">{{ number_format($product->max_quantity) }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>Status</span>
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-medium">Active</span>
                </div>
            </div>

            <div class="space-y-1.5 text-xs text-gray-500">
                @foreach(['High-quality engagement', 'Fast delivery', '24/7 customer support', 'Safe and secure'] as $feat)
                <div class="flex items-center gap-2">
                    <i class="ri-check-line text-emerald-500 flex-shrink-0"></i>
                    <span>{{ $feat }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── Order form ── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-4">Place Order</p>

            <form id="orderForm" action="{{ route('user.social-media-boosting.purchase', $product) }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Social Media Link <span class="text-red-500">*</span></label>
                    <input type="url" id="social_media_link" name="social_media_link" required
                           placeholder="https://instagram.com/username"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition-all">
                    <p class="text-[11px] text-gray-400 mt-1">Enter the full URL of your account</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" id="quantity" name="quantity"
                           min="{{ $product->min_quantity }}" max="{{ $product->max_quantity }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition-all">
                    <p class="text-[11px] text-gray-400 mt-1">
                        Min: {{ number_format($product->min_quantity) }} | Max: {{ number_format($product->max_quantity) }}
                    </p>
                </div>

                {{-- Summary --}}
                <div class="bg-gray-50 rounded-xl p-3 space-y-2 text-xs">
                    <div class="flex justify-between text-gray-500">
                        <span>Price per 1,000</span>
                        <span class="font-semibold text-gray-700">₦{{ number_format($product->price_per_1000, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Quantity</span>
                        <span class="font-semibold text-gray-700" id="displayQuantity">0</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 pt-2 font-bold text-sm">
                        <span class="text-gray-700">Total</span>
                        <span class="text-indigo-600" id="totalAmount">₦0</span>
                    </div>
                </div>

                {{-- Balance --}}
                <div class="flex items-center justify-between bg-emerald-50 rounded-xl px-3 py-2.5 text-xs">
                    <span class="text-gray-500 flex items-center gap-1.5"><i class="ri-wallet-3-line text-emerald-500"></i> Wallet Balance</span>
                    <span class="font-bold text-emerald-600">₦{{ number_format(auth()->user()->balance, 0) }}</span>
                </div>

                <button type="submit" id="submitBtn"
                        class="w-full flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-xs font-bold text-white transition-all btn-glow disabled:opacity-50 disabled:cursor-not-allowed"
                        style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                    <i class="ri-shopping-bag-2-line"></i> Place Order
                </button>
            </form>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const displayQuantity = document.getElementById('displayQuantity');
    const totalAmount = document.getElementById('totalAmount');
    const submitBtn = document.getElementById('submitBtn');
    const pricePerThousand = {{ $product->price_per_1000 }};
    const userBalance = {{ auth()->user()->balance }};
    const minQuantity = {{ $product->min_quantity }};
    const maxQuantity = {{ $product->max_quantity }};

    function calculatePrice() {
        const quantity = parseInt(quantityInput.value) || 0;
        const total = Math.round((quantity / 1000) * pricePerThousand);
        displayQuantity.textContent = quantity.toLocaleString();
        totalAmount.textContent = '₦' + total.toLocaleString();
        const isValidQuantity = quantity >= minQuantity && quantity <= maxQuantity;
        const hasSufficientBalance = total <= userBalance;
        if (!isValidQuantity) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="ri-close-line mr-1"></i>Invalid Quantity';
        } else if (!hasSufficientBalance) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="ri-wallet-3-line mr-1"></i>Insufficient Balance';
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="ri-shopping-bag-2-line mr-1"></i>Place Order';
        }
    }

    quantityInput.addEventListener('input', calculatePrice);
    calculatePrice();
});
</script>
@endsection
