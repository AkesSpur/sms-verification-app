@extends('layouts.app')

@section('title', $category->name . ' - Social Media Boosting')

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">

    {{-- ── Back / Header ── --}}
    <div>
        <a href="{{ route('user.social-media-boosting.index') }}"
           class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-indigo-600 transition-colors mb-1">
            <i class="ri-arrow-left-line"></i> Back to Social Media Boosting
        </a>
        <h1 class="text-sm font-bold text-gray-900">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-[11px] text-gray-400 mt-0.5">{{ $category->description }}</p>
        @endif
    </div>

    {{-- ── Products ── --}}
    @if($products->count() > 0)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">
                {{ $products->count() }} Product{{ $products->count() !== 1 ? 's' : '' }} Available
            </p>
        </div>

        <div class="divide-y divide-gray-50">
            @foreach($products as $product)
            <div class="flex items-center gap-4 p-4 hover:bg-gray-50/60 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                    <i class="ri-bar-chart-fill text-indigo-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $product->name }}</p>
                    @if($product->description)
                        <p class="text-[11px] text-gray-400 truncate mt-0.5">{{ Str::limit(strip_tags($product->description), 80) }}</p>
                    @endif
                    <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                        <span>Min: <span class="font-semibold text-gray-700">{{ number_format($product->min_quantity) }}</span></span>
                        <span>Max: <span class="font-semibold text-gray-700">{{ number_format($product->max_quantity) }}</span></span>
                    </div>
                </div>
                <div class="flex-shrink-0 text-right">
                    <p class="text-xs text-gray-400 mb-1">per 1,000</p>
                    <p class="text-sm font-bold text-indigo-600 mb-2">₦{{ number_format($product->price_per_1000, 0) }}</p>
                    <a href="{{ route('user.social-media-boosting.product', [$category->slug, $product->slug]) }}"
                       class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-white transition-all btn-glow"
                       style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                        <i class="ri-shopping-bag-2-line"></i> Order
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @else
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-14 text-center">
        <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mb-3 mx-auto">
            <i class="ri-box-3-line text-gray-200 text-3xl"></i>
        </div>
        <p class="text-sm font-semibold text-gray-400">No products available</p>
        <p class="text-xs text-gray-300 mt-1">Products in this category will appear here soon.</p>
    </div>
    @endif

</div>
@endsection
