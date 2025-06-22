@extends('admin.layouts.master')

@section('content')
    <!-- Main Content -->
    <section class="section">
        <div class="section-header">
            <h1>Digital Product Orders</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Digital Product Orders</div>
            </div>
        </div>

        <div class="section-body">
           

            <!-- Orders Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Digital Product Orders</h4>
                            <div class="card-header-action">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#filterModal">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('admin.digital-product-orders.export', request()->query()) }}" class="btn btn-success">
                                    <i class="fas fa-download"></i> Export CSV
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Search -->
                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-6">
                                    <form method="GET" action="{{ route('admin.digital-product-orders.index') }}">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="search" 
                                                   placeholder="Search by order id, customer name or email..." 
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
                                            <th>#ORD-ID</th>
                                            <th>User</th>
                                            <th style="min-width:160px;">Product Name</th>
                                            <th>Quantity</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                            <th>Purchase Date</th>
                                            <th>Actions</th>
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
                                                        <strong>{{ $order->user->name ?? 'N/A' }}</strong><br>
                                                        <small class="text-muted">{{ $order->user->email ?? 'N/A' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $order->product->name ?? 'N/A' }}</strong><br>
                                                        <small class="text-muted">{{ $order->product->category->name ?? 'N/A' }}</small>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $order->quantity }}</td>
                                                <td>
                                                    <strong>{{ $order->formatted_total }}</strong><br>
                                                    <small class="text-muted">{{ $order->formatted_unit_price }} each</small>
                                                </td>
                                                <td>
                                                    @if($order->status == 'completed')
                                                        <span class="badge badge-success">Completed</span>
                                                    @elseif($order->status == 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @elseif($order->status == 'failed')
                                                        <span class="badge badge-danger">Failed</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>
                                                        {{ $order->purchased_at->format('M d, Y') }}<br>
                                                        <small class="text-muted">{{ $order->purchased_at->format('h:i A') }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.digital-product-orders.show', $order->id) }}" 
                                                           class="btn btn-sm btn-primary m-1" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($order->log)
                                                        <a href="{{ route('admin.digital-product-logs.edit', $order->log->id) }}" 
                                                           class="btn btn-sm btn-warning m-1" title="Update Log">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @endif
                                                        <button class="btn m-1 btn-sm btn-danger delete-btn" 
                                                                data-id="{{ $order->id }}" 
                                                                data-url="{{ route('admin.digital-product-orders.destroy', $order->id) }}"
                                                                title="Delete Order">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4">
                                                    <div class="empty-state">
                                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                        <h5 class="text-muted">No orders found</h5>
                                                        <p class="text-muted">There are no digital product orders to display.</p>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Orders</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="GET" action="{{ route('admin.digital-product-orders.index') }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment Status</label>
                                    <select name="payment_status" class="form-control">
                                        <option value="">All Payment Statuses</option>
                                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <a href="{{ route('admin.digital-product-orders.index') }}" class="btn btn-warning">Clear Filters</a>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="updateStatusForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Notes (Optional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" 
                                      placeholder="Add any notes about this status change..."></textarea>
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
$(document).ready(function() {
    let currentOrderId = null;



    // Update status modal
    $('.update-status-btn').click(function() {
        currentOrderId = $(this).data('id');
        const currentStatus = $(this).data('status');
        const currentNotes = $(this).data('notes');
        
        $('#status').val(currentStatus);
        $('#notes').val(currentNotes);
        $('#updateStatusModal').modal('show');
    });

    // Handle status update form submission
    $('#updateStatusForm').submit(function(e) {
        e.preventDefault();
        
        if (!currentOrderId) return;
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: `/admin/digital-product-orders/${currentOrderId}/update-status`,
            method: 'POST',
            data: formData,
            success: function(response) {
                toastr.success(response.message);
                $('#updateStatusModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                toastr.error('An error occurred while updating the order status');
            }
        });
    });

    // SweetAlert delete logic
    $('.delete-btn').click(function(e) {
        e.preventDefault();
        const deleteUrl = $(this).data('url');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'DELETE',
                    url: deleteUrl,
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(data){
                        if(data.status == 'success'){
                            Swal.fire('Deleted!', data.message, 'success');
                            location.reload();
                        }else if (data.status == 'error'){
                            Swal.fire('Cant Delete', data.message, 'error');
                        }
                    },
                    error: function(xhr, status, error){
                        Swal.fire('Error', 'An error occurred while deleting the order', 'error');
                    }
                })
            }
        })
    });
});
</script>
@endpush