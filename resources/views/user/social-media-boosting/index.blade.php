@extends('layouts.app')

@section('title', 'Social Media Boosting')

@section('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endsection

@section('content')
{{-- PHP Data Prep --}}
@php
    $categoriesData = $categories->load('activeProducts')->map(function($c) {
        return [
            'id' => $c->id,
            'name' => $c->name,
            'description' => $c->description,
            'active_products' => $c->activeProducts->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price_per_1000' => $p->price_per_1000,
                    'min_quantity' => $p->min_quantity,
                    'max_quantity' => $p->max_quantity,
                ];
            })->values()
        ];
    })->values();
@endphp

{{-- Alpine Component Script --}}
<script>
    window.__categoriesData = @json($categoriesData);

    function smbOrderForm() {
        return {
            categories: window.__categoriesData || [],
            
            // Category Picker
            catOpen: false,
            catSearch: '',
            selectedCategory: null,
            
            // Product Picker
            prodOpen: false,
            prodSearch: '',
            selectedProduct: null,
            
            // Form
            link: '',
            quantity: '',
            processing: false,
            
            // Computed
            get filteredCategories() {
                const q = this.catSearch.toLowerCase().trim();
                return this.categories.filter(c => !q || c.name.toLowerCase().includes(q));
            },
            
            get currentProducts() {
                return this.selectedCategory ? this.selectedCategory.active_products : [];
            },
            
            get filteredProducts() {
                const q = this.prodSearch.toLowerCase().trim();
                return this.currentProducts.filter(p => !q || p.name.toLowerCase().includes(q));
            },
            
            get pricePer1k() {
                return this.selectedProduct ? Number(this.selectedProduct.price_per_1000) : 0;
            },
            
            get minQty() { return this.selectedProduct ? Number(this.selectedProduct.min_quantity) : 0; },
            get maxQty() { return this.selectedProduct ? Number(this.selectedProduct.max_quantity) : 0; },
            
            get totalPrice() {
                if (!this.selectedProduct || !this.quantity) return 0;
                return Math.ceil((this.quantity / 1000) * this.pricePer1k);
            },
            
            get userBalance() {
                return {{ auth()->user()->balance }};
            },
            
            // Methods
            pickCategory(c) {
                this.selectedCategory = c;
                this.selectedProduct = null;
                this.quantity = '';
                this.catOpen = false;
                this.catSearch = '';
            },
            
            pickProduct(p) {
                this.selectedProduct = p;
                this.quantity = p.min_quantity;
                this.prodOpen = false;
                this.prodSearch = '';
            },
            
            clearCategory() {
                this.selectedCategory = null;
                this.selectedProduct = null;
                this.quantity = '';
                this.catSearch = '';
            },
            
            clearProduct() {
                this.selectedProduct = null;
                this.quantity = '';
                this.prodSearch = '';
            },
            
            formatNumber(num) {
                return new Intl.NumberFormat().format(num);
            },
            
            submitOrder() {
                if (!this.selectedProduct) return;
                
                if (!this.link) {
                    if(typeof notify === 'function') notify('error', 'Please enter a social media link');
                    else alert('Please enter a social media link');
                    return;
                }
                
                const q = parseInt(this.quantity);
                if (isNaN(q) || q < this.minQty || q > this.maxQty) {
                    const msg = `Quantity must be between ${this.formatNumber(this.minQty)} and ${this.formatNumber(this.maxQty)}`;
                    if(typeof notify === 'function') notify('error', msg);
                    else alert(msg);
                    return;
                }
                
                if (this.totalPrice > this.userBalance) {
                    if(typeof notify === 'function') notify('error', 'Insufficient wallet balance. Please fund your wallet.');
                    else alert('Insufficient wallet balance');
                    return;
                }
                
                this.processing = true;
                
                // Submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('user.social-media-boosting.purchase', '') }}/${this.selectedProduct.id}`;
                
                const csrf = document.createElement('input');
                csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);
                
                const link = document.createElement('input');
                link.type = 'hidden'; link.name = 'social_media_link'; link.value = this.link;
                form.appendChild(link);
                
                const qty = document.createElement('input');
                qty.type = 'hidden'; qty.name = 'quantity'; qty.value = this.quantity;
                form.appendChild(qty);
                
                document.body.appendChild(form);
                form.submit();
            }
        };
    }
</script>

<div class="space-y-5 max-w-4xl mx-auto" x-data="smbOrderForm()">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-xs">
        <i class="ri-check-line text-emerald-500 flex-shrink-0"></i>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-2xl text-xs">
        <i class="ri-error-warning-line text-red-500 flex-shrink-0"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-sm font-bold text-gray-900">Social Media Boosting</h1>
            <p class="text-[11px] text-gray-400 mt-0.5">Boost your social media presence with premium services</p>
        </div>
        <a href="{{ route('user.social-media-orders.index') }}"
           class="relative flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-700 hover:bg-slate-800 transition-colors">
            <i class="ri-list-check-2"></i> My Orders
            @if($uncompletedOrdersCount > 0)
                <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[9px] rounded-full h-4 w-4 flex items-center justify-center font-bold">{{ $uncompletedOrdersCount }}</span>
            @endif
        </a>
    </div>

    @if($categories->count() > 0)
    {{-- ── Order form ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Service selection --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 h-fit">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-4">Select Service</p>

            <div class="space-y-4">
                {{-- Category Picker --}}
                <div class="relative">
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Category</label>
                    <button type="button" @click="catOpen = !catOpen" @click.outside="catOpen = false"
                            class="w-full flex items-center justify-between gap-3 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm hover:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200 transition-all">
                        <span class="flex items-center gap-2 min-w-0">
                            <i class="ri-folder-3-line text-gray-400 flex-shrink-0"></i>
                            <span class="truncate text-xs font-medium" :class="selectedCategory ? 'text-gray-800' : 'text-gray-400'"
                                  x-text="selectedCategory ? selectedCategory.name : 'Select a category'"></span>
                        </span>
                        <span class="flex items-center gap-1.5 flex-shrink-0">
                            <span x-show="selectedCategory" x-cloak @click.stop="clearCategory()"
                                  class="cursor-pointer text-gray-300 hover:text-gray-500 transition-colors leading-none">
                                <i class="ri-close-circle-line text-sm"></i>
                            </span>
                            <i class="ri-arrow-down-s-line text-gray-400 text-sm transition-transform duration-200" :class="catOpen ? 'rotate-180' : ''"></i>
                        </span>
                    </button>

                    {{-- Category Dropdown --}}
                    <div x-show="catOpen" x-cloak x-transition
                         class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl border border-gray-100 shadow-2xl z-20 overflow-hidden">
                        <div class="p-2.5 border-b border-gray-50">
                            <div class="relative">
                                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                <input type="text" x-model="catSearch" placeholder="Search categories..."
                                       class="w-full pl-8 pr-3 py-2 text-xs bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-transparent placeholder-gray-400 transition-all">
                            </div>
                        </div>
                        <ul class="max-h-60 overflow-y-auto py-1">
                            <template x-for="c in filteredCategories" :key="c.id">
                                <li @click="pickCategory(c)" class="flex items-center justify-between px-4 py-2.5 cursor-pointer hover:bg-primary-50 transition-colors"
                                    :class="selectedCategory && selectedCategory.id === c.id ? 'bg-primary-50' : ''">
                                    <span class="text-sm text-gray-700" x-text="c.name"></span>
                                    <span class="text-xs font-bold text-primary-600 ml-3 flex-shrink-0" x-text="c.active_products.length"></span>
                                </li>
                            </template>
                            <li x-show="filteredCategories.length === 0" class="px-4 py-5 text-center text-xs text-gray-400">
                                No categories found
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Product Picker --}}
                <div class="relative">
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Product</label>
                    <button type="button" @click="selectedCategory ? (prodOpen = !prodOpen) : null" @click.outside="prodOpen = false"
                            :disabled="!selectedCategory"
                            class="w-full flex items-center justify-between gap-3 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm hover:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="flex items-center gap-2 min-w-0">
                            <i class="ri-price-tag-3-line text-gray-400 flex-shrink-0"></i>
                            <span class="truncate text-xs font-medium" :class="selectedProduct ? 'text-gray-800' : 'text-gray-400'"
                                  x-text="selectedProduct ? selectedProduct.name : (selectedCategory ? 'Select a product' : 'Select a category first')"></span>
                        </span>
                        <span class="flex items-center gap-1.5 flex-shrink-0">
                            <span x-show="selectedProduct" x-cloak
                                  x-text="'₦' + formatNumber(selectedProduct.price_per_1000) + '/1k'"
                                  class="text-xs font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded-md"></span>
                            <span x-show="selectedProduct" x-cloak @click.stop="clearProduct()"
                                  class="cursor-pointer text-gray-300 hover:text-gray-500 transition-colors leading-none">
                                <i class="ri-close-circle-line text-sm"></i>
                            </span>
                            <i class="ri-arrow-down-s-line text-gray-400 text-sm transition-transform duration-200" :class="prodOpen ? 'rotate-180' : ''"></i>
                        </span>
                    </button>

                    {{-- Product Dropdown --}}
                    <div x-show="prodOpen" x-cloak x-transition
                         class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl border border-gray-100 shadow-2xl z-20 overflow-hidden">
                        <div class="p-2.5 border-b border-gray-50">
                            <div class="relative">
                                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                <input type="text" x-model="prodSearch" placeholder="Search products..."
                                       class="w-full pl-8 pr-3 py-2 text-xs bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-transparent placeholder-gray-400 transition-all">
                            </div>
                        </div>
                        <ul class="max-h-60 overflow-y-auto py-1">
                            <template x-for="p in filteredProducts" :key="p.id">
                                <li @click="pickProduct(p)" class="flex items-center justify-between px-4 py-2.5 cursor-pointer hover:bg-primary-50 transition-colors"
                                    :class="selectedProduct && selectedProduct.id === p.id ? 'bg-primary-50' : ''">
                                    <span class="text-sm text-gray-700 truncate mr-2" x-text="p.name"></span>
                                    <span class="text-xs font-bold text-primary-600 flex-shrink-0" x-text="'₦' + formatNumber(p.price_per_1000)"></span>
                                </li>
                            </template>
                            <li x-show="filteredProducts.length === 0" class="px-4 py-5 text-center text-xs text-gray-400">
                                No products found
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Product details (shown when product selected) --}}
                <div x-show="selectedProduct" x-cloak class="bg-gray-50 rounded-xl p-3 space-y-2 text-xs border border-gray-100">
                    <div class="flex justify-between text-gray-500">
                        <span>Price per 1,000</span>
                        <span class="font-bold text-primary-600" x-text="'₦' + formatNumber(pricePer1k)"></span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Min Quantity</span>
                        <span class="font-semibold text-gray-700" x-text="formatNumber(minQty)"></span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Max Quantity</span>
                        <span class="font-semibold text-gray-700" x-text="formatNumber(maxQty)"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order form --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 h-fit">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-4">Place Order</p>

            {{-- Form Inputs --}}
            <div x-show="selectedProduct" x-cloak class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Social Media Link <span class="text-red-500">*</span></label>
                    <input type="url" x-model="link"
                           placeholder="https://instagram.com/username"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-200 focus:border-primary-400 outline-none transition-all">
                    <p class="text-[11px] text-gray-400 mt-1">Enter the full URL of your account</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" x-model="quantity"
                           :min="minQty" :max="maxQty"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-200 focus:border-primary-400 outline-none transition-all"
                           :class="!isValidQuantity && quantity ? 'border-red-300 focus:border-red-400 focus:ring-red-200' : ''">
                    <p class="text-[11px] text-gray-400 mt-1">
                        Min: <span x-text="formatNumber(minQty)"></span> | Max: <span x-text="formatNumber(maxQty)"></span>
                    </p>
                </div>

                {{-- Order summary --}}
                <div class="bg-gray-50 rounded-xl p-3 space-y-2 text-xs border border-gray-100">
                    <div class="flex justify-between text-gray-500">
                        <span>Price per 1,000</span>
                        <span class="font-semibold text-gray-700" x-text="'₦' + formatNumber(pricePer1k)"></span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Quantity</span>
                        <span class="font-semibold text-gray-700" x-text="quantity ? formatNumber(quantity) : '0'"></span>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 pt-2 font-bold text-sm">
                        <span class="text-gray-700">Total</span>
                        <span class="text-primary-600" x-text="'₦' + formatNumber(totalPrice)"></span>
                    </div>
                </div>

                {{-- Wallet balance --}}
                <div class="flex items-center justify-between bg-emerald-50 rounded-xl px-3 py-2.5 text-xs border border-emerald-100">
                    <span class="text-gray-500 flex items-center gap-1.5"><i class="ri-wallet-3-line text-emerald-500"></i> Wallet Balance</span>
                    <span class="font-bold text-emerald-600">₦{{ number_format(auth()->user()->balance, 0) }}</span>
                </div>

                <button type="button" @click="submitOrder()" :disabled="processing || !isValidQuantity || !link"
                        class="w-full flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-xs font-bold text-white transition-all btn-glow disabled:opacity-50 disabled:cursor-not-allowed"
                        style="background: linear-gradient(135deg, #475569 0%, #1e293b 100%);">
                    <i class="ri-shopping-bag-2-line" x-show="!processing"></i>
                    <i class="ri-loader-4-line animate-spin" x-show="processing" x-cloak></i>
                    <span x-text="processing ? 'Processing...' : 'Place Order'"></span>
                </button>
            </div>

            {{-- No Product Selected Message --}}
            <div x-show="!selectedProduct" class="flex flex-col items-center py-10 text-center">
                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                    <i class="ri-arrow-left-line text-gray-300 text-2xl"></i>
                </div>
                <p class="text-sm font-semibold text-gray-400">Select a product</p>
                <p class="text-xs text-gray-300 mt-1">Choose a category and product to continue</p>
            </div>
        </div>
    </div>

    @else
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-14 text-center">
        <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mb-3 mx-auto">
            <i class="ri-bar-chart-line text-gray-200 text-3xl"></i>
        </div>
        <p class="text-sm font-semibold text-gray-400">No categories available</p>
        <p class="text-xs text-gray-300 mt-1">Social media boosting services will appear here soon.</p>
    </div>
    @endif

</div>
@endsection
