@extends('layouts.app')

@section('title', 'All Categories - ' . ($settings->site_name ?? 'BlizzLogspot'))

@section('content')
@php
    $waPhone = preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '');
    $waLink  = 'https://wa.me/' . $waPhone . '?text=' . urlencode('Hello, I am interested in a product that is out of stock.');
@endphp

    {{-- Banner carousel --}}
    @if($banners->count() > 0)
    <section class="mb-8 -mx-4 sm:-mx-6 -mt-4 sm:-mt-6">
        <div class="relative overflow-hidden">
            <div class="carousel-container overflow-hidden">
                <div class="carousel-wrapper flex transition-transform duration-500 ease-in-out" id="carousel">
                    @foreach($banners as $banner)
                    <div class="carousel-slide w-full flex-shrink-0">
                        <div class="relative h-[140px] sm:h-[200px] md:h-[240px] overflow-hidden">
                            @if($banner->link_url)
                                <a href="{{ $banner->link_url }}" target="_blank" class="block w-full h-full">
                                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title ?? 'Banner' }}"
                                         class="w-full h-full object-cover">
                                </a>
                            @else
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title ?? 'Banner' }}"
                                     class="w-full h-full object-cover" loading="lazy">
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @if($banners->count() > 1)
            <button class="carousel-prev absolute left-3 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 rounded-full shadow-md z-10 transition-all">
                <i class="ri-arrow-left-s-line text-xl"></i>
            </button>
            <button class="carousel-next absolute right-3 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 rounded-full shadow-md z-10 transition-all">
                <i class="ri-arrow-right-s-line text-xl"></i>
            </button>
            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
                @foreach($banners as $banner)
                    <button class="carousel-dot w-2 h-2 rounded-full bg-white/50 hover:bg-white transition-colors" data-slide="{{ $loop->index }}"></button>
                @endforeach
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- Page heading --}}
    <div class="mb-8">
        <h1 class="text-xl font-bold text-gray-900">Digital Log Store</h1>
        <p class="text-sm text-gray-500 mt-1">Browse all available product categories</p>
    </div>

    {{-- Category groups with products --}}
    @foreach($digitalCategories as $index => $category)
    @php
        $subs = $category->activeSubcategories->filter(fn($s) => $s->activeProducts->count() > 0);
    @endphp
    @if($subs->isNotEmpty())
        @foreach($subs as $subcategory)
        <div class="mb-10" data-aos="fade-up">
            {{-- Section header --}}
            <div class="flex items-center justify-between mb-4 pl-4 border-l-4 border-indigo-500">
                <div class="flex items-center gap-3">
                    @if($subcategory->image)
                        <img src="{{ asset($subcategory->image) }}"
                             alt="{{ $subcategory->name }}"
                             class="w-8 h-8 rounded-lg object-cover border border-gray-200">
                    @else
                        <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center">
                            <i class="ri-box-3-line text-indigo-400 text-sm"></i>
                        </div>
                    @endif
                    <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider">{{ $subcategory->name }}</h2>
                </div>
                <a href="{{ route('subcategory.show', $subcategory->slug) }}"
                   class="text-xs font-semibold text-indigo-600 border border-indigo-300 px-3 py-1.5 rounded-full hover:bg-indigo-50 transition-colors flex items-center gap-1.5">
                    View All <i class="ri-arrow-right-line text-xs"></i>
                </a>
            </div>

            {{-- Product list --}}
            <div class="space-y-2.5">
                @foreach($subcategory->activeProducts as $product)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-indigo-100 transition-all duration-200 p-4 flex items-center gap-4">
                    {{-- Product image --}}
                    @if($product->image)
                        <img src="{{ asset($product->image) }}"
                             alt="{{ $product->name }}"
                             class="w-12 h-12 rounded-xl object-cover border border-gray-100 flex-shrink-0">
                    @else
                        <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="ri-box-3-line text-slate-300 text-xl"></i>
                        </div>
                    @endif

                    {{-- Product info --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 text-sm truncate">{{ $product->name }}</p>
                        <div class="flex flex-wrap items-center gap-3 mt-1">
                            <span class="text-xs text-gray-500">
                                In Stock:&nbsp;
                                @if($product->available_stock > 0)
                                    <span class="text-emerald-600 font-semibold">{{ $product->available_stock }} qty.</span>
                                @else
                                    <span class="text-red-500 font-semibold">0 qty.</span>
                                @endif
                            </span>
                            <span class="text-xs font-bold text-gray-800">
                                Price: &#8358;{{ number_format($product->price) }}
                            </span>
                        </div>
                    </div>

                    {{-- Action button --}}
                    <div class="flex-shrink-0">
                        @if($product->available_stock > 0)
                            @auth
                                <a href="{{ route('product.show', $product->slug) }}"
                                   class="inline-flex items-center bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-all duration-200 hover:shadow-[0_4px_16px_rgba(99,102,241,0.3)]">
                                    Request
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                   class="inline-flex items-center bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-all duration-200 hover:shadow-[0_4px_16px_rgba(99,102,241,0.3)]">
                                    Request
                                </a>
                            @endauth
                        @else
                            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-all duration-200">
                                <i class="ri-whatsapp-line"></i> Request
                            </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    @endif
    @endforeach

    {{-- Feature highlights --}}
    <div class="mt-12 grid sm:grid-cols-3 gap-6 pb-4" data-aos="fade-up">
        <div class="text-center">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl flex items-center justify-center"
                 style="background:linear-gradient(135deg,#6366f1,#4f46e5);">
                <i class="ri-flashlight-line text-white text-2xl"></i>
            </div>
            <h4 class="font-bold text-gray-900 mb-1">Instant Delivery</h4>
            <p class="text-sm text-gray-500">Get your logs delivered instantly after purchase</p>
        </div>
        <div class="text-center">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl flex items-center justify-center"
                 style="background:linear-gradient(135deg,#10b981,#059669);">
                <i class="ri-shield-check-line text-white text-2xl"></i>
            </div>
            <h4 class="font-bold text-gray-900 mb-1">100% Secure</h4>
            <p class="text-sm text-gray-500">All transactions are encrypted and protected</p>
        </div>
        <div class="text-center">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl flex items-center justify-center"
                 style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
                <i class="ri-customer-service-2-line text-white text-2xl"></i>
            </div>
            <h4 class="font-bold text-gray-900 mb-1">24/7 Support</h4>
            <p class="text-sm text-gray-500">Our support team is available around the clock</p>
        </div>
    </div>

@push('scripts')
<script>
    @if($banners->count() > 1)
    let currentSlide = 0;
    const totalSlides = {{ $banners->count() }};
    let carouselInterval;

    function updateCarousel() {
        const c = document.getElementById('carousel');
        if (c) c.style.transform = `translateX(-${currentSlide * 100}%)`;
        document.querySelectorAll('.carousel-dot').forEach((dot, i) => {
            dot.classList.toggle('bg-white', i === currentSlide);
            dot.classList.toggle('bg-white/50', i !== currentSlide);
        });
    }

    function nextSlide() { currentSlide = (currentSlide + 1) % totalSlides; updateCarousel(); }
    function prevSlide() { currentSlide = (currentSlide - 1 + totalSlides) % totalSlides; updateCarousel(); }
    function startCarousel() { carouselInterval = setInterval(nextSlide, 5000); }
    function stopCarousel() { clearInterval(carouselInterval); }

    document.addEventListener('DOMContentLoaded', function () {
        updateCarousel();
        startCarousel();
        document.querySelector('.carousel-prev')?.addEventListener('click', () => { stopCarousel(); prevSlide(); startCarousel(); });
        document.querySelector('.carousel-next')?.addEventListener('click', () => { stopCarousel(); nextSlide(); startCarousel(); });
        document.querySelectorAll('.carousel-dot').forEach((dot, i) => {
            dot.addEventListener('click', () => { stopCarousel(); currentSlide = i; updateCarousel(); startCarousel(); });
        });
        const container = document.querySelector('.carousel-container');
        if (container) {
            container.addEventListener('mouseenter', stopCarousel);
            container.addEventListener('mouseleave', startCarousel);
        }
    });
    @endif
</script>
@endpush

@endsection
