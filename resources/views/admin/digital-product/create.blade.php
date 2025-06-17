@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Create Digital Product</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.digital-products.index') }}">Digital Products</a></div>
            <div class="breadcrumb-item">Create</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Create New Product</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.digital-products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Category <span class="text-danger">*</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <select name="category_id" id="category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Subcategory <span class="text-danger">*</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <select name="subcategory_id" id="subcategory_id" class="form-control" required>
                                        <option value="">Select Subcategory</option>
                                        @foreach($subcategories as $subcategory)
                                            <option value="{{ $subcategory->id }}" data-category="{{ $subcategory->category_id }}" {{ old('subcategory_id') == $subcategory->id ? 'selected' : '' }}>
                                                {{ $subcategory->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subcategory_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Name <span class="text-danger">*</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Slug</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}">
                                    <small class="form-text text-muted">Leave empty to auto-generate from name</small>
                                    @error('slug')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Description</label>
                                <div class="col-sm-12 col-md-7">
                                    <textarea name="description" class="form-control summernote" rows="4">{!! old('description') !!}</textarea>
                                    @error('description')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Image</label>
                                <div class="col-sm-12 col-md-7">
                                    <div id="image-preview" class="image-preview">
                                        <label for="image-upload" id="image-label">Choose File</label>
                                        <input type="file" name="image" id="image-upload" accept="image/*">
                                    </div>
                                    <small class="form-text text-muted">Supported formats: JPEG, JPG, PNG, WEBP. Max size: 2MB</small>
                                    @error('image')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Price <span class="text-danger">*</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">{{ $settings->currency_icon }}</div>
                                        </div>
                                        <input type="number" name="price" class="form-control" value="{{ old('price') }}" step="0.01" min="0" required>
                                    </div>
                                    @error('price')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Status <span class="text-danger">*</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <select name="status" class="form-control" required>
                                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Sort Order</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                                    @error('sort_order')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                <div class="col-sm-12 col-md-7">
                                    <button type="submit" class="btn btn-primary me-2 mb-2">Create Product</button>
                                    <a href="{{ route('admin.digital-products.index') }}" class="btn btn-secondary mb-2">Cancel</a>
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
        // Initialize Summernote
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        // Initialize image preview
        $.uploadPreview({
            input_field: "#image-upload",
            preview_box: "#image-preview",
            label_field: "#image-label",
            label_default: "Choose File",
            label_selected: "Change File",
            no_label: false
        });

        // Filter subcategories based on selected category
        $('#category_id').on('change', function() {
            let categoryId = $(this).val();
            let subcategorySelect = $('#subcategory_id');
            
            // Reset subcategory dropdown
            subcategorySelect.html('<option value="">Select Subcategory</option>');
            
            if (categoryId) {
                // Show subcategories for selected category
                $('#subcategory_id option[data-category]').each(function() {
                    if ($(this).data('category') == categoryId) {
                        subcategorySelect.append($(this).clone());
                    }
                });
            }
        });

        // Trigger category change on page load if category is selected
        if ($('#category_id').val()) {
            $('#category_id').trigger('change');
        }

        // Auto-generate slug from name
        $('input[name="name"]').on('input', function() {
            let name = $(this).val();
            let slug = name.toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove special characters
                .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
                .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
            
            if ($('input[name="slug"]').val() === '' || $('input[name="slug"]').data('auto-generated')) {
                $('input[name="slug"]').val(slug).data('auto-generated', true);
            }
        });

        // Mark slug as manually edited
        $('input[name="slug"]').on('input', function() {
            $(this).data('auto-generated', false);
        });
    });
</script>
@endpush