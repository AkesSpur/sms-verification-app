@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Create New Social Media Category</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.social-media-categories.index') }}">Social Media Categories</a></div>
            <div class="breadcrumb-item active">Create</div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Category Information</h4>
                    <div class="card-header-action">
                        <a href="{{ route('admin.social-media-categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Categories
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.social-media-categories.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Name -->
                                <div class="form-group">
                                    <label for="name" class="form-label required">Category Name</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
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
                                           value="{{ old('slug') }}" 
                                           placeholder="instagram-followers">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Leave empty to auto-generate from name</small>
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control summernote @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              placeholder="Describe this category and what services it includes...">{{ old('description') }}</textarea>
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
                                        <option value="1" {{ old('status', 1) === 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status') === 0 ? 'selected' : '' }}>Inactive</option>
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
                                           value="{{ old('sort_order', 0) }}" 
                                           min="0">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Lower numbers appear first</small>
                                </div>

                                <!-- Info Box -->
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Category Guidelines</h6>
                                    <ul class="mb-0 small">
                                        <li>Use clear, descriptive names</li>
                                        <li>Categories group related products</li>
                                        <li>Inactive categories are hidden from users</li>
                                        <li>Sort order controls display sequence</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.social-media-categories.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Category
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
</script>
@endpush