@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Edit Category: {{ $socialMediaCategory->name }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.social-media-categories.index') }}">Social Media Categories</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.social-media-categories.show', $socialMediaCategory) }}">{{ $socialMediaCategory->name }}</a></div>
            <div class="breadcrumb-item active">Edit</div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Category Information</h4>
                    <div class="card-header-action">
                        <div class="btn-group">
                            <a href="{{ route('admin.social-media-categories.show', $socialMediaCategory) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('admin.social-media-categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Categories
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.social-media-categories.update', $socialMediaCategory->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Name -->
                                <div class="form-group">
                                    <label for="name" class="form-label required">Category Name</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $socialMediaCategory->name) }}" 
                                           placeholder="e.g., Instagram Followers" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Enter a descriptive name for this category</small>
                                </div>

                                <!-- Slug -->
                                <div class="form-group">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input type="text" 
                                           class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" 
                                           name="slug" 
                                           value="{{ old('slug', $socialMediaCategory->slug) }}" 
                                           placeholder="instagram-followers">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">URL-friendly version of the name</small>
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control summernote @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              placeholder="Describe this category and what services it includes...">{{ old('description', $socialMediaCategory->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Optional description for this category</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <!-- Status -->
                                <div class="form-group">
                                    <label for="status" class="form-label required">Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="1" {{ old('status', $socialMediaCategory->status) === 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $socialMediaCategory->status) === 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Only active categories are visible to users</small>
                                </div>

                                <!-- Sort Order -->
                                <div class="form-group">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" 
                                           class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" 
                                           name="sort_order" 
                                           value="{{ old('sort_order', $socialMediaCategory->sort_order) }}" 
                                           min="0">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Lower numbers appear first</small>
                                </div>

                                <!-- Category Stats -->
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-chart-bar"></i> Category Statistics</h6>
                                    <ul class="mb-0 small">
                                        <li><strong>Products:</strong> {{ $socialMediaCategory->products->count() }}</li>
                                        <li><strong>Created:</strong> {{ $socialMediaCategory->created_at->format('M d, Y') }}</li>
                                        <li><strong>Updated:</strong> {{ $socialMediaCategory->updated_at->format('M d, Y') }}</li>
                                    </ul>
                                </div>

                                <!-- Warning -->
                                @if($socialMediaCategory->products->count() > 0)
                                    <div class="alert alert-warning">
                                        <h6><i class="fas fa-exclamation-triangle"></i> Warning</h6>
                                        <p class="mb-0 small">This category has {{ $socialMediaCategory->products->count() }} product(s). Changing the status to inactive will hide all products in this category.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.social-media-categories.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Category
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
// Auto-generate slug from name (only if slug is empty)
document.getElementById('name').addEventListener('input', function() {
    const slugField = document.getElementById('slug');
    
    // Only auto-generate if slug field is empty
    if (slugField.value.trim() === '') {
        const name = this.value;
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
            .replace(/\s+/g, '-') // Replace spaces with hyphens
            .replace(/-+/g, '-') // Replace multiple hyphens with single
            .trim('-'); // Remove leading/trailing hyphens
        
        slugField.value = slug;
    }
});
</script>
@endpush