@extends('layouts.user')

@section('title', $category->name . ' - Social Media Boosting')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('user.social-media-boosting.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <i class="fas fa-home mr-2"></i>
                    Social Media Boosting
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">{{ $category->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-8 mb-8 text-white">
        <div class="flex items-center mb-4">
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $category->name }}</h1>
                <p class="text-blue-100">{{ $products->count() }} products available</p>
            </div>
        </div>
        @if($category->description)
            <p class="text-blue-100">{{ $category->description }}</p>
        @endif
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                                <div class="flex items-center text-sm text-gray-500 mb-2">
                                    <i class="fas fa-tag mr-1"></i>
                                    <span>{{ $category->name }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-blue-600">₦{{ number_format($product->price_per_1000, 0) }}</div>
                                <div class="text-xs text-gray-500">per 1,000</div>
                            </div>
                        </div>
                        
                        @if($product->description)
                            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($product->description, 100) }}</p>
                        @endif
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Min Quantity:</span>
                                <span class="font-medium">{{ number_format($product->min_quantity) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Max Quantity:</span>
                                <span class="font-medium">{{ number_format($product->max_quantity) }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center text-sm text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>
                                <span>Active</span>
                            </div>
                            <a href="{{ route('user.social-media-boosting.product', [$category->slug, $product->slug]) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                Order Now
                                <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-box-open text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Products Available</h3>
            <p class="text-gray-500 mb-4">Products in this category will appear here when they become available.</p>
            <a href="{{ route('user.social-media-boosting.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Categories
            </a>
        </div>
    @endif

    <!-- Category Info -->
    <div class="mt-12 bg-gray-50 rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">About {{ $category->name }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">How It Works</h3>
                <ol class="list-decimal list-inside space-y-2 text-gray-600 text-sm">
                    <li>Choose your preferred product from the list above</li>
                    <li>Enter your social media account link</li>
                    <li>Select the quantity you want (within min/max limits)</li>
                    <li>Complete the payment using your wallet balance</li>
                    <li>Your order will be processed and delivered</li>
                </ol>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Important Notes</h3>
                <ul class="list-disc list-inside space-y-2 text-gray-600 text-sm">
                    <li>All orders are processed manually by our team</li>
                    <li>Delivery time may vary depending on the service</li>
                    <li>Make sure your account is public for faster processing</li>
                    <li>Contact support if you have any questions</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection