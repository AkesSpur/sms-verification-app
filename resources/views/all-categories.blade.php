@extends('layouts.main')

@section('title', 'All Categories - Digital Gift Cards')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-20 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-50 via-white to-gray-100"></div>
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl animate-blob"></div>
            <div class="absolute top-40 right-10 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-20 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-4000"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16" data-aos="fade-up">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                    All Digital <span class="gradient-text">Gift Cards</span>
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Browse our complete collection of digital gift cards for all your favorite platforms and services
                </p>
            </div>
        </div>
    </section>

    <!-- All Categories Section -->
    <section class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Gaming Categories -->
            <div class="mb-16" data-aos="fade-up">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Gaming Platforms</h2>
                    <p class="text-gray-600">Digital gift cards for popular gaming platforms</p>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <!-- Steam Gift Card -->
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale cursor-pointer group" onclick="openProductModal('Steam')">
                        <div class="p-6 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-xl overflow-hidden group-hover:scale-110 transition-transform">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/8/83/Steam_icon_logo.svg" alt="Steam" class="w-full h-full object-contain">
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">Steam</h4>
                            <div class="text-xs text-green-600 font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>Instant Delivery
                            </div>
                        </div>
                    </div>

                    <!-- PlayStation Gift Card -->
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale cursor-pointer group" onclick="openProductModal('PlayStation')">
                        <div class="p-6 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-xl overflow-hidden group-hover:scale-110 transition-transform">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/0/00/PlayStation_logo.svg" alt="PlayStation" class="w-full h-full object-contain">
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">PlayStation</h4>
                            <div class="text-xs text-green-600 font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>Instant Delivery
                            </div>
                        </div>
                    </div>

                    <!-- Xbox Gift Card -->
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale cursor-pointer group" onclick="openProductModal('Xbox')">
                        <div class="p-6 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-xl overflow-hidden group-hover:scale-110 transition-transform">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/f/f9/Xbox_one_logo.svg" alt="Xbox" class="w-full h-full object-contain">
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">Xbox</h4>
                            <div class="text-xs text-green-600 font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>Instant Delivery
                            </div>
                        </div>
                    </div>

                    <!-- Google Play Gift Card -->
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale cursor-pointer group" onclick="openProductModal('Google Play')">
                        <div class="p-6 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-xl overflow-hidden group-hover:scale-110 transition-transform">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play" class="w-full h-full object-contain">
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">Google Play</h4>
                            <div class="text-xs text-green-600 font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>Instant Delivery
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Entertainment Categories -->
            <div class="mb-16" data-aos="fade-up" data-aos-delay="200">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Entertainment & Media</h2>
                    <p class="text-gray-600">Digital gift cards for streaming and entertainment services</p>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <!-- Netflix Gift Card -->
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale cursor-pointer group" onclick="openProductModal('Netflix')">
                        <div class="p-6 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-xl overflow-hidden group-hover:scale-110 transition-transform">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/0/08/Netflix_2015_logo.svg" alt="Netflix" class="w-full h-full object-contain">
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">Netflix</h4>
                            <div class="text-xs text-green-600 font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>Instant Delivery
                            </div>
                        </div>
                    </div>

                    <!-- Spotify Gift Card -->
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale cursor-pointer group" onclick="openProductModal('Spotify')">
                        <div class="p-6 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-xl overflow-hidden group-hover:scale-110 transition-transform">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/1/19/Spotify_logo_without_text.svg" alt="Spotify" class="w-full h-full object-contain">
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">Spotify</h4>
                            <div class="text-xs text-green-600 font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>Instant Delivery
                            </div>
                        </div>
                    </div>

                    <!-- iTunes Gift Card -->
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale cursor-pointer group" onclick="openProductModal('iTunes')">
                        <div class="p-6 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-xl overflow-hidden group-hover:scale-110 transition-transform">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/d/df/ITunes_logo.svg" alt="iTunes" class="w-full h-full object-contain">
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">iTunes</h4>
                            <div class="text-xs text-green-600 font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>Instant Delivery
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- E-commerce Categories -->
            <div class="mb-16" data-aos="fade-up" data-aos-delay="400">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">E-commerce & Shopping</h2>
                    <p class="text-gray-600">Digital gift cards for online shopping platforms</p>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <!-- Amazon Gift Card -->
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale cursor-pointer group" onclick="openProductModal('Amazon')">
                        <div class="p-6 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-xl overflow-hidden group-hover:scale-110 transition-transform">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg" alt="Amazon" class="w-full h-full object-contain">
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">Amazon</h4>
                            <div class="text-xs text-green-600 font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>Instant Delivery
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="mt-16 grid md:grid-cols-3 gap-8" data-aos="fade-up" data-aos-delay="600">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center">
                        <i class="fas fa-bolt text-white text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-2">Instant Delivery</h4>
                    <p class="text-gray-600">Get your digital gift cards delivered to your email instantly after purchase</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-2">100% Secure</h4>
                    <p class="text-gray-600">All transactions are encrypted and protected with industry-standard security</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl flex items-center justify-center">
                        <i class="fas fa-headset text-white text-2xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-2">24/7 Support</h4>
                    <p class="text-gray-600">Our dedicated support team is available around the clock to assist you</p>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Product data for modal with Naira pricing and stock
        const productData = {
            'Amazon': [
                { name: '₦2,000 Amazon Gift Card', price: '₦2,000', stock: 15 },
                { name: '₦4,000 Amazon Gift Card', price: '₦4,000', stock: 12 },
                { name: '₦10,000 Amazon Gift Card', price: '₦10,000', stock: 8 },
                { name: '₦20,000 Amazon Gift Card', price: '₦20,000', stock: 5 },
                { name: '₦40,000 Amazon Gift Card', price: '₦40,000', stock: 3 },
                { name: '₦80,000 Amazon Gift Card', price: '₦80,000', stock: 2 }
            ],
            'iTunes': [
                { name: '₦4,000 iTunes Gift Card', price: '₦4,000', stock: 20 },
                { name: '₦6,000 iTunes Gift Card', price: '₦6,000', stock: 15 },
                { name: '₦10,000 iTunes Gift Card', price: '₦10,000', stock: 10 },
                { name: '₦20,000 iTunes Gift Card', price: '₦20,000', stock: 7 },
                { name: '₦40,000 iTunes Gift Card', price: '₦40,000', stock: 4 }
            ],
            'Google Play': [
                { name: '₦4,000 Google Play Gift Card', price: '₦4,000', stock: 18 },
                { name: '₦10,000 Google Play Gift Card', price: '₦10,000', stock: 12 },
                { name: '₦20,000 Google Play Gift Card', price: '₦20,000', stock: 8 },
                { name: '₦40,000 Google Play Gift Card', price: '₦40,000', stock: 5 }
            ],
            'Steam': [
                { name: '₦2,000 Steam Gift Card', price: '₦2,000', stock: 25 },
                { name: '₦4,000 Steam Gift Card', price: '₦4,000', stock: 20 },
                { name: '₦8,000 Steam Gift Card', price: '₦8,000', stock: 15 },
                { name: '₦20,000 Steam Gift Card', price: '₦20,000', stock: 10 },
                { name: '₦40,000 Steam Gift Card', price: '₦40,000', stock: 6 }
            ],
            'PlayStation': [
                { name: '₦4,000 PlayStation Gift Card', price: '₦4,000', stock: 14 },
                { name: '₦8,000 PlayStation Gift Card', price: '₦8,000', stock: 11 },
                { name: '₦20,000 PlayStation Gift Card', price: '₦20,000', stock: 7 },
                { name: '₦40,000 PlayStation Gift Card', price: '₦40,000', stock: 4 }
            ],
            'Xbox': [
                { name: '₦4,000 Xbox Gift Card', price: '₦4,000', stock: 16 },
                { name: '₦10,000 Xbox Gift Card', price: '₦10,000', stock: 12 },
                { name: '₦20,000 Xbox Gift Card', price: '₦20,000', stock: 8 },
                { name: '₦40,000 Xbox Gift Card', price: '₦40,000', stock: 5 }
            ],
            'Netflix': [
                { name: '₦6,000 Netflix Gift Card', price: '₦6,000', stock: 22 },
                { name: '₦12,000 Netflix Gift Card', price: '₦12,000', stock: 18 },
                { name: '₦24,000 Netflix Gift Card', price: '₦24,000', stock: 10 },
                { name: '₦40,000 Netflix Gift Card', price: '₦40,000', stock: 6 }
            ],
            'Spotify': [
                { name: '₦4,000 Spotify Gift Card', price: '₦4,000', stock: 19 },
                { name: '₦12,000 Spotify Gift Card', price: '₦12,000', stock: 14 },
                { name: '₦24,000 Spotify Gift Card', price: '₦24,000', stock: 8 }
            ]
        };
        
        // Get product image based on category
        function getProductImage(category) {
            const images = {
                'Amazon': 'https://images.unsplash.com/photo-1523474253046-8cd2748b5fd2?w=400&h=300&fit=crop&crop=center',
                'iTunes': 'https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=400&h=300&fit=crop&crop=center',
                'Google Play': 'https://images.unsplash.com/photo-1607252650355-f7fd0460ccdb?w=400&h=300&fit=crop&crop=center',
                'Steam': 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=400&h=300&fit=crop&crop=center',
                'PlayStation': 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=400&h=300&fit=crop&crop=center',
                'Xbox': 'https://images.unsplash.com/photo-1621259182978-fbf93132d53d?w=400&h=300&fit=crop&crop=center',
                'Netflix': 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?w=400&h=300&fit=crop&crop=center',
                'Spotify': 'https://images.unsplash.com/photo-1611339555312-e607c8352fd7?w=400&h=300&fit=crop&crop=center'
            };
            return images[category] || 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=400&h=300&fit=crop&crop=center';
        }

        // Open product modal with redesigned cards
        function openProductModal(category) {
            const products = productData[category] || [];
            const productImage = getProductImage(category);
            
            const modalHTML = `
                <div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[80vh] overflow-y-auto">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="text-2xl font-bold text-gray-900">${category} Gift Cards</h3>
                                <button onclick="closeProductModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                ${products.map(product => {
                                    const stockStatus = product.stock > 10 ? 'text-green-600' : product.stock > 5 ? 'text-yellow-600' : 'text-red-600';
                                    const stockText = product.stock > 10 ? 'In Stock' : product.stock > 0 ? `${product.stock} left` : 'Out of Stock';
                                    const stockBg = product.stock > 10 ? 'bg-green-100' : product.stock > 5 ? 'bg-yellow-100' : 'bg-red-100';
                                    
                                    return `
                                        <div class="bg-white border border-gray-200 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden cursor-pointer transform hover:-translate-y-1" onclick="redirectToCheckout('${category}', '${product.name}', '${product.price}', ${product.stock})">
                                            <div class="relative">
                                                <img src="${productImage}" alt="${product.name}" class="w-full h-48 object-cover">
                                                <div class="absolute top-3 right-3 ${stockBg} ${stockStatus} px-2 py-1 rounded-full text-xs font-semibold">
                                                    ${stockText}
                                                </div>
                                            </div>
                                            <div class="p-4">
                                                <h4 class="font-semibold text-gray-900 mb-2 text-sm">${product.name}</h4>
                                                <div class="flex items-center justify-between mb-3">
                                                    <span class="text-xl font-bold text-slate-800">${product.price}</span>
                                                    <div class="flex items-center text-green-600 text-xs">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Instant Delivery
                                                    </div>
                                                </div>
                                                <button class="w-full bg-gradient-to-r from-slate-800 to-gray-900 hover:from-slate-900 hover:to-black text-white py-2 px-4 rounded-lg font-semibold transition-all duration-200 text-sm ${product.stock === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-lg'}" ${product.stock === 0 ? 'disabled' : ''}>
                                                    ${product.stock === 0 ? 'Sold Out' : 'Buy Now'}
                                                </button>
                                            </div>
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }
        
        // Close product modal
        function closeProductModal() {
            const modal = document.getElementById('productModal');
            if (modal) {
                modal.remove();
            }
        }
        
        // Redirect to checkout page
        function redirectToCheckout(category, productName, price, stock) {
            // Create URL parameters for the checkout page
            const params = new URLSearchParams({
                category: category,
                product: productName,
                price: price,
                stock: stock
            });
            
            // Redirect to checkout page
            window.location.href = `/checkout?${params.toString()}`;
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.id === 'productModal') {
                closeProductModal();
            }
        });
    </script>
@endsection