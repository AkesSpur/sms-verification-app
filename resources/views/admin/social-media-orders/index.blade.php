@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Social Media Orders</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Social Media Orders</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>All Social Media Orders</h4>
                    </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                            <small>Total Orders</small>
                                        </div>
                                        <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                                            <small>Pending Orders</small>
                                        </div>
                                        <i class="fas fa-clock fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['processing'] }}</h4>
                                            <small>Processing</small>
                                        </div>
                                        <i class="fas fa-cog fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['completed'] }}</h4>
                                            <small>Completed</small>
                                        </div>
                                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.social-media-orders.index') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">All Statuses</option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-control" id="category_id" name="category_id">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="search" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="Order number, user name...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('admin.social-media-orders.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    @if($orders->count() > 0)
                        <div class="card mb-4">
                            <div class="card-body">
                                <form id="bulkActionForm" method="POST" action="{{ route('admin.social-media-orders.bulk-update-status') }}">
                                    @csrf
                                    <div class="row align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label">Bulk Actions</label>
                                            <select class="form-control" name="status" required>
                                                <option value="">Select Status</option>
                                                <option value="pending">Mark as Pending</option>
                                                <option value="processing">Mark as Processing</option>
                                                <option value="completed">Mark as Completed</option>
                                                <option value="cancelled">Mark as Cancelled</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Admin Notes (Optional)</label>
                                            <input type="text" class="form-control" name="admin_notes" placeholder="Add notes for status change...">
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-warning" onclick="return confirmBulkAction()">
                                                <i class="fas fa-edit"></i> Update Selected
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Orders Table -->
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                        </th>
                                        <th>Order #</th>
                                        <th>User</th>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Quantity</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-checkbox" form="bulkActionForm">
                                            </td>
                                            <td>
                                                <code>{{ $order->order_number }}</code>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $order->user->name }}</strong><br>
                                                    <small class="text-muted">{{ $order->user->email }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $order->product->name }}</strong><br>
                                                    <small class="text-muted">₦{{ number_format($order->unit_price, 2) }} per 1k</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $order->product->category->name }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ number_format($order->quantity) }}</strong>
                                            </td>
                                            <td>
                                                <strong class="text-success">₦{{ number_format($order->total_amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                @switch($order->status)
                                                    @case('pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                        @break
                                                    @case('processing')
                                                        <span class="badge badge-info">Processing</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge badge-success">Completed</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge badge-danger">Cancelled</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $order->created_at->format('M d, Y') }}<br>
                                                    <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.social-media-orders.show', $order) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-warning dropdown-toggle" 
                                                                data-toggle="dropdown" title="Update Status">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            @if($order->status !== 'pending')
                                                                <a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 'pending')">
                                                                    <i class="fas fa-clock text-warning"></i> Mark as Pending
                                                                </a>
                                                            @endif
                                                            @if($order->status !== 'processing')
                                                                <a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 'processing')">
                                                                    <i class="fas fa-cog text-info"></i> Mark as Processing
                                                                </a>
                                                            @endif
                                                            @if($order->status !== 'completed')
                                                                <a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 'completed')">
                                                                    <i class="fas fa-check-circle text-success"></i> Mark as Completed
                                                                </a>
                                                            @endif
                                                            @if($order->status !== 'cancelled')
                                                                <a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 'cancelled')">
                                                                    <i class="fas fa-times-circle text-danger"></i> Mark as Cancelled
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <p class="text-muted mb-0">
                                    Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} results
                                </p>
                            </div>
                            <div>
                                {{ $orders->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No orders found</h5>
                            <p class="text-muted">No social media orders match your current filters.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="statusUpdateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modalStatus">New Status</label>
                        <select class="form-control" id="modalStatus" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="modalNotes">Admin Notes (Optional)</label>
                        <textarea class="form-control" id="modalNotes" name="admin_notes" rows="3" 
                                  placeholder="Add notes about this status change..."></textarea>
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
// Toggle select all checkboxes
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.order-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// Update individual order status
function updateOrderStatus(orderId, status) {
    const form = document.getElementById('statusUpdateForm');
    const statusSelect = document.getElementById('modalStatus');
    
    form.action = `/admin/social-media-orders/${orderId}/update-status`;
    statusSelect.value = status;
    
    $('#statusUpdateModal').modal('show');
}

// Confirm bulk action
function confirmBulkAction() {
    const selectedOrders = document.querySelectorAll('.order-checkbox:checked');
    
    if (selectedOrders.length === 0) {
        alert('Please select at least one order.');
        return false;
    }
    
    return confirm(`Are you sure you want to update the status of ${selectedOrders.length} selected order(s)?`);
}

// Auto-submit filters on change
document.getElementById('status').addEventListener('change', function() {
    this.form.submit();
});

document.getElementById('category_id').addEventListener('change', function() {
    this.form.submit();
});
</script>
@endpush