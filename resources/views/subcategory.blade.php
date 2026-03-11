@extends('layouts.app')

@section('title', $subcategory->name . ' - ' . ($settings->site_name ?? 'BlizzLogspot'))

@section('content')
@php
    $waPhone = preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '');
    $waLink  = 'https://wa.me/' . $waPhone . '?text=' . urlencode('Hello, I am interested in a product that is out of stock.');
@endphp

@if(isset($banners) && $banners->count() > 0)
    <div class="max-w-5xl mx-auto px-2 sm:px-4">
        <x-banner-carousel :banners="$banners" />
    </div>
@endif

<div class="max-w-5xl mx-auto py-6 px-4 sm:px-6">

    {{-- Section header --}}
    <div class="flex flex-wrap items-center gap-4 mb-8 pl-4 border-l-4 border-indigo-500">
        @if($subcategory->image)
            <img src="{{ asset($subcategory->image) }}"
                 alt="{{ $subcategory->name }}"
                 class="w-10 h-10 rounded-xl object-cover border border-gray-200 flex-shrink-0">
        @else
            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="ri-box-3-line text-indigo-500 text-lg"></i>
            </div>
        @endif
        <div class="min-w-0 flex-1">
            <h1 class="text-lg font-bold text-gray-900 uppercase tracking-wide break-words">{{ $subcategory->name }}</h1>
            <p class="text-xs text-gray-400 mt-0.5">{{ $subcategory->activeProducts->count() }} product{{ $subcategory->activeProducts->count() !== 1 ? 's' : '' }} available</p>
        </div>
    </div>

    {{-- Products --}}
    @if($subcategory->activeProducts->isEmpty())
        <div class="text-center py-20">
            <i class="ri-inbox-line text-5xl text-gray-200 block mb-3"></i>
            <p class="font-medium text-gray-400">No products available in this category.</p>
            <a href="{{ route('all-categories') }}"
               class="mt-4 inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="ri-arrow-left-line"></i> Browse other categories
            </a>
        </div>
    @else
        <div class="space-y-2.5">
            @foreach($subcategory->activeProducts as $product)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-gray-200 transition-all duration-200 p-4 flex items-center gap-4">
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

                {{-- Product Details & Footer --}}
                <div class="flex-grow min-w-0 flex flex-col justify-between">
                    <h3 class="font-bold text-slate-800 text-sm mb-2 truncate" title="{{ $product->name }}">
                        {{ $product->name }}
                    </h3>
                    
                    {{-- Footer: Stock/Price and Action Button --}}
                    <div class="flex items-end justify-between gap-2 mt-auto">
                        <div class="space-y-1 text-xs text-slate-500">
                            <div class="flex items-center gap-2">
                                <span>In Stock:</span>
                                @if($product->available_stock > 0)
                                    <span class="font-medium text-emerald-600">{{ $product->available_stock }} qty.</span>
                                @else
                                    <span class="font-medium text-red-500">0 qty.</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <span>Price:</span>
                                <span class="font-bold text-slate-900">
                                    &#8358;{{ number_format($product->price) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex-shrink-0">
                            @if($product->available_stock > 0)
                                @auth
                                    <a href="{{ route('product.show', $product->slug) }}" 
                                       class="inline-flex items-center justify-center px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg text-xs transition-colors duration-300 gap-1.5 shadow-sm whitespace-nowrap">
                                        <i class="ri-shopping-cart-2-line text-sm block"></i>
                                        Buy Now
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" 
                                       class="inline-flex items-center justify-center px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-lg text-xs transition-colors duration-300 gap-1.5 shadow-sm whitespace-nowrap">
                                        <i class="ri-shopping-cart-2-line text-sm block"></i>
                                        Buy Now
                                    </a>
                                @endauth
                            @else
                                <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg text-xs transition-colors duration-300 gap-1.5 shadow-sm whitespace-nowrap">
                                    <i class="ri-whatsapp-line text-sm block"></i>
                                    Request
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    @endif
</div>
@endsection
