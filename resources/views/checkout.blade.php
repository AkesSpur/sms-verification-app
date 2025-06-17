@extends('layouts.main')

@section('title', 'Checkout - ' . ($product ?? 'Gift Card'))

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
                <span class="text-slate-800 font-medium" id="breadcrumbProductName">Product</span>
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
                            <img id="productImage" src="https://images.unsplash.com/photo-1523474253046-8cd2748b5fd2?w=400&h=300&fit=crop&crop=center" alt="Product Image" class="w-24 h-24 object-cover rounded-lg">
                        </div>
                        <div class="flex-1">
                            <h3 id="productName" class="text-lg font-semibold text-gray-900 mb-2">₦10,000 Amazon Gift Card</h3>
                            <p class="text-gray-600 mb-2">Category: <span id="productCategory" class="font-medium">Amazon</span></p>
                            <div class="flex items-center space-x-4 mb-4">
                                <span class="text-2xl font-bold text-slate-800" id="productPrice">₦10,000</span>
                                <div class="flex items-center text-green-600 text-sm">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Instant Delivery
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 mb-4">
                                <span class="text-sm text-gray-600">Stock Available:</span>
                                <span id="stockCount" class="text-sm font-semibold text-green-600">8 items</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Share Product -->
                <div class="bg-white rounded-xl shadow-md p-6 order-3 lg:order-3">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Share This Product</h2>
                    <div class="flex flex-wrap gap-3">
                        <button onclick="shareToFacebook()" class="w-12 h-12 rounded-lg bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center transition-colors" title="Share on Facebook">
                            <i class="fab fa-facebook-f text-lg"></i>
                        </button>
                        <button onclick="shareToTwitter()" class="w-12 h-12 rounded-lg bg-blue-400 hover:bg-blue-500 text-white flex items-center justify-center transition-colors" title="Share on Twitter">
                            <i class="fab fa-twitter text-lg"></i>
                        </button>
                        <button onclick="shareToWhatsApp()" class="w-12 h-12 rounded-lg bg-green-500 hover:bg-green-600 text-white flex items-center justify-center transition-colors" title="Share on WhatsApp">
                            <i class="fab fa-whatsapp text-lg"></i>
                        </button>
                        <button onclick="shareToTelegram()" class="w-12 h-12 rounded-lg bg-blue-500 hover:bg-blue-600 text-white flex items-center justify-center transition-colors" title="Share on Telegram">
                            <i class="fab fa-telegram-plane text-lg"></i>
                        </button>
                        <button onclick="copyPageUrl()" class="w-12 h-12 rounded-lg bg-gray-600 hover:bg-gray-700 text-white flex items-center justify-center transition-colors" title="Copy Link">
                            <i class="fas fa-copy text-lg"></i>
                        </button>
                    </div>
                    <div id="copySuccess" class="hidden mt-2 text-green-600 text-sm">
                        <i class="fas fa-check mr-1"></i>
                        Link copied to clipboard!
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
                            <input type="number" id="quantity" value="1" min="1" max="8" class="w-20 text-center border border-gray-300 rounded-lg py-2 font-semibold">
                            <button id="increaseBtn" class="w-10 h-10 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-50 transition-colors">
                                <i class="fas fa-plus text-gray-600"></i>
                            </button>
                        </div>
                        <p class="text-sm text-gray-600">Maximum available: <span id="maxQuantity" class="font-semibold">8</span></p>
                    </div>

                    <!-- Total Information -->
                    <div class="border-t pt-4 mb-6">
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Unit Price:</span>
                                <span id="unitPrice" class="font-semibold">₦10,000</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Quantity:</span>
                                <span id="summaryQuantity" class="font-semibold">1</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t pt-2">
                                <span class="text-gray-900">Total:</span>
                                <span id="totalPrice" class="text-slate-800">₦10,000</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Button -->
                    <button class="w-full bg-gradient-to-r from-slate-800 to-gray-900 hover:from-slate-900 hover:to-black text-white py-3 px-6 rounded-lg font-semibold transition-all duration-200 hover:shadow-lg mb-4">
                        Proceed to Payment
                    </button>

                    <!-- Trust Indicators -->
                    <div class="text-center space-y-2">
                        <div class="flex items-center justify-center space-x-2 text-green-600 text-sm">
                            <i class="fas fa-shield-alt"></i>
                            <span>Secure Payment</span>
                        </div>
                        <div class="flex items-center justify-center space-x-2 text-green-600 text-sm">
                            <i class="fas fa-clock"></i>
                            <span>Instant Delivery</span>
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
// Get URL parameters
const urlParams = new URLSearchParams(window.location.search);
const category = urlParams.get('category') || 'Amazon';
const productName = urlParams.get('product') || '₦10,000 Amazon Gift Card';
const price = urlParams.get('price') || '₦10,000';
const stock = parseInt(urlParams.get('stock')) || 8;

// Product images mapping
const productImages = {
    'Amazon': 'https://images.unsplash.com/photo-1523474253046-8cd2748b5fd2?w=400&h=300&fit=crop&crop=center',
    'iTunes': 'https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=400&h=300&fit=crop&crop=center',
    'Google Play': 'https://images.unsplash.com/photo-1607252650355-f7fd0460ccdb?w=400&h=300&fit=crop&crop=center',
    'Steam': 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=400&h=300&fit=crop&crop=center',
    'PlayStation': 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=400&h=300&fit=crop&crop=center',
    'Xbox': 'https://images.unsplash.com/photo-1621259182978-fbf93132d53d?w=400&h=300&fit=crop&crop=center',
    'Netflix': 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?w=400&h=300&fit=crop&crop=center',
    'Spotify': 'https://images.unsplash.com/photo-1611339555312-e607c8352fd7?w=400&h=300&fit=crop&crop=center'
};

// Initialize page with product data
document.addEventListener('DOMContentLoaded', function() {
    // Update product information
    document.getElementById('productImage').src = productImages[category] || productImages['Amazon'];
    document.getElementById('productName').textContent = productName;
    document.getElementById('productCategory').textContent = category;
    document.getElementById('productPrice').textContent = price;
    document.getElementById('stockCount').textContent = `${stock} items`;
    document.getElementById('maxQuantity').textContent = stock;
    document.getElementById('quantity').max = stock;
    document.getElementById('unitPrice').textContent = price;
    
    // Update breadcrumb with product name
    document.getElementById('breadcrumbProductName').textContent = productName;
    
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
    if (currentValue < stock) {
        quantityInput.value = currentValue + 1;
        updateSummary();
    }
});

quantityInput.addEventListener('input', function() {
    const value = parseInt(this.value);
    if (value < 1) this.value = 1;
    if (value > stock) this.value = stock;
    updateSummary();
});

// Update order summary
function updateSummary() {
    const quantity = parseInt(quantityInput.value);
    const unitPrice = parseInt(price.replace('₦', '').replace(',', ''));
    const subtotal = quantity * unitPrice;
    
    document.getElementById('summaryQuantity').textContent = quantity;
    document.getElementById('totalPrice').textContent = `₦${subtotal.toLocaleString()}`;
}

// Social sharing functions
function shareToFacebook() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(`Check out this ${productName} - ${price}`);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
}

function shareToTwitter() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(`Check out this ${productName} - ${price}`);
    window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank', 'width=600,height=400');
}

function shareToWhatsApp() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(`Check out this ${productName} - ${price} ${url}`);
    window.open(`https://wa.me/?text=${text}`, '_blank');
}

function shareToTelegram() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(`Check out this ${productName} - ${price}`);
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