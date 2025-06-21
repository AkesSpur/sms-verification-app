@extends('layouts.main')

@section('title', 'All Gifts - SMS Verification')

@section('styles')
<style>
    .gradient-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }

    .animate-blob {
        animation: blob 7s infinite;
    }

    .animation-delay-2000 {
        animation-delay: 2s;
    }

    .animation-delay-4000 {
        animation-delay: 4s;
    }

    .gift-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
        }
    .gift-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #3b82f6;
    }
    .gift-card img {
        transition: transform 0.3s ease;
    }
    .gift-card:hover img {
        transform: scale(1.05);
    }
</style>
@endsection

@section('content')
<div>

    <!-- Hero Section -->
    <section class="relative pt-20 pb-6 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-50 via-white to-gray-100"></div>
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl animate-blob"></div>
            <div class="absolute top-40 right-10 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-20 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-4000"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16" data-aos="fade-up">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                    Beautiful <span class="gradient-text">Gifts</span>
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                    Discover our complete collection of thoughtful gifts perfect for any special occasion
                </p>
                <div class="flex flex-wrap justify-center gap-4 text-sm mt-8">
                    <div class="bg-white bg-opacity-80 backdrop-blur-sm px-6 py-3 rounded-full shadow-md border border-gray-200">
                        <i class="fas fa-heart mr-2 text-red-500"></i><span class="text-gray-700">Thoughtfully Curated</span>
                    </div>
                    <div class="bg-white bg-opacity-80 backdrop-blur-sm px-6 py-3 rounded-full shadow-md border border-gray-200">
                        <i class="fas fa-truck mr-2 text-blue-500"></i><span class="text-gray-700">Fast Delivery</span>
                    </div>
                    <div class="bg-white bg-opacity-80 backdrop-blur-sm px-6 py-3 rounded-full shadow-md border border-gray-200">
                        <i class="fas fa-gift mr-2 text-purple-500"></i><span class="text-gray-700">Beautiful Packaging</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gifts Collection -->
    <section class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" data-aos="fade-up" data-aos-delay="200">
                @forelse($gifts as $gift)
                    <div class="gift-card cursor-pointer" onclick="window.location.href='{{ route('gift.show', $gift->slug) }}'">
                        <div class="aspect-square overflow-hidden relative rounded-t-2xl">
                            @if($gift->main_image)
                                <img src="{{ asset($gift->main_image) }}" alt="{{ $gift->name }}" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                            @else
                                {{-- <img src="https://via.placeholder.com/400x400?text=No+Image" alt="{{ $gift->name }}" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300"> --}}
                            @endif
                            
                            @if($gift->customizable)
                                <div class="absolute top-2 right-2 bg-purple-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                    <i class="fas fa-magic mr-1"></i>Customizable
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h4 class="font-bold text-gray-900 mb-2 text-sm">{{ $gift->name }}</h4>
                            <p class="text-lg font-bold text-slate-700">₦{{ number_format($gift->price, 2) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-12">
                        <p class="text-gray-500">No gifts available at the moment. Please check back later.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>


<!-- Footer -->
<footer class="bg-slate-800 text-white py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="mb-8">
            <h3 class="text-2xl font-bold mb-4">
                <i class="fas fa-mobile-alt mr-2"></i>{{$settings->site_name ?? 'SMS Verification'}}
            </h3>
            <p class="text-gray-300 mb-6">Secure, fast, and reliable SMS verification services for all your needs.</p>
            <div class="flex justify-center space-x-6">
                <a href="#" class="text-gray-300 hover:text-white transition-colors">
                    <i class="fab fa-twitter text-xl"></i>
                </a>
                <a href="#" class="text-gray-300 hover:text-white transition-colors">
                    <i class="fab fa-facebook text-xl"></i>
                </a>
                <a href="#" class="text-gray-300 hover:text-white transition-colors">
                    <i class="fab fa-instagram text-xl"></i>
                </a>
                <a href="#" class="text-gray-300 hover:text-white transition-colors">
                    <i class="fab fa-linkedin text-xl"></i>
                </a>
            </div>
        </div>
        <div class="border-t border-gray-700 pt-8">
            <p class="text-gray-400 text-sm">
                © 2024 {{$settings->site_name ?? 'SMS Verification'}}. All rights reserved.
            </p>
        </div>
    </div>
</footer>

</div>
@endsection

@section('scripts')
<script>
    // Gift redirect function
    function redirectToGift(giftId, giftName, giftPrice) {
        // Store gift data in localStorage or pass via URL
        const giftData = {
            id: giftId,
            name: giftName,
            price: giftPrice
        };
        localStorage.setItem('selectedGift', JSON.stringify(giftData));
        
        // Redirect to gift page with ID parameter
        window.location.href = `/gift/${giftId}?name=${encodeURIComponent(giftName)}&price=${giftPrice}`;
    }
</script>
@endsection