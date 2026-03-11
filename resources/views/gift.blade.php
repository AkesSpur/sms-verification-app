@extends('layouts.app')

@section('title', 'Gift Checkout - ' . $gift->name)

@section('content')

<div class="pb-20">
        <div class="grid lg:grid-cols-12 gap-8">
            <!-- Left Column: Checkout Form -->
            <div class="lg:col-span-7 xl:col-span-8 order-2 lg:order-1">
                <form id="giftCheckoutForm" action="{{ route('gift-order.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="gift_id" value="{{ $gift->id }}">
                    <input type="hidden" name="gift_name" value="{{ $gift->name }}">
                    <input type="hidden" name="gift_price" value="{{ $gift->price }}">
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                        <!-- Section: Recipient & Delivery -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <i class="ri-map-pin-line mr-2"></i> Delivery Info
                            </h3>
                            
                            <div class="grid md:grid-cols-2 gap-5 mb-5">
                                <div class="space-y-1.5">
                                    <input type="text" name="recipient_name" value="{{ old('recipient_name') }}" required 
                                        class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-900 focus:border-transparent transition-all outline-none"
                                        placeholder="Recipient's Name">
                                    @error('recipient_name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="space-y-1.5">
                                    <input type="tel" name="recipient_phone" value="{{ old('recipient_phone') }}" required 
                                        class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-900 focus:border-transparent transition-all outline-none"
                                        placeholder="Recipient's Phone">
                                    @error('recipient_phone')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div class="space-y-1.5">
                                    <textarea name="delivery_address" required rows="2" 
                                        class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-900 focus:border-transparent transition-all outline-none resize-none"
                                        placeholder="Street Address">{{ old('delivery_address') }}</textarea>
                                    @error('delivery_address')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="grid md:grid-cols-2 gap-5">
                                    <div class="space-y-1.5">
                                        <input type="text" name="delivery_apartment" value="{{ old('delivery_apartment') }}" 
                                            class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-900 focus:border-transparent transition-all outline-none"
                                            placeholder="Apartment, Suite, Unit (Optional)">
                                    </div>
                                    <div class="space-y-1.5">
                                        <input type="text" name="delivery_city" value="{{ old('delivery_city') }}" required 
                                            class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-900 focus:border-transparent transition-all outline-none"
                                            placeholder="City">
                                        @error('delivery_city')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="grid md:grid-cols-3 gap-5">
                                    <div class="space-y-1.5">
                                        <input type="text" name="delivery_state" value="{{ old('delivery_state') }}" required 
                                            class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-900 focus:border-transparent transition-all outline-none"
                                            placeholder="State">
                                        @error('delivery_state')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="space-y-1.5">
                                        <input type="text" name="delivery_country" value="{{ old('delivery_country') }}" required 
                                            class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-900 focus:border-transparent transition-all outline-none"
                                            placeholder="Country">
                                    </div>
                                    <div class="space-y-1.5">
                                        <input type="text" name="delivery_zip" value="{{ old('delivery_zip') }}" 
                                            class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-900 focus:border-transparent transition-all outline-none"
                                            placeholder="ZIP Code">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100 my-8">

                        <!-- Section: Sender -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <i class="ri-user-star-line mr-2"></i> Sender Info
                            </h3>
                            
                            <div class="grid md:grid-cols-2 gap-5 mb-5">
                                <div class="space-y-1.5">
                                    <input type="text" name="sender_name" value="{{ old('sender_name') }}" required 
                                            class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-900 focus:border-transparent transition-all outline-none"
                                            placeholder="Your Name">
                                    @error('sender_name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="space-y-1.5">
                                    <input type="tel" name="sender_phone" value="{{ old('sender_phone') }}" required 
                                            class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-900 focus:border-transparent transition-all outline-none"
                                            placeholder="Your Phone">
                                    @error('sender_phone')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="space-y-1.5 hidden">
                                <input type="email" name="sender_email" value="{{ old('sender_email', auth()->user()->email ?? '') }}" required 
                                            class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-slate-900 focus:border-transparent transition-all outline-none"
                                            placeholder="Your Email">
                                @error('sender_email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section: Customization -->
                    @if($gift->customizable)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-pink-50 flex items-center justify-center text-pink-600 mr-3">
                                        <i class="ri-palette-line text-sm"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">Customization</h3>
                                        @if($gift->customization_cost)
                                            <p class="text-sm text-gray-500">+₦{{ number_format($gift->customization_cost, 0) }}</p>
                                        @endif
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="customize_gift" value="0">
                                    <input type="checkbox" name="customize_gift" id="customizeToggle" value="1" class="sr-only peer" onchange="toggleCustomization()" {{ old('customize_gift') ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-slate-900"></div>
                                </label>
                            </div>
                            
                            <div id="customImageSection" class="{{ old('customize_gift') ? '' : 'hidden' }} pt-4 border-t border-gray-100 animate-fade-in">
                                <label class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-3 block">Upload Custom Image</label>
                                <div class="relative group cursor-pointer" id="imageUploadArea" onclick="document.getElementById('customImage').click()">
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-black transition-colors bg-gray-50 hover:bg-white">
                                        <input type="file" name="custom_image" id="customImage" accept="image/*" class="hidden" onchange="handleImageUpload(event)">
                                        <div id="uploadContent">
                                            <div class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mx-auto mb-3 text-gray-400 group-hover:text-black transition-colors">
                                                <i class="ri-upload-cloud-2-line text-xl"></i>
                                            </div>
                                            <p class="text-sm font-medium text-gray-900">Click or drag image to upload</p>
                                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, WEBP (Max 5MB)</p>
                                        </div>
                                        <div id="imagePreview" class="hidden">
                                            <img id="previewImg" src="" alt="Preview" class="max-h-64 mx-auto rounded-lg shadow-sm object-contain">
                                            <p class="text-xs text-gray-500 mt-3">Click to change</p>
                                        </div>
                                    </div>
                                    @error('custom_image')
                                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Submit Button Mobile (Sticky Bottom) -->
                    <div class="lg:hidden fixed bottom-0 left-0 right-0 p-4 bg-white border-t border-gray-100 z-40">
                         @auth
                            <button type="submit" id="submitBtnMobile" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-4 rounded-xl font-bold text-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg">
                                Pay <span id="submitBtnTextMobile">₦{{ number_format($gift->price, 0) }}</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="block w-full bg-slate-900 hover:bg-slate-800 text-white py-4 rounded-xl font-bold text-lg text-center transition-all">
                                Login to Order
                            </a>
                        @endauth
                    </div>
                </form>
            </div>

            <!-- Right Column: Summary -->
            <div class="lg:col-span-5 xl:col-span-4 order-1 lg:order-2">
                <div class="sticky top-24 space-y-6">
                    <!-- Gift Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <!-- Image Gallery -->
                        <div class="relative aspect-square bg-gray-100">
                             @if(count($gift->images) > 0)
                                <div class="absolute inset-0 flex transition-transform duration-500 ease-out" id="carouselTrack">
                                    <!-- Main Image -->
                                    <div class="min-w-full h-full relative">
                                        <img src="{{ asset($gift->featured_image) }}" class="w-full h-full object-cover">
                                    </div>
                                    <!-- Gallery Images -->
                                    @foreach($gift->images as $image)
                                        <div class="min-w-full h-full relative">
                                            <img src="{{ asset($image->image_url) }}" class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                                @if(count($gift->images) > 0)
                                    <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
                                        <button onclick="goToSlide(0)" class="w-2 h-2 rounded-full bg-white transition-all w-4 carousel-indicator"></button>
                                        @foreach($gift->images as $index => $image)
                                            <button onclick="goToSlide({{ $index + 1 }})" class="w-2 h-2 rounded-full bg-white/50 hover:bg-white transition-all carousel-indicator"></button>
                                        @endforeach
                                    </div>
                                    <button onclick="previousSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white hover:bg-white/40 transition-all">
                                        <i class="ri-arrow-left-s-line text-xs"></i>
                                    </button>
                                    <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white hover:bg-white/40 transition-all">
                                        <i class="ri-arrow-right-s-line text-xs"></i>
                                    </button>
                                @endif
                            @else
                                <img src="{{ asset($gift->featured_image) }}" alt="{{ $gift->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $gift->name }}</h2>
                            <p class="text-gray-500 text-sm leading-relaxed mb-4 line-clamp-3">{{ $gift->description }}</p>
                            
                            <div class="space-y-3 pt-4 border-t border-gray-100">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Base Price</span>
                                    <span class="font-medium text-gray-900">₦{{ number_format($gift->price, 0) }}</span>
                                </div>
                                @if($gift->customizable)
                                    <div class="flex justify-between text-sm hidden" id="customizationFeeSummary">
                                        <span class="text-gray-500">Customization</span>
                                        <span class="font-medium text-gray-900">₦{{ number_format($gift->customization_cost ?? 0, 0) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between text-lg font-bold pt-2">
                                    <span>Total</span>
                                    <span id="totalPrice">₦{{ number_format($gift->price, 0) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Balance & Action -->
                    <div class="mt-6">
                         @auth
                            <button type="submit" form="giftCheckoutForm" id="submitBtnDesktop" class="hidden lg:block w-full bg-slate-900 hover:bg-slate-800 text-white py-4 rounded-xl font-bold text-lg transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                Pay <span id="submitBtnTextDesktop">₦{{ number_format($gift->price, 0) }}</span>
                            </button>

                            @if(auth()->user()->balance < $gift->price)
                                <div class="mt-4 p-3 bg-red-50 text-red-700 text-xs rounded-lg flex items-start">
                                    <i class="ri-error-warning-line mt-0.5 mr-2"></i>
                                    <span>Insufficient funds. Please top up your wallet.</span>
                                </div>
                            @endif
                        @else
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                                <p class="text-sm text-gray-500 mb-4">Login to complete your purchase</p>
                                <a href="{{ route('login') }}" class="hidden lg:block w-full bg-slate-900 hover:bg-slate-800 text-white py-4 rounded-xl font-bold text-lg transition-all shadow-lg hover:shadow-xl">
                                    Login
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-white/80 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="text-center">
        <div class="w-12 h-12 border-4 border-gray-200 border-t-black rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-sm font-medium text-gray-900">Processing...</p>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const giftPrice = {{ $gift->price }};
    const customizationFee = {{ $gift->customization_cost ?? 0 }};
    const userBalance = {{ auth()->check() ? auth()->user()->balance : 0 }};
    
    // Carousel Logic
    let currentSlide = 0;
    const totalSlides = {{ count($gift->images) + 1 }}; // Featured image + gallery
    
    function updateCarousel() {
        const track = document.getElementById('carouselTrack');
        if(track) track.style.transform = `translateX(-${currentSlide * 100}%)`;
        
        document.querySelectorAll('.carousel-indicator').forEach((dot, idx) => {
            if(idx === currentSlide) {
                dot.classList.add('bg-white', 'w-4');
                dot.classList.remove('bg-white/50');
            } else {
                dot.classList.remove('bg-white', 'w-4');
                dot.classList.add('bg-white/50');
            }
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
    
    function goToSlide(idx) {
        currentSlide = idx;
        updateCarousel();
    }

    // Customization Logic
    function toggleCustomization() {
        const toggle = document.getElementById('customizeToggle');
        const section = document.getElementById('customImageSection');
        const feeSummary = document.getElementById('customizationFeeSummary');
        const isChecked = toggle.checked;
        
        section.classList.toggle('hidden', !isChecked);
        if(feeSummary) feeSummary.classList.toggle('hidden', !isChecked);
        
        updateTotal();
    }
    
    function updateTotal() {
        const isCustomized = document.getElementById('customizeToggle')?.checked || false;
        const total = giftPrice + (isCustomized ? customizationFee : 0);
        const formattedTotal = '₦' + total.toLocaleString();
        
        document.getElementById('totalPrice').textContent = formattedTotal;
        
        ['submitBtnTextDesktop', 'submitBtnTextMobile'].forEach(id => {
            const el = document.getElementById(id);
            if(el) el.textContent = formattedTotal;
        });
        
        ['submitBtnDesktop', 'submitBtnMobile'].forEach(id => {
            const btn = document.getElementById(id);
            if(btn) btn.disabled = userBalance < total;
        });
    }

    // Image Upload Logic
    function handleImageUpload(event) {
        const file = event.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert('File too large. Max 5MB.');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('uploadContent').classList.add('hidden');
                document.getElementById('imagePreview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    // Form Submission
    document.getElementById('giftCheckoutForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const giftName = document.querySelector('input[name="gift_name"]').value;
        const isCustomized = document.getElementById('customizeToggle')?.checked || false;
        const totalAmount = giftPrice + (isCustomized ? customizationFee : 0);
        
        showGiftConfirmationModal(giftName, totalAmount, isCustomized, function() {
            setGiftLoadingState(true);
            document.getElementById('loadingOverlay').classList.remove('hidden');
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                setGiftLoadingState(false);
                document.getElementById('loadingOverlay').classList.add('hidden');
                
                if (data.success) {
                    showGiftOrderDetails(data.data);
                    // Reset form or update UI as needed
                    form.reset();
                    // Update user balance if displayed on page (optional, but good UX)
                    // if (typeof updateUserBalance === 'function') updateUserBalance(data.data.remaining_balance);
                } else {
                    showNotification(data.message || 'Failed to place order.', 'error');
                }
            })
            .catch(error => {
                setGiftLoadingState(false);
                document.getElementById('loadingOverlay').classList.add('hidden');
                showNotification('An error occurred. Please try again.', 'error');
                console.error('Error:', error);
            });
        });
    });

    // Init
    document.addEventListener('DOMContentLoaded', () => {
        updateTotal();
        // Auto-advance carousel
        if(totalSlides > 1) setInterval(nextSlide, 5000);
    });

    // Show gift confirmation modal
    function showGiftConfirmationModal(giftName, totalAmount, isCustomized, onConfirm) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-gift-line text-pink-500 text-2xl"></i>
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
                            <span class="font-medium text-purple-600">Yes (+₦${customizationFee.toLocaleString()})</span>
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
                        <i class="ri-heart-line mr-1"></i>Send Gift
                    </button>
                </div>  
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Store the callback function
        window.currentGiftOrderCallback = onConfirm;
    }

    function confirmGiftOrder(button) {
        closeModal(button);
        if (window.currentGiftOrderCallback) {
            window.currentGiftOrderCallback();
            window.currentGiftOrderCallback = null;
        }
    }

    function closeModal(button) {
        const modal = button.closest('.fixed');
        document.body.removeChild(modal);
    }

    // Set gift loading state
    function setGiftLoadingState(loading) {
        const submitBtnDesktop = document.getElementById('submitBtnDesktop');
        const submitBtnMobile = document.getElementById('submitBtnMobile');
        
        if (loading) {
            if (submitBtnDesktop) {
                submitBtnDesktop.disabled = true;
                submitBtnDesktop.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i>Processing Gift Order...';
            }
            if (submitBtnMobile) {
                submitBtnMobile.disabled = true;
                submitBtnMobile.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i>Processing...';
            }
        } else {
            if (submitBtnDesktop) {
                submitBtnDesktop.disabled = false;
                submitBtnDesktop.innerHTML = 'Pay <span id="submitBtnTextDesktop">₦' + updateTotal() + '</span>';
            }
            if (submitBtnMobile) {
                submitBtnMobile.disabled = false;
                submitBtnMobile.innerHTML = 'Pay <span id="submitBtnTextMobile">₦' + updateTotal() + '</span>';
            }
        }
    }

    function showGiftOrderDetails(data) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-check-line text-green-500 text-2xl"></i>
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

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        const bgColor = type === 'error' ? 'bg-red-500' : (type === 'success' ? 'bg-green-500' : 'bg-blue-500');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;
        
        // Set notification style based on type
        if (type == 'success') {
            notification.className += ' bg-green-500 text-white';
            notification.innerHTML = `<i class="ri-checkbox-circle-line mr-2"></i>${message}`;
        } else if (type == 'error') {
            notification.className += ' bg-red-500 text-white';
            notification.innerHTML = `<i class="ri-error-warning-line mr-2"></i>${message}`;
        } else {
            notification.className += ' bg-blue-500 text-white';
            notification.innerHTML = `<i class="ri-information-line mr-2"></i>${message}`;
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
</script>
@endpush
