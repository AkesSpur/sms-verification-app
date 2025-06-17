@extends('layouts.main')

@section('title', ($gift['name'] ?? 'Gift Details') . ' - SMS Verification')

@section('content')
<!-- Custom Styles -->
<style>
    .carousel-container {
        position: relative;
        overflow: hidden;
        width: 100%;
    }
    .carousel-track {
        display: flex;
        transition: transform 0.3s ease;
        width: 100%;
    }
    .carousel-slide {
        width: 100%;
        min-width: 100%;
        flex-shrink: 0;
        flex-grow: 0;
    }
    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        z-index: 10;
        transition: background 0.3s ease;
    }
    .carousel-btn:hover {
        background: rgba(0, 0, 0, 0.7);
    }
    .carousel-btn.prev {
        left: 10px;
    }
    .carousel-btn.next {
        right: 10px;
    }
    .thumbnail {
        cursor: pointer;
        opacity: 0.6;
        transition: opacity 0.3s ease;
    }
    .thumbnail.active {
        opacity: 1;
        border: 2px solid #4f46e5;
    }
    .image-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        transition: border-color 0.3s ease;
        cursor: pointer;
    }
    .image-upload-area:hover {
        border-color: #4f46e5;
    }
    .image-upload-area.dragover {
        border-color: #4f46e5;
        background-color: #f8fafc;
    }
</style>

<!-- Navigation Breadcrumb -->
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center h-16">
            <nav class="flex items-center space-x-2 text-sm text-gray-600 mt-40 mb-10">
                <a href="{{ route('home') }}" class="hover:text-slate-800">Home</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <a href="{{ route('all-gifts') }}" class="hover:text-slate-800">Gifts</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-slate-800 font-medium">{{ $gift['name'] ?? 'Gift Details' }}</span>
            </nav>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-10">
    <div class="grid lg:grid-cols-2 gap-12">
        <!-- Product Images -->
        <div>
            <!-- Main Image Carousel -->
            <div class="carousel-container bg-white rounded-xl shadow-lg mb-4 aspect-square">
                <div class="carousel-track" id="mainCarousel">
                    @if(isset($gift['images']) && is_array($gift['images']))
                        @foreach($gift['images'] as $index => $image)
                            <div class="carousel-slide">
                                <img src="{{ $image }}?w=600&h=600&fit=crop" alt="Gift Image {{ $index + 1 }}" class="w-full h-full object-cover rounded-xl">
                            </div>
                        @endforeach
                    @else
                        <div class="carousel-slide">
                            <img src="https://images.unsplash.com/photo-1563241527-3004b7be0ffd?w=600&h=600&fit=crop" alt="Gift Image 1" class="w-full h-full object-cover rounded-xl">
                        </div>
                        <div class="carousel-slide">
                            <img src="https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=600&h=600&fit=crop" alt="Gift Image 2" class="w-full h-full object-cover rounded-xl">
                        </div>
                        <div class="carousel-slide">
                            <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=600&h=600&fit=crop" alt="Gift Image 3" class="w-full h-full object-cover rounded-xl">
                        </div>
                        <div class="carousel-slide">
                            <img src="https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=600&h=600&fit=crop" alt="Gift Image 4" class="w-full h-full object-cover rounded-xl">
                        </div>
                    @endif
                </div>
                <button class="carousel-btn prev" onclick="previousSlide()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-btn next" onclick="nextSlide()">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <!-- Thumbnail Images -->
            <div class="flex gap-2 overflow-x-auto">
                @if(isset($gift['images']) && is_array($gift['images']))
                    @foreach($gift['images'] as $index => $image)
                        <img src="{{ $image }}?w=150&h=150&fit=crop" alt="Thumbnail {{ $index + 1 }}" class="thumbnail {{ $index === 0 ? 'active' : '' }} aspect-square object-cover rounded-lg flex-shrink-0 w-20 h-20" onclick="goToSlide({{ $index }})">
                    @endforeach
                @else
                    <img src="https://images.unsplash.com/photo-1563241527-3004b7be0ffd?w=150&h=150&fit=crop" alt="Thumbnail 1" class="thumbnail active aspect-square object-cover rounded-lg flex-shrink-0 w-20 h-20" onclick="goToSlide(0)">
                    <img src="https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=150&h=150&fit=crop" alt="Thumbnail 2" class="thumbnail aspect-square object-cover rounded-lg flex-shrink-0 w-20 h-20" onclick="goToSlide(1)">
                    <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=150&h=150&fit=crop" alt="Thumbnail 3" class="thumbnail aspect-square object-cover rounded-lg flex-shrink-0 w-20 h-20" onclick="goToSlide(2)">
                    <img src="https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=150&h=150&fit=crop" alt="Thumbnail 4" class="thumbnail aspect-square object-cover rounded-lg flex-shrink-0 w-20 h-20" onclick="goToSlide(3)">
                @endif
            </div>
        </div>

        <!-- Product Info & Gift Form -->
        <div>
            <!-- Product Info -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                <h1 class="text-xl font-bold text-gray-900 mb-3" id="giftName">{{ $gift['name'] ?? 'Beautiful Flower Bouquet' }}</h1>
                <div class="text-2xl font-bold text-slate-700 mb-4" id="giftPrice">₦{{ number_format($gift['price'] ?? 45.99, 2) }}</div>
                
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex items-center text-green-600">
                        <i class="fas fa-check-circle mr-1"></i>
                        <span class="text-sm">Available</span>
                    </div>
                    <div class="flex items-center text-blue-600">
                        <i class="fas fa-truck mr-1"></i>
                        <span class="text-sm">Fast delivery</span>
                    </div>
                </div>
                
                <!-- Social Sharing -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Share this gift:</h4>
                    <div class="flex gap-3">
                        <button onclick="shareOnFacebook()" class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                        <button onclick="shareOnTwitter()" class="flex items-center justify-center w-10 h-10 bg-blue-400 text-white rounded-full hover:bg-blue-500 transition-colors">
                            <i class="fab fa-twitter"></i>
                        </button>
                        <button onclick="shareOnWhatsApp()" class="flex items-center justify-center w-10 h-10 bg-green-500 text-white rounded-full hover:bg-green-600 transition-colors">
                            <i class="fab fa-whatsapp"></i>
                        </button>
                        <button onclick="copyLink()" class="flex items-center justify-center w-10 h-10 bg-gray-600 text-white rounded-full hover:bg-gray-700 transition-colors">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
                
                <div class="border-t pt-4">
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Description</h3>
                    <p class="text-gray-600 text-sm leading-relaxed line-clamp-3">
                        {{ $gift['description'] ?? 'Perfect for any special occasion. Comes with premium packaging and can be customized.' }}
                    </p>
                </div>
            </div>

            <!-- Gift Form -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-heart mr-2 text-red-500"></i>Send This Gift
                </h2>
                
                <form id="giftForm" action="{{ route('gift.order') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="gift_id" id="hiddenGiftId" value="{{ $gift['id'] ?? '' }}">
                    <input type="hidden" name="gift_name" id="hiddenGiftName" value="{{ $gift['name'] ?? '' }}">
                    <input type="hidden" name="gift_price" id="hiddenGiftPrice" value="{{ $gift['price'] ?? 0 }}">
                    
                    <!-- Recipient Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recipient Information</h3>
                        
                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Recipient's Name</label>
                                <input type="text" name="recipient_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Recipient's Number</label>
                                <input type="tel" name="recipient_phone" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sender Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sender Information</h3>
                        
                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Your Name</label>
                                <input type="text" name="sender_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" name="sender_phone" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="sender_email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <!-- Delivery Address -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Delivery Address</h3>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Street Address</label>
                            <input type="text" name="delivery_address" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <input type="text" name="delivery_city" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                                <input type="text" name="delivery_state" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                <input type="text" name="delivery_country" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="Nigeria">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Zip Code</label>
                                <input type="text" name="delivery_zip" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Customize Gift -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-slate-50 to-gray-50 rounded-lg border border-slate-200">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Customize Gift</h3>
                                <p class="text-sm text-gray-600">Add a personal touch to your gift</p>
                                <p class="text-sm font-medium text-slate-600">Customization fee: ₦5,000</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="customize_gift" id="customizeToggle" class="sr-only peer" onchange="toggleCustomization()">
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-slate-300 rounded-full peer-checked:bg-slate-600 transition-colors duration-200">
                                </div>
                                <div class="absolute top-[2px] left-[2px] bg-white border border-gray-300 rounded-full h-5 w-5 transition-transform duration-200 peer-checked:translate-x-full peer-checked:border-white"></div>
                            </label>
                        </div>
                        
                        <!-- Custom Image Upload (Hidden by default) -->
                        <div id="customImageSection" class="mt-4" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Custom Image</label>
                            <div class="image-upload-area" id="imageUploadArea" onclick="document.getElementById('customImage').click()">
                                <input type="file" name="custom_image" id="customImage" accept="image/*" class="hidden" onchange="handleImageUpload(event)">
                                <div id="uploadContent">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                    <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                                    <p class="text-sm text-gray-500">PNG, JPG or GIF (MAX. 2MB)</p>
                                </div>
                                <div id="imagePreview" class="hidden">
                                    <img id="previewImg" src="" alt="Preview" class="max-w-full max-h-48 mx-auto rounded-lg">
                                    <p class="text-sm text-gray-600 mt-2">Click to change image</p>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Custom Message (Optional)</label>
                                <textarea name="custom_message" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Add a personal message to your gift..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Gift Price:</span>
                                <span class="font-medium" id="summaryPrice">₦{{ number_format($gift['price'] ?? 45.99, 2) }}</span>
                            </div>
                            <div class="flex justify-between" id="customizationFee" style="display: none;">
                                <span class="text-gray-600">Customization Fee:</span>
                                <span class="font-medium">₦5,000</span>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total:</span>
                                    <span id="totalPrice">₦{{ number_format($gift['price'] ?? 45.99, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-gradient-to-r from-slate-800 to-gray-900 hover:from-slate-900 hover:to-black text-white py-4 px-6 rounded-lg font-semibold text-lg transition-all shadow-lg hover:shadow-xl">
                        <i class="fas fa-heart mr-2"></i>Send Gift Now
                    </button>
                    
                    <p class="text-xs text-gray-500 mt-4 text-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Note: Only one gift can be sent per order. To send multiple gifts, please place separate orders.
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let currentSlide = 0;
    let totalSlides = 4;
    
    // Initialize gift data
    document.addEventListener('DOMContentLoaded', function() {
        const giftPrice = {{ $gift['price'] ?? 45.99 }};
        
        // Update page content
        document.getElementById('summaryPrice').textContent = `₦${giftPrice.toFixed(2)}`;
        document.getElementById('totalPrice').textContent = `₦${giftPrice.toFixed(2)}`;
        
        // Initialize carousel if gift images are available
        @if(isset($gift['images']) && is_array($gift['images']))
            totalSlides = {{ count($gift['images']) }};
        @endif
    });
    
    function getGiftIdFromUrl() {
        const pathParts = window.location.pathname.split('/');
        return pathParts[pathParts.length - 1];
    }
    
    function goToSlide(slideIndex) {
        currentSlide = slideIndex;
        const track = document.getElementById('mainCarousel');
        const translateX = -slideIndex * 100;
        track.style.transform = `translateX(${translateX}%)`;
        
        // Update thumbnail active state
        document.querySelectorAll('.thumbnail').forEach((thumb, index) => {
            thumb.classList.toggle('active', index === slideIndex);
        });
    }
    
    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        goToSlide(currentSlide);
    }
    
    function previousSlide() {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        goToSlide(currentSlide);
    }
    
    // Toggle customization section
    function toggleCustomization() {
        const toggle = document.getElementById('customizeToggle');
        const section = document.getElementById('customImageSection');
        const fee = document.getElementById('customizationFee');
        
        if (toggle.checked) {
            section.style.display = 'block';
            fee.style.display = 'flex';
            updateTotalPrice();
        } else {
            section.style.display = 'none';
            fee.style.display = 'none';
            updateTotalPrice();
        }
    }
    
    // Social sharing functions
    function shareOnFacebook() {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(document.getElementById('giftName').textContent);
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
    }
    
    function shareOnTwitter() {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(document.getElementById('giftName').textContent);
        const text = encodeURIComponent(`Check out this amazing gift: ${title}`);
        window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank', 'width=600,height=400');
    }
    
    function shareOnWhatsApp() {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(document.getElementById('giftName').textContent);
        const text = encodeURIComponent(`Check out this amazing gift: ${title} ${url}`);
        window.open(`https://wa.me/?text=${text}`, '_blank');
    }
    
    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            // Show a temporary notification
            const button = event.target.closest('button');
            const originalIcon = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.classList.add('bg-green-600');
            setTimeout(() => {
                button.innerHTML = originalIcon;
                button.classList.remove('bg-green-600');
            }, 2000);
        });
    }
    
    // Update total price
    function updateTotalPrice() {
        const basePrice = {{ $gift['price'] ?? 45.99 }};
        const customizationFee = document.getElementById('customizeToggle').checked ? 5000 : 0;
        const total = basePrice + customizationFee;
        
        document.getElementById('totalPrice').textContent = `₦${total.toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }
    
    // Handle image upload
    function handleImageUpload(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const uploadContent = document.getElementById('uploadContent');
                const imagePreview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImg');
                
                previewImg.src = e.target.result;
                uploadContent.classList.add('hidden');
                imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }
    
    // Drag and drop functionality
    const uploadArea = document.getElementById('imageUploadArea');
    
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('customImage').files = files;
            handleImageUpload({ target: { files: files } });
        }
    });
</script>
@endsection