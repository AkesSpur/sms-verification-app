@extends('layouts.main')

@section('title', ($gift->name ?? 'Gift Details') . ' - SMS Verification')

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
                <span class="text-slate-800 font-medium">{{ $gift->name ?? 'Gift Details' }}</span>
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
                    <div class="carousel-slide">
                        <img src="{{asset($gift->featured_image)}}?w=600&h=600&fit=crop" alt="{{$gift->alt_text ?? 'Gift Image ' . (1) }}" class="w-full h-full object-cover rounded-xl">
                    </div>
                    @foreach($gift->images as $index => $image)
                        <div class="carousel-slide">
                            <img src="{{ $image->image_url }}?w=600&h=600&fit=crop" alt="{{ $image->alt_text ?? 'Gift Image ' . ($index + 2) }}" class="w-full h-full object-cover rounded-xl">
                        </div>
                    @endforeach
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
                    <img src="{{ asset($gift->featured_image) }}?w=150&h=150&fit=crop" alt="{{ $image->alt_text ?? 'Thumbnail '. 1 }}" class="thumbnail active aspect-square object-cover rounded-lg flex-shrink-0 w-20 h-20" onclick="goToSlide(0)">
                @foreach($gift->images as $index => $image)
                    <img src="{{ $image->image_url }}?w=150&h=150&fit=crop" alt="{{ $image->alt_text ?? 'Thumbnail ' . ($index + 2) }}" class="thumbnail aspect-square object-cover rounded-lg flex-shrink-0 w-20 h-20" onclick="goToSlide({{ $index + 1}})">
                @endforeach
            </div>
        </div>

        <!-- Product Info & Gift Form -->
        <div>
            <!-- Product Info -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                <h1 class="text-xl font-bold text-gray-900 mb-3" id="giftName">{{ $gift->name }}</h1>
                <div class="text-2xl font-bold text-slate-700 mb-4" id="giftPrice">₦{{ number_format($gift->price, 0) }}</div>
                
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex items-center text-green-600">
                        <i class="fas fa-check-circle mr-1"></i>
                        <span class="text-sm">In Stock</span>
                    </div>
                    <div class="flex items-center text-blue-600">
                        <i class="fas fa-truck mr-1"></i>
                        <span class="text-sm">Delivery: 1-3 days</span>
                    </div>
                    @if($gift->customizable)
                        <div class="px-2 py-1 text-xs font-semibold bg-purple-100 text-purple-800 rounded-full">
                            Customizable
                        </div>
                    @endif
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
                    <div class="prose prose-sm text-gray-600 mb-6">
                        {!! $gift->description ?? 'This beautiful gift package includes premium items carefully selected to bring joy and happiness. Perfect for any occasion, this thoughtful present will surely make your loved ones smile.' !!}
                    </div>
                </div>
            </div>

            <!-- Gift Form -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-heart mr-2 text-red-500"></i>Send This Gift
                </h2>
                
                <form id="giftForm" action="{{ route('gift-order.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="gift_id" id="hiddenGiftId" value="{{ $gift->id }}">
                    <input type="hidden" name="gift_name" id="hiddenGiftName" value="{{ $gift->name }}">
                    <input type="hidden" name="gift_price" id="hiddenGiftPrice" value="{{ $gift->price }}">
                    
                    <!-- Recipient Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recipient Information</h3>
                        
                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Recipient's Name</label>
                                <input type="text" name="recipient_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <div class="error-message text-red-500 text-sm mt-1 hidden" data-field="recipient_name"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Recipient's Number</label>
                                <input type="tel" name="recipient_phone" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <div class="error-message text-red-500 text-sm mt-1 hidden" data-field="recipient_phone"></div>
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
                                <div class="error-message text-red-500 text-sm mt-1 hidden" data-field="sender_name"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" name="sender_phone" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <div class="error-message text-red-500 text-sm mt-1 hidden" data-field="sender_phone"></div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="sender_email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <div class="error-message text-red-500 text-sm mt-1 hidden" data-field="sender_email"></div>
                        </div>
                    </div>
                    
                    <!-- Delivery Address -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Delivery Address</h3>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Street Address</label>
                            <input type="text" name="delivery_address" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <div class="error-message text-red-500 text-sm mt-1 hidden" data-field="delivery_address"></div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Apartment/Suite (Optional)</label>
                            <input type="text" name="delivery_apartment" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Apt, Suite, Unit, Building, Floor, etc.">
                            <div class="error-message text-red-500 text-sm mt-1 hidden" data-field="delivery_apartment"></div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <input type="text" name="delivery_city" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <div class="error-message text-red-500 text-sm mt-1 hidden" data-field="delivery_city"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                                <input type="text" name="delivery_state" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <div class="error-message text-red-500 text-sm mt-1 hidden" data-field="delivery_state"></div>
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                <input type="text" name="delivery_country" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="Nigeria">
                                <div class="error-message text-red-500 text-sm mt-1 hidden" data-field="delivery_country"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Zip Code</label>
                                <input type="text" name="delivery_zip" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <div class="error-message text-red-500 text-sm mt-1 hidden" data-field="delivery_zip"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Customize Gift -->
                    @if($gift->customizable)
                        <div class="mb-6">
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-slate-50 to-gray-50 rounded-lg border border-slate-200">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Customize Gift</h3>
                                    <p class="text-sm text-gray-600">Add a personal touch to your gift</p>
                                    <p class="text-sm font-medium text-slate-600">Customization fee: ₦{{ number_format($gift->customization_cost ?? 5000, 0) }}</p>
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
                                    <div class="error-message text-red-500 text-sm mt-1 hidden" data-field="custom_image"></div>
                                    <div id="uploadContent">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                        <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                                        <p class="text-sm text-gray-500">PNG, JPG or GIF (MAX. 5MB)</p>
                                    </div>
                                    <div id="imagePreview" class="hidden">
                                        <img id="previewImg" src="" alt="Preview" class="max-w-full max-h-48 mx-auto rounded-lg">
                                        <p class="text-sm text-gray-600 mt-2">Click to change image</p>
                                    </div>
                                </div>
                                

                            </div>
                        </div>
                    @endif
                    
                    <!-- Order Summary -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Gift Price:</span>
                                <span class="font-medium" id="summaryPrice">₦{{ number_format($gift->price, 0) }}</span>
                            </div>
                            @if($gift->customizable)
                                <div class="flex justify-between" id="customizationFee" style="display: none;">
                                    <span class="text-gray-600">Customization Fee:</span>
                                    <span class="font-medium">₦{{ number_format($gift->customization_cost ?? 5000, 0) }}</span>
                                </div>
                            @endif
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total:</span>
                                    <span id="totalPrice">₦{{ number_format($gift->price, 0) }}</span>
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
@endsection

@section('scripts')
<script>
// Gift data for JavaScript calculations
const giftPrice = {{ $gift->price }};
const customizationCost = {{ $gift->customization_cost ?? 0 }};
const giftName = '{{ addslashes($gift->name) }}';
const giftId = {{ $gift->id }};
@auth
const userBalance = {{ auth()->user()->balance }};
const isAuthenticated = true;
@else
const userBalance = 0;
const isAuthenticated = false;
@endauth

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    updateTotalPrice();
    initializeGiftForm();
    
    // Add event listeners to form fields for real-time error clearing
    const formFields = document.querySelectorAll('input[name], select[name], textarea[name]');
    formFields.forEach(field => {
        field.addEventListener('input', function() {
            clearFieldError(this.name);
        });
        field.addEventListener('change', function() {
            clearFieldError(this.name);
        });
    });
});

// Carousel functionality
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-slide');
const totalSlides = slides.length;

function updateCarousel() {
    const track = document.getElementById('mainCarousel');
    track.style.transform = `translateX(-${currentSlide * 100}%)`;
    
    // Update thumbnails
    document.querySelectorAll('.thumbnail').forEach((thumb, index) => {
        thumb.classList.toggle('active', index === currentSlide);
    });
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    updateCarousel();
}

function previousSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    updateCarousel();
}

function goToSlide(index) {
    currentSlide = index;
    updateCarousel();
}

// Customization toggle
function toggleCustomization() {
    const toggle = document.getElementById('customizeToggle');
    const section = document.getElementById('customImageSection');
    const feeRow = document.getElementById('customizationFee');
    
    if (toggle.checked) {
        section.style.display = 'block';
        if (feeRow) feeRow.style.display = 'flex';
    } else {
        section.style.display = 'none';
        if (feeRow) feeRow.style.display = 'none';
        // Clear the file input
        document.getElementById('customImage').value = '';
        resetImageUpload();
    }
    
    updateTotalPrice();
}

// Image upload handling
function handleImageUpload(event) {
    const file = event.target.files[0];
    if (file) {
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            showNotification('File size must be less than 5MB', 'error');
            event.target.value = '';
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            showNotification('Please select a valid image file (JPEG, PNG, JPG, or GIF)', 'error');
            event.target.value = '';
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('uploadContent').style.display = 'none';
            document.getElementById('imagePreview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function resetImageUpload() {
    document.getElementById('uploadContent').style.display = 'block';
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('previewImg').src = '';
}

// Drag and drop functionality
const uploadArea = document.getElementById('imageUploadArea');

if (uploadArea) {
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('customImage').files = files;
            handleImageUpload({ target: { files: files } });
        }
    });
}

// Update total price
function updateTotalPrice() {
    const isCustomized = document.getElementById('customizeToggle')?.checked || false;
    const total = giftPrice + (isCustomized ? customizationCost : 0);
    
    document.getElementById('totalPrice').textContent = `₦${total.toLocaleString()}`;
}

// Social sharing functions
function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(`Check out this amazing gift: ${giftName}!`);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(`Check out this amazing gift: ${giftName}!`);
    window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank', 'width=600,height=400');
}

function shareOnWhatsApp() {
    const url = window.location.href;
    const text = encodeURIComponent(`Check out this amazing gift: ${giftName}! ${url}`);
    window.open(`https://wa.me/?text=${text}`, '_blank');
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        showNotification('Link copied to clipboard!', 'success');
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        showNotification('Failed to copy link', 'error');
    });
}

// Initialize gift form
function initializeGiftForm() {
    const giftForm = document.getElementById('giftForm');
    if (giftForm && isAuthenticated) {
        giftForm.addEventListener('submit', handleGiftOrder);
    }
}

// Handle gift order process
function handleGiftOrder(e) {
    e.preventDefault();
    
    // Clear any existing errors
    clearValidationErrors();
    
    // Calculate total amount
    const isCustomized = document.getElementById('customizeToggle')?.checked || false;
    const totalAmount = giftPrice + (isCustomized ? customizationCost : 0);
    
    // Basic client-side validation
    const requiredFields = document.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('border-red-500');
        } else {
            field.classList.remove('border-red-500');
        }
    });
    
    if (!isValid) {
        showNotification('Please fill in all required fields.', 'error');
        return;
    }
    
    // Check user balance
    if (userBalance < totalAmount) {
        const needed = totalAmount - userBalance;
        showNotification(`Insufficient balance. You need ₦${needed.toLocaleString()} more to complete this purchase.`, 'error');
        return;
    }
    
    // Show confirmation modal
    showGiftConfirmationModal(giftName, totalAmount, isCustomized, () => {
        // Disable button and show loading
        setGiftLoadingState(true);
        
        // Continue with gift order
        processGiftOrder(totalAmount);
    });
}

// Show gift confirmation modal
function showGiftConfirmationModal(giftName, totalAmount, isCustomized, onConfirm) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-gifts text-pink-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Confirm Gift Order</h3>
                <p class="text-gray-600">Are you sure you want to send this gift?</p>
            </div>
            
            <div class="border-t pt-4 mb-4">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Gift:</span>
                        <span class="font-medium">${giftName}</span>
                    </div>
                    ${isCustomized ? `
                    <div class="flex justify-between">
                        <span class="text-gray-600">Customization:</span>
                        <span class="font-medium text-purple-600">Yes (+₦${customizationCost.toLocaleString()})</span>
                    </div>
                    ` : ''}
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Amount:</span>
                        <span class="font-medium text-lg">₦${totalAmount.toLocaleString()}</span>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-3">
                <button onclick="closeModal(this)" class="flex-1 bg-gray-200 text-gray-800 py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button onclick="confirmGiftOrder(this)" class="flex-1 bg-slate-800 text-white py-2 px-4 rounded-lg hover:bg-slate-900 transition-colors">
                    <i class="fas fa-heart mr-1"></i>Send Gift
                </button>
            </div>  
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Store the callback function
    window.currentGiftOrderCallback = onConfirm;
}

// Confirm gift order from modal
function confirmGiftOrder(button) {
    closeModal(button);
    if (window.currentGiftOrderCallback) {
        window.currentGiftOrderCallback();
        window.currentGiftOrderCallback = null;
    }
}

// Process the actual gift order
function processGiftOrder(totalAmount) {
    const formData = new FormData(document.getElementById('giftForm'));
    
    // Explicitly set the customize_gift boolean value
    const isCustomized = document.getElementById('customizeToggle')?.checked || false;
    formData.set('customize_gift', isCustomized ? '1' : '0');
    
    // Make AJAX request
    fetch('{{ route("gift-order.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        setGiftLoadingState(false);
        
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Update UI with new data
            if (data.data) {
                updateUserBalance(data.data.remaining_balance);
                
                // Show gift order details
                setTimeout(() => {
                    showGiftOrderDetails(data.data);
                }, 1500);
            }
        } else {
            showNotification(data.message || 'Gift order failed. Please try again.', 'error');
            if (data.errors) {
                // Display validation errors
                displayValidationErrors(data.errors);
                displayValidationErrorsAsToast(data.errors);
            }
        }
    })
    .catch(error => {
        console.error('Gift order error:', error);
        setGiftLoadingState(false);
        showNotification('An unexpected error occurred. Please try again.', 'error');
    });
}

// Set gift loading state
function setGiftLoadingState(loading) {
    const submitBtn = document.querySelector('#giftForm button[type="submit"]');
    
    if (loading) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing Gift Order...';
    } else {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-heart mr-2"></i>Send Gift';
    }
}

// Update user balance display
function updateUserBalance(newBalance) {
    const balanceElement = document.getElementById('userBalance');
    if (balanceElement) {
        balanceElement.textContent = `₦${newBalance.toLocaleString()}`;
    }
}

// Show notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;
    
    // Set notification style based on type
    if (type == 'success') {
        notification.className += ' bg-green-500 text-white';
        notification.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${message}`;
    } else if (type == 'error') {
        notification.className += ' bg-red-500 text-white';
        notification.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i>${message}`;
    } else {
        notification.className += ' bg-blue-500 text-white';
        notification.innerHTML = `<i class="fas fa-info-circle mr-2"></i>${message}`;
    }
    
    // Add to page
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after delay
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 5000);
}

// Show gift order details modal
function showGiftOrderDetails(data) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-green-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Gift Order Successful!</h3>
                <p class="text-gray-600">Your gift has been ordered and will be delivered soon.</p>
            </div>
            
            <div class="border-t pt-4 mb-4">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Gift:</span>
                        <span class="font-medium">${data.gift_name}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Order Number:</span>
                        <span class="font-medium">#${data.order_number}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Paid:</span>
                        <span class="font-medium">₦${data.total_amount.toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Remaining Balance:</span>
                        <span class="font-medium">₦${data.remaining_balance.toLocaleString()}</span>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-3">
                <button onclick="closeModal(this)" class="flex-1 bg-gray-200 text-gray-800 py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors">
                    Close
                </button>
                <a href="{{ route('user.order-history') }}" class="flex-1 bg-slate-800 text-white py-2 px-4 rounded-lg hover:bg-slate-900 transition-colors text-center">
                    View Orders
                </a>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Close modal
function closeModal(button) {
    const modal = button.closest('.fixed');
    if (modal && document.body.contains(modal)) {
        document.body.removeChild(modal);
    }
}

// Clear specific field error
function clearFieldError(fieldName) {
    const fieldElement = document.querySelector(`[name="${fieldName}"]`);
    const errorContainer = document.querySelector(`[data-field="${fieldName}"]`);
    
    if (fieldElement) {
        fieldElement.classList.remove('border-red-500', 'border-red-400');
        fieldElement.classList.add('border-gray-300');
    }
    
    if (errorContainer) {
        errorContainer.classList.add('hidden');
        errorContainer.textContent = '';
    }
}

// Clear validation errors
function clearValidationErrors() {
    // Remove error styling from input fields
    const inputFields = document.querySelectorAll('input[name], select[name], textarea[name]');
    inputFields.forEach(field => {
        field.classList.remove('border-red-500', 'border-red-400');
        field.classList.add('border-gray-300');
    });
    
    // Hide all error messages
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(errorMsg => {
        errorMsg.classList.add('hidden');
        errorMsg.textContent = '';
    });
}

// Display validation errors
function displayValidationErrors(errors) {
    Object.keys(errors).forEach(field => {
        const fieldElement = document.querySelector(`[name="${field}"]`);
        const errorContainer = document.querySelector(`[data-field="${field}"]`);
        
        if (fieldElement) {
            // Add error styling to input field
            fieldElement.classList.remove('border-gray-300');
            fieldElement.classList.add('border-red-500');
            
            // Focus on first error field
            if (Object.keys(errors)[0] === field) {
                fieldElement.focus();
                fieldElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        
        if (errorContainer && errors[field][0]) {
            // Show error message
            errorContainer.textContent = errors[field][0];
            errorContainer.classList.remove('hidden');
        }
    });
}

// Display validation errors as notifications
function displayValidationErrorsAsToast(errors) {
    Object.keys(errors).forEach(field => {
        const fieldErrors = errors[field];
        if (Array.isArray(fieldErrors) && fieldErrors.length > 0) {
            // Get field label for better user experience
            const fieldElement = document.querySelector(`[name="${field}"]`);
            let fieldLabel = field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            
            if (fieldElement) {
                const label = fieldElement.closest('.mb-4, .mb-6')?.querySelector('label');
                if (label) {
                    fieldLabel = label.textContent.replace('*', '').trim();
                }
            }
            
            // Show notification for each error
            fieldErrors.forEach(error => {
                showNotification(`${fieldLabel}: ${error}`, 'error');
            });
        }
    });
}
</script>


@endsection