@extends('admin.layouts.master')

@section('content')
    <!-- Main Content -->
    <section class="section">
        <div class="section-header">
            <h1>SMS Orders Management</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">SMS Orders</div>
            </div>
        </div>

        <div class="section-body">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-sms"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Orders</h4>
                            </div>
                            <div class="card-body" id="total-orders">
                                {{ $orders->total() }}
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
                                <h4>Pending Orders</h4>
                            </div>
                            <div class="card-body" id="pending-orders">
                                {{ $orders->where('status', 'pending')->count() }}
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
                                <h4>Completed Orders</h4>
                            </div>
                            <div class="card-body" id="completed-orders">
                                {{ $orders->where('status', 'completed')->count() }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Needs Review</h4>
                            </div>
                            <div class="card-body" id="needs-review">
                                {{ $orders->where('needs_review', true)->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Statistics Cards -->
            <div class="row">
                @php
                    // Get exchange rate from general settings
                    $exchangeRate = $generalSettings->naira_to_dollar_rate ?? 1700.00;
                    
                    // Calculate financial statistics for completed orders only
                    // Final price is already in Naira, API price is in USD
                    $completedOrders = $orders->where('status', App\Models\Order::STATUS_COMPLETED);
                    $totalRevenueNaira = $completedOrders->sum('final_price');
                    $totalApiCostUsd = $completedOrders->sum('api_price');
                    $totalApiCostNaira = $totalApiCostUsd * $exchangeRate;
                    $totalProfitNaira = $totalRevenueNaira - $totalApiCostNaira;
                    $averageProfitMargin = $totalApiCostNaira > 0 ? ($totalProfitNaira / $totalApiCostNaira) * 100 : 0;
                @endphp
                
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Revenue</h4>
                            </div>
                            <div class="card-body" id="total-revenue">
                                ₦{{ number_format($totalRevenueNaira, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-secondary">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>API Costs</h4>
                            </div>
                            <div class="card-body" id="api-costs">
                                ${{ number_format($totalApiCostUsd, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-{{ $totalProfitNaira > 0 ? 'success' : 'danger' }}">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Profit</h4>
                            </div>
                            <div class="card-body" id="total-profit">
                                ₦{{ number_format($totalProfitNaira, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-{{ $averageProfitMargin > 0 ? 'success' : 'danger' }}">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Avg Profit Margin</h4>
                            </div>
                            <div class="card-body" id="avg-profit-margin">
                                {{ number_format($averageProfitMargin, 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>SMS Orders</h4>
                            <div class="card-header-action">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#filterModal">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <button class="btn btn-info" onclick="refreshStats()">
                                    <i class="fas fa-sync"></i> Refresh
                                </button>
                                <a href="{{ route('admin.sms-orders.export', request()->query()) }}" class="btn btn-success">
                                    <i class="fas fa-download"></i> Export CSV
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Search and Bulk Actions -->
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <form id="bulk-action-form">
                                        <div class="input-group">
                                            <select class="form-control" id="bulk-action" name="action">
                                                <option value="">Bulk Actions</option>
                                                <option value="cancel">Cancel Orders</option>
                                                <option value="mark_review">Mark for Review</option>
                                                <option value="remove_review">Remove from Review</option>
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button" onclick="performBulkAction()">
                                                    Apply
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <form method="GET" action="{{ route('admin.sms-orders.index') }}">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="search" 
                                                   placeholder="Search by order ID, phone, user, service..." 
                                                   value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="select-all">
                                            </th>
                                            <th>#Order ID</th>
                                            <th>User</th>
                                            <th>Service</th>
                                            <th>Country</th>
                                            <th>Phone Number</th>
                                            <th>SMS Code</th>
                                            <th>Status</th>
                                            <th>Price</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders as $order)
                                            <tr id="order-row-{{ $order->id }}">
                                                <td>
                                                    <input type="checkbox" class="order-checkbox" value="{{ $order->id }}">
                                                </td>
                                                <td>
                                                    <strong>{{ $order->id }}</strong>
                                                    @if($order->needs_review)
                                                        <span class="badge badge-warning ml-1">Review</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $order->user->name ?? 'N/A' }}</strong><br>
                                                        <small class="text-muted">{{ $order->user->email ?? 'N/A' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $order->service->name ?? 'N/A' }}</strong><br>
                                                        <small class="text-muted">{{ $order->service->code ?? 'N/A' }}</small><br>
                                                        @if($order->api_provider === 'smsbower')
                                                            <span class="badge badge-info">SmsBower</span>
                                                        @else
                                                            <span class="badge badge-secondary">SMSPool</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        {{-- @if($order->country && $order->country->flag)
                                                            <img src="{{ $order->country->flag }}" alt="{{ $order->country->name }}" 
                                                                 class="mr-2" style="width: 20px; height: 15px;">
                                                        @endif --}}
                                                        <span>{{ $order->country->name ?? 'N/A' }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($order->phone_number)
                                                        <div class="d-flex align-items-center">
                                                            <span class="font-weight-bold">{{ $order->phone_number }}</span>
                                                            <button class="btn btn-sm btn-link p-0 ml-2" 
                                                                    onclick="copyToClipboard('{{ $order->phone_number }}')"
                                                                    title="Copy phone number">
                                                                <i class="fas fa-copy"></i>
                                                            </button>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Not assigned</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->sms_code)
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge badge-success">{{ $order->sms_code }}</span>
                                                            <button class="btn btn-sm btn-link p-0 ml-2" 
                                                                    onclick="copyToClipboard('{{ $order->sms_code }}')"
                                                                    title="Copy SMS code">
                                                                <i class="fas fa-copy"></i>
                                                            </button>
                                                        </div>
                                                    @else
                                                        @if($order->status === 'pending')
                                                            <span class="text-muted">Waiting...</span>
                                                        @else
                                                            <span class="text-muted">No SMS code</span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $order->status_color }}">
                                                        {{ $order->status_text }}
                                                    </span>
                                                    @if($order->refunded)
                                                        <br><small class="text-success">Refunded</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>₦{{ number_format($order->final_price, 2) }}</strong><br>
                                                        <small class="text-muted">API: ${{ number_format($order->api_price, 2) }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        {{ $order->created_at->format('M d, Y') }}<br>
                                                        <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.sms-orders.show', $order->id) }}" 
                                                           class="btn btn-sm btn-primary" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        
                                                        @if(in_array($order->status, ['pending', 'active']))
                                                            <button class="btn btn-sm btn-info" 
                                                                    onclick="retrySms({{ $order->id }})"
                                                                    title="Retry SMS">
                                                                <i class="fas fa-redo"></i>
                                                            </button>
                                                        @endif
                                                        
                                                        @if($order->canBeCancelled() || $order->shouldBeAutoCancelled())
                                                            <button class="btn btn-sm btn-warning" 
                                                                    onclick="showCancelModal({{ $order->id }})"
                                                                    title="Cancel Order">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                        
                                                        @if(!$order->needs_review)
                                                            <button class="btn btn-sm btn-secondary" 
                                                                    onclick="showReviewModal({{ $order->id }})"
                                                                    title="Mark for Review">
                                                                <i class="fas fa-flag"></i>
                                                            </button>
                                                        @else
                                                            <button class="btn btn-sm btn-success" 
                                                                    onclick="removeFromReview({{ $order->id }})"
                                                                    title="Remove from Review">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        @endif
                                                        
                                                        @if(in_array($order->status, ['cancelled', 'expired', 'failed']))
                                                            <button class="btn btn-sm btn-danger" 
                                                                    onclick="deleteOrder({{ $order->id }})"
                                                                    title="Delete Order">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center py-4">
                                                    <div class="empty-state">
                                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                        <h5 class="text-muted">No SMS orders found</h5>
                                                        <p class="text-muted">There are no SMS orders to display.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($orders->hasPages())
                                <div class="d-flex justify-content-center">
                                    {{ $orders->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Orders</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="GET" action="{{ route('admin.sms-orders.index') }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        <option value="">All Statuses</option>
                                        @foreach($statuses as $key => $value)
                                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Country</label>
                                    <select class="form-control select2" name="country_id" data-placeholder="Search countries...">
                                        <option value="">All Countries</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Service</label>
                                    <select class="form-control select2" name="service_id" data-placeholder="Search services...">
                                        <option value="">All Services</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Needs Review</label>
                                    <select class="form-control" name="needs_review">
                                        <option value="">All Orders</option>
                                        <option value="1" {{ request('needs_review') == '1' ? 'selected' : '' }}>Needs Review</option>
                                        <option value="0" {{ request('needs_review') == '0' ? 'selected' : '' }}>No Review Needed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <a href="{{ route('admin.sms-orders.index') }}" class="btn btn-warning">Clear Filters</a>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Order</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="cancel-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Cancellation Reason</label>
                            <textarea class="form-control" name="reason" rows="3" required 
                                      placeholder="Enter reason for cancellation..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Cancel Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mark for Review</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="review-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Review Reason</label>
                            <textarea class="form-control" name="reason" rows="3" required 
                                      placeholder="Enter reason for review..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Mark for Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Action Modal -->
    <div class="modal fade" id="bulkActionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Action</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="bulk-action-modal-form">
                    <div class="modal-body">
                        <p>You have selected <span id="selected-count">0</span> orders.</p>
                        <div class="form-group" id="bulk-reason-group" style="display: none;">
                            <label>Reason</label>
                            <textarea class="form-control" name="reason" rows="3" 
                                      placeholder="Enter reason..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Execute Action</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let currentOrderId = null;

    // Select all checkbox functionality
    $('#select-all').change(function() {
        $('.order-checkbox').prop('checked', this.checked);
    });

    // Copy to clipboard function
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            toastr.success('Copied to clipboard!');
        });
    }

    // Show cancel modal
    function showCancelModal(orderId) {
        currentOrderId = orderId;
        $('#cancelModal').modal('show');
    }

    // Show review modal
    function showReviewModal(orderId) {
        currentOrderId = orderId;
        $('#reviewModal').modal('show');
    }

    // Cancel order
    $('#cancel-form').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: `/admin/sms-orders/${currentOrderId}/force-cancel`,
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#cancelModal').modal('hide');
                toastr.success(response.message);
                location.reload();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response.message || 'Failed to cancel order');
            }
        });
    });

    // Mark for review
    $('#review-form').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: `/admin/sms-orders/${currentOrderId}/mark-review`,
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#reviewModal').modal('hide');
                toastr.success(response.message);
                location.reload();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response.message || 'Failed to mark for review');
            }
        });
    });

    // Remove from review
    function removeFromReview(orderId) {
        if (confirm('Are you sure you want to remove this order from review queue?')) {
            $.ajax({
                url: `/admin/sms-orders/${orderId}/remove-review`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.message);
                    location.reload();
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response.message || 'Failed to remove from review');
                }
            });
        }
    }

    // Retry SMS
    function retrySms(orderId) {
        $.ajax({
            url: `/admin/sms-orders/${orderId}/retry-sms`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                toastr.success(response.message);
                if (response.sms_code) {
                    location.reload();
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response.message || 'Failed to retry SMS');
            }
        });
    }

    // Delete order
    function deleteOrder(orderId) {
        if (confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
            $.ajax({
                url: `/admin/sms-orders/${orderId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.message);
                    $(`#order-row-${orderId}`).remove();
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response.message || 'Failed to delete order');
                }
            });
        }
    }

    // Bulk actions
    function performBulkAction() {
        const action = $('#bulk-action').val();
        const selectedOrders = $('.order-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (!action) {
            toastr.warning('Please select an action');
            return;
        }

        if (selectedOrders.length === 0) {
            toastr.warning('Please select at least one order');
            return;
        }

        $('#selected-count').text(selectedOrders.length);
        
        // Show reason field for certain actions
        if (['cancel', 'mark_review'].includes(action)) {
            $('#bulk-reason-group').show();
            $('#bulk-reason-group textarea').prop('required', true);
        } else {
            $('#bulk-reason-group').hide();
            $('#bulk-reason-group textarea').prop('required', false);
        }

        $('#bulkActionModal').modal('show');
    }

    // Execute bulk action
    $('#bulk-action-modal-form').submit(function(e) {
        e.preventDefault();
        
        const action = $('#bulk-action').val();
        const selectedOrders = $('.order-checkbox:checked').map(function() {
            return this.value;
        }).get();
        const reason = $(this).find('textarea[name="reason"]').val();

        $.ajax({
            url: '/admin/sms-orders/bulk-action',
            method: 'POST',
            data: {
                action: action,
                order_ids: selectedOrders,
                reason: reason
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#bulkActionModal').modal('hide');
                toastr.success(response.message);
                location.reload();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response.message || 'Failed to perform bulk action');
            }
        });
    });

    // Refresh statistics
    function refreshStats() {
        $.ajax({
            url: '/admin/sms-orders/statistics',
            method: 'GET',
            success: function(data) {
                // Update order statistics
                $('#total-orders').text(data.total_orders);
                $('#pending-orders').text(data.pending_orders);
                $('#completed-orders').text(data.completed_orders);
                $('#needs-review').text(data.needs_review);
                
                // Update financial statistics
                $('#total-revenue').text('₦' + data.total_revenue_naira);
                $('#api-costs').text('$' + data.total_api_cost_usd);
                $('#total-profit').text('₦' + data.total_profit_naira);
                $('#avg-profit-margin').text(data.average_profit_margin + '%');
                
                toastr.success('Statistics refreshed');
            },
            error: function() {
                toastr.error('Failed to refresh statistics');
            }
        });
    }
    
    // Initialize Select2 specifically for this page
    $(document).ready(function() {
        // Safely destroy existing Select2 instances first
        $('.select2').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
        
        // Re-initialize with proper configuration
        $('select[name="country_id"]').select2({
            placeholder: 'Search countries...',
            allowClear: true,
            width: '100%',
            theme: 'bootstrap4'
        });
        
        $('select[name="service_id"]').select2({
            placeholder: 'Search services...',
            allowClear: true,
            width: '100%',
            theme: 'bootstrap4'
        });
    });
</script>
@endpush