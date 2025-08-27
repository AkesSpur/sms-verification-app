@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Daisy SMS Orders</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Daisy SMS Orders</div>
            </div>
        </div>

        <div class="section-body">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="far fa-clock"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pending Orders</h4>
                            </div>
                            <div class="card-body">
                                {{ $stats['pending_orders'] ?? 0 }}
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
                                <h4>Active Orders</h4>
                            </div>
                            <div class="card-body">
                                {{ $stats['active_orders'] ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info">
                            <i class="fas fa-sms"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Completed Orders</h4>
                            </div>
                            <div class="card-body">
                                {{ $stats['completed_orders'] ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Revenue (NGN)</h4>
                            </div>
                            <div class="card-body">
                                ₦{{ number_format($stats['total_spent'] ?? 0, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card">
                <div class="card-header">
                    <h4>Filter Orders</h4>
                    <div class="card-header-action">
                        <a href="{{ route('admin.daisy-orders.export', request()->query()) }}" class="btn btn-success">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.daisy-orders.index') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Service</label>
                                    <select name="service_code" class="form-control">
                                        <option value="">All Services</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->service_code }}" {{ request('service_code') == $service->service_code ? 'selected' : '' }}>
                                                {{ $service->service_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Country</label>
                                    <select name="country_code" class="form-control">
                                        <option value="">All Countries</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->country_code }}" {{ request('country_code') == $country->country_code ? 'selected' : '' }}>
                                                {{ $country->country_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>User</label>
                                    <select name="user_id" class="form-control">
                                        <option value="">All Users</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Search</label>
                                    <input type="text" name="search" class="form-control" placeholder="Search by rental ID, phone, transaction, SMS code, user name/email..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('admin.daisy-orders.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h4>Orders ({{ $orders->total() }} total)</h4>
                    <div class="card-header-action">
                        <button type="button" class="btn btn-warning" id="bulkActionBtn" style="display: none;">
                            <i class="fas fa-cogs"></i> Bulk Actions
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-md">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="custom-checkbox custom-control">
                                            <input type="checkbox" class="custom-control-input" id="selectAll">
                                            <label for="selectAll" class="custom-control-label">&nbsp;</label>
                                        </div>
                                    </th>
                                    <th>Order ID</th>
                                    <th>User</th>
                                    <th>Service</th>
                                    <th>Phone Number</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>SMS Code</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>
                                            <div class="custom-checkbox custom-control">
                                                <input type="checkbox" class="custom-control-input order-checkbox" id="order-{{ $order->id }}" value="{{ $order->id }}">
                                                <label for="order-{{ $order->id }}" class="custom-control-label">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>#{{ $order->id }}</strong><br>
                                            <small class="text-muted">{{ $order->trx }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <strong>{{ $order->user->name ?? 'N/A' }}</strong><br>
                                                    <small class="text-muted">{{ $order->user->email ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $order->service_name }}</strong><br>
                                            <small class="text-muted">{{ $order->country_name }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $order->phone_number }}</strong><br>
                                            <small class="text-muted">Rental: {{ $order->rental_id }}</small>
                                        </td>
                                        <td>
                                            <strong>₦{{ number_format($order->price, 2) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $order->status_badge }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($order->sms_code)
                                                <span class="badge badge-success">{{ $order->sms_code }}</span>
                                            @elseif(!$order->sms_code && !in_array($order->status, ['expired', 'cancelled']))
                                                <span class="text-muted">Waiting...</span>
                                            @else
                                                <span class="text-danger">No Code</span>

                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $order->created_at->format('M d, Y') }}</small><br>
                                            <small class="text-muted">{{ $order->created_at->format('H:i A') }}</small>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <a href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle">Actions</a>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('admin.daisy-orders.show', $order) }}">
                                                        <i class="fas fa-eye"></i> View Details
                                                    </a>
                                                    @if($order->isActive())
                                                        <a class="dropdown-item refresh-sms" href="#" data-id="{{ $order->id }}">
                                                            <i class="fas fa-sync"></i> Refresh SMS
                                                        </a>
                                                    @endif
                                                    @if(!$order->isCompleted() && !$order->isCancelled())
                                                        <a class="dropdown-item cancel-order" href="#" data-id="{{ $order->id }}">
                                                            <i class="fas fa-times"></i> Cancel Order
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <h5>No orders found</h5>
                                                <p class="text-muted">No Daisy SMS orders match your current filters.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($orders->hasPages())
                    <div class="card-footer">
                        {{ $orders->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>

<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Actions</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="bulkActionForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Action</label>
                        <select name="action" class="form-control" required>
                            <option value="">Select Action</option>
                            <option value="cancel">Cancel Orders</option>
                            <option value="mark_completed">Mark as Completed</option>
                            <option value="mark_expired">Mark as Expired</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Reason (Optional)</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Enter reason for this action..."></textarea>
                    </div>
                    <div class="alert alert-info">
                        <strong>Selected Orders:</strong> <span id="selectedCount">0</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Action</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Order</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="cancelOrderForm">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> This action will cancel the order and process a refund to the user's account.
                    </div>
                    <p>Are you sure you want to cancel this order?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Select all checkbox functionality
    $('#selectAll').change(function() {
        $('.order-checkbox').prop('checked', $(this).is(':checked'));
        toggleBulkActionButton();
    });

    // Individual checkbox change
    $('.order-checkbox').change(function() {
        toggleBulkActionButton();
        
        // Update select all checkbox
        const totalCheckboxes = $('.order-checkbox').length;
        const checkedCheckboxes = $('.order-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    // Toggle bulk action button
    function toggleBulkActionButton() {
        const checkedCount = $('.order-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#bulkActionBtn').show();
            $('#selectedCount').text(checkedCount);
        } else {
            $('#bulkActionBtn').hide();
        }
    }

    // Bulk action button click
    $('#bulkActionBtn').click(function() {
        $('#bulkActionModal').modal('show');
    });

    // Bulk action form submit
    $('#bulkActionForm').submit(function(e) {
        e.preventDefault();
        
        const selectedIds = $('.order-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        if (selectedIds.length === 0) {
            toastr.error('Please select at least one order');
            return;
        }
        
        const formData = {
            action: $('select[name="action"]').val(),
            reason: $('textarea[name="reason"]').val(),
            order_ids: selectedIds
        };
        
        formData._token = '{{ csrf_token() }}';
        
        $.ajax({
            url: '{{ route("admin.daisy-orders.bulk-update") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('An error occurred while processing the request');
            }
        });
        
        $('#bulkActionModal').modal('hide');
    });

    // Refresh SMS status
    $('.refresh-sms').click(function(e) {
        e.preventDefault();
        const orderId = $(this).data('id');
        const button = $(this);
        
        button.html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');
        
        $.ajax({
            url: '{{ route("admin.daisy-orders.refresh-sms", ":id") }}'.replace(':id', orderId),
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Failed to refresh SMS status');
            },
            complete: function() {
                button.html('<i class="fas fa-sync"></i> Refresh SMS');
            }
        });
    });

    // Cancel order
    let cancelOrderId = null;
    $('.cancel-order').click(function(e) {
        e.preventDefault();
        cancelOrderId = $(this).data('id');
        $('#cancelOrderModal').modal('show');
    });

    // Cancel order form submit
    $('#cancelOrderForm').submit(function(e) {
        e.preventDefault();
        
        if (!cancelOrderId) return;
        
        $.ajax({
            url: '{{ route("admin.daisy-orders.cancel", ":id") }}'.replace(':id', cancelOrderId),
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Failed to cancel order');
            }
        });
        
        $('#cancelOrderModal').modal('hide');
    });

    // Auto-submit filter form on select change
    $('select[name="status"], select[name="service_code"], select[name="country_code"], select[name="user_id"]').change(function() {
        $('#filterForm').submit();
    });
});
</script>
@endpush