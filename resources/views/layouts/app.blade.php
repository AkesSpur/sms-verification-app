<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $settings->site_name ?? 'BlizzLogspot')</title>

    @if($logoSetting && $logoSetting->favicon)
        <link rel="icon" type="image/png" href="{{ asset($logoSetting->favicon) }}">
    @endif

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* Sidebar scrollbar */
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

        /* Active sidebar link — primary accent on white bg */
        .sidebar-active {
            background: rgba(71,85,105,0.08);
            color: #475569 !important;
            border-right: 2px solid #475569;
        }
        .sidebar-active .nav-icon { color: #475569 !important; }

        /* Sidebar hover */
        .sidebar-link:hover:not(.sidebar-active) { background: #f8fafc; }

        /* Button glow on hover */
        .btn-glow {
            transition: all 0.2s ease;
        }
        .btn-glow:hover {
            box-shadow: 0 4px 22px rgba(71,85,105,0.35);
            transform: translateY(-1px);
        }

        /* Category dropdown scroll */
        .cat-list { max-height: 16rem; overflow-y: auto; }
        .cat-list::-webkit-scrollbar { width: 4px; }
        .cat-list::-webkit-scrollbar-thumb { background: rgba(71,85,105,0.3); border-radius: 4px; }
    </style>

    @yield('styles')
</head>
<body class="bg-white font-sans antialiased"
      @auth x-data="{ sidebarOpen: false, orderHistoryOpen: {{ request()->routeIs('user.orders.*') ? 'true' : 'false' }} }" @endauth>

<!-- ============================================================
     SIDEBAR — toggleable overlay drawer, auth only, hidden by default
     ============================================================ -->
@auth
    {{-- Backdrop overlay --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm"></div>

    {{-- Sidebar drawer (slides in from left) --}}
    <div x-show="sidebarOpen" x-cloak
         x-transition:enter="transition ease-out duration-250 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 w-72 z-50 flex flex-col bg-white shadow-2xl"
         style="border-right:1px solid #e5e7eb;">
        @include('layouts.partials.sidebar')
    </div>
@endauth

<!-- ============================================================
     CONTENT WRAPPER — always full width (no sidebar offset)
     ============================================================ -->
<div class="flex flex-col min-h-screen">

    <!-- ========================================================
         HEADER
         ======================================================== -->
    <header class="sticky top-0 z-30 bg-white shadow-sm">

        <!-- Row 1: Logo + User Actions -->
        <div class="border-b border-gray-100">
            <div class="flex items-center justify-between h-14 px-4 sm:px-6">

                <!-- Left: logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        {{-- @if($logoSetting && $logoSetting->logo)
                            <img src="{{ asset($logoSetting->logo) }}" alt="{{ $settings->site_name ?? 'Logo' }}" class="h-8 w-auto">
                        @else --}}
                            <span class="text-xl font-bold text-slate-900">{{ $settings->site_name ?? 'BlizzLogspot' }}</span>
                        {{-- @endif --}}
                    </a>
                </div>

                <!-- Right: auth actions -->
                <div class="flex items-center gap-2 sm:gap-3">
                    @auth
                        {{-- Admin Dashboard Link --}}
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                               class="hidden md:flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-colors text-sm font-medium">
                                <i class="ri-dashboard-line"></i> Admin
                            </a>
                        @endif

                        {{-- Balance widget --}}
                        <a href="{{ route('user.transaction') }}"
                           class="flex items-center gap-1.5 px-3 py-1.5 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors">
                            <i class="ri-wallet-3-line text-primary-600 text-sm"></i>
                            <span class="text-sm font-semibold text-primary-800">&#8358;{{ number_format(auth()->user()->balance ?? 0) }}</span>
                        </a>

                        {{-- Sidebar toggle button --}}
                        <button @click="sidebarOpen = true"
                                class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition-colors btn-glow">
                            <i class="ri-menu-line text-lg"></i>
                            <span class="hidden sm:block text-sm font-medium">Menu</span>
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                           class="text-sm font-medium text-gray-600 hover:text-gray-900 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                           class="text-sm font-medium text-white bg-slate-800 hover:bg-slate-700 px-4 py-1.5 rounded-lg btn-glow">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Row 2: Search + Category (all pages) -->
        <div class="bg-slate-50 border-b border-gray-100">
            <div class="flex items-center gap-3 px-4 sm:px-6 py-2.5">

                {{-- AJAX Search --}}
                <div class="flex-1 min-w-0 relative"
                     x-data="{
                        q: '',
                        results: [],
                        loading: false,
                        open: false,
                        debounce: null,
                        search() {
                            clearTimeout(this.debounce);
                            if (this.q.length < 2) { this.results = []; this.open = false; return; }
                            this.loading = true;
                            this.debounce = setTimeout(() => {
                                fetch('{{ route('search') }}?q=' + encodeURIComponent(this.q), {
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                })
                                .then(r => r.json())
                                .then(data => {
                                    this.results = data.results;
                                    this.open = true;
                                    this.loading = false;
                                })
                                .catch(() => { this.loading = false; });
                            }, 320);
                        }
                     }"
                     @click.away="open = false"
                     @keydown.escape.window="open = false">

                    <div class="relative">
                        <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                        <input type="text"
                               x-model="q"
                               @input="search()"
                               @focus="q.length >= 2 && results.length && (open = true)"
                               placeholder="Search logs..."
                               autocomplete="off"
                               class="w-full pl-9 pr-8 py-2 text-sm bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-transparent placeholder-gray-400 transition-all">
                        {{-- Loading spinner --}}
                        <span x-show="loading" x-cloak class="absolute right-3 top-1/2 -translate-y-1/2">
                            <i class="ri-loader-4-line text-primary-400 text-sm animate-spin"></i>
                        </span>
                        {{-- Clear --}}
                        <button x-show="q.length > 0 && !loading" x-cloak
                                @click="q = ''; results = []; open = false"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="ri-close-line text-sm"></i>
                        </button>
                    </div>

                    {{-- Results dropdown --}}
                    <div x-show="open && (results.length > 0 || q.length >= 2)"
                         x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute top-full left-0 right-0 mt-1.5 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden max-h-80 overflow-y-auto">

                        <template x-if="results.length === 0 && !loading">
                            <div class="px-4 py-6 text-center text-sm text-gray-400">
                                <i class="ri-search-line text-2xl block mb-2 text-gray-300"></i>
                                No products found for "<span x-text="q" class="font-medium text-gray-500"></span>"
                            </div>
                        </template>

                        <template x-for="item in results" :key="item.url">
                            <a :href="item.url"
                               @click="open = false"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-primary-50 transition-colors border-b border-gray-50 last:border-0">
                                {{-- Image --}}
                                <template x-if="item.image">
                                    <img :src="item.image" :alt="item.name" class="w-9 h-9 rounded-lg object-cover border border-gray-100 flex-shrink-0">
                                </template>
                                <template x-if="!item.image">
                                    <div class="w-9 h-9 rounded-lg bg-primary-50 flex items-center justify-center flex-shrink-0">
                                        <i class="ri-box-3-line text-primary-400 text-sm"></i>
                                    </div>
                                </template>
                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate" x-text="item.name"></p>
                                    <p class="text-xs text-gray-400 truncate" x-text="item.subcategory"></p>
                                </div>
                                {{-- Price + stock --}}
                                <div class="text-right flex-shrink-0">
                                    <p class="text-sm font-bold text-gray-800">&#8358;<span x-text="item.price"></span></p>
                                    <p class="text-xs"
                                       :class="item.stock > 0 ? 'text-emerald-600' : 'text-red-400'"
                                       x-text="item.stock > 0 ? item.stock + ' in stock' : 'Sold out'"></p>
                                </div>
                            </a>
                        </template>

                        <template x-if="results.length > 0">
                            <div class="su-4 py-2 bg-slate-50 border-t border-gray-100">
                                <a :href="'{{ route('home') }}?search=' + encodeURIComponent(q)"
                                   @click="open = false"
                                   class="text-xs text-primary-600 hover:text-primary-800 font-medium flex items-center gap-1">
                                    <i class="ri-search-line"></i>
                                    See all results for "<span x-text="q"></span>"
                                </a>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Category dropdown --}}
                <div class="relative flex-shrink-0" x-data="{ catOpen: false }" @keydown.escape.window="catOpen = false">
                    <button @click="catOpen = !catOpen"
                            class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:border-primary-400 hover:text-primary-600 transition-all whitespace-nowrap">
                        <i class="ri-apps-2-line text-sm"></i>
                        <span class="text-xs sm:text-sm">Category</span>
                        <i class="ri-arrow-down-s-line text-gray-400 text-sm transition-transform duration-200" :class="{ 'rotate-180': catOpen }"></i>
                    </button>

                    <div x-show="catOpen"
                         @click.away="catOpen = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         x-cloak
                         class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Browse Categories</p>
                            <button @click="catOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="ri-close-line text-sm"></i>
                            </button>
                        </div>
                        <div class="cat-list py-1">
                            @forelse($allSubcategories ?? [] as $sub)
                                <a href="{{ route('subcategory.show', $sub->slug) }}"
                                   @click="catOpen = false"
                                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition-colors {{ request()->route('slug') === $sub->slug ? 'bg-primary-50 font-medium text-primary-700' : '' }}">
                                    @if($sub->image)
                                        <img src="{{ asset($sub->image) }}" alt="{{ $sub->name }}" class="w-6 h-6 rounded object-cover flex-shrink-0">
                                    @else
                                        <div class="w-6 h-6 rounded bg-slate-100 flex items-center justify-center flex-shrink-0">
                                            <i class="ri-box-3-line text-slate-400 text-xs"></i>
                                        </div>
                                    @endif
                                    <span class="truncate">{{ $sub->name }}</span>
                                </a>
                            @empty
                                <p class="px-4 py-3 text-sm text-gray-400 text-center">No categories found</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ========================================================
         MAIN CONTENT
         ======================================================== -->
    <main class="flex-1">
        @auth
            <div class="p-4 sm:p-6">
                @yield('content')
            </div>
        @else
            @yield('content')
        @endauth
    </main>

    <!-- ========================================================
         FOOTER
         ======================================================== -->
    <footer class="bg-white border-t border-gray-100 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2">
                    {{-- <div class="w-7 h-7 rounded-lg bg-primary-600 flex items-center justify-center">
                        <i class="ri-store-2-fill text-white text-xs"></i>
                    </div> --}}
                    <span class="font-bold text-slate-900">{{ $settings->site_name ?? 'BlizzLogspot' }}</span>
                </div>
                <div class="flex items-center gap-6">
                    @if($settings && $settings->whatsapp_support_link)
                        <a href="{{ $settings->whatsapp_support_link }}" target="_blank"
                           class="flex items-center gap-1.5 text-slate-500 hover:text-green-600 transition-colors text-sm">
                            <i class="ri-whatsapp-line"></i> WhatsApp
                        </a>
                    @endif
                    @if($settings && $settings->telegram_support_link)
                        <a href="{{ $settings->telegram_support_link }}" target="_blank"
                           class="flex items-center gap-1.5 text-slate-500 hover:text-blue-500 transition-colors text-sm">
                            <i class="ri-telegram-line"></i> Telegram
                        </a>
                    @endif
                </div>
                <span class="text-slate-400 text-sm">&copy; {{ date('Y') }} {{ $settings->site_name ?? 'BlizzLogspot' }}. All rights reserved.</span>
            </div>
        </div>
    </footer>

</div>{{-- /content wrapper --}}

<!-- Floating support buttons -->
@if($settings && $settings->telegram_support_link)
    <a href="{{ $settings->telegram_support_link }}" target="_blank"
       class="fixed bottom-6 right-4 z-50 bg-blue-500 hover:bg-blue-600 text-white w-12 h-12 flex items-center justify-center rounded-full shadow-lg transition-all duration-300 hover:scale-110">
        <i class="ri-telegram-fill text-2xl"></i>
    </a>
@endif
@if($settings && $settings->whatsapp_support_link)
    <a href="{{ $settings->whatsapp_support_link }}" target="_blank"
       class="fixed bottom-20 right-4 z-50 bg-green-500 hover:bg-green-600 text-white w-12 h-12 flex items-center justify-center rounded-full shadow-lg transition-all duration-300 hover:scale-110 md:hidden">
        <i class="ri-whatsapp-fill text-2xl"></i>
    </a>
@endif

<!-- Toast container -->
<div id="toast-container" class="fixed top-20 right-4 z-50 space-y-2"></div>

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<!-- AOS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof AOS !== 'undefined') {
            AOS.init({ duration: 600, easing: 'ease-in-out', once: true });
        }
    });

    function showToast(message, type = 'success') {
        const colors = { success: 'bg-green-500', error: 'bg-red-500', info: 'bg-primary-500' };
        const icons  = { success: 'ri-check-line', error: 'ri-close-circle-line', info: 'ri-information-line' };
        const toast  = document.createElement('div');
        toast.className = `${colors[type] || colors.info} text-white px-5 py-3 rounded-xl shadow-xl flex items-center gap-3 transform transition-all duration-300 translate-x-full max-w-xs`;
        toast.innerHTML = `<i class="${icons[type] || icons.info} text-lg"></i><span class="text-sm flex-1">${message}</span><button onclick="this.parentElement.remove()" class="ml-1 opacity-70 hover:opacity-100"><i class="ri-close-line text-sm"></i></button>`;
        const container = document.getElementById('toast-container');
        if (container) container.appendChild(toast);
        setTimeout(() => toast.classList.remove('translate-x-full'), 100);
        setTimeout(() => { toast.classList.add('translate-x-full'); setTimeout(() => toast.remove(), 300); }, 5000);
    }

    function notify(type, message) { showToast(message, type); }

    function stripHtmlTags(html) {
        const div = document.createElement('div');
        div.innerHTML = html;
        return div.textContent || div.innerText || '';
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(stripHtmlTags(text))
            .then(() => showToast('Copied to clipboard!', 'success'))
            .catch(() => showToast('Failed to copy', 'error'));
    }
</script>

<!--StartofTawk.toScript-->
{{-- <script type="text/javascript">var Tawk_API=Tawk_API||{},Tawk_LoadStart=new Date();(function(){var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];s1.async=true;s1.src='https://embed.tawk.to/68ea1533ca0084195466fde7/default';s1.charset='UTF-8';s1.setAttribute('crossorigin','*');s0.parentNode.insertBefore(s1,s0);})();</script> --}}
<!--End of Tawk.to Script-->

@stack('scripts')
</body>
</html>
