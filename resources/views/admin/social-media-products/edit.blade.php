@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Edit Product: {{ $socialMediaProduct->name }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.social-media-products.index') }}">Social Media Products</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.social-media-products.show', $socialMediaProduct) }}">{{ $socialMediaProduct->name }}</a></div>
            <div class="breadcrumb-item active">Edit</div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                        <h4>Edit Product Information</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.social-media-products.show', $socialMediaProduct) }}" class="btn btn-info me-2">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('admin.social-media-products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Products
                            </a>
                        </div>
                </div>
                <div class="card-body">
                    @if($socialMediaProduct->orders()->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> This product has {{ $socialMediaProduct->orders()->count() }} associated orders. 
                            Changes to pricing or quantity limits may affect future orders.
                        </div>
                    @endif

                    <form action="{{ route('admin.social-media-products.update', $socialMediaProduct) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Category -->
                                <div class="form-group">
                                    <label for="category_id" class="form-label required">Category</label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" 
                                            id="category_id" 
                                            name="category_id" 
                                            required>
                                        <option value="">Select a category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ (old('category_id', $socialMediaProduct->category_id) == $category->id) ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Select the category this product belongs to</small>
                                </div>

                                <!-- Name -->
                                <div class="form-group">
                                    <label for="name" class="form-label required">Product Name</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $socialMediaProduct->name) }}" 
                                           placeholder="e.g., Premium Instagram Followers" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Enter a descriptive name for this product</small>
                                </div>

                                <!-- Slug -->
                                <div class="form-group">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input type="text" 
                                           class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" 
                                           name="slug" 
                                           value="{{ old('slug', $socialMediaProduct->slug) }}" 
                                           placeholder="premium-instagram-followers">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">URL-friendly version of the product name</small>
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control summernote @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              placeholder="Describe this product, its features, and delivery details...">{{ old('description', $socialMediaProduct->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Detailed description of the product and service</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <!-- Price per 1000 -->
                                <div class="form-group">
                                    <label for="price_per_1000" class="form-label required">Price per 1,000</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">₦</span>
                                        </div>
                                        <input type="number" 
                                               class="form-control @error('price_per_1000') is-invalid @enderror" 
                                               id="price_per_1000" 
                                               name="price_per_1000" 
                                               value="{{ old('price_per_1000', $socialMediaProduct->price_per_1000) }}" 
                                               min="1" 
                                               step="0.01" 
                                               placeholder="4000" 
                                               required>
                                    </div>
                                    @error('price_per_1000')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Price for 1,000 units of this service</small>
                                </div>

                                <!-- Min Quantity -->
                                <div class="form-group">
                                    <label for="min_quantity" class="form-label required">Minimum Quantity</label>
                                    <input type="number" 
                                           class="form-control @error('min_quantity') is-invalid @enderror" 
                                           id="min_quantity" 
                                           name="min_quantity" 
                                           value="{{ old('min_quantity', $socialMediaProduct->min_quantity) }}" 
                                           min="1" 
                                           placeholder="100" 
                                           required>
                                    @error('min_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Minimum quantity users can order</small>
                                </div>

                                <!-- Max Quantity -->
                                <div class="form-group">
                                    <label for="max_quantity" class="form-label required">Maximum Quantity</label>
                                    <input type="number" 
                                           class="form-control @error('max_quantity') is-invalid @enderror" 
                                           id="max_quantity" 
                                           name="max_quantity" 
                                           value="{{ old('max_quantity', $socialMediaProduct->max_quantity) }}" 
                                           min="1" 
                                           placeholder="10000" 
                                           required>
                                    @error('max_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Maximum quantity users can order</small>
                                </div>

                                <!-- Status -->
                                <div class="form-group">
                                    <label for="status" class="form-label required">Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="1" {{ old('status', $socialMediaProduct->status) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $socialMediaProduct->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Only active products are visible to users</small>
                                </div>

                                <!-- Sort Order -->
                                <div class="form-group">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" 
                                           class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" 
                                           name="sort_order" 
                                           value="{{ old('sort_order', $socialMediaProduct->sort_order) }}" 
                                           min="0">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Lower numbers appear first</small>
                                </div>

                                <!-- Product Statistics -->
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-chart-bar"></i> Product Statistics</h6>
                                    <div class="small">
                                        <div class="d-flex justify-content-between">
                                            <span>Total Orders:</span>
                                            <strong>{{ $socialMediaProduct->orders()->count() }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Pending Orders:</span>
                                            <strong>{{ $socialMediaProduct->orders()->where('status', 'pending')->count() }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Completed Orders:</span>
                                            <strong>{{ $socialMediaProduct->orders()->where('status', 'completed')->count() }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Created:</span>
                                            <strong>{{ $socialMediaProduct->created_at->format('M d, Y') }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Last Updated:</span>
                                            <strong>{{ $socialMediaProduct->updated_at->format('M d, Y') }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price Calculator -->
                                <div class="alert alert-success">
                                    <h6><i class="fas fa-calculator"></i> Price Calculator</h6>
                                    <div class="small">
                                        <div class="mb-2">
                                            <label>Test Quantity:</label>
                                            <input type="number" class="form-control form-control-sm" id="testQuantity" placeholder="1000" min="1">
                                        </div>
                                        <div>
                                            <strong>Price: ₦<span id="calculatedPrice">0</span></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.social-media-products.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Product
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
        .replace(/\s+/g, '-') // Replace spaces with hyphens
        .replace(/-+/g, '-') // Replace multiple hyphens with single
        .trim('-'); // Remove leading/trailing hyphens
    
    document.getElementById('slug').value = slug;
});

// Price calculator
function calculatePrice() {
    const pricePerThousand = parseFloat(document.getElementById('price_per_1000').value) || 0;
    const quantity = parseInt(document.getElementById('testQuantity').value) || 0;
    const totalPrice = Math.round((quantity / 1000) * pricePerThousand);
    
    document.getElementById('calculatedPrice').textContent = totalPrice.toLocaleString();
}

document.getElementById('price_per_1000').addEventListener('input', calculatePrice);
document.getElementById('testQuantity').addEventListener('input', calculatePrice);

// Validate min/max quantities
document.getElementById('min_quantity').addEventListener('input', function() {
    const minQty = parseInt(this.value);
    const maxQtyField = document.getElementById('max_quantity');
    const maxQty = parseInt(maxQtyField.value);
    
    if (maxQty && minQty > maxQty) {
        maxQtyField.value = minQty;
    }
});

document.getElementById('max_quantity').addEventListener('input', function() {
    const maxQty = parseInt(this.value);
    const minQtyField = document.getElementById('min_quantity');
    const minQty = parseInt(minQtyField.value);
    
    if (minQty && maxQty < minQty) {
        minQtyField.value = maxQty;
    }
});

// Initialize calculator on page load
document.addEventListener('DOMContentLoaded', function() {
    calculatePrice();
});
</script>
@endpush