@extends('layouts.main')

@section('title', 'BlizzSMS: Purchase - ' . ($product->name ?? 'Gift Card'))

@section('content')
<div class="min-h-screen bg-gray-50 py-8 pt-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-4">
                <a href="{{ route('home') }}" class="hover:text-slate-800">Home</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <a href="{{ route('all-categories') }}" class="hover:text-slate-800">Digital Products</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-slate-800 font-medium">{{ $product->name }}</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900">Complete Your Purchase</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Details -->
            <div class="lg:col-span-1 order-1 lg:order-1">
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Product Details</h2>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <img src="{{ $product->image ? asset($product->image) : ($product->subcategory && $product->subcategory->image ? asset($product->subcategory->image) : 'https://images.unsplash.com/photo-1523474253046-8cd2748b5fd2?w=400&h=300&fit=crop&crop=center') }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover rounded-lg">
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                            <p class="text-gray-600 mb-2">Category: <span class="font-medium">{{ $product->subcategory ? $product->subcategory->category->name : 'Digital Product' }}</span></p>
                            <div class="flex items-center space-x-4 mb-4">
                                <span class="text-2xl font-bold text-slate-800">₦{{ number_format($product->price) }}</span>
                                <div class="flex items-center text-green-600 text-sm">
                                    <i class="fas fa-clock mr-1"></i>
                                    Instant Delivery                                    
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 mb-4">
                                <span class="text-sm text-gray-600">Stock Available:</span>
                                <span class="text-sm font-semibold text-green-600">{{ $product->available_stock ?? 0}} items</span>
                            </div>
                        </div>
                    </div>                
                </div>


            </div>

            <!-- Quantity & Order Section -->
            <div class="lg:col-span-1 order-2 lg:order-2">
                <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Quantity & Total</h2>
                    
                    <!-- Quantity Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Quantity</label>
                        <div class="flex items-center space-x-4 mb-4">
                            <button id="decreaseBtn" class="w-10 h-10 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-50 transition-colors">
                                <i class="fas fa-minus text-gray-600"></i>
                            </button>
                            <input type="number" id="quantity" value="1" min="1" max="{{ $product->available_stock ?? 0 }}" class="w-20 text-center border border-gray-300 rounded-lg py-2 font-semibold">
                            <button id="increaseBtn" class="w-10 h-10 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-50 transition-colors">
                                <i class="fas fa-plus text-gray-600"></i>
                            </button>
                        </div>
                        <p class="text-sm text-gray-600">Maximum available: <span class="font-semibold">{{ $product->available_stock ?? 0 }}</span></p>
                    </div>

                    <!-- Total Information -->
                    <div class="border-t pt-4 mb-6">
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Unit Price:</span>
                                <span id="unitPrice" class="font-semibold">₦{{ number_format($product->price) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Quantity:</span>
                                <span id="summaryQuantity" class="font-semibold">1</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t pt-2">
                                <span class="text-gray-900">Total:</span>
                                <span id="totalPrice" class="text-slate-800">₦{{ number_format($product->price) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Button -->
                    @if(($product->available_stock ?? 0) > 0)
                        <button class="w-full bg-gradient-to-r from-slate-800 to-gray-900 hover:from-slate-900 hover:to-black text-white py-3 px-6 rounded-lg font-semibold transition-all duration-200 hover:shadow-lg mb-4">
                            Proceed to Payment
                        </button>
                    @else
                        <button disabled class="w-full bg-gray-400 text-white py-3 px-6 rounded-lg font-semibold cursor-not-allowed mb-4">
                            Out of Stock
                        </button>
                    @endif

                    <!-- Share Product -->
                    <div class="text-center space-y-3">
                        <h3 class="text-sm font-semibold text-gray-900">Share This Product</h3>
                        <div class="flex justify-center gap-3">
                            <button onclick="shareToFacebook()" class="w-10 h-10 rounded-lg bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center transition-colors" title="Share on Facebook">
                                <i class="fab fa-facebook-f text-sm"></i>
                            </button>
                            <button onclick="shareToTwitter()" class="w-10 h-10 rounded-lg bg-blue-400 hover:bg-blue-500 text-white flex items-center justify-center transition-colors" title="Share on Twitter">
                                <i class="fab fa-twitter text-sm"></i>
                            </button>
                            <button onclick="shareToWhatsApp()" class="w-10 h-10 rounded-lg bg-green-500 hover:bg-green-600 text-white flex items-center justify-center transition-colors" title="Share on WhatsApp">
                                <i class="fab fa-whatsapp text-sm"></i>
                            </button>
                            <button onclick="shareToTelegram()" class="w-10 h-10 rounded-lg bg-blue-500 hover:bg-blue-600 text-white flex items-center justify-center transition-colors" title="Share on Telegram">
                                <i class="fab fa-telegram-plane text-sm"></i>
                            </button>
                            <button onclick="copyPageUrl()" class="w-10 h-10 rounded-lg bg-gray-600 hover:bg-gray-700 text-white flex items-center justify-center transition-colors" title="Copy Link">
                                <i class="fas fa-copy text-sm"></i>
                            </button>
                        </div>
                        <div id="copySuccess" class="hidden text-green-600 text-sm">
                            <i class="fas fa-check mr-1"></i>
                            Link copied to clipboard!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Product data for JavaScript calculations
const productPrice = {{ $product->price }};
const maxStock = {{ $product->available_stock ?? 0 }};
const productName = '{{ addslashes($product->name) }}';

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    updateSummary();
});

// Quantity controls
const quantityInput = document.getElementById('quantity');
const decreaseBtn = document.getElementById('decreaseBtn');
const increaseBtn = document.getElementById('increaseBtn');

decreaseBtn.addEventListener('click', function() {
    const currentValue = parseInt(quantityInput.value);
    if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
        updateSummary();
    }
});

increaseBtn.addEventListener('click', function() {
    const currentValue = parseInt(quantityInput.value);
    if (currentValue < maxStock) {
        quantityInput.value = currentValue + 1;
        updateSummary();
    }
});

quantityInput.addEventListener('input', function() {
    const value = parseInt(this.value);
    if (value < 1) this.value = 1;
    if (value > maxStock) this.value = maxStock;
    updateSummary();
});

// Update order summary
function updateSummary() {
    const quantity = parseInt(quantityInput.value);
    const subtotal = quantity * productPrice;
    
    document.getElementById('summaryQuantity').textContent = quantity;
    document.getElementById('totalPrice').textContent = `₦${subtotal.toLocaleString()}`;
}

// Social sharing functions
function shareToFacebook() {
    const url = encodeURIComponent(window.location.href);
    const priceFormatted = `₦${productPrice.toLocaleString()}`;
    const text = encodeURIComponent(`Check out this ${productName} - ${priceFormatted}`);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
}

function shareToTwitter() {
    const url = encodeURIComponent(window.location.href);
    const priceFormatted = `₦${productPrice.toLocaleString()}`;
    const text = encodeURIComponent(`Check out this ${productName} - ${priceFormatted}`);
    window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank', 'width=600,height=400');
}

function shareToWhatsApp() {
    const url = window.location.href;
    const priceFormatted = `₦${productPrice.toLocaleString()}`;
    const text = encodeURIComponent(`Check out this ${productName} - ${priceFormatted} ${url}`);
    window.open(`https://wa.me/?text=${text}`, '_blank');
}

function shareToTelegram() {
    const url = encodeURIComponent(window.location.href);
    const priceFormatted = `₦${productPrice.toLocaleString()}`;
    const text = encodeURIComponent(`Check out this ${productName} - ${priceFormatted}`);
    window.open(`https://t.me/share/url?url=${url}&text=${text}`, '_blank');
}

function copyPageUrl() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        const successMsg = document.getElementById('copySuccess');
        successMsg.classList.remove('hidden');
        setTimeout(() => {
            successMsg.classList.add('hidden');
        }, 3000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
@endsection