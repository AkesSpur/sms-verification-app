@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Social Media Products</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active">Social Media Products</div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>All Products</h4>
                    <div class="card-header-action">
                        <a href="{{ route('admin.social-media-products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Product
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select class="form-control" id="categoryFilter" onchange="filterByCategory()">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" id="statusFilter" onchange="filterByStatus()">
                                <option value="">All Status</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search products..." value="{{ request('search') }}">
                        </div>
                    </div>

                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Product Name</th>
                                        <th width="15%">Category</th>
                                        <th width="10%">Price/1000</th>
                                        <th width="15%">Quantity Range</th>
                                        <th width="10%">Status</th>
                                        <th width="15%">Created</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
                                            <td>
                                                <strong>{{ $product->name }}</strong>
                                                <br>
                                                <small class="text-muted">{!! Str::limit($product->description, 50) !!}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $product->category->name }}</span>
                                            </td>
                                            <td>
                                                <strong class="text-success">₦{{ number_format($product->price_per_1000, 0) }}</strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    Min: {{ number_format($product->min_quantity) }}<br>
                                                    Max: {{ number_format($product->max_quantity) }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($product->status == 1)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $product->created_at->format('M d, Y') }}<br>
                                                    {{ $product->created_at->format('h:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.social-media-products.show', $product->id) }}" 
                                                       class="btn btn-sm btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.social-media-products.edit', $product->id) }}" 
                                                       class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="Delete"
                                                            onclick="deleteProduct({{ $product->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($products->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $products->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Products Found</h5>
                            @if(request()->hasAny(['category', 'status', 'search']))
                                <p class="text-muted">No products match your current filters.</p>
                                <a href="{{ route('admin.social-media-products.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear Filters
                                </a>
                            @else
                                <p class="text-muted">Start by creating your first social media product.</p>
                                <a href="{{ route('admin.social-media-products.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Product
                                </a>
                            @endif
                        </div>
                    @endif
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                <p class="text-warning"><strong>Warning:</strong> All orders for this product will be affected.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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

@push('scripts')
<script>
function deleteProduct(productId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/admin/social-media-products/${productId}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function filterByCategory() {
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchInput').value;
    
    updateFilters(category, status, search);
}

function filterByStatus() {
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchInput').value;
    
    updateFilters(category, status, search);
}

function updateFilters(category, status, search) {
    const url = new URL(window.location.href);
    
    if (category) {
        url.searchParams.set('category', category);
    } else {
        url.searchParams.delete('category');
    }
    
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    
    if (search) {
        url.searchParams.set('search', search);
    } else {
        url.searchParams.delete('search');
    }
    
    window.location.href = url.toString();
}

// Search with debounce
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const category = document.getElementById('categoryFilter').value;
        const status = document.getElementById('statusFilter').value;
        const search = this.value;
        
        updateFilters(category, status, search);
    }, 500);
});
</script>
@endpush