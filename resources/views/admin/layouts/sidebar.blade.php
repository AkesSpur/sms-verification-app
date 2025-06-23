
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
                <li class="menu-header">Dashboard</li>
                <li class="dropdown {{ setActive(['admin.dashboard']) }}">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link"><i
                            class="fas fa-fire"></i><span>Dashboard</span></a>
                </li>

                <li class="dropdown">
                    <a href="/" class="nav-link"><i class="fas fa-globe"></i><span>Visit
                            Store</span></a>
                </li>

                <li class="menu-header">Ecommerce</li>
  
                <li class="dropdown {{ setActive(['admin.digital-product-categories.*', 'admin.digital-product-subcategories.*', 'admin.digital-products.*', 'admin.digital-product-logs.*', 'admin.digital-product-orders.*', 'admin.gift-orders.*']) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-digital-tachograph"></i>
                        <span>Digital Products</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ setActive(['admin.digital-product-categories.*']) }}"><a class="nav-link"
                                href="{{ route('admin.digital-product-categories.index') }}">Categories</a>
                        </li>
                        <li class="{{ setActive(['admin.digital-product-subcategories.*']) }}"><a class="nav-link"
                                href="{{ route('admin.digital-product-subcategories.index') }}">Subcategories</a>
                        </li>
                        <li class="{{ setActive(['admin.digital-products.*']) }}"><a class="nav-link"
                                href="{{ route('admin.digital-products.index') }}">Products</a>
                        </li>
                        <li class="{{ setActive(['admin.digital-product-logs.*']) }}"><a class="nav-link"
                                href="{{ route('admin.digital-product-logs.index') }}">Product Logs</a>
                        </li>
                        <li class="{{ setActive(['admin.digital-product-orders.*']) }}"><a class="nav-link"
                                href="{{ route('admin.digital-product-orders.index') }}">Log Orders</a>
                        </li>
                        <li class="{{ setActive(['admin.gift-orders.*']) }}"><a class="nav-link"
                                href="{{ route('admin.gift-orders.index') }}">Gift Orders</a>
                        </li>
                    </ul>
                </li>

                <li class="{{ setActive(['admin.gifts.*']) }}">
                    <a href="{{ route('admin.gifts.index') }}" class="nav-link">
                        <i class="fas fa-gift"></i>
                        <span>Gift Management</span>
                    </a>
                </li>
                <li class="{{ setActive(['admin.transactions.*']) }}">
                    <a href="{{ route('admin.transactions.index') }}" class="nav-link">
                        <i class="fas fa-money-bill"></i>
                        <span>Transactions</span>
                    </a>
                </li>

                <li class="{{ setActive(['admin.banners.*']) }}">
                    <a href="{{ route('admin.banners.index') }}" class="nav-link">
                        <i class="fas fa-images"></i>
                        <span>Banner Management</span>
                    </a>
                </li>

                {{-- <li
                    class="dropdown {{ setActive(['admin.order.*', 'admin.gift-info', 'admin.pending-gift-orders']) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-cart-plus"></i>
                        <span>Orders</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ setActive(['admin.order.*']) }}"><a class="nav-link"
                                href="{{ route('admin.order.index') }}">All Orders</a>
                        </li>
                        <li class="{{ setActive(['admin.pending-gift-orders']) }}"><a class="nav-link"
                                href="{{ route('admin.pending-gift-orders') }}">All Pending Gift Orders</a>
                        </li>
                    </ul>
                </li> --}}

                {{-- <li class="{{ setActive(['admin.transaction']) }}"><a class="nav-link"
                        href="{{ route('admin.transaction') }}"><i class="fas fa-money-bill-alt"></i>
                        <span>Transactions</span></a>
                </li> --}}



                <li
                    class="dropdown {{ setActive(['admin.customer.index', 'admin.manage-user.index', 'admin.admin-list.index']) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-users"></i>
                        <span>Users</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ setActive(['admin.customer.index']) }}"><a class="nav-link"
                                href="{{ route('admin.customer.index') }}">Customer list</a></li>

                        <li class="{{ setActive(['admin.admin-list.index']) }}"><a class="nav-link"
                                href="{{ route('admin.admin-list.index') }}">Admin Lists</a></li>

                        <li class="{{ setActive(['admin.manage-user.index']) }}"><a class="nav-link"
                                href="{{ route('admin.manage-user.index') }}">Manage user</a></li>

                    </ul>
                </li>


           
                <li class="menu-header">Settings & More</li>

                {{-- <li class="{{ setActive(['admin.country-list.*']) }}"><a class="nav-link"
                        href="{{ route('admin.country-list.index') }}"><i class="fas fa-flag"></i>
                        Country list
                    </a></li> --}}

                {{-- <li class="{{ setActive(['admin.support-info.*']) }}"><a class="nav-link"
                        href="{{ route('admin.support-info.index') }}">
                        <i class="fas fa-headset"></i>
                        <span>
                            Support Info
                        </span>
                    </a>
                </li> --}}

                <li class="{{ setActive(['admin.country-service.*']) }}"><a class="nav-link"
                        href="{{ route('admin.country-service.index') }}"><i class="fas fa-globe-americas"></i>
                        <span>Country Service Pricing </span>
                    </a></li>

                <li class="{{ setActive(['admin.services.*']) }}"><a class="nav-link"
                        href="{{ route('admin.services.index') }}"><i class="fas fa-cogs"></i>
                        <span>Service Management </span>
                    </a></li>

                <li class="{{ setActive(['admin.payment-settings.*']) }}"><a class="nav-link"
                        href="{{ route('admin.payment-settings.index') }}"><i class="fas fa-cog"></i>
                        <span> Payment Settings</span>
                    </a></li>


                <li class="{{ setActive(['admin.settings.index']) }}"><a class="nav-link"
                        href="{{ route('admin.settings.index') }}"><i class="fas fa-wrench"></i>
                        <span>Settings</span></a>
                </li>

            </ul>

        </aside>
    </div>
