@extends('layouts.main')

@section('title', 'All Categories - Digital Gift Cards')

@section('content')
    <!-- Image Banner Carousel Section -->
    @if($banners->count() > 0)
    <section class="pt-16 pb-4 bg-gradient-to-r from-slate-50 to-gray-100 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Carousel Container -->
            <div class="relative" data-aos="fade-up">
                <div class="carousel-container overflow-hidden rounded-2xl shadow-2xl">
                    <div class="carousel-wrapper flex transition-transform duration-500 ease-in-out" id="carousel">
                        @foreach($banners as $banner)
                        <!-- Banner Slide {{ $loop->iteration }} -->
                        <div class="carousel-slide w-full flex-shrink-0 relative">
                            <div class="relative h-[155px] sm:h-[200px] md:h-[250px] lg:h-[300px] xl:h-[300px] overflow-hidden">
                                @if($banner->link_url)
                                    <a href="{{ $banner->link_url }}" target="_blank" class="block w-full h-full">
                                        <img src="{{ $banner->image_url }}" 
                                             alt="{{ $banner->title ?? 'Banner' }}" 
                                             class="w-[100%] h-full object-cover hover:scale-105 transition-transform duration-300">
                                    </a>
                                @else
                                    <img src="{{ $banner->image_url }}" 
                                         alt="{{ $banner->title ?? 'Banner' }}" 
                                         class="w-full h-full object-cover"
                                         loading="lazy">
                                @endif                                                                
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                @if($banners->count() > 1)
                <!-- Navigation Arrows -->
                <button class="carousel-btn carousel-prev absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 text-gray-800 p-3 rounded-full shadow-lg z-10">
                    <i class="fas fa-chevron-left text-xl"></i>
                </button>
                <button class="carousel-btn carousel-next absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 text-gray-800 p-3 rounded-full shadow-lg z-10">
                    <i class="fas fa-chevron-right text-xl"></i>
                </button>
                
                <!-- Dots Indicator -->
                <div class="flex justify-center mt-8 space-x-2">
                    @foreach($banners as $banner)
                        <button class="carousel-dot w-3 h-3 rounded-full bg-gray-400 hover:bg-gray-600 transition-colors" data-slide="{{ $loop->index }}"></button>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </section>
    @else
    <!-- Fallback Hero Section when no banners -->
    <section class="relative pt-16 pb-4 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-50 via-white to-gray-100"></div>
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl animate-blob"></div>
            <div class="absolute top-40 right-10 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-20 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-4000"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-8" data-aos="fade-up">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                    All Digital <span class="gradient-text">Gift Cards</span>
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Browse our complete collection of digital gift cards for all your favorite platforms and services
                </p>
            </div>
        </div>
    </section>
    @endif

    <!-- All Categories Section -->
    <section class="pt-6 pb-4 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @foreach($digitalCategories as $index => $category)
            <!-- {{ $category->name }} Categories -->
            <div class="mb-12" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $category->name }}</h2>
                    <p class="text-gray-600">{{ $category->description ?? 'Digital gift cards for ' . strtolower($category->name) }}</p>
                </div>
                
                <!-- Initial 8 subcategories -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6" id="subcategories-grid-{{ $index }}">
                    @php
                        $activeSubcategories = $category->activeSubcategories->filter(function($subcategory) {
                            return $subcategory->activeProducts->count() > 0;
                        });
                        $totalSubcategories = $activeSubcategories->count();
                    @endphp
                    
                    @foreach($activeSubcategories->take(8) as $subcategory)
                        <!-- {{ $subcategory->name }} Gift Card -->
                        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale cursor-pointer group" onclick="openProductModal('{{ $subcategory->name }}', {{ $subcategory->id }})">
                            <div class="p-6 text-center">
                                <div class="w-20 h-20 mx-auto mb-4 rounded-xl overflow-hidden group-hover:scale-110 transition-transform">
                                    @if($subcategory->image)
                                        <img src="{{ asset( $subcategory->image) }}" alt="{{ $subcategory->name }}" class="w-full h-full object-contain">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                            <span class="text-white font-bold text-lg">{{ substr($subcategory->name, 0, 2) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <h4 class="font-bold text-gray-900 mb-2">{{ $subcategory->name }}</h4>
                                <div class="text-xs text-green-600 font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i>Instant Delivery
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Hidden additional subcategories -->
                @if($totalSubcategories > 8)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6 hidden" id="additional-subcategories-{{ $index }}">
                    @foreach($activeSubcategories->skip(8) as $subcategory)
                        <!-- {{ $subcategory->name }} Gift Card -->
                        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover-scale cursor-pointer group" onclick="openProductModal('{{ $subcategory->name }}', {{ $subcategory->id }})">
                            <div class="p-6 text-center">
                                <div class="w-20 h-20 mx-auto mb-4 rounded-xl overflow-hidden group-hover:scale-110 transition-transform">
                                    @if($subcategory->image)
                                        <img src="{{ asset( $subcategory->image) }}" alt="{{ $subcategory->name }}" class="w-full h-full object-contain">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                            <span class="text-white font-bold text-lg">{{ substr($subcategory->name, 0, 2) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <h4 class="font-bold text-gray-900 mb-2">{{ $subcategory->name }}</h4>
                                <div class="text-xs text-green-600 font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i>Instant Delivery
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- View More/Less Button -->
                <div class="text-center mt-8">
                    <button id="toggle-button-{{ $index }}" onclick="toggleSubcategories({{ $index }})" class="bg-gradient-to-r from-slate-800 to-gray-900 hover:from-slate-900 hover:to-black text-white px-6 py-3 rounded-lg font-semibold transition-all hover-scale shadow-lg">
                        <i class="fas fa-chevron-down mr-2" id="toggle-icon-{{ $index }}"></i>View More ({{ $totalSubcategories - 8 }} more)
                    </button>
                </div>
                @endif
            </div>
            @endforeach

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
        // Product data from database
        const digitalProductsData = @json($digitalProductsData);
        
        // Convert to the format expected by the modal
        const productData = {};
        digitalProductsData.forEach(subcategory => {
            productData[subcategory.name] = subcategory.products.map(product => ({
                id: product.id,
                name: product.name,
                price: '₦' + parseFloat(product.price).toLocaleString(),
                stock: product.stock,
                slug: product.slug,
                image: product.image
            }));
        });
        
        // Get product image based on category
        function getProductImage(category, subcategoryId) {
            const subcategory = digitalProductsData.find(sub => sub.id === subcategoryId);
            if (subcategory && subcategory.image) {
                return `{{ asset('') }}${subcategory.image}`;
            }
            // Fallback to a default image
            return 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=400&h=300&fit=crop&crop=center';
        }

        // Open product modal with redesigned cards
        function openProductModal(category, subcategoryId) {
            const products = productData[category] || [];
            const subcategory = digitalProductsData.find(sub => sub.id === subcategoryId);
            const subcategoryImage = subcategory && subcategory.image ? `{{ asset('') }}${subcategory.image}` : null;
            
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
                                    
                                    const productImageSrc = product.image ? `{{ asset('') }}${product.image}` : (subcategoryImage || 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=400&h=300&fit=crop&crop=center');
                                    
                                    return `
                                        <div class="bg-white border border-gray-200 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden cursor-pointer transform hover:-translate-y-1" onclick="redirectToCheckout('${product.slug}')">
                                            <div class="relative">
                                                <img src="${productImageSrc}" alt="${product.name}" class="w-full h-48 object-cover">
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
        
        // Redirect to checkout page with product details
        function redirectToCheckout(productSlug) {
            window.location.href = `/product/${productSlug}`;
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.id === 'productModal') {
                closeProductModal();
            }
        });
        
        // Toggle subcategories visibility
        function toggleSubcategories(categoryIndex) {
            const additionalSubcategories = document.getElementById(`additional-subcategories-${categoryIndex}`);
            const toggleButton = document.getElementById(`toggle-button-${categoryIndex}`);
            const toggleIcon = document.getElementById(`toggle-icon-${categoryIndex}`);
            
            if (additionalSubcategories.classList.contains('hidden')) {
                // Show additional subcategories
                additionalSubcategories.classList.remove('hidden');
                toggleButton.innerHTML = '<i class="fas fa-chevron-up mr-2"></i>View Less';
            } else {
                // Hide additional subcategories
                additionalSubcategories.classList.add('hidden');
                const totalSubcategories = additionalSubcategories.children.length + 8;
                const remainingCount = totalSubcategories - 8;
                toggleButton.innerHTML = `<i class="fas fa-chevron-down mr-2"></i>View More (${remainingCount} more)`;
            }
        }
        
        // Carousel functionality
        @if($banners->count() > 0)
        let currentSlide = 0;
        const totalSlides = {{ $banners->count() }};
        let carouselInterval;

        function updateCarousel() {
            const carousel = document.getElementById('carousel');
            if (carousel) {
                carousel.style.transform = `translateX(-${currentSlide * 100}%)`;
                
                // Update dots
                document.querySelectorAll('.carousel-dot').forEach((dot, index) => {
                    if (index == currentSlide) {
                        dot.classList.remove('bg-gray-400');
                        dot.classList.add('bg-gray-800');
                    } else {
                        dot.classList.remove('bg-gray-800');
                        dot.classList.add('bg-gray-400');
                    }
                });
            }
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateCarousel();
        }

        function goToSlide(slideIndex) {
            currentSlide = slideIndex;
            updateCarousel();
        }

        // Auto-play carousel
        function startCarousel() {
            carouselInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
        }

        function stopCarousel() {
            if (carouselInterval) {
                clearInterval(carouselInterval);
            }
        }

        // Initialize carousel when page loads
        document.addEventListener('DOMContentLoaded', function() {
            updateCarousel();
            startCarousel();

            // Add event listeners for navigation buttons
            const prevBtn = document.querySelector('.carousel-prev');
            const nextBtn = document.querySelector('.carousel-next');
            
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    stopCarousel();
                    prevSlide();
                    startCarousel();
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    stopCarousel();
                    nextSlide();
                    startCarousel();
                });
            }

            // Add event listeners for dots
            document.querySelectorAll('.carousel-dot').forEach((dot, index) => {
                dot.addEventListener('click', function() {
                    stopCarousel();
                    goToSlide(index);
                    startCarousel();
                });
            });

            // Pause carousel on hover
            const carouselContainer = document.querySelector('.carousel-container');
            if (carouselContainer) {
                carouselContainer.addEventListener('mouseenter', stopCarousel);
                carouselContainer.addEventListener('mouseleave', startCarousel);
            }
        });
        @endif
    </script>
@endsection