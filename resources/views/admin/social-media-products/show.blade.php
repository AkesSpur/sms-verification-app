@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Product Details: {{ $socialMediaProduct->name }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.social-media-products.index') }}">Social Media Products</a></div>
            <div class="breadcrumb-item active">{{ $socialMediaProduct->name }}</div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Product Details</h4>
                    <div class="card-header-action">
                        <div class="btn-group">
                            <a href="{{ route('admin.social-media-products.edit', $socialMediaProduct) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.social-media-products.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Products
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Product Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Product Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Product Name:</label>
                                                <p class="mb-2">{{ $socialMediaProduct->name }}</p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">Category:</label>
                                                <p class="mb-2">
                                                    <span class="badge badge-primary">{{ $socialMediaProduct->category->name }}</span>
                                                </p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">Slug:</label>
                                                <p class="mb-2"><code>{{ $socialMediaProduct->slug }}</code></p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">Status:</label>
                                                <p class="mb-2">
                                                    @if($socialMediaProduct->status == 1)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Price per 1,000:</label>
                                                <p class="mb-2 text-success font-weight-bold">₦{{ number_format($socialMediaProduct->price_per_1000, 2) }}</p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">Quantity Range:</label>
                                                <p class="mb-2">{{ number_format($socialMediaProduct->min_quantity) }} - {{ number_format($socialMediaProduct->max_quantity) }}</p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">Sort Order:</label>
                                                <p class="mb-2">{{ $socialMediaProduct->sort_order }}</p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="font-weight-bold">Created:</label>
                                                <p class="mb-2">{{ $socialMediaProduct->created_at->format('M d, Y \a\t h:i A') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($socialMediaProduct->description)
                                        <div class="form-group">
                                            <label class="font-weight-bold">Description:</label>
                                            <div class="border p-3 bg-light rounded">
                                                {!! $socialMediaProduct->description !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Recent Orders -->
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Recent Orders</h5>
                                        <a href="{{ route('admin.social-media-orders.index', ['product_id' => $socialMediaProduct->id]) }}" class="btn btn-sm btn-outline-primary">
                                            View All Orders
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($socialMediaProduct->orders()->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Order #</th>
                                                        <th>User</th>
                                                        <th>Quantity</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($socialMediaProduct->orders()->latest()->limit(5)->get() as $order)
                                                        <tr>
                                                            <td><code>{{ $order->order_number }}</code></td>
                                                            <td>{{ $order->user->name }}</td>
                                                            <td>{{ number_format($order->quantity) }}</td>
                                                            <td>₦{{ number_format($order->total_amount, 2) }}</td>
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
                                                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                            <td>
                                                                <a href="{{ route('admin.social-media-orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No orders found for this product yet.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Statistics -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-right">
                                                <h4 class="text-primary mb-1">{{ $socialMediaProduct->orders()->count() }}</h4>
                                                <small class="text-muted">Total Orders</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success mb-1">₦{{ number_format($socialMediaProduct->orders()->sum('total_amount'), 2) }}</h4>
                                            <small class="text-muted">Total Revenue</small>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small">Pending Orders</span>
                                            <span class="badge badge-warning">{{ $socialMediaProduct->orders()->where('status', 'pending')->count() }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small">Processing Orders</span>
                                            <span class="badge badge-info">{{ $socialMediaProduct->orders()->where('status', 'processing')->count() }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small">Completed Orders</span>
                                            <span class="badge badge-success">{{ $socialMediaProduct->orders()->where('status', 'completed')->count() }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="small">Cancelled Orders</span>
                                            <span class="badge badge-danger">{{ $socialMediaProduct->orders()->where('status', 'cancelled')->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Price Calculator -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-calculator"></i> Price Calculator</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="quantity">Quantity:</label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="quantity" 
                                               min="{{ $socialMediaProduct->min_quantity }}" 
                                               max="{{ $socialMediaProduct->max_quantity }}" 
                                               value="{{ $socialMediaProduct->min_quantity }}" 
                                               placeholder="Enter quantity">
                                        <small class="form-text text-muted">
                                            Min: {{ number_format($socialMediaProduct->min_quantity) }} | 
                                            Max: {{ number_format($socialMediaProduct->max_quantity) }}
                                        </small>
                                    </div>
                                    
                                    <div class="alert alert-info mb-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Total Price:</span>
                                            <strong class="h5 mb-0 text-success">₦<span id="totalPrice">{{ number_format(($socialMediaProduct->min_quantity / 1000) * $socialMediaProduct->price_per_1000, 2) }}</span></strong>
                                        </div>
                                        <small class="text-muted">
                                            Rate: ₦{{ number_format($socialMediaProduct->price_per_1000, 2) }} per 1,000
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.social-media-products.edit', $socialMediaProduct) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit Product
                                        </a>
                                        
            
                                        
                                        <a href="{{ route('admin.social-media-orders.index', ['product_id' => $socialMediaProduct->id]) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-list"></i> View All Orders
                                        </a>
                                        
                                        @if($socialMediaProduct->orders()->count() === 0)
                                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteProduct()">
                                                <i class="fas fa-trash"></i> Delete Product
                                            </button>
                                        @endif
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                <p><strong>Product:</strong> {{ $socialMediaProduct->name }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.social-media-products.destroy', $socialMediaProduct) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Product</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Price calculator
function calculatePrice() {
    const quantity = parseInt(document.getElementById('quantity').value) || 0;
    const pricePerThousand = {{ $socialMediaProduct->price_per_1000 }};
    const totalPrice = Math.round((quantity / 1000) * pricePerThousand * 100) / 100;
    
    document.getElementById('totalPrice').textContent = totalPrice.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Initialize calculator
document.getElementById('quantity').addEventListener('input', calculatePrice);

// Delete product function
function deleteProduct() {
    $('#deleteModal').modal('show');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculatePrice();
});
</script>
@endpush