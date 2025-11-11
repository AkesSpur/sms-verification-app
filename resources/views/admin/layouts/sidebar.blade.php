
<div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
            <div class="sidebar-brand">
                <a href="">
                    {{ $settings->site_name }}
                </a>
            </div>
            <div class="sidebar-brand sidebar-brand-sm">
                <a href="">||</a>
            </div>
            <ul class="sidebar-menu">
                <!-- Dashboard Section -->
                <li class="menu-header">Dashboard</li>
                <li class="dropdown {{ setActive(['admin.dashboard']) }}">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="fas fa-fire"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="/" class="nav-link">
                        <i class="fas fa-globe"></i>
                        <span>Visit Store</span>
                    </a>
                </li>

                <!-- SMS & Digital Products Section -->
                <!-- Service Management Section -->
                <li class="menu-header">Service Management</li>
                <li class="dropdown {{ setActive(['admin.daisy-services.*']) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-mobile-alt"></i>
                        <span>DaisySMS Services</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ setActive(['admin.daisy-services.index']) }}">
                            <a class="nav-link" href="{{ route('admin.daisy-services.index') }}">All Services</a>
                        </li>
                        <li class="{{ setActive(['admin.daisy-services.create']) }}">
                            <a class="nav-link" href="{{ route('admin.daisy-services.create') }}">Add New Service</a>
                        </li>
                    </ul>
                </li>
                <li class="menu-header">SMS & Digital Products</li>
                <li class="{{ setActive(['admin.services.*']) }}">
                    <a href="{{ route('admin.services.index') }}" class="nav-link">
                        <i class="fas fa-sms"></i>
                        <span>SMS Services</span>
                    </a>
                </li>
                <li class="{{ setActive(['admin.country-service.*']) }}">
                    <a href="{{ route('admin.country-service.index') }}" class="nav-link">
                        <i class="fas fa-globe-americas"></i>
                        <span>Country Service Pricing</span>
                    </a>
                </li>
                <li class="dropdown {{ setActive(['admin.digital-product-categories.*', 'admin.digital-product-subcategories.*', 'admin.digital-products.*', 'admin.digital-product-logs.*']) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-digital-tachograph"></i>
                        <span>Digital Products</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ setActive(['admin.digital-product-categories.*']) }}">
                            <a class="nav-link" href="{{ route('admin.digital-product-categories.index') }}">Categories</a>
                        </li>
                        <li class="{{ setActive(['admin.digital-product-subcategories.*']) }}">
                            <a class="nav-link" href="{{ route('admin.digital-product-subcategories.index') }}">Subcategories</a>
                        </li>
                        <li class="{{ setActive(['admin.digital-products.*']) }}">
                            <a class="nav-link" href="{{ route('admin.digital-products.index') }}">Products</a>
                        </li>
                        <li class="{{ setActive(['admin.digital-product-logs.*']) }}">
                            <a class="nav-link" href="{{ route('admin.digital-product-logs.index') }}">Product Logs</a>
                        </li>
                    </ul>
                </li>
                <li class="{{ setActive(['admin.gifts.*']) }}">
                    <a href="{{ route('admin.gifts.index') }}" class="nav-link">
                        <i class="fas fa-gift"></i>
                        <span>Gift Management</span>
                    </a>
                </li>
                <li class="dropdown {{ setActive(['admin.social-media-categories.*', 'admin.social-media-products.*']) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fab fa-instagram"></i>
                        <span>Social Media Boosting</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ setActive(['admin.social-media-categories.*']) }}">
                            <a class="nav-link" href="{{ route('admin.social-media-categories.index') }}">Categories</a>
                        </li>
                        <li class="{{ setActive(['admin.social-media-products.*']) }}">
                            <a class="nav-link" href="{{ route('admin.social-media-products.index') }}">Products</a>
                        </li>
                    </ul>
                </li>

                <li class="menu-header">Reseller Management</li>
                <li class="dropdown {{ setActive(['admin.reseller-products.*', 'admin.reseller-product-logs.*', 'admin.reseller-requests.*']) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-tags"></i>
                        <span>Resellers</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ setActive(['admin.reseller-products.*']) }}">
                            <a class="nav-link" href="{{ route('admin.reseller-products.index') }}">Products</a>
                        </li>
                        <li class="{{ setActive(['admin.reseller-product-logs.*']) }}">
                            <a class="nav-link" href="{{ route('admin.reseller-product-logs.index') }}">Product Logs</a>
                        </li>
                        <li class="{{ setActive(['admin.reseller-requests.*']) }}">
                            <a class="nav-link" href="{{ route('admin.reseller-requests.index') }}">Access Requests</a>
                        </li>
                    </ul>
                </li>

                <!-- Order Management Section -->
                <li class="menu-header">Order Management</li>
                <li class="dropdown {{ setActive(['admin.sms-orders.*', 'admin.daisy-orders.*', 'admin.digital-product-orders.*', 'admin.gift-orders.*', 'admin.social-media-orders.*', 'admin.reseller-orders.*', 'admin.order.*', 'admin.gift-info', 'admin.pending-gift-orders']) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Orders</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ setActive(['admin.sms-orders.*']) }}">
                            <a class="nav-link" href="{{ route('admin.sms-orders.index') }}">SMS Orders</a>
                        </li>
                        <li class="{{ setActive(['admin.daisy-orders.*']) }}">
                            <a href="{{ route('admin.daisy-orders.index') }}" class="nav-link">
                                Daisy SMS Orders
                            </a>
                        </li>
                        <li class="{{ setActive(['admin.digital-product-orders.*']) }}">
                            <a class="nav-link" href="{{ route('admin.digital-product-orders.index') }}">Digital Product Orders</a>
                        </li>
                        <li class="{{ setActive(['admin.gift-orders.*']) }}">
                            <a class="nav-link" href="{{ route('admin.gift-orders.index') }}">Gift Orders</a>
                        </li>
                        <li class="{{ setActive(['admin.social-media-orders.*']) }}">
                            <a class="nav-link" href="{{ route('admin.social-media-orders.index') }}">Social Media Orders</a>
                        </li>
                        <li class="{{ setActive(['admin.reseller-orders.*']) }}">
                            <a class="nav-link" href="{{ route('admin.reseller-orders.index') }}">Reseller Orders</a>
                        </li>
                        {{-- Uncomment when routes are available
                        <li class="{{ setActive(['admin.order.*']) }}">
                            <a class="nav-link" href="{{ route('admin.order.index') }}">All Orders</a>
                        </li>
                        <li class="{{ setActive(['admin.pending-gift-orders']) }}">
                            <a class="nav-link" href="{{ route('admin.pending-gift-orders') }}">Pending Gift Orders</a>
                        </li>
                        --}}
                    </ul>
                </li>
                <li class="{{ setActive(['admin.transactions.*']) }}">
                    <a href="{{ route('admin.transactions.index') }}" class="nav-link">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Transactions</span>
                    </a>
                </li>

                <!-- User Management Section -->
                <li class="menu-header">User Management</li>
                <li class="dropdown {{ setActive(['admin.customer.index', 'admin.manage-user.index', 'admin.admin-list.index']) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ setActive(['admin.customer.index']) }}">
                            <a class="nav-link" href="{{ route('admin.customer.index') }}">Customers</a>
                        </li>
                        <li class="{{ setActive(['admin.admin-list.index']) }}">
                            <a class="nav-link" href="{{ route('admin.admin-list.index') }}">Administrators</a>
                        </li>
                        <li class="dropdown {{ setActive(['admin.resellers.index']) }}">
                            <a href="{{ route('admin.resellers.index') }}" class="nav-link">Reseller Users</a>
                        </li>
                        <li class="{{ setActive(['admin.manage-user.index']) }}">
                            <a class="nav-link" href="{{ route('admin.manage-user.index') }}">Manage Users</a>
                        </li>
                    </ul>
                </li>

                <!-- Content & Marketing Section -->
                <li class="menu-header">Content & Marketing</li>
                <li class="{{ setActive(['admin.banners.*']) }}">
                    <a href="{{ route('admin.banners.index') }}" class="nav-link">
                        <i class="fas fa-images"></i>
                        <span>Banner Management</span>
                    </a>
                </li>

                <!-- System Settings Section -->
                <li class="menu-header">System Settings</li>
                <li class="{{ setActive(['admin.payment-settings.*']) }}">
                    <a href="{{ route('admin.payment-settings.index') }}" class="nav-link">
                        <i class="fas fa-credit-card"></i>
                        <span>Payment Settings</span>
                    </a>
                </li>
                <li class="{{ setActive(['admin.settings.index']) }}">
                    <a href="{{ route('admin.settings.index') }}" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>General Settings</span>
                    </a>
                </li>

            </ul>

        </aside>
    </div>
