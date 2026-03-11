@extends('layouts.app')

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
            /* border: 1px solid #e5e7eb; */
            border-radius: 16px;
        }
    .gift-card:hover {
        transform: translateY(-5px);
        /* box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); */
        /* border-color: #3b82f6; */
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
<div class="max-w-5xl mx-auto py-4 px-2 sm:px-4">

    <div class="mb-6">
        <h1 class="text-lg font-bold text-gray-900">Gift Collection</h1>
        <p class="text-xs text-gray-500 mt-0.5">Discover our complete collection of thoughtful gifts</p>
    </div>

    <div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($gifts as $gift)
                    <div class="gift-card cursor-pointer" onclick="window.location.href='{{ route('gift.show', $gift->slug) }}'">
                        <div class="aspect-square overflow-hidden relative rounded-t-lg">
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
                        <div class="px-2 py-4">
                            <h4 class="font-normal text-gray-900 mb-2 text-sm">{{ $gift->name }}</h4>
                            <p class="text-lg font-normal text-slate-700">₦{{ number_format($gift->price, 0) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-12">
                        <p class="text-gray-500">No gifts available at the moment. Please check back later.</p>
                    </div>
                @endforelse
            </div>
    </div>
</div>
@endsection

@push('scripts')
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



@endpush