@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>

                        <!-- API Balances -->
        <div class="row mb-4">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title">API Balances (Cached: 10m)</div>
                    </div>
                    <div class="card-icon shadow-primary bg-primary">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>DaisySMS Balance</h4>
                        </div>
                        <div class="card-body">
                            {{ $apiBalances['daisy'] ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
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
            
            <!-- Pending & Processing Social Media Orders -->
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-thumbs-up text-success"></i> Pending & Processing Social Media Orders</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.social-media-orders.index') }}" class="btn btn-primary">View All</a>
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
                                            <th>Progress</th>
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
                                                <td>
                                                    @if($order->status === 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @elseif($order->status === 'processing')
                                                        <span class="badge badge-info">Processing</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ ucfirst($order->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->status === 'processing' && $order->external_order_id)
                                                        @php
                                                            // Calculate delivered quantity based on order status
                                                            if ($order->status === 'processing') {
                                                                if (($order->external_remains ?? 0) == 0) {
                                                                    $delivered = 0;
                                                                } else {
                                                                    $delivered = $order->quantity - ($order->external_remains ?? 0);
                                                                }
                                                            } elseif ($order->status === 'completed') {
                                                                $delivered = $order->quantity;
                                                            } else {
                                                                $delivered = max(0, ($order->external_start_count ?? 0) - ($order->external_remains ?? 0));
                                                            }
                                                            $progress = $order->quantity > 0 ? min(100, ($delivered / $order->quantity) * 100) : 0;
                                                        @endphp
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                                                {{ number_format($progress, 1) }}%
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">{{ number_format($delivered) }}/{{ number_format($order->quantity) }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-thumbs-up fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No pending or processing social media orders</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        {{-- <!-- Pending Daisy Orders -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-flower text-info"></i> Pending & Processing Daisy Orders</h4>
                        <div class="card-header-action">
                            <a href="#" class="btn btn-primary">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($pendingDaisyOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>User</th>
                                            <th>Service</th>
                                            <th>Phone</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingDaisyOrders as $order)
                                            <tr>
                                                <td>#{{ $order->id }}</td>
                                                <td>{{ $order->user->name ?? 'Guest' }}</td>
                                                <td>{{ $order->service->name ?? 'N/A' }}</td>
                                                <td>{{ $order->phone ?? 'N/A' }}</td>
                                                <td>₦{{ number_format($order->price, 2) }}</td>
                                                <td>
                                                    @if($order->status === 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @elseif($order->status === 'processing')
                                                        <span class="badge badge-info">Processing</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ ucfirst($order->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-flower fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No pending or processing Daisy orders</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
         --}}
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

        <!-- Reseller Orders Statistics -->
        <div class="row">
            <div class="col-12">
                <h5 class="mb-3"><i class="fas fa-store"></i> Reseller Orders</h5>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Reseller Orders</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($stats['reseller_total_orders']) }}
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
                            {{ number_format($stats['reseller_completed_orders']) }}
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
                            <h4>Reseller Revenue</h4>
                        </div>
                        <div class="card-body">
                            ₦{{ number_format($stats['reseller_total_revenue']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-dark">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Resellers</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($stats['total_resellers']) }}
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
                                @if ($processingSocialOrders)
                                <span class="text-small text-muted">
                                 ({{$processingSocialOrders}} in process)
                                </span>                                    
                                @endif
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

        <!-- Daisy Orders Statistics -->
        <div class="row">
            <div class="col-12">
                <h5 class="mb-3"><i class="fas fa-flower"></i> Daisy Orders</h5>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-flower"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Daisy Orders</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($stats['daisy_total_orders']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Pending Daisy Orders</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($stats['daisy_pending_orders']) }}
                            @if ($processingDaisyOrders)
                            <span class="text-small text-muted">
                             ({{$processingDaisyOrders}} in process)
                            </span>                                    
                            @endif
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
                            {{ number_format($stats['daisy_completed_orders']) }}
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
                            <h4>Daisy Revenue</h4>
                        </div>
                        <div class="card-body">
                            ₦{{ number_format($stats['daisy_total_revenue']) }}
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

        {{-- <!-- Recent Daisy Orders -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-phone"></i> Recent Daisy Orders (Last 24 Hours)</h4>
                    </div>
                    <div class="card-body">
                        @if($recentDaisyOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Service Name</th>
                                            <th>Customer</th>
                                            <th>Phone Number</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Order Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentDaisyOrders as $order)
                                            <tr>
                                                <td>{{ $order->service->name ?? 'N/A' }}</td>
                                                <td>{{ $order->user->name ?? 'Guest' }}</td>
                                                <td>{{ $order->phone_number ?? 'N/A' }}</td>
                                                <td>₦{{ number_format($order->amount, 2) }}</td>
                                                <td>
                                                    @if($order->status == 'completed')
                                                        <span class="badge badge-success">Completed</span>
                                                    @elseif($order->status == 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @elseif($order->status == 'processing')
                                                        <span class="badge badge-info">Processing</span>
                                                    @elseif($order->status == 'cancelled')
                                                        <span class="badge badge-secondary">Cancelled</span>
                                                    @else
                                                        <span class="badge badge-danger">{{ ucfirst($order->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-phone fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No Daisy orders in the last 24 hours</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div> --}}


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
        
        <!-- Second Row for Daisy Revenue -->
         <div class="row mt-3">
             <!-- Daisy Orders Revenue -->
             <div class="col-lg-3 col-md-6">
                 <div class="card">
                     <div class="card-header">
                         <h4><i class="fas fa-flower text-info"></i> Daisy Orders Revenue</h4>
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <div class="col-6">
                                 <div class="text-muted">Today</div>
                                 <div class="font-weight-bold text-primary">₦{{ number_format($todaysDaisyRevenue) }}</div>
                             </div>
                             <div class="col-6">
                                 <div class="text-muted">This Month</div>
                                 <div class="font-weight-bold text-info">₦{{ number_format($monthDaisyRevenue) }}</div>
                             </div>
                         </div>
                         <div class="row mt-2">
                             <div class="col-6">
                                 <div class="text-muted">This Year</div>
                                 <div class="font-weight-bold text-success">₦{{ number_format($yearDaisyRevenue) }}</div>
                             </div>
                             <div class="col-6">
                                 <div class="text-muted">All Time</div>
                                 <div class="font-weight-bold text-dark">₦{{ number_format($stats['daisy_total_revenue']) }}</div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
             <!-- Reseller Revenue -->
             <div class="col-lg-3 col-md-6">
                 <div class="card">
                     <div class="card-header">
                         <h4><i class="fas fa-store text-dark"></i> Reseller Revenue</h4>
                     </div>
                     <div class="card-body">
                         <div class="row">
                             <div class="col-6">
                                 <div class="text-muted">Today</div>
                                 <div class="font-weight-bold text-primary">₦{{ number_format($todaysResellerRevenue) }}</div>
                             </div>
                             <div class="col-6">
                                 <div class="text-muted">This Month</div>
                                 <div class="font-weight-bold text-info">₦{{ number_format($monthResellerRevenue) }}</div>
                             </div>
                         </div>
                         <div class="row mt-2">
                             <div class="col-6">
                                 <div class="text-muted">This Year</div>
                                 <div class="font-weight-bold text-success">₦{{ number_format($yearResellerRevenue) }}</div>
                             </div>
                             <div class="col-6">
                                 <div class="text-muted">All Time</div>
                                 <div class="font-weight-bold text-dark">₦{{ number_format($stats['reseller_total_revenue']) }}</div>
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
