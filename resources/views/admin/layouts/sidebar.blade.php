@if (auth()->user()->id == 1)
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
                    <a href="{{ route('products.index') }}" class="nav-link"><i class="fas fa-globe"></i><span>Visit
                            Store</span></a>
                </li>

                <li class="menu-header">Ecommerce</li>

                <li class="dropdown {{ setActive(['admin.category.*']) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-list"></i>
                        <span>Manage Categories</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ setActive(['admin.category.create']) }}"><a class="nav-link"
                                href="{{ route('admin.category.create') }}">Create Category </a>
                        </li>
                        <li class="{{ setActive(['admin.category.index']) }}"><a class="nav-link"
                                href="{{ route('admin.category.index') }}">View Categories</a>
                        </li>
                    </ul>
                </li>

                <li
                    class="dropdown {{ setActive([
                        // 'admin.product-log.*',
                        'admin.products.*',
                        'admin.products-image-gallery.*',
                        'admin.customization.index',
                        'admin.product-format-links.*',
                    ]) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-box"></i>
                        <span>Manage Products</span></a>
                    <ul class="dropdown-menu">
                        {{-- <li
                            class="{{ setActive(['admin.product-log.*']) }}">
                            <a class="nav-link" href="{{ route('admin.product-log.index') }}">Logins</a>
                        </li> --}}
                        <li
                            class="{{ setActive(['admin.products.*']) }}">
                            <a class="nav-link" href="{{ route('admin.products.index') }}">Products</a>
                        </li>
                        <li class="{{ setActive(['admin.customization.index']) }}"><a class="nav-link"
                                href="{{ route('admin.customization.index') }}">Custom Gift Price</a>
                        </li>
                    </ul>
                </li>



                <li
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
                </li>

                <li class="{{ setActive(['admin.transaction']) }}"><a class="nav-link"
                        href="{{ route('admin.transaction') }}"><i class="fas fa-money-bill-alt"></i>
                        <span>Transactions</span></a>
                </li>



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


                <li
                    class="dropdown {{ setActive([
                        'admin.slider.*',
                        'admin.vendor-condition.index',
                        'admin.about.index',
                        'admin.terms-and-conditions.index',
                    ]) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-cog"></i>
                        <span>Manage Website</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ setActive(['admin.slider.*']) }}"><a class="nav-link"
                                href="{{ route('admin.slider.index') }}">Slider</a></li>
                        <li class="{{ setActive(['admin.about.index']) }}"><a class="nav-link"
                                href="{{ route('admin.about.index') }}">About page</a></li>
                        <li class="{{ setActive(['admin.terms-and-conditions.index']) }}"><a class="nav-link"
                                href="{{ route('admin.terms-and-conditions.index') }}">Terms Page</a></li>

                    </ul>
                </li>



                <li class="menu-header">Settings & More</li>

                <li class="{{ setActive(['admin.country-list.*']) }}"><a class="nav-link"
                        href="{{ route('admin.country-list.index') }}"><i class="fas fa-flag"></i>
                        Country list
                    </a></li>

                <li class="{{ setActive(['admin.support-info.*']) }}"><a class="nav-link"
                        href="{{ route('admin.support-info.index') }}">
                        <i class="fas fa-headset"></i>
                        <span>
                            Support Info
                        </span>
                    </a>
                </li>

                <li class="{{ setActive(['admin.payment-settings.*']) }}"><a class="nav-link"
                        href="{{ route('admin.payment-settings.index') }}"><i class="fas fa-cog"></i>
                        Payment Settings
                    </a></li>


                <li class="{{ setActive(['admin.settings.index']) }}"><a class="nav-link"
                        href="{{ route('admin.settings.index') }}"><i class="fas fa-wrench"></i>
                        <span>Settings</span></a>
                </li>

            </ul>

        </aside>
    </div>
@else
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
                    <a href="{{ route('products.index') }}" class="nav-link"><i class="fas fa-globe"></i><span>Visit
                            Store</span></a>
                </li>

                <li class="menu-header">Ecommerce</li>


                <li
                    class="dropdown {{ setActive([
                        'admin.product-log.*',
                        'admin.products.*',
                        'admin.customization.index',
                        'admin.product-format-links.*',
                    ]) }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i
                            class="fas fa-box"></i>
                        <span>Manage Products</span></a>
                    <ul class="dropdown-menu">
                        <li
                            class="{{ setActive(['admin.product-log.*']) }}">
                            <a class="nav-link" href="{{ route('admin.product-log.index') }}">Logins</a>
                        </li>
                        <li
                            class="{{ setActive(['admin.products.*']) }}">
                            <a class="nav-link" href="{{ route('admin.products.index') }}">Products</a>
                        </li>
                        <li class="{{ setActive(['admin.customization.index']) }}"><a class="nav-link"
                                href="{{ route('admin.customization.index') }}">Custom Gift Price</a>
                        </li>
                    </ul>
                </li>



                <li
                    class="dropdown {{ setActive(['admin.order.*', 'admin.pending-gift-orders']) }}">
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
                </li>

            </ul>

        </aside>
    </div>
@endif
