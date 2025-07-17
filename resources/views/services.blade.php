@extends('layouts.main')

@section('title', 'Social Media Boosting Services')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .service-card {
            transition: all 0.3s ease;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0, 0,0.1);
        }
        
        .purchase-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-20 rounded-lg">
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

    <!-- Breadcrumb -->
    <nav class="flex mb-6 mt-20 pt-20" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Social Media Boosting Services</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Social Media Boosting Services</h1>
                <p class="text-gray-600">Boost your social media presence with our premium account boosting services</p>
            </div>
            @auth
                <a href="{{ route('user.social-media-orders.index') }}" 
                   class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors relative">
                    <i class="fas fa-list mr-2"></i>
                    My Orders
                </a>
            @endauth
        </div>
    </div>

    @if(isset($categories) && $categories->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Selection -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 ">Select Service</h2>
                
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

                    @auth
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
                                class="w-full bg-gradient-to-r from-slate-800 to-gray-900 hover:from-slate-900 hover:to-black text-white px-8 py-4 rounded-lg font-semibold transition-all hover-scale shadow-lg duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Place Order
                        </button>
                    @else
                        <!-- Login Required Message -->
                        <div class="mb-6 bg-yellow-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                                <span class="text-sm font-medium text-yellow-800">Please login to place orders</span>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <a href="{{ route('login') }}" 
                           class="w-full bg-gray-400 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 text-center block">
                            <i class="fas fa-lock mr-2"></i>
                            Login to Purchase
                        </a>
                    @endauth
                    
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

    <!-- CTA Section -->
    @guest
    <section class="bg-white py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6" data-aos="fade-up">
                Ready to Get Started?
            </h2>
            <p class="text-xl text-gray-600 mb-8" data-aos="fade-up" data-aos-delay="200">
                Join thousands of satisfied customers and start using our premium digital services today
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up" data-aos-delay="400">
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-blue-700 transition-all">
                    <i class="fas fa-user-plus mr-2"></i>Create Account
                </a>
                <a href="{{ route('login') }}" class="border-2 border-blue-600 text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-blue-600 hover:text-white transition-all">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            </div>
        </div>
    </section>
    @endguest
    
       <!-- Logged-in User Section -->
    @auth
    <section class="bg-white py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6" data-aos="fade-up">
                Welcome Back, {{ auth()->user()->name }}!
            </h2>
            <p class="text-xl text-gray-600 mb-8" data-aos="fade-up" data-aos-delay="200">
                Continue boosting your social media presence with our premium services
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up" data-aos-delay="400">
                <a href="{{ route('user.social-media-orders.index') }}" class="bg-gradient-to-r from-slate-800 to-gray-900 hover:from-slate-900 hover:to-black text-white px-8 py-4 rounded-lg font-semibold transition-all hover-scale shadow-lg">
                    <i class="fas fa-list mr-2"></i>My Orders
                </a>
                <a href="{{ route('user.transaction') }}" class="border-2 border-primary-600 text-primary-600 px-8 py-4 rounded-lg font-semibold hover:bg-primary-600 transition-all">
                    <i class="fas fa-wallet mr-2"></i>Add Funds
                </a>
            </div>
            <div class="mt-6"  data-aos="fade-up" data-aos-delay="600">
                <div class="flex items-center justify-center text-green-600">
                    <i class="fas fa-wallet mr-2"></i>
                    <span class="text-lg font-medium">Current Balance: ₦{{ number_format(auth()->user()->balance, 0) }}</span>
                </div>
            </div>
        </div>
    </section>
    @endauth
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category');
            const productSelect = document.getElementById('product');
            const orderForm = document.getElementById('orderForm');
            const noProductMessage = document.getElementById('noProductMessage');
            const productDetails = document.getElementById('productDetails');
            const quantityInput = document.getElementById('quantity');
            const socialMediaLinkInput = document.getElementById('social_media_link');
            const submitBtn = document.getElementById('submitBtn');
            
            let selectedProduct = null;
            let categories = @json($categories ?? []);
            
            // Category change handler
            categorySelect.addEventListener('change', function() {
                const categoryId = this.value;
                productSelect.innerHTML = '<option value="">Loading products...</option>';
                productSelect.disabled = true;
                
                if (categoryId) {
                    const category = categories.find(cat => cat.id == categoryId);
                    if (category && category.active_products) {
                        productSelect.innerHTML = '<option value="">Select a product</option>';
                        category.active_products.forEach(product => {
                            const option = document.createElement('option');
                            option.value = product.id;
                            option.textContent = `${product.name} - ₦${product.price_per_1000}/1k`;
                            option.dataset.product = JSON.stringify(product);
                            productSelect.appendChild(option);
                        });
                        productSelect.disabled = false;
                    }
                } else {
                    productSelect.innerHTML = '<option value="">Select a category first</option>';
                    productSelect.disabled = true;
                    hideOrderForm();
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
                    selectedProduct = null;
                    hideOrderForm();
                }
            });
            
            // Quantity input handler
            quantityInput.addEventListener('input', function() {
                if (selectedProduct) {
                    updatePriceCalculation();
                    validateForm();
                }
            });
            
            // Social media link input handler
            socialMediaLinkInput.addEventListener('input', validateForm);
            
            function showProductDetails(product) {
                document.getElementById('priceDisplay').textContent = `₦${product.price_per_1000}`;
                document.getElementById('minQuantityDisplay').textContent = product.min_quantity;
                document.getElementById('maxQuantityDisplay').textContent = product.max_quantity;
                document.getElementById('quantityHelp').textContent = `Min: ${product.min_quantity} | Max: ${product.max_quantity}`;
                
                quantityInput.min = product.min_quantity;
                quantityInput.max = product.max_quantity;
                quantityInput.value = product.min_quantity;
                
                productDetails.classList.remove('hidden');
                updatePriceCalculation();
            }
            
            function showOrderForm() {
                orderForm.style.display = 'block';
                noProductMessage.style.display = 'none';
                document.getElementById('selectedProductId').value = selectedProduct.id;
            }
            
            function hideOrderForm() {
                orderForm.style.display = 'none';
                noProductMessage.style.display = 'block';
                productDetails.classList.add('hidden');
            }
            
            function updatePriceCalculation() {
                if (!selectedProduct) return;
                
                const quantity = parseInt(quantityInput.value) || 0;
                const pricePerThousand = parseFloat(selectedProduct.price_per_1000);
                const totalAmount = (quantity / 1000) * pricePerThousand;
                
                document.getElementById('summaryPrice').textContent = `₦${pricePerThousand}`;
                document.getElementById('displayQuantity').textContent = quantity.toLocaleString();
                document.getElementById('totalAmount').textContent = `₦${totalAmount.toFixed(2)}`;
            }
            
            function validateForm() {
                if (!selectedProduct) {
                    submitBtn.disabled = true;
                    return;
                }
                
                const quantity = parseInt(quantityInput.value);
                const socialMediaLink = socialMediaLinkInput.value.trim();
                
                const isQuantityValid = quantity >= selectedProduct.min_quantity && quantity <= selectedProduct.max_quantity;
                const isLinkValid = socialMediaLink.length > 0 && isValidUrl(socialMediaLink);
                
                submitBtn.disabled = !(isQuantityValid && isLinkValid);
            }
            
            function isValidUrl(string) {
                try {
                    new URL(string);
                    return true;
                } catch (_) {
                    return false;
                }
            }
            
            // Form submission handler
            orderForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!selectedProduct || submitBtn.disabled) {
                    return;
                }
                
                const formData = new FormData(this);
                
                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                
                // Submit the form
                 fetch(`{{ route('user.social-media-boosting.purchase', '') }}/${selectedProduct.id}`, {
                     method: 'POST',
                     body: formData,
                     headers: {
                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                         'X-Requested-With': 'XMLHttpRequest',
                         'Accept': 'application/json'
                     }
                 })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response is not JSON');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Show success modal
                        showOrderSuccessModal(data);
                        
                        // Reset form
                        orderForm.reset();
                        categorySelect.value = '';
                        productSelect.innerHTML = '<option value="">Select a category first</option>';
                        productSelect.disabled = true;
                        hideOrderForm();
                        
                        // Update wallet balance if displayed
                        if (data.new_balance !== undefined) {
                            const balanceElement = document.querySelector('.text-green-600');
                            if (balanceElement) {
                                balanceElement.textContent = `₦${data.new_balance.toLocaleString()}`;
                            }
                        }
                    } else {
                        showAlert('error', data.message || 'An error occurred while processing your order.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'An error occurred while processing your order.');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-shopping-cart mr-2"></i>Place Order';
                    validateForm();
                });
            });
            
            function showAlert(type, message) {
                // Create alert element
                const alert = document.createElement('div');
                alert.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
                    type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                }`;
                alert.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas ${
                            type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'
                        } mr-2"></i>
                        <span>${message}</span>
                        <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                
                document.body.appendChild(alert);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (alert.parentElement) {
                        alert.remove();
                    }
                }, 5000);
            }
        });
        
        // Function to show order success modal
        function showOrderSuccessModal(data) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check text-green-500 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Order Placed Successfully!</h3>
                        <p class="text-gray-600">Your social media boosting order has been created.</p>
                    </div>
                    
                    <div class="border-t pt-4 mb-4">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Order ID:</span>
                                <span class="font-medium">#${data.order_id}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="font-medium text-yellow-600">Processing</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Remaining Balance:</span>
                                <span class="font-medium text-green-600">₦${new Intl.NumberFormat().format(data.new_balance)}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button onclick="closeOrderModal(this)" class="flex-1 bg-gray-200 text-gray-800 py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors">
                            Close
                        </button>
                        <a href="{{ route('user.social-media-orders.index') }}" class="flex-1 bg-gradient-to-r from-slate-800 to-gray-900 hover:from-slate-900 hover:to-black text-white py-2 px-4 rounded-lg transition-all text-center">
                            View Orders
                        </a>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }
        
        // Function to close order modal
        function closeOrderModal(button) {
            const modal = button.closest('.fixed');
            if (modal) {
                modal.remove();
            }
        }
    </script>
@endsection