@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Category Details: {{ $socialMediaCategory->name }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.social-media-categories.index') }}">Social Media Categories</a></div>
            <div class="breadcrumb-item active">{{ $socialMediaCategory->name }}</div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Category Details</h4>
                    <div class="card-header-action">
                        <div class="btn-group">
                            <a href="{{ route('admin.social-media-categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Categories
                            </a>
                            <a href="{{ route('admin.social-media-categories.edit', $socialMediaCategory->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Category
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Category Information -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-info-circle"></i> Category Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <dl class="row">
                                                <dt class="col-sm-4">Name:</dt>
                                                <dd class="col-sm-8">{{ $socialMediaCategory->name }}</dd>
                                                
                                                <dt class="col-sm-4">Slug:</dt>
                                                <dd class="col-sm-8"><code>{{ $socialMediaCategory->slug }}</code></dd>
                                                
                                                <dt class="col-sm-4">Status:</dt>
                                                <dd class="col-sm-8">
                                                    @if($socialMediaCategory->status)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    @endif
                                                </dd>
                                                
                                                <dt class="col-sm-4">Sort Order:</dt>
                                                <dd class="col-sm-8">{{ $socialMediaCategory->sort_order }}</dd>
                                            </dl>
                                        </div>
                                        <div class="col-md-6">
                                            <dl class="row">
                                                <dt class="col-sm-4">Created:</dt>
                                                <dd class="col-sm-8">{{ $socialMediaCategory->created_at->format('M d, Y H:i') }}</dd>
                                                
                                                <dt class="col-sm-4">Updated:</dt>
                                                <dd class="col-sm-8">{{ $socialMediaCategory->updated_at->format('M d, Y H:i') }}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                    
                                    @if($socialMediaCategory->description)
                                        <div class="mt-3">
                                            <h6>Description:</h6>
                                            <div class="border p-3 bg-light rounded">
                                                {!! $socialMediaCategory->description !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Associated Products -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0"><i class="fas fa-box"></i> Associated Products ({{ $socialMediaCategory->products->count() }})</h5>
                                        <a href="{{ route('admin.social-media-products.create', ['category' => $socialMediaCategory->id]) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> Add Product
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($socialMediaCategory->products->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Price (per 1000)</th>
                                                        <th>Quantity Range</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($socialMediaCategory->products as $product)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $product->name }}</strong>
                                                                <br>
                                                                <small class="text-muted">{{ $product->orders_count ?? 0 }} orders</small>
                                                            </td>
                                                            <td>₦{{ number_format($product->price_per_1000, 2) }}</td>
                                                            <td>{{ number_format($product->min_quantity) }} - {{ number_format($product->max_quantity) }}</td>
                                                            <td>
                                                                @if($product->status)
                                                                    <span class="badge badge-success">Active</span>
                                                                @else
                                                                    <span class="badge badge-secondary">Inactive</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="{{ route('admin.social-media-products.show', $product->id) }}" class="btn btn-info" title="View">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('admin.social-media-products.edit', $product->id) }}" class="btn btn-warning" title="Edit">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <h6 class="text-muted">No Products Found</h6>
                                            <p class="text-muted">This category doesn't have any products yet.</p>
                                            <a href="{{ route('admin.social-media-products.create', ['category' => $socialMediaCategory->id]) }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Add First Product
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Statistics & Quick Actions -->
                        <div class="col-md-4">
                            <!-- Statistics -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-chart-bar"></i> Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-right">
                                                <h4 class="text-primary">{{ $socialMediaCategory->products->count() }}</h4>
                                                <small class="text-muted">Total Products</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success">{{ $socialMediaCategory->products->where('status', 1)->count() }}</h4>
                                            <small class="text-muted">Active Products</small>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-right">
                                                <h4 class="text-info">{{ $socialMediaCategory->products->sum('orders_count') ?? 0 }}</h4>
                                                <small class="text-muted">Total Orders</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-warning">₦{{ number_format($socialMediaCategory->products->sum('revenue') ?? 0, 2) }}</h4>
                                            <small class="text-muted">Revenue</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.social-media-categories.edit', $socialMediaCategory->id) }}" class="btn btn-warning btn-block">
                                            <i class="fas fa-edit"></i> Edit Category
                                        </a>
                                        
                                        @if($socialMediaCategory->status)
                                            <form action="{{ route('admin.social-media-categories.update', $socialMediaCategory->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="name" value="{{ $socialMediaCategory->name }}">
                                                <input type="hidden" name="slug" value="{{ $socialMediaCategory->slug }}">
                                                <input type="hidden" name="description" value="{{ $socialMediaCategory->description }}">
                                                <input type="hidden" name="status" value="0">
                                                <input type="hidden" name="sort_order" value="{{ $socialMediaCategory->sort_order }}">
                                                <button type="submit" class="btn btn-secondary btn-block">
                                                    <i class="fas fa-eye-slash"></i> Deactivate
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.social-media-categories.update', $socialMediaCategory->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="name" value="{{ $socialMediaCategory->name }}">
                                                <input type="hidden" name="slug" value="{{ $socialMediaCategory->slug }}">
                                                <input type="hidden" name="description" value="{{ $socialMediaCategory->description }}">
                                                <input type="hidden" name="status" value="1">
                                                <input type="hidden" name="sort_order" value="{{ $socialMediaCategory->sort_order }}">
                                                <button type="submit" class="btn btn-success btn-block">
                                                    <i class="fas fa-eye"></i> Activate
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <a href="{{ route('admin.social-media-products.create', ['category' => $socialMediaCategory->id]) }}" class="btn btn-primary btn-block">
                                            <i class="fas fa-plus"></i> Add Product
                                        </a>
                                        
                                        <a href="{{ route('admin.social-media-orders.index', ['category' => $socialMediaCategory->id]) }}" class="btn btn-info btn-block">
                                            <i class="fas fa-list"></i> View Orders
                                        </a>
                                        
                                        @if($socialMediaCategory->products->count() == 0)
                                            <button type="button" class="btn btn-danger btn-block" onclick="deleteCategory({{ $socialMediaCategory->id }})">
                                                <i class="fas fa-trash"></i> Delete Category
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Timeline -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-clock"></i> Timeline</h5>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-success"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Category Created</h6>
                                                <p class="timeline-text">{{ $socialMediaCategory->created_at->format('M d, Y H:i') }}</p>
                                            </div>
                                        </div>
                                        
                                        @if($socialMediaCategory->updated_at != $socialMediaCategory->created_at)
                                            <div class="timeline-item">
                                                <div class="timeline-marker bg-info"></div>
                                                <div class="timeline-content">
                                                    <h6 class="timeline-title">Last Updated</h6>
                                                    <p class="timeline-text">{{ $socialMediaCategory->updated_at->format('M d, Y H:i') }}</p>
                                                </div>
                                            </div>
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
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this category? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> Make sure this category has no products before deleting.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
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
}

.timeline-content {
    padding-left: 10px;
}

.timeline-title {
    margin-bottom: 5px;
    font-size: 14px;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 0;
    font-size: 12px;
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
function deleteCategory(categoryId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/admin/social-media-categories/${categoryId}`;
    $('#deleteModal').modal('show');
}
</script>
@endpush