@extends('layouts.app')

@section('title', ($settings->site_name ?? 'BlizzLogspot') . ' - Digital Products Store')

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

<div class="max-w-5xl mx-auto py-4 px-2 sm:px-4">

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
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pl-4 border-l-4 border-indigo-500">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        @if($subcategory->image)
                            <img src="{{ asset($subcategory->image) }}"
                                 alt="{{ $subcategory->name }}"
                                 class="w-8 h-8 rounded-lg object-cover border border-gray-200 flex-shrink-0">
                        @else
                            <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="ri-box-3-line text-indigo-400 text-sm"></i>
                            </div>
                        @endif
                        <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider truncate">{{ $subcategory->name }}</h2>
                    </div>
                    <a href="{{ route('subcategory.show', $subcategory->slug) }}"
                       class="text-xs font-semibold text-indigo-600 border border-indigo-300 px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition-colors flex-shrink-0 flex items-center gap-1.5 whitespace-nowrap">
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
            </div>
            @endif
        @endforeach
    @endif

</div>
@endsection
