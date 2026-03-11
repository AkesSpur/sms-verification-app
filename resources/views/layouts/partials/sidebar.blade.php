{{-- Sidebar partial — white theme, toggleable overlay --}}

<!-- Brand header -->
<div class="flex items-center justify-between h-16 px-5 flex-shrink-0 border-b border-gray-100">
    <a href="{{ route('home') }}" class="flex items-center gap-2.5 min-w-0">
        {{-- @if($logoSetting && $logoSetting->logo)
            <img src="{{ asset($logoSetting->logo) }}" alt="Logo" class="h-7 w-auto flex-shrink-0">
        @else
            <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center flex-shrink-0">
                <i class="ri-store-2-fill text-white text-sm"></i>
            </div>
        @endif --}}
        <span class="text-slate-800 font-bold text-sm tracking-wide truncate">{{ $settings->site_name ?? 'BlizzLogspot' }}</span>
    </a>
    <button @click="sidebarOpen = false"
            class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors flex-shrink-0">
        <i class="ri-close-line text-xl"></i>
    </button>
</div>

<!-- Navigation -->
<nav class="sidebar-nav flex-1 overflow-y-auto py-4 px-3 space-y-0.5">

    <!-- Dashboard -->
    <a href="{{ route('user.dashboard') }}"
       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 {{ request()->routeIs('user.dashboard') ? 'sidebar-active' : 'text-gray-600 hover:text-gray-900' }}">
        <i class="nav-icon ri-dashboard-2-line text-base flex-shrink-0 {{ request()->routeIs('user.dashboard') ? 'text-indigo-600' : 'text-gray-400' }}"></i>
        <span class="font-medium">Dashboard</span>
    </a>

    {{-- Admin Dashboard Link (Sidebar) --}}
    @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 text-gray-600 hover:text-gray-900">
            <i class="nav-icon ri-shield-keyhole-line text-base flex-shrink-0 text-gray-400"></i>
            <span class="font-medium">Admin Panel</span>
        </a>
    @endif

    <p class="px-3 pt-5 pb-1.5 text-[10px] font-bold uppercase tracking-[0.1em] text-gray-400">SMS Services</p>

    <!-- USA Numbers 1 -->
    <a href="{{ route('user.sms.rental.index') }}"
       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 {{ request()->routeIs('user.sms.rental.*') ? 'sidebar-active' : 'text-gray-600 hover:text-gray-900' }}">
        <i class="nav-icon ri-sim-card-line text-base flex-shrink-0 {{ request()->routeIs('user.sms.rental.*') ? 'text-indigo-600' : 'text-gray-400' }}"></i>
        <span class="font-medium">USA Numbers 1</span>
    </a>

    <!-- USA Numbers 2 -->
    <a href="{{ route('user.usa-numbers') }}"
       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 {{ request()->routeIs('user.usa-numbers') ? 'sidebar-active' : 'text-gray-600 hover:text-gray-900' }}">
        <i class="nav-icon ri-smartphone-line text-base flex-shrink-0 {{ request()->routeIs('user.usa-numbers') ? 'text-indigo-600' : 'text-gray-400' }}"></i>
        <span class="font-medium">USA Numbers 2</span>
    </a>

    <!-- All Countries -->
    <a href="{{ route('user.all-countries') }}"
       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 {{ request()->routeIs('user.all-countries') ? 'sidebar-active' : 'text-gray-600 hover:text-gray-900' }}">
        <i class="nav-icon ri-earth-line text-base flex-shrink-0 {{ request()->routeIs('user.all-countries') ? 'text-indigo-600' : 'text-gray-400' }}"></i>
        <span class="font-medium">All Countries</span>
    </a>

    <p class="px-3 pt-5 pb-1.5 text-[10px] font-bold uppercase tracking-[0.1em] text-gray-400">Store</p>

    <!-- Reseller Store -->
    <a href="{{ route('user.reseller') }}"
       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 {{ request()->routeIs('user.reseller') ? 'sidebar-active' : 'text-gray-600 hover:text-gray-900' }}">
        <i class="nav-icon ri-price-tag-3-line text-base flex-shrink-0 {{ request()->routeIs('user.reseller') ? 'text-indigo-600' : 'text-gray-400' }}"></i>
        <span class="font-medium">Reseller Store</span>
    </a>

    <!-- Logs Store -->
    <a href="{{ route('home') }}"
       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 {{ request()->routeIs('home') || request()->routeIs('all-categories') || request()->routeIs('subcategory.show') ? 'sidebar-active' : 'text-gray-600 hover:text-gray-900' }}">
        <i class="nav-icon ri-archive-drawer-line text-base flex-shrink-0 {{ request()->routeIs('home') || request()->routeIs('all-categories') || request()->routeIs('subcategory.show') ? 'text-indigo-600' : 'text-gray-400' }}"></i>
        <span class="font-medium">Logs Store</span>
    </a>

    <!-- Boosting -->
    <a href="{{ route('user.social-media-boosting.index') }}"
       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 {{ request()->routeIs('user.social-media-boosting.*') || request()->routeIs('user.social-media-orders.*') ? 'sidebar-active' : 'text-gray-600 hover:text-gray-900' }}">
        <i class="nav-icon ri-rocket-line text-base flex-shrink-0 {{ request()->routeIs('user.social-media-boosting.*') ? 'text-indigo-600' : 'text-gray-400' }}"></i>
        <span class="font-medium">Boosting Of Accounts</span>
    </a>

    <!-- Gift Store -->
    <a href="{{ route('all-gifts') }}"
       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 {{ request()->routeIs('all-gifts') || request()->routeIs('gift.show') ? 'sidebar-active' : 'text-gray-600 hover:text-gray-900' }}">
        <i class="nav-icon ri-gift-line text-base flex-shrink-0 {{ request()->routeIs('all-gifts') || request()->routeIs('gift.show') ? 'text-indigo-600' : 'text-gray-400' }}"></i>
        <span class="font-medium">Gift Store</span>
    </a>

    <p class="px-3 pt-5 pb-1.5 text-[10px] font-bold uppercase tracking-[0.1em] text-gray-400">History</p>

    <!-- Order History (collapsible group) -->
    <button @click="orderHistoryOpen = !orderHistoryOpen"
            class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 {{ request()->routeIs('user.orders.*') || request()->routeIs('user.order-history') ? 'sidebar-active' : 'text-gray-600 hover:text-gray-900' }}">
        <i class="nav-icon ri-history-line text-base flex-shrink-0 {{ request()->routeIs('user.orders.*') ? 'text-indigo-600' : 'text-gray-400' }}"></i>
        <span class="font-medium flex-1 text-left">Order History</span>
        <i class="ri-arrow-down-s-line text-gray-400 text-base transition-transform duration-200"
           :class="{ 'rotate-180': orderHistoryOpen }"></i>
    </button>

    <div x-show="orderHistoryOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-cloak
         class="pl-8 mt-0.5 space-y-0.5">
        <a href="{{ route('user.orders.sms') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('user.orders.sms') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}">
            <i class="ri-message-2-line text-sm"></i> SMS Orders
        </a>
        <a href="{{ route('user.orders.logs') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('user.orders.logs') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}">
            <i class="ri-box-3-line text-sm"></i> Log Orders
        </a>
        <a href="{{ route('user.orders.gifts') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('user.orders.gifts') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}">
            <i class="ri-gift-line text-sm"></i> Gift Orders
        </a>
        <a href="{{ route('user.orders.reseller') }}"
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('user.orders.reseller') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}">
            <i class="ri-price-tag-3-line text-sm"></i> Reseller Orders
        </a>
    </div>

    <!-- Transactions -->
    <a href="{{ route('user.transaction') }}"
       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 {{ request()->routeIs('user.transaction') ? 'sidebar-active' : 'text-gray-600 hover:text-gray-900' }}">
        <i class="nav-icon ri-bank-card-line text-base flex-shrink-0 {{ request()->routeIs('user.transaction') ? 'text-indigo-600' : 'text-gray-400' }}"></i>
        <span class="font-medium">Transactions</span>
    </a>

    <!-- Profile Settings -->
    <a href="{{ route('profile.edit') }}"
       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 {{ request()->routeIs('profile.edit') ? 'sidebar-active' : 'text-gray-600 hover:text-gray-900' }}">
        <i class="nav-icon ri-user-settings-line text-base flex-shrink-0 {{ request()->routeIs('profile.edit') ? 'text-indigo-600' : 'text-gray-400' }}"></i>
        <span class="font-medium">Profile Settings</span>
    </a>

</nav>

<!-- User info + Logout -->
<div class="flex-shrink-0 p-4 border-t border-gray-100">
    <div class="flex items-center gap-3 mb-3">
        <div class="w-9 h-9 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-700 text-sm font-bold flex-shrink-0">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name ?? 'User' }}</p>
            <p class="text-xs text-gray-400">&#8358;{{ number_format(auth()->user()->balance ?? 0) }}</p>
        </div>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="flex items-center gap-2.5 w-full px-3 py-2 text-xs font-medium text-red-500 rounded-xl hover:bg-red-50 hover:text-red-600 transition-all">
            <i class="ri-logout-box-r-line text-sm"></i>
            Sign Out
        </button>
    </form>
</div>
