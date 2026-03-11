@extends('layouts.app')

@section('title', 'Order History')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Page header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Order History</h1>
        <p class="text-sm text-gray-500 mt-1">View your orders across all product categories</p>
    </div>

    {{-- Order type cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- SMS Orders --}}
        <a href="{{ route('user.orders.sms') }}"
           class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-200 p-6 group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-colors flex-shrink-0">
                    <i class="ri-message-3-line text-blue-600 text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 group-hover:text-slate-800">SMS Orders</h3>
                    <p class="text-sm text-gray-500 mt-0.5">USA & International number verification orders</p>
                </div>
                <i class="ri-arrow-right-s-line text-gray-300 group-hover:text-slate-500 transition-colors flex-shrink-0"></i>
            </div>
        </a>

        {{-- Log Orders --}}
        <a href="{{ route('user.orders.logs') }}"
           class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-200 p-6 group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center group-hover:bg-green-100 transition-colors flex-shrink-0">
                    <i class="ri-file-list-3-line text-green-600 text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 group-hover:text-slate-800">Log Orders</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Digital product & account log purchases</p>
                </div>
                <i class="ri-arrow-right-s-line text-gray-300 group-hover:text-slate-500 transition-colors flex-shrink-0"></i>
            </div>
        </a>

        {{-- Gift Orders --}}
        <a href="{{ route('user.orders.gifts') }}"
           class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-200 p-6 group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-pink-50 rounded-xl flex items-center justify-center group-hover:bg-pink-100 transition-colors flex-shrink-0">
                    <i class="ri-gift-line text-pink-500 text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 group-hover:text-slate-800">Gift Orders</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Physical gift purchases and delivery tracking</p>
                </div>
                <i class="ri-arrow-right-s-line text-gray-300 group-hover:text-slate-500 transition-colors flex-shrink-0"></i>
            </div>
        </a>

        {{-- Reseller Orders --}}
        <a href="{{ route('user.orders.reseller') }}"
           class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-200 p-6 group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center group-hover:bg-yellow-100 transition-colors flex-shrink-0">
                    <i class="ri-store-2-line text-yellow-600 text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 group-hover:text-slate-800">Reseller Orders</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Bulk reseller product purchases</p>
                </div>
                <i class="ri-arrow-right-s-line text-gray-300 group-hover:text-slate-500 transition-colors flex-shrink-0"></i>
            </div>
        </a>

    </div>

    {{-- Quick link to transactions --}}
    <div class="mt-6 pt-6 border-t border-gray-100">
        <a href="{{ route('user.transaction') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-slate-800 transition-colors">
            <i class="ri-history-line text-gray-400"></i>
            View all transactions and payment history
            <i class="ri-arrow-right-line text-xs"></i>
        </a>
    </div>

</div>
@endsection
