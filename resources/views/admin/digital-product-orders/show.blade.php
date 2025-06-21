@extends('admin.layouts.master')

@section('content')
    <!-- Main Content -->
    <section class="section">
        <div class="section-header">
            <h1>Digital Product Order Details</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.digital-product-orders.index') }}">Digital Product Orders</a></div>
                <div class="breadcrumb-item">#ORD-{{ $order->id }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <!-- Order Information -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Order Information</h4>
                            <div class="card-header-action">
                                @if($order->log)
    <a href="{{ route('admin.digital-product-logs.show', $order->log->id) }}" class="btn btn-info ">
        <i class="fas fa-eye"></i> View Log
    </a>
@endif
                                <a href="{{ route('admin.digital-product-orders.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Orders
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Order Number:</strong></td>
                                            <td>#ORD-{{ $order->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($order->status == 'completed')
                                                    <span class="badge badge-success badge-lg">Completed</span>
                                                @elseif($order->status == 'pending')
                                                    <span class="badge badge-warning badge-lg">Pending</span>
                                                @elseif($order->status == 'failed')
                                                    <span class="badge badge-danger badge-lg">Failed</span>
                                                @endif
                                            </td>
                                        </tr>
                                       
                                        <tr>
                                            <td><strong>Payment Method:</strong></td>
                                            <td>{{ ucfirst($order->payment_method) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Purchase Date:</strong></td>
                                            <td>{{ $order->purchased_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Quantity:</strong></td>
                                            <td>{{ $order->quantity }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Unit Price:</strong></td>
                                            <td>{{ $order->formatted_unit_price }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Amount:</strong></td>
                                            <td><strong class="text-primary">{{ $order->formatted_total }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Updated:</strong></td>
                                            <td>{{ $order->updated_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($order->notes)
                                <div class="mt-4">
                                    <h6><strong>Notes:</strong></h6>
                                    <div class="alert alert-info">
                                        {{ $order->notes }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Product Information -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Product Information</h4>
                        </div>
                        <div class="card-body">
                            @if($order->product)
                                <div class="row">
                                    <div class="col-md-3">
                                        @if($order->product->image)
                                            <img src="{{ asset($order->product->image) }}" 
                                                 alt="{{ $order->product->name }}" 
                                                 class="img-fluid rounded">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="height: 120px;">
                                                <i class="fas fa-image fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-9">
                                        <h5>{{ $order->product->name }}</h5>
                                        <p class="text-muted mb-2">
                                            <strong>Category:</strong> {{ $order->product->category->name ?? 'N/A' }}
                                        </p>
                                        @if($order->product->subcategory)
                                            <p class="text-muted mb-2">
                                                <strong>Subcategory:</strong> {{ $order->product->subcategory->name }}
                                            </p>
                                        @endif                                       
                                        @if($order->product->description)
                                            <p class="mb-0">
                                                <strong>Description:</strong><br>
                                                {!! Str::limit($order->product->description, 200) !!}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Product information is not available (product may have been deleted).
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Purchased Content -->
                    @if($order->log)
                        <div class="card">
                            <div class="card-header">
                                <h4>Purchased Content</h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    This is the actual content that was purchased by the customer.
                                </div>
                                
                                <div class="bg-light p-3 rounded">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Content Details</h6>
                                        <button class="btn btn-sm btn-outline-primary" onclick="copyContent()">
                                            <i class="fas fa-copy"></i> Copy Content
                                        </button>
                                    </div>
                                    <hr>
                                    <div id="purchased-content">
                                        {!! $order->log->log_item !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Customer Information -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Customer Information</h4>
                        </div>
                        <div class="card-body">
                            @if($order->user)
                                <div class="text-center mb-3">                                    
                                    <h5 class="mb-1">{{ $order->user->name }}</h5>
                                    <p class="text-muted">{{ $order->user->email }}</p>
                                </div>
                                
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td><strong>User ID:</strong></td>
                                        <td>{{ $order->user->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $order->user->phone ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            @if($order->user->status == 'active')
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email Verified:</strong></td>
                                        <td>
                                            @if($order->user->email_verified_at)
                                                <span class="badge badge-success">Verified</span>
                                            @else
                                                <span class="badge badge-warning">Unverified</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Joined:</strong></td>
                                        <td>{{ $order->user->created_at->format('M d, Y') }}</td>
                                    </tr>
                                </table>                              
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Customer information is not available.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Quick Actions</h4>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if($order->status == 'pending')
                                    <button class="btn btn-success btn-block quick-status-btn" 
                                            data-id="{{ $order->id }}" 
                                            data-status="completed">
                                        <i class="fas fa-check"></i> Mark as Completed
                                    </button>
                                    <button class="btn btn-danger btn-block quick-status-btn" 
                                            data-id="{{ $order->id }}" 
                                            data-status="failed">
                                        <i class="fas fa-times"></i> Mark as Failed
                                    </button>
                                @elseif($order->status == 'failed')
                                    <button class="btn btn-success btn-block quick-status-btn" 
                                            data-id="{{ $order->id }}" 
                                            data-status="completed">
                                        <i class="fas fa-check"></i> Mark as Completed
                                    </button>
                                @endif
                                <hr>                                
                                <button class="btn btn-danger btn-block delete-btn" 
                                        data-id="{{ $order->id }}">
                                    <i class="fas fa-trash"></i> Delete Order
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

  
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentOrderId = {{ $order->id }};

    
   

   
 
});

// Function to copy content to clipboard
function copyContent() {
    const content = document.getElementById('purchased-content');
    const textContent = content.innerText || content.textContent;
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(textContent).then(function() {
            toastr.success('Content copied to clipboard!');
        }).catch(function() {
            fallbackCopyTextToClipboard(textContent);
        });
    } else {
        fallbackCopyTextToClipboard(textContent);
    }
}

// Fallback copy function for older browsers
function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        toastr.success('Content copied to clipboard!');
    } catch (err) {
        toastr.error('Failed to copy content');
    }
    
    document.body.removeChild(textArea);
}
</script>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
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
                            window.location.href = '{{ route("admin.digital-product-orders.index") }}';
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
