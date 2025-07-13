@extends('layouts.user')

@section('title', $product->name . ' - Social Media Boosting')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('user.social-media-boosting.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="fas fa-home mr-2"></i>
                    Social Media Boosting
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('user.social-media-boosting.category', $category->slug) }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">
                        {{ $category->name }}
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">{{ $product->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-6">
                <div class="flex items-center mb-2">
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $category->name }}</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>
                
                @if($product->description)
                    <div class="prose prose-sm text-gray-600 mb-6">
                        <p>{{ $product->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Product Details -->
            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Product Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-500">Price per 1,000</span>
                            <div class="text-xl font-bold text-blue-600">₦{{ number_format($product->price_per_1000, 0) }}</div>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Status</span>
                            <div class="flex items-center mt-1">
                                <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                <span class="text-sm font-medium text-green-600">Active</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Min Quantity</span>
                            <div class="text-lg font-semibold text-gray-900">{{ number_format($product->min_quantity) }}</div>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Max Quantity</span>
                            <div class="text-lg font-semibold text-gray-900">{{ number_format($product->max_quantity) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Features -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Features</h3>
                    <ul class="space-y-2">
                        <li class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            High-quality engagement
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Fast delivery
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            24/7 customer support
                        </li>
                        <li class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Safe and secure
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Order Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Place Your Order</h2>
            
            <form id="orderForm" action="{{ route('user.social-media-boosting.purchase', $product) }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                <!-- Social Media Link -->
                <div class="mb-6">
                    <label for="social_media_link" class="block text-sm font-medium text-gray-700 mb-2">
                        Social Media Account Link <span class="text-red-500">*</span>
                    </label>
                    <input type="url" 
                           id="social_media_link" 
                           name="social_media_link" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="https://instagram.com/username" 
                           required>
                    <p class="text-xs text-gray-500 mt-1">Enter the full URL of your social media account</p>
                </div>

                <!-- Quantity -->
                <div class="mb-6">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                        Quantity <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="quantity" 
                           name="quantity" 
                           min="{{ $product->min_quantity }}" 
                           max="{{ $product->max_quantity }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Enter quantity" 
                           required>
                    <p class="text-xs text-gray-500 mt-1">
                        Min: {{ number_format($product->min_quantity) }} | Max: {{ number_format($product->max_quantity) }}
                    </p>
                </div>

                <!-- Price Calculator -->
                <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Order Summary</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Price per 1,000:</span>
                            <span class="font-medium">₦{{ number_format($product->price_per_1000, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Quantity:</span>
                            <span class="font-medium" id="displayQuantity">0</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-gray-900">Total Amount:</span>
                            <span class="text-blue-600" id="totalAmount">₦0</span>
                        </div>
                    </div>
                </div>

                <!-- Wallet Balance -->
                <div class="mb-6 bg-green-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-wallet text-green-600 mr-2"></i>
                            <span class="text-sm font-medium text-gray-700">Wallet Balance:</span>
                        </div>
                        <span class="text-lg font-bold text-green-600">₦{{ number_format(auth()->user()->balance, 0) }}</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        id="submitBtn"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Place Order
                </button>
                
                <p class="text-xs text-gray-500 mt-3 text-center">
                    By placing this order, you agree to our terms and conditions
                </p>
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
        
        // Check if user has sufficient balance and quantity is valid
        const isValidQuantity = quantity >= minQuantity && quantity <= maxQuantity;
        const hasSufficientBalance = total <= userBalance;
        
        if (!isValidQuantity) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Invalid Quantity';
        } else if (!hasSufficientBalance) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Insufficient Balance';
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-shopping-cart mr-2"></i>Place Order';
        }
    }

    quantityInput.addEventListener('input', calculatePrice);
    
    // Initial calculation
    calculatePrice();
});
</script>
@endsection