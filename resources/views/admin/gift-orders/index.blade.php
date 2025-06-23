@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Gift Order Management</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Gift Orders</div>
        </div>
    </div>

    <div class="section-body">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Orders</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['total_orders'] }}
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
                        <div class="card-body">
                            {{ $stats['pending_orders'] }}
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
                            <h4>Confirmed Orders</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['confirmed_orders'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Cancelled Orders</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['cancelled_orders'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Filter Orders</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.gift-orders.export') }}" class="btn btn-success">
                                <i class="fas fa-download"></i> Export CSV
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.gift-orders.index') }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control select2">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Payment Status</label>
                                        <select name="payment_status" class="form-control">
                                                <option value="">All Payment Status</option>
                                                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                                <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                            </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Search</label>
                                        <input type="text" name="search" class="form-control" placeholder="Order number, recipient, sender..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                            <a href="{{ route('admin.gift-orders.index') }}" class="btn btn-secondary">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Gift Orders ({{ $orders->total() }} total)</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#GIFT-ID</th>
                                        <th>Customer</th>
                                        <th>Gift</th>
                                        <th>Recipient</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                    <tr>
                                        <td>
                                            <strong>{{ $order->id }}</strong>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $order->user->name }}</strong><br>
                                                <small class="text-muted">{{ $order->user->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($order->gift->main_image)
                                                    <img src="{{ asset($order->gift->main_image) }}" alt="{{ $order->gift->name }}" 
                                                         class="img-thumbnail me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                                <div class="ml-2">
                                                    <strong>{{ $order->gift->name }}</strong><br>
                                                    <small class="text-muted">Qty: {{ $order->quantity }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $order->recipient_name }}</strong><br>
                                                <small class="text-muted">{{ $order->recipient_phone }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>₦{{ number_format($order->total_amount, 2) }}</strong>
                                            @if($order->is_customized)
                                                <br><small class="text-info">Customized</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'confirmed' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                            @endphp
                                            <span class="badge badge-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $paymentColors = [
                                                    'pending' => 'warning',
                                                    'paid' => 'success',
                                                    'refunded' => 'info'
                                                ];
                                            @endphp
                                            <span class="badge badge-{{ $paymentColors[$order->payment_status] ?? 'secondary' }}">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                {{ $order->created_at->format('M d, Y') }}<br>
                                                <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown" style="min-width: 120px;">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                        style="white-space: nowrap;">
                                                    <i class="fas fa-cog"></i> Actions
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right" style="min-width: 200px;">
                                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.gift-orders.show', $order->id) }}">
                                                        <i class="fas fa-eye mr-1" style="width: 16px;"></i> View Details
                                                    </a>
                                                    
                                                    @if(in_array($order->status, ['pending']))
                                        <div class="dropdown-divider"></div>
                                        
                                        @if($order->status == 'pending')
                                            <a class="dropdown-item d-flex align-items-center" href="#" onclick="updateStatus({{ $order->id }}, 'confirmed')">
                                                <i class="fas fa-check mr-2 text-success" style="width: 16px;"></i> Confirm Order
                                            </a>
                                        @endif
                                        
                                        @if(!in_array($order->status, ['cancelled']))
                                            <a class="dropdown-item d-flex align-items-center" href="#" onclick="updateStatus({{ $order->id }}, 'cancelled')">
                                                <i class="fas fa-times mr-2 text-warning" style="width: 16px;"></i> Cancel Order
                                            </a>
                                        @endif
                                    @endif
                                                    
                                                    @if(($order->status == 'cancelled') || ($order->status == 'confirmed'))
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item d-flex align-items-center text-danger" href="#" onclick="deleteOrder({{ $order->id }})">
                                            <i class="fas fa-trash mr-2" style="width: 16px;"></i> Delete Order
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
                                                <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No gift orders found</h5>
                                                <p class="text-muted">Gift orders will appear here when customers place orders.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
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

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <input type="hidden" id="orderId">
                    <input type="hidden" id="newStatus">
                    
                    <div class="form-group">
                        <label>Tracking Number (Optional)</label>
                        <textarea id="trackingNumber" class="form-control summernote-simple" placeholder="Enter tracking number" style="height: 80px;"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Notes (Optional)</label>
                        <textarea id="statusNotes" class="form-control summernote-simple" placeholder="Add any notes about this status update" style="height: 100px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Initialize Summernote
$(document).ready(function() {
    $('.summernote-simple').summernote({
        height: 80,
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol']],
            ['insert', ['link']]
        ],
        placeholder: 'Enter content...',
        disableResizeEditor: true
    });
});

function updateStatus(orderId, status) {
    $('#orderId').val(orderId);
    $('#newStatus').val(status);
    $('#statusModal').modal('show');
}

function deleteOrder(orderId) {
    if (confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/gift-orders/${orderId}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '{{ csrf_token() }}';
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

$('#statusForm').on('submit', function(e) {
    e.preventDefault();
    
    const orderId = $('#orderId').val();
    const status = $('#newStatus').val();
    const trackingNumber = $('#trackingNumber').summernote('code');
    const notes = $('#statusNotes').summernote('code');
    
    $.ajax({
        url: `/admin/gift-orders/${orderId}/status`,
        method: 'PUT',
        data: {
            _token: '{{ csrf_token() }}',
            status: status,
            tracking_number: trackingNumber,
            notes: notes
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error updating status: ' + response.message);
            }
        },
        error: function() {
            alert('Error updating status. Please try again.');
        }
    });
});
</script>
@endpush