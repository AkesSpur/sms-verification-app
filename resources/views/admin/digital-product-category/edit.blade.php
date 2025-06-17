@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Edit Digital Product Category</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.digital-product-categories.index') }}">Digital Product Categories</a></div>
            <div class="breadcrumb-item">Edit</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Category: {{ $digitalProductCategory->name }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.digital-product-categories.update', $digitalProductCategory) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Name <span class="text-danger">*</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $digitalProductCategory->name) }}" required>
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Slug</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" name="slug" class="form-control" value="{{ old('slug', $digitalProductCategory->slug) }}">
                                    <small class="form-text text-muted">Leave empty to auto-generate from name</small>
                                    @error('slug')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Description</label>
                                <div class="col-sm-12 col-md-7">
                                    <textarea name="description" class="form-control" rows="4">{{ old('description', $digitalProductCategory->description) }}</textarea>
                                    @error('description')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Status <span class="text-danger">*</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <select name="status" class="form-control" required>
                                        <option value="1" {{ old('status', $digitalProductCategory->status) == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $digitalProductCategory->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Sort Order</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $digitalProductCategory->sort_order) }}" min="0">
                                    @error('sort_order')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                <div class="col-sm-12 col-md-7">
                                    <button type="submit" class="btn btn-primary">Update Category</button>
                                    <a href="{{ route('admin.digital-product-categories.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
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
        let originalSlug = '{{ $digitalProductCategory->slug }}';
        
        // Auto-generate slug from name only if slug matches the original or is empty
        $('input[name="name"]').on('input', function() {
            let name = $(this).val();
            let currentSlug = $('input[name="slug"]').val();
            
            let slug = name.toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove special characters
                .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
                .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
            
            if (currentSlug === originalSlug || currentSlug === '' || $('input[name="slug"]').data('auto-generated')) {
                $('input[name="slug"]').val(slug).data('auto-generated', true);
            }
        });

        // Mark slug as manually edited
        $('input[name="slug"]').on('input', function() {
            if ($(this).val() !== originalSlug) {
                $(this).data('auto-generated', false);
            }
        });
    });
</script>
@endpush