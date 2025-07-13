@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>
        
        <!-- Pending Orders Section -->
        <div class="row">
            <div class="col-12">
                <h5 class="mb-3"><i class="fas fa-clock text-warning"></i> Pending Orders</h5>
            </div>
            
            <!-- Pending Gift Orders -->
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-gift text-danger"></i> Pending Gift Orders</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.gift-orders.index', ['status' => 'pending']) }}" class="btn btn-primary">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($pendingGiftOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>User</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingGiftOrders as $order)
                                            <tr>
                                                <td>#{{ $order->id }}</td>
                                                <td>{{ $order->user->name ?? 'Guest' }}</td>
                                                <td>₦{{ number_format($order->total_amount, 2) }}</td>
                                                <td><span class="badge badge-warning">{{ ucfirst($order->status) }}</span></td>
                                                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No pending gift orders</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Pending Social Media Orders -->
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-thumbs-up text-success"></i> Pending Social Media Orders</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.social-media-orders.index', ['status' => 'pending']) }}" class="btn btn-primary">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($pendingSocialOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>User</th>
                                            <th>Service</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingSocialOrders as $order)
                                            <tr>
                                                <td>#{{ $order->id }}</td>
                                                <td>{{ $order->user->name ?? 'Guest' }}</td>
                                                <td>{{ $order->product->name ?? 'N/A' }}</td>
                                                <td>₦{{ number_format($order->total_amount, 2) }}</td>
                                                <td><span class="badge badge-warning">{{ ucfirst($order->status) }}</span></td>
                                                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-thumbs-up fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No pending social media orders</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
           <!-- Gift Orders Statistics -->
           <div class="row">
            <div class="col-12">
                <h5 class="mb-3"><i class="fas fa-gift"></i> Gift Orders</h5>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Gift Orders</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($stats['gift_total_orders']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <a href="{{ route('admin.gift-orders.index', ['status' => 'pending']) }}">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pending Gift Orders</h4>
                            </div>
                            <div class="card-body">
                                {{ number_format($stats['gift_pending_orders']) }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Confirmed</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($stats['gift_confirmed_orders']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Gift Revenue</h4>
                        </div>
                        <div class="card-body">
                            ₦{{ number_format($stats['gift_total_revenue']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <!-- Digital Product Orders Statistics -->
        <div class="row">
            <div class="col-12">
                <h5 class="mb-3"><i class="fas fa-laptop"></i> Digital Product Orders</h5>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Digital Orders</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($stats['digital_total_orders']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Completed</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($stats['digital_completed_orders']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Digital Revenue</h4>
                        </div>
                        <div class="card-body">
                            ₦{{ number_format($stats['digital_total_revenue']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media Boosting Statistics -->
        <div class="row">
            <div class="col-12">
                <h5 class="mb-3"><i class="fas fa-thumbs-up"></i> Social Media Boosting Orders</h5>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-thumbs-up"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Social Orders</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($stats['social_total_orders']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <a href="{{ route('admin.social-media-orders.index', ['status' => 'pending']) }}">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pending Social Orders</h4>
                            </div>
                            <div class="card-body">
                                {{ number_format($stats['social_pending_orders']) }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Completed</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($stats['social_completed_orders']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Social Revenue</h4>
                        </div>
                        <div class="card-body">
                            ₦{{ number_format($stats['social_total_revenue']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Statistics -->
        <div class="row">
            <div class="col-12">
                <h5 class="mb-3"><i class="fas fa-exchange-alt"></i> Transactions</h5>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <a href="{{ route('admin.transactions.index') }}">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Transactions</h4>
                            </div>
                            <div class="card-body">
                                {{ number_format($stats['total_transactions']) }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Credits</h4>
                        </div>
                        <div class="card-body">
                            ₦{{ number_format($stats['total_credits'], 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Debits</h4>
                        </div>
                        <div class="card-body">
                            ₦{{ number_format($stats['total_debits'], 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Today's Transactions</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($stats['today_transactions']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Digital Product Purchases -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-shopping-bag"></i> Recent Digital Product Purchases (Last 24 Hours)</h4>
                    </div>
                    <div class="card-body">
                        @if($recentDigitalPurchases->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Customer</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Purchase Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentDigitalPurchases as $purchase)
                                            <tr>
                                                <td>{{ $purchase->product->name ?? 'N/A' }}</td>
                                                <td>{{ $purchase->user->name ?? 'Guest' }}</td>
                                                <td>{{ $purchase->quantity }}</td>
                                                <td>₦{{ number_format($purchase->total_amount, 2) }}</td>
                                                <td>
                                                    @if($purchase->status == 'completed')
                                                        <span class="badge badge-success">Completed</span>
                                                    @elseif($purchase->status == 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @else
                                                        <span class="badge badge-danger">{{ ucfirst($purchase->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $purchase->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No digital product purchases in the last 24 hours</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Social Media Purchases -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-thumbs-up"></i> Recent Social Media Purchases (Last 24 Hours)</h4>
                    </div>
                    <div class="card-body">
                        @if($recentSocialPurchases->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Service Name</th>
                                            <th>Customer</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Purchase Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentSocialPurchases as $purchase)
                                            <tr>
                                                <td>{{ $purchase->product->name ?? 'N/A' }}</td>
                                                <td>{{ $purchase->user->name ?? 'Guest' }}</td>
                                                <td>{{ $purchase->quantity }}</td>
                                                <td>₦{{ number_format($purchase->total_amount, 2) }}</td>
                                                <td>
                                                    @if($purchase->status == 'completed')
                                                        <span class="badge badge-success">Completed</span>
                                                    @elseif($purchase->status == 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @elseif($purchase->status == 'processing')
                                                        <span class="badge badge-info">Processing</span>
                                                    @else
                                                        <span class="badge badge-danger">{{ ucfirst($purchase->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $purchase->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-thumbs-up fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No social media purchases in the last 24 hours</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Statistics -->
        <div class="row">
            <div class="col-12">
                <h5 class="mb-3"><i class="fas fa-chart-line"></i> Revenue Overview</h5>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Daily Revenue</h4>
                        </div>
                        <div class="card-body">
                            ₦{{ number_format($todaysRevenue) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Monthly Revenue</h4>
                        </div>
                        <div class="card-body">
                            ₦{{ number_format($monthRevenue) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Yearly Revenue</h4>
                        </div>
                        <div class="card-body">
                            ₦{{ number_format($yearRevenue) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Services</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($totalServices) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Revenue Breakdown -->
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="mb-3"><i class="fas fa-chart-pie"></i> Revenue Breakdown by Service Type</h5>
            </div>
            
            <!-- SMS Revenue -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-sms text-primary"></i> SMS Verification Revenue</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-muted">Today</div>
                                <div class="font-weight-bold text-primary">₦{{ number_format($todaysSmsRevenue) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">This Month</div>
                                <div class="font-weight-bold text-info">₦{{ number_format($monthSmsRevenue) }}</div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <div class="text-muted">This Year</div>
                                <div class="font-weight-bold text-success">₦{{ number_format($yearSmsRevenue) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">All Time</div>
                                <div class="font-weight-bold text-dark">₦{{ number_format($stats['sms_total_revenue']) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Digital Products Revenue -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-download text-warning"></i> Digital Products Revenue</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-muted">Today</div>
                                <div class="font-weight-bold text-primary">₦{{ number_format($todaysDigitalRevenue) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">This Month</div>
                                <div class="font-weight-bold text-info">₦{{ number_format($monthDigitalRevenue) }}</div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <div class="text-muted">This Year</div>
                                <div class="font-weight-bold text-success">₦{{ number_format($yearDigitalRevenue) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">All Time</div>
                                <div class="font-weight-bold text-dark">₦{{ number_format($stats['digital_total_revenue']) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gift Orders Revenue -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-gift text-danger"></i> Gift Orders Revenue</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-muted">Today</div>
                                <div class="font-weight-bold text-primary">₦{{ number_format($todaysGiftRevenue) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">This Month</div>
                                <div class="font-weight-bold text-info">₦{{ number_format($monthGiftRevenue) }}</div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <div class="text-muted">This Year</div>
                                <div class="font-weight-bold text-success">₦{{ number_format($yearGiftRevenue) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">All Time</div>
                                <div class="font-weight-bold text-dark">₦{{ number_format($stats['gift_total_revenue']) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Social Media Boosting Revenue -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-thumbs-up text-success"></i> Social Media Revenue</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-muted">Today</div>
                                <div class="font-weight-bold text-primary">₦{{ number_format($todaysSocialRevenue) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">This Month</div>
                                <div class="font-weight-bold text-info">₦{{ number_format($monthSocialRevenue) }}</div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <div class="text-muted">This Year</div>
                                <div class="font-weight-bold text-success">₦{{ number_format($yearSocialRevenue) }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">All Time</div>
                                <div class="font-weight-bold text-dark">₦{{ number_format($stats['social_total_revenue']) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <a href="{{ route('admin.order.index') }}">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-cart-plus"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Todays Orders</h4>
                            </div>
                            <div class="card-body">
                                {{ $todaysOrder }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <a href="{{ route('admin.pending-gift-orders') }}">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-cart-plus"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Todays Peding Orders</h4>
                            </div>
                            <div class="card-body">
                                {{ $todaysPendingOrder }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <a href="{{ route('admin.order.index') }}">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-cart-plus"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Orders</h4>
                            </div>
                            <div class="card-body">
                                {{ $totalOrders }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <a href="{{ route('admin.pending-gift-orders') }}">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-cart-plus"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Pending Orders</h4>
                            </div>
                            <div class="card-body">
                                {{ $totalPendingOrders }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>


            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <a href="{{ route('admin.order.index') }}">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="fas fa-cart-plus"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Complelte Orders</h4>
                            </div>
                            <div class="card-body">
                                {{ $totalCompleteOrders }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <a href="">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="fas fa-money-bill-alt"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Todays Earnings</h4>
                            </div>
                            <div class="card-body">
                                {{$settings->currency_icon}}{{ round($todaysEarnings) }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <a href="">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="fas fa-money-bill-alt"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>This Month Earnings</h4>
                            </div>
                            <div class="card-body">
                                {{$settings->currency_icon}}{{ round($monthEarnings) }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <a href="">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info">
                            <i class="fas fa-money-bill-alt"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>This Years Earnings</h4>
                            </div>
                            <div class="card-body">
                                {{$settings->currency_icon}}{{ round($yearEarnings) }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <a href="{{route('admin.category.index')}}">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Categories</h4>
                            </div>
                            <div class="card-body">
                                {{ $totalCategories }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>

             <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <a href="{{route('admin.customer.index')}}">
                 <div class="card card-statistic-1">
                     <div class="card-icon bg-warning">
                         <i class="far fa-file"></i>
                     </div>
                     <div class="card-wrap">
                         <div class="card-header">
                             <h4>Total Users</h4>
                         </div>
                         <div class="card-body">
                             {{$totalUsers}}
                         </div>
                     </div>
                 </div>
             </a>
             </div>

        </div> --}}

    </section>
@endsection
