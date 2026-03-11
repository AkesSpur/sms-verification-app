@extends('layouts.app')

@section('title', ($settings->site_name ?? 'BlizzLogspot') . ' - Digital Products Store')

@section('content')
@php
    $waPhone = preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '');
    $waLink  = 'https://wa.me/' . $waPhone . '?text=' . urlencode('Hello, I am interested in a product that is out of stock.');
@endphp

@if(isset($banners) && $banners->count() > 0)
    <div class="max-w-4xl mx-auto px-2 sm:px-4">
        <x-banner-carousel :banners="$banners" />
    </div>
@endif

<div class="max-w-4xl mx-auto py-4 px-2 sm:px-4">

    {{-- Session flash messages --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">
            <i class="ri-check-line text-green-500 text-lg"></i>
            <span class="text-sm">{{ session('success') }}</span>
        </div>
    @endif

    @if($activeSubcategories->isEmpty())
        <div class="text-center py-20 text-gray-400">
            <i class="ri-store-2-line text-5xl mb-4 block text-gray-300"></i>
            <p class="text-lg font-medium text-gray-500">No products available yet.</p>
            <p class="text-sm mt-1">Check back soon for new products.</p>
        </div>
    @else
        @foreach($activeSubcategories as $subcategory)
            @if($subcategory->activeProducts->isNotEmpty())
            <div class="mb-10">
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
                       class="text-xs font-semibold text-indigo-600 border border-indigo-300 px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition-colors flex items-center gap-1.5">
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
                                       class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-all duration-200 hover:shadow-[0_4px_16px_rgba(99,102,241,0.3)]">
                                        <i class="ri-shopping-cart-2-line"></i>
                                        Buy Now
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                       class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-all duration-200 hover:shadow-[0_4px_16px_rgba(99,102,241,0.3)]">
                                        <i class="ri-shopping-cart-2-line"></i>
                                        Buy Now
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
            @endif
        @endforeach
    @endif

</div>
@endsection
