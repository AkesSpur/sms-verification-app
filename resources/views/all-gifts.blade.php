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
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" data-aos="fade-up" data-aos-delay="200">
                <!-- All Gifts in One Grid -->
                <div class="gift-card cursor-pointer" onclick="redirectToGift('flowers', 'Beautiful Flower Bouquet', 45.99)">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1563241527-3004b7be0ffd?w=400&h=400&fit=crop" alt="Beautiful Flower Bouquet" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">Beautiful Flower Bouquet</h4>
                        <p class="text-lg font-bold text-slate-700">₦45.99</p>
                    </div>
                </div>
                
                <div class="gift-card cursor-pointer" onclick="redirectToGift('jewelry', 'Elegant Necklace', 89.99)">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=400&h=400&fit=crop" alt="Elegant Necklace" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">Elegant Necklace</h4>
                        <p class="text-lg font-bold text-slate-700">₦89.99</p>
                    </div>
                </div>
                
                <div class="gift-card cursor-pointer" onclick="redirectToGift('perfume', 'Designer Perfume', 75.99)">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop" alt="Designer Perfume" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">Designer Perfume</h4>
                        <p class="text-lg font-bold text-slate-700">₦75.99</p>
                    </div>
                </div>
                
                <div class="gift-card cursor-pointer" onclick="redirectToGift('wine', 'Premium Wine Bottle', 65.99)">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=400&h=400&fit=crop" alt="Premium Wine Bottle" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">Premium Wine Bottle</h4>
                        <p class="text-lg font-bold text-slate-700">₦65.99</p>
                    </div>
                </div>
                
                <div class="gift-card cursor-pointer" onclick="redirectToGift('watch', 'Luxury Watch', 199.99)">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=400&h=400&fit=crop" alt="Luxury Watch" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">Luxury Watch</h4>
                        <p class="text-lg font-bold text-slate-700">₦199.99</p>
                    </div>
                </div>
                
                <div class="gift-card cursor-pointer" onclick="redirectToGift('handbag', 'Designer Handbag', 150.99)">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&h=400&fit=crop" alt="Designer Handbag" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">Designer Handbag</h4>
                        <p class="text-lg font-bold text-slate-700">₦150.99</p>
                    </div>
                </div>
                
                <div class="gift-card cursor-pointer" onclick="redirectToGift('sunglasses', 'Premium Sunglasses', 85.99)">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400&h=400&fit=crop" alt="Premium Sunglasses" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">Premium Sunglasses</h4>
                        <p class="text-lg font-bold text-slate-700">₦85.99</p>
                    </div>
                </div>
                
                <div class="gift-card cursor-pointer" onclick="redirectToGift('wallet', 'Leather Wallet', 45.99)">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1627123424574-724758594e93?w=400&h=400&fit=crop" alt="Leather Wallet" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">Leather Wallet</h4>
                        <p class="text-lg font-bold text-slate-700">₦45.99</p>
                    </div>
                </div>
                
                <div class="gift-card cursor-pointer" onclick="redirectToGift('chocolate', 'Premium Chocolate Box', 29.99)">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=400&h=400&fit=crop" alt="Premium Chocolate Box" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">Premium Chocolate Box</h4>
                        <p class="text-lg font-bold text-slate-700">₦29.99</p>
                    </div>
                </div>
                
                <div class="gift-card cursor-pointer" onclick="redirectToGift('cupcakes', 'Gourmet Cupcakes', 25.99)">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1587668178277-295251f900ce?w=400&h=400&fit=crop" alt="Gourmet Cupcakes" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">Gourmet Cupcakes</h4>
                        <p class="text-lg font-bold text-slate-700">₦25.99</p>
                    </div>
                </div>
                
                <div class="gift-card cursor-pointer" onclick="redirectToGift('cookies', 'Artisan Cookie Set', 19.99)">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&h=400&fit=crop" alt="Artisan Cookie Set" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">Artisan Cookie Set</h4>
                        <p class="text-lg font-bold text-slate-700">₦19.99</p>
                    </div>
                </div>
                
                <div class="gift-card cursor-pointer" onclick="redirectToGift('macarons', 'French Macarons', 35.99)">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1569864358642-9d1684040f43?w=400&h=400&fit=crop" alt="French Macarons" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">French Macarons</h4>
                        <p class="text-lg font-bold text-slate-700">₦35.99</p>
                    </div>
                </div>
                
                <div class="gift-card cursor-pointer" onclick="redirectToGift('teddy', 'Cute Teddy Bear', 25.99)">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=400&h=400&fit=crop" alt="Cute Teddy Bear" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">Cute Teddy Bear</h4>
                        <p class="text-lg font-bold text-slate-700">₦25.99</p>
                    </div>
                </div>
                
                <div class="gift-card cursor-pointer" onclick="redirectToGift('candles', 'Scented Candle Set', 35.99)">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1602874801006-e26d405c9c8f?w=400&h=400&fit=crop" alt="Scented Candle Set" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">Scented Candle Set</h4>
                        <p class="text-lg font-bold text-slate-700">₦35.99</p>
                    </div>
                </div>
                
                <div class="gift-card cursor-pointer" onclick="redirectToGift('blanket', 'Cozy Throw Blanket', 45.99)">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=400&fit=crop" alt="Cozy Throw Blanket" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">Cozy Throw Blanket</h4>
                        <p class="text-lg font-bold text-slate-700">₦45.99</p>
                    </div>
                </div>
                
                <div class="gift-card cursor-pointer" onclick="redirectToGift('pillow', 'Memory Foam Pillow', 39.99)">
                    <div class="aspect-square overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1586047844557-42d3e3c7b8d4?w=400&h=400&fit=crop" alt="Memory Foam Pillow" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 text-sm">Memory Foam Pillow</h4>
                        <p class="text-lg font-bold text-slate-700">₦39.99</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

        </div>
    </section>
</div>

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