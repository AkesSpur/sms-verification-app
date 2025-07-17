@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Order Details</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.social-media-orders.index') }}">Social Media Orders</a></div>
            <div class="breadcrumb-item">Order #{{ $order->order_number }}</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Order #{{ $order->order_number }}</h4>
                        <div class="card-header-action">
                            <button type="button" class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                <i class="fas fa-edit"></i> Update Status
                            </button>
                            <a href="{{ route('admin.social-media-products.show', $order->product) }}" class="btn btn-info btn-sm me-2">
                                <i class="fas fa-eye"></i> View Product
                            </a>
                            <a href="{{ route('admin.social-media-orders.index', ['user' => $order->user_id]) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-list"></i> User Orders
                            </a>
                        </div>
                    </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Order Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Order Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Order Number:</label>
                                                <p class="mb-2"><code>{{ $order->order_number }}</code></p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">Status:</label>
                                                <p class="mb-2">
                                                    @switch($order->status)
                                                        @case('pending')
                                                            <span class="badge badge-warning badge-lg">Pending</span>
                                                            @break
                                                        @case('processing')
                                                            <span class="badge badge-info badge-lg">Processing</span>
                                                            @break
                                                        @case('completed')
                                                            <span class="badge badge-success badge-lg">Completed</span>
                                                            @break
                                                        @case('cancelled')
                                                            <span class="badge badge-danger badge-lg">Cancelled</span>
                                                            @break
                                                    @endswitch
                                                </p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">Order Date:</label>
                                                <p class="mb-2">{{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">Last Updated:</label>
                                                <p class="mb-2">{{ $order->updated_at->format('M d, Y \a\t h:i A') }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Social Media Link:</label>
                                                <p class="mb-2">
                                                    <a href="{{ $order->social_media_link }}" target="_blank" class="text-primary">
                                                        {{ $order->social_media_link }}
                                                        <i class="fas fa-external-link-alt ml-1"></i>
                                                    </a>
                                                </p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">Quantity:</label>
                                                <p class="mb-2"><strong>{{ number_format($order->quantity) }}</strong></p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">Unit Price:</label>
                                                <p class="mb-2">₦{{ number_format($order->unit_price, 2) }} per 1,000</p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">Total Amount:</label>
                                                <p class="mb-2"><strong class="text-success h5">₦{{ number_format($order->total_amount, 2) }}</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($order->admin_notes)
                                        <div class="form-group">
                                            <label class="font-weight-bold">Admin Notes:</label>
                                            <div class="alert alert-info">
                                                {{ $order->admin_notes }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- External Order Information -->
                            @if($order->external_order_id)
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-external-link-alt"></i> External Order Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">External Order ID:</label>
                                                <p class="mb-2"><code>{{ $order->external_order_id }}</code></p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">External Status:</label>
                                                <p class="mb-2">
                                                    @if($order->external_status)
                                                        <span class="badge badge-info">{{ ucfirst($order->external_status) }}</span>
                                                    @else
                                                        <span class="text-muted">Not available</span>
                                                    @endif
                                                </p>
                                            </div>
                                            
                                            @if($order->external_start_count)
                                            <div class="form-group">
                                                <label class="font-weight-bold">Start Count:</label>
                                                <p class="mb-2">{{ number_format($order->external_start_count) }}</p>
                                            </div>
                                            @endif
                                        </div>
                                        
                                        <div class="col-md-6">
                                            @if($order->external_remains !== null)
                                            <div class="form-group">
                                                <label class="font-weight-bold">Remaining:</label>
                                                <p class="mb-2">{{ number_format($order->external_remains) }}</p>
                                            </div>
                                            @endif
                                            
                                            @if($order->external_charge)
                                            <div class="form-group">
                                                <label class="font-weight-bold">External Charge:</label>
                                                <p class="mb-2">₦{{ number_format($order->external_charge, 2) }}</p>
                                            </div>
                                            @endif
                                            
                                            @if($order->external_start_count && $order->external_remains !== null)
                                            <div class="form-group">
                                                <label class="font-weight-bold">Progress:</label>
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
                                                <div class="progress mb-2" style="height: 25px;">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                                        {{ number_format($progress, 1) }}%
                                                    </div>
                                                </div>
                                                <p class="mb-2 small text-muted">
                                                    {{ number_format($delivered) }} delivered of {{ number_format($order->quantity) }} ordered
                                                </p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Product Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-box"></i> Product Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Product Name:</label>
                                                <p class="mb-2">
                                                    <a href="{{ route('admin.social-media-products.show', $order->product) }}" class="text-primary">
                                                        {{ $order->product->name }}
                                                    </a>
                                                </p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">Category:</label>
                                                <p class="mb-2">
                                                    <span class="badge badge-secondary">{{ $order->product->category->name }}</span>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Current Price:</label>
                                                <p class="mb-2">₦{{ number_format($order->product->price_per_1000, 2) }} per 1,000</p>
                                                @if($order->unit_price != $order->product->price_per_1000)
                                                    <small class="text-warning">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        Price has changed since order was placed
                                                    </small>
                                                @endif
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">Product Status:</label>
                                                <p class="mb-2">
                                                    @if($order->product->status)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($order->product->description)
                                        <div class="form-group">
                                            <label class="font-weight-bold">Product Description:</label>
                                            <div class="border p-3 bg-light rounded">
                                                {!! $order->product->description !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Customer Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-user"></i> Customer Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Customer Name:</label>
                                                <p class="mb-2">{{ $order->user->name }}</p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">Email:</label>
                                                <p class="mb-2">
                                                    <a href="mailto:{{ $order->user->email }}">{{ $order->user->email }}</a>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Customer Since:</label>
                                                <p class="mb-2">{{ $order->user->created_at->format('M d, Y') }}</p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">Total Orders:</label>
                                                <p class="mb-2">{{ $order->user->socialMediaOrders()->count() }} social media orders</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Information -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-credit-card"></i> Payment Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Payment Method:</label>
                                                <p class="mb-2">
                                                    <span class="badge badge-info">{{ ucfirst($order->payment_method) }}</span>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Payment Status:</label>
                                                <p class="mb-2">
                                                    @if($order->payment_status === 'paid')
                                                        <span class="badge badge-success">Paid</span>
                                                    @else
                                                        <span class="badge badge-warning">{{ ucfirst($order->payment_status) }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Quick Actions -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        @if($order->status !== 'pending')
                                            <button type="button" class="btn btn-warning btn-sm" onclick="updateStatus('pending')">
                                                <i class="fas fa-clock"></i> Mark as Pending
                                            </button>
                                        @endif
                                        
                                        @if($order->status !== 'processing')
                                            <button type="button" class="btn btn-info btn-sm" onclick="updateStatus('processing')">
                                                <i class="fas fa-cog"></i> Mark as Processing
                                            </button>
                                        @endif
                                        
                                        @if($order->status !== 'completed')
                                            <button type="button" class="btn btn-success btn-sm" onclick="updateStatus('completed')">
                                                <i class="fas fa-check-circle"></i> Mark as Completed
                                            </button>
                                        @endif
                                        
                                        @if($order->status !== 'cancelled')
                                            <button type="button" class="btn btn-danger btn-sm" onclick="updateStatus('cancelled')">
                                                <i class="fas fa-times-circle"></i> Mark as Cancelled
                                            </button>
                                        @endif
                                        
                                        <hr>
                                        
                                        <a href="{{ route('admin.social-media-products.show', $order->product) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-box"></i> View Product
                                        </a>
                                        
                                        <a href="{{ route('admin.social-media-orders.index', ['search' => $order->user->email]) }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-user"></i> View Customer Orders
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Order Timeline -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-history"></i> Order Timeline</h5>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary"></div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1">Order Created</h6>
                                                <p class="mb-0 small text-muted">{{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
                                            </div>
                                        </div>
                                        
                                        @if($order->status === 'processing' || $order->status === 'completed' || $order->status === 'cancelled')
                                            <div class="timeline-item">
                                                <div class="timeline-marker bg-info"></div>
                                                <div class="timeline-content">
                                                    <h6 class="mb-1">Status Updated</h6>
                                                    <p class="mb-0 small text-muted">{{ $order->updated_at->format('M d, Y \a\t h:i A') }}</p>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($order->status === 'completed')
                                            <div class="timeline-item">
                                                <div class="timeline-marker bg-success"></div>
                                                <div class="timeline-content">
                                                    <h6 class="mb-1">Order Completed</h6>
                                                    <p class="mb-0 small text-muted">Service delivered successfully</p>
                                                </div>
                                            </div>
                                        @elseif($order->status === 'cancelled')
                                            <div class="timeline-item">
                                                <div class="timeline-marker bg-danger"></div>
                                                <div class="timeline-content">
                                                    <h6 class="mb-1">Order Cancelled</h6>
                                                    <p class="mb-0 small text-muted">Order was cancelled</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Order Summary -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-calculator"></i> Order Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Quantity:</span>
                                        <strong>{{ number_format($order->quantity) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Unit Price:</span>
                                        <strong>₦{{ number_format($order->unit_price, 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Rate per 1,000:</span>
                                        <strong>₦{{ number_format($order->unit_price, 2) }}</strong>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <span class="h6">Total Amount:</span>
                                        <strong class="h5 text-success">₦{{ number_format($order->total_amount, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Status Update Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="statusUpdateForm" method="POST" action="{{ route('admin.social-media-orders.update-status', $order) }}">
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
                                  placeholder="Add notes about this status change...">{{ $order->admin_notes }}</textarea>
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

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -22px;
    top: 20px;
    height: calc(100% + 10px);
    width: 2px;
    background-color: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -26px;
    top: 4px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}

.badge-lg {
    font-size: 0.9em;
    padding: 0.5em 0.75em;
}
</style>
@endpush

@push('scripts')
<script>
function updateStatus(status) {
    const modal = document.getElementById('statusUpdateModal');
    const statusSelect = document.getElementById('modalStatus');
    
    statusSelect.value = status;
    $('#updateStatusModal').modal('show');
}
</script>
@endpush