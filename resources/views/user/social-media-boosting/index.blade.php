@extends('layouts.user')

@section('title', 'Social Media Boosting')

@section('content')
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none'">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.parentElement.style.display='none'">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Social Media Boosting</h1>
                <p class="text-gray-600">Boost your social media presence with our premium services</p>
            </div>
            <a href="{{ route('user.social-media-orders.index') }}" 
               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors relative">
                <i class="fas fa-list mr-2"></i>
                My Orders
                @if($uncompletedOrdersCount > 0)
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">{{ $uncompletedOrdersCount }}</span>
                @endif
            </a>
        </div>
    </div>

    @if($categories->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Selection -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Select Service</h2>
                
                <!-- Category Selection -->
                <div class="mb-6">
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" data-description="{{ $category->description }}">
                                {{ $category->name }} ({{ $category->activeProducts->count() }} products)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Product Selection -->
                <div class="mb-6">
                    <label for="product" class="block text-sm font-medium text-gray-700 mb-2">
                        Product <span class="text-red-500">*</span>
                    </label>
                    <select id="product" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" disabled>
                        <option value="">Select a category first</option>
                    </select>
                </div>

                {{-- Category Description --}}
                {{-- <div id="categoryDescription" class="hidden mb-6 p-4 bg-blue-50 rounded-lg">
                    <h3 class="text-sm font-medium text-blue-900 mb-2">About this category</h3>
                    <p class="text-sm text-blue-800"></p>
                </div> --}}

                <!-- Product Details -->
                <div id="productDetails" class="hidden mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Product Details</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Price per 1,000:</span>
                            <div class="font-semibold text-blue-600" id="priceDisplay">-</div>
                        </div>
                        <div>
                            <span class="text-gray-500">Status:</span>
                            <div class="font-semibold text-green-600">Active</div>
                        </div>
                        <div>
                            <span class="text-gray-500">Min Quantity:</span>
                            <div class="font-semibold" id="minQuantityDisplay">-</div>
                        </div>
                        <div>
                            <span class="text-gray-500">Max Quantity:</span>
                            <div class="font-semibold" id="maxQuantityDisplay">-</div>
                        </div>
                    </div>
                    {{-- <div id="productDescriptionDiv" class="mt-3 hidden">
                        <span class="text-gray-500">Description:</span>
                        <p class="text-gray-700 text-sm mt-1" id="productDescription"></p>
                    </div> --}}
                </div>
            </div>

            <!-- Order Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Place Your Order</h2>
                
                <form id="orderForm" style="display: none;">
                    @csrf
                    <input type="hidden" id="selectedProductId" name="product_id">
                    
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
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Enter quantity" 
                               required>
                        <p class="text-xs text-gray-500 mt-1" id="quantityHelp">
                            Min: - | Max: -
                        </p>
                    </div>

                    <!-- Price Calculator -->
                    <div class="mb-6 bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Order Summary</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Price per 1,000:</span>
                                <span class="font-medium" id="summaryPrice">₦0</span>
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
                            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Place Order
                    </button>
                    
                    <p class="text-xs text-gray-500 mt-3 text-center">
                        By placing this order, you agree to our terms and conditions
                    </p>
                </form>

                <!-- No Product Selected Message -->
                <div id="noProductMessage" class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-arrow-left text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Select a Product</h3>
                    <p class="text-gray-500">Choose a category and product from the left to start placing your order.</p>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chart-line text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Categories Available</h3>
            <p class="text-gray-500">Social media boosting categories will appear here when they become available.</p>
        </div>
    @endif

    <!-- Features Section -->
    <div class="mt-12 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Why Choose Our Boosting Services?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-rocket text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Fast Delivery</h3>
                <p class="text-gray-600 text-sm">Quick processing and delivery of your social media boost orders</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Safe & Secure</h3>
                <p class="text-gray-600 text-sm">All our services are safe and comply with platform guidelines</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-purple-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">24/7 Support</h3>
                <p class="text-gray-600 text-sm">Round-the-clock customer support for all your queries</p>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const productSelect = document.getElementById('product');
    // const categoryDescription = document.getElementById('categoryDescription');
    const productDetails = document.getElementById('productDetails');
    const orderForm = document.getElementById('orderForm');
    const noProductMessage = document.getElementById('noProductMessage');
    const quantityInput = document.getElementById('quantity');
    const selectedProductId = document.getElementById('selectedProductId');
    
    // Store all products data
    const categoriesData = @json($categories->load('activeProducts'));
    let selectedProduct = null;
    
    // Category change handler
    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        
        // Reset product selection
        productSelect.innerHTML = '<option value="">Select a product</option>';
        productSelect.disabled = !categoryId;
        hideProductDetails();
        
        if (categoryId) {
            // Show category description
            // const description = selectedOption.dataset.description;
            // if (description && description.trim()) {
            //     categoryDescription.querySelector('p').textContent = description;
            //     categoryDescription.classList.remove('hidden');
            // } else {
            //     categoryDescription.classList.add('hidden');
            // }
            
            // Load products for selected category
            const category = categoriesData.find(cat => cat.id == categoryId);
            if (category && category.active_products) {
                category.active_products.forEach(product => {
                    const option = document.createElement('option');
                    option.value = product.id;
                    option.textContent = `${product.name} - ₦${formatNumber(product.price_per_1000)}/1k`;
                    option.dataset.product = JSON.stringify(product);
                    productSelect.appendChild(option);
                });
            }
        } else {
            // categoryDescription.classList.add('hidden');
        }
    });
    
    // Product change handler
    productSelect.addEventListener('change', function() {
        const productId = this.value;
        
        if (productId) {
            const selectedOption = this.options[this.selectedIndex];
            selectedProduct = JSON.parse(selectedOption.dataset.product);
            showProductDetails(selectedProduct);
            showOrderForm();
        } else {
            hideProductDetails();
            hideOrderForm();
        }
    });
    
    // Quantity input handler
    quantityInput.addEventListener('input', function() {
        if (selectedProduct) {
            updatePriceCalculation();
            validateQuantity();
        }
    });
    
    // Form submission handler
    orderForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!selectedProduct) {
            alert('Please select a product first.');
            return;
        }
        
        const quantity = parseInt(quantityInput.value);
        const totalCost = calculateTotalCost(quantity);
        const userBalance = {{ auth()->user()->balance }};
        
        // Validate quantity
        if (quantity < selectedProduct.min_quantity || quantity > selectedProduct.max_quantity) {
            alert(`Quantity must be between ${formatNumber(selectedProduct.min_quantity)} and ${formatNumber(selectedProduct.max_quantity)}`);
            return;
        }
        
        // Check balance
        if (totalCost > userBalance) {
            alert('Insufficient wallet balance. Please fund your wallet first.');
            return;
        }
        
        // Submit form
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
        
        // Create actual form and submit
        const actualForm = document.createElement('form');
        actualForm.method = 'POST';
        actualForm.action = `{{ route('user.social-media-boosting.purchase', '') }}/${selectedProduct.id}`;
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        actualForm.appendChild(csrfInput);
        
        // Add social media link
        const linkInput = document.createElement('input');
        linkInput.type = 'hidden';
        linkInput.name = 'social_media_link';
        linkInput.value = document.getElementById('social_media_link').value;
        actualForm.appendChild(linkInput);
        
        // Add quantity
        const quantityInputHidden = document.createElement('input');
        quantityInputHidden.type = 'hidden';
        quantityInputHidden.name = 'quantity';
        quantityInputHidden.value = quantity;
        actualForm.appendChild(quantityInputHidden);
        
        document.body.appendChild(actualForm);
        actualForm.submit();
    });
    
    function showProductDetails(product) {
        document.getElementById('priceDisplay').textContent = `₦${formatNumber(product.price_per_1000)}`;
        document.getElementById('minQuantityDisplay').textContent = formatNumber(product.min_quantity);
        document.getElementById('maxQuantityDisplay').textContent = formatNumber(product.max_quantity);
        
        // if (product.description && product.description.trim()) {
        //     document.getElementById('productDescription').innerHTML = product.description;
        //     document.getElementById('productDescriptionDiv').classList.remove('hidden');
        // } else {
        //     document.getElementById('productDescriptionDiv').classList.add('hidden');
        // }
        
        productDetails.classList.remove('hidden');
        
        // Update quantity input attributes
        quantityInput.min = product.min_quantity;
        quantityInput.max = product.max_quantity;
        quantityInput.value = product.min_quantity;
        
        // Update quantity help text
        document.getElementById('quantityHelp').textContent = `Min: ${formatNumber(product.min_quantity)} | Max: ${formatNumber(product.max_quantity)}`;
        
        // Update hidden product ID
        selectedProductId.value = product.id;
        
        updatePriceCalculation();
    }
    
    function hideProductDetails() {
        productDetails.classList.add('hidden');
        selectedProduct = null;
    }
    
    function showOrderForm() {
        orderForm.style.display = 'block';
        noProductMessage.style.display = 'none';
    }
    
    function hideOrderForm() {
        orderForm.style.display = 'none';
        noProductMessage.style.display = 'block';
    }
    
    function updatePriceCalculation() {
        if (!selectedProduct) return;
        
        const quantity = parseInt(quantityInput.value) || 0;
        const totalCost = calculateTotalCost(quantity);
        
        document.getElementById('summaryPrice').textContent = `₦${formatNumber(selectedProduct.price_per_1000)}`;
        document.getElementById('displayQuantity').textContent = formatNumber(quantity);
        document.getElementById('totalAmount').textContent = `₦${formatNumber(totalCost)}`;
    }
    
    function calculateTotalCost(quantity) {
        if (!selectedProduct || !quantity) return 0;
        return Math.ceil((quantity / 1000) * selectedProduct.price_per_1000);
    }
    
    function validateQuantity() {
        if (!selectedProduct) return;
        
        const quantity = parseInt(quantityInput.value);
        const submitBtn = document.getElementById('submitBtn');
        
        if (quantity < selectedProduct.min_quantity || quantity > selectedProduct.max_quantity) {
            submitBtn.disabled = true;
            quantityInput.classList.add('border-red-500');
        } else {
            submitBtn.disabled = false;
            quantityInput.classList.remove('border-red-500');
        }
    }
    
    function formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }
});
</script>
@endsection