@extends('layouts.app')

@section('title', 'Checkout - ' . $product->name)

@section('content')
<div class="font-sans antialiased text-slate-900 min-h-screen flex flex-col bg-white">
    <!-- Content -->
    <main class="flex-grow px-5 pt-8 pb-10 max-w-md mx-auto w-full">
        <!-- Product Header -->
        <div class="flex items-center gap-4 mb-8">
            <div class="relative w-20 h-20 flex-shrink-0 rounded-2xl overflow-hidden shadow-sm bg-slate-50 border border-slate-100 group">
                @if($product->image)
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-105">
                @elseif($product->subcategory && $product->subcategory->image)
                    <img src="{{ asset($product->subcategory->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-105">
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-400">
                        <i class="ri-box-3-line text-3xl"></i>
                    </div>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-medium text-indigo-600 uppercase tracking-widest mb-1">
                    {{ $product->subcategory ? $product->subcategory->category->name : 'Digital Product' }}
                </p>
                <h2 class="text-lg font-bold leading-tight text-slate-900 mb-1 break-words">{{ $product->name }}</h2>
                <div class="flex items-center gap-2">
                    <span class="text-base font-medium text-slate-700">₦{{ number_format($product->price) }}</span>
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-medium uppercase tracking-wider text-slate-400">Details</h3>
                <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100">
                    {{ $product->available_stock ?? 0 }} in stock
                </span>
            </div>
            <div class="text-sm text-slate-600 leading-relaxed prose prose-sm max-w-none">
                {!! $product->description ?? 'Premium ' . $product->name . ' account. Verified ownership and ready for instant transfer.' !!}
            </div>
            <div class="mt-3 inline-flex items-center px-2.5 py-1 rounded-md bg-emerald-50 border border-emerald-100">
                <span class="text-emerald-600 text-xs mr-1.5"><i class="ri-flashlight-fill"></i></span>
                <span class="text-xs font-medium text-emerald-700">Instant Delivery</span>
            </div>
        </div>

        <div class="h-px w-full bg-slate-100 mb-6"></div>

        <!-- Summary -->
        <div class="space-y-3">
            <div class="flex justify-between items-center pt-2">
                <span class="font-medium text-base text-slate-900">Total to pay</span>
                <span class="font-bold text-xl text-slate-900" id="summaryTotal">₦{{ number_format($product->price) }}</span>
            </div>
        </div>

        <!-- Social Share -->
        <div class="mt-8 pt-6 border-t border-slate-100">
            <h3 class="text-xs font-medium uppercase tracking-wider text-slate-400 mb-4 text-center">Share this Product</h3>
            <div class="flex items-center justify-center gap-4">
                <button onclick="shareSocial('twitter')" class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center hover:opacity-90 transition-opacity transform active:scale-95" aria-label="Share on X">
                    <i class="ri-twitter-x-line"></i>
                </button>
                <button onclick="shareSocial('whatsapp')" class="w-10 h-10 rounded-full bg-[#25D366] text-white flex items-center justify-center hover:opacity-90 transition-opacity transform active:scale-95" aria-label="Share on WhatsApp">
                    <i class="ri-whatsapp-line"></i>
                </button>
                <button onclick="shareSocial('telegram')" class="w-10 h-10 rounded-full bg-[#0088cc] text-white flex items-center justify-center hover:opacity-90 transition-opacity transform active:scale-95" aria-label="Share on Telegram">
                    <i class="ri-telegram-line"></i>
                </button>
                <button onclick="copyPageUrl()" class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 transition-colors transform active:scale-95" aria-label="Copy URL">
                    <i class="ri-file-copy-line"></i>
                </button>
            </div>
        </div>
    </main>

    <!-- Sticky Footer -->
    <footer class="fixed bottom-0 left-0 right-0 p-4 bg-white/80 backdrop-blur-md border-t border-slate-100 z-40 safe-pb">
        <div class="max-w-md mx-auto w-full flex items-stretch gap-3">
            <!-- Quantity Selector -->
            <div class="flex items-center bg-slate-50 rounded-xl px-2 border border-slate-200 h-[56px]">
                <button id="decreaseBtn" class="w-10 h-full flex items-center justify-center text-slate-400 hover:text-slate-900 active:scale-90 transition-transform disabled:opacity-30">
                    <i class="ri-subtract-line text-sm"></i>
                </button>
                <input type="number" id="quantity" value="1" min="1" max="{{ $product->available_stock ?? 0 }}" class="w-10 text-center text-sm font-medium text-slate-900 bg-transparent border-none p-0 focus:ring-0" readonly>
                <button id="increaseBtn" class="w-10 h-full flex items-center justify-center text-slate-400 hover:text-slate-900 active:scale-90 transition-transform">
                    <i class="ri-add-line text-sm"></i>
                </button>
            </div>

            <!-- Pay Button -->
            @auth
                @if(($product->available_stock ?? 0) > 0)
                    <button id="purchaseBtn" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-xl shadow-lg active:scale-[0.98] transition-all flex items-center justify-between px-6 h-[56px] group">
                        <span class="text-sm font-medium opacity-90" id="purchaseText">Pay</span>
                        <div class="flex items-center">
                            <span class="text-lg mr-2 font-bold" id="payButtonPrice">₦{{ number_format($product->price) }}</span>
                            <i class="ri-arrow-right-line text-lg group-hover:translate-x-1 transition-transform" id="payIcon"></i>
                        </div>
                        <span id="loadingSpinner" class="hidden w-full text-center">
                            <i class="ri-loader-4-line animate-spin mr-2"></i>Processing...
                        </span>
                    </button>
                @else
                    <button disabled class="flex-1 bg-slate-200 text-slate-400 font-medium rounded-xl flex items-center justify-center px-6 h-[56px] cursor-not-allowed">
                        Out of Stock
                    </button>
                @endif
            @else
                <a href="{{ route('login') }}" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-xl shadow-lg active:scale-[0.98] transition-all flex items-center justify-center px-6 h-[56px]">
                    Login to Pay
                </a>
            @endauth
        </div>
        <div class="h-1 w-full"></div>
    </footer>
</div>

@push('styles')
<style>
    .safe-pb {
        padding-bottom: max(1rem, env(safe-area-inset-bottom));
    }
    /* Hide number input spinners */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
@endpush

<!-- Success Modal (Hidden) -->
<div id="successModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-8 max-w-sm w-full mx-4 text-center shadow-2xl transform transition-all scale-100">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="ri-check-line text-2xl text-emerald-600"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Payment Successful!</h3>
        <p class="text-sm text-slate-600 mb-6">Your order has been processed.</p>
        
        <div class="space-y-3">
            <button onclick="viewOrder()" class="w-full bg-slate-900 text-white py-3 rounded-xl text-sm font-medium hover:bg-slate-800 transition-colors shadow-lg">
                View Order
            </button>
            <button onclick="closeSuccessModal()" class="w-full bg-slate-100 text-slate-700 py-3 rounded-xl text-sm font-medium hover:bg-slate-200 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const productPrice = {{ $product->price }};
const maxStock = {{ $product->available_stock ?? 0 }};
const productId = {{ $product->id }};
@auth
const userBalance = {{ auth()->user()->balance }};
const isAuthenticated = true;
@else
const userBalance = 0;
const isAuthenticated = false;
@endauth

let currentOrderNumber = null;

// UI Elements
const quantityInput = document.getElementById('quantity');
const decreaseBtn = document.getElementById('decreaseBtn');
const increaseBtn = document.getElementById('increaseBtn');
const summaryTotal = document.getElementById('summaryTotal');
const payButtonPrice = document.getElementById('payButtonPrice');
const purchaseBtn = document.getElementById('purchaseBtn');

document.addEventListener('DOMContentLoaded', function() {
    if (purchaseBtn && isAuthenticated) {
        purchaseBtn.addEventListener('click', handlePurchase);
    }
    
    // Initial UI update
    updateUI();
});

// Quantity Logic
if (decreaseBtn && increaseBtn) {
    decreaseBtn.addEventListener('click', () => {
        let val = parseInt(quantityInput.value);
        if (val > 1) {
            quantityInput.value = val - 1;
            updateUI();
        }
    });

    increaseBtn.addEventListener('click', () => {
        let val = parseInt(quantityInput.value);
        if (val < maxStock) {
            quantityInput.value = val + 1;
            updateUI();
        }
    });
}

function updateUI() {
    const qty = parseInt(quantityInput.value);
    const total = qty * productPrice;
    
    // Update text
    const formattedTotal = '₦' + total.toLocaleString();
    if(summaryTotal) summaryTotal.textContent = formattedTotal;
    if(payButtonPrice) payButtonPrice.textContent = formattedTotal;
    
    // Update button states
    if(decreaseBtn) decreaseBtn.disabled = qty <= 1;
    if(decreaseBtn) decreaseBtn.classList.toggle('opacity-30', qty <= 1);
    
    if(increaseBtn) increaseBtn.disabled = qty >= maxStock;
    if(increaseBtn) increaseBtn.classList.toggle('opacity-30', qty >= maxStock);
}

function handlePurchase() {
    const quantity = parseInt(quantityInput.value);
    const totalAmount = quantity * productPrice;

    // Check balance
    if (userBalance < totalAmount) {
        showNotification(`Insufficient balance. You need ₦${(totalAmount - userBalance).toLocaleString()} more.`, 'error');
        return;
    }

    setLoadingState(true);

    fetch('{{ route("digital-products.purchase") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        setLoadingState(false);
        if (data.success) {
            currentOrderNumber = data.data.orders[0].order_number; // Accessing order number from the first order object
            document.getElementById('successModal').classList.remove('hidden');
        } else {
            showNotification(data.message || 'Purchase failed.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        setLoadingState(false);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

function setLoadingState(loading) {
    const text = document.getElementById('purchaseText');
    const icon = document.getElementById('payIcon');
    const price = document.getElementById('payButtonPrice');
    const spinner = document.getElementById('loadingSpinner');
    
    if (loading) {
        purchaseBtn.disabled = true;
        text.classList.add('hidden');
        if(icon) icon.classList.add('hidden');
        if(price) price.classList.add('hidden');
        if(spinner) spinner.classList.remove('hidden');
    } else {
        purchaseBtn.disabled = false;
        text.classList.remove('hidden');
        if(icon) icon.classList.remove('hidden');
        if(price) price.classList.remove('hidden');
        if(spinner) spinner.classList.add('hidden');
    }
}

function viewOrder() {
    // Redirect to order history
    window.location.href = '{{ route("user.order-history") }}';
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
    window.location.href = '{{ route("home") }}';
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const bgColor = type === 'error' ? 'bg-red-500' : (type === 'success' ? 'bg-emerald-500' : 'bg-blue-500');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-xl shadow-xl text-white text-sm font-medium ${bgColor} transition-all duration-300 transform translate-x-full flex items-center gap-2`;
    notification.innerHTML = `
        <i class="${type === 'error' ? 'ri-error-warning-line' : 'ri-information-line'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    requestAnimationFrame(() => notification.classList.remove('translate-x-full'));
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Social Share Logic
function shareSocial(platform) {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent("Check out " + "{{ $product->name }}" + " on BlizzSMS");
    let shareUrl = '';

    switch(platform) {
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
            break;
        case 'whatsapp':
            shareUrl = `https://api.whatsapp.com/send?text=${title} ${url}`;
            break;
        case 'telegram':
            shareUrl = `https://t.me/share/url?url=${url}&text=${title}`;
            break;
    }

    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400,noopener,noreferrer');
    }
}

function copyPageUrl() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        showNotification('Link copied to clipboard!', 'success');
    }).catch(() => {
        showNotification('Failed to copy link.', 'error');
    });
}
</script>
@endpush
