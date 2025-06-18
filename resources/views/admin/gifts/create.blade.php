@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Create New Gift</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.gifts.index') }}">Gifts</a></div>
            <div class="breadcrumb-item">Create</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Gift Information</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.gifts.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Gift Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="slug">Slug</label>
                                        <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                               id="slug" name="slug" value="{{ old('slug') }}">
                                        <small class="form-text text-muted">Leave empty to auto-generate from name</small>
                                        @error('slug')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="price">Price <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{{ $settings->currency_icon }}</span>
                                            </div>
                                            <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                                   id="price" name="price" value="{{ old('price') }}" 
                                                   step="0.01" min="0" required>
                                        </div>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sort_order">Sort Order</label>
                                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                               id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                        @error('sort_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customizable">Customizable Gift</label>
                                        <select class="form-control @error('customizable') is-invalid @enderror" id="customizable" name="customizable">
                                            <option value="0" {{ old('customizable', '0') == '0' ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('customizable') == '1' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                        @error('customizable')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group" id="customization-cost-group" style="display: none;">
                                        <label for="customization_cost">Customization Cost</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{{ $settings->currency_icon }}</span>
                                            </div>
                                            <input type="number" class="form-control @error('customization_cost') is-invalid @enderror" 
                                                   id="customization_cost" name="customization_cost" 
                                                   value="{{ old('customization_cost') }}" step="0.01" min="0">
                                        </div>
                                        @error('customization_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control summernote @error('description') is-invalid @enderror" 
                                          id="description" name="description">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="featured_image">Featured Image</label>
                                        <input type="file" class="form-control-file @error('featured_image') is-invalid @enderror" 
                                               id="featured_image" name="featured_image" accept="image/*">
                                        <small class="form-text text-muted">Recommended size: 800x600px</small>
                                        @error('featured_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gallery_images">Gallery Images</label>
                                        <input type="file" class="form-control-file @error('gallery_images.*') is-invalid @enderror" 
                                               id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                                        <small class="form-text text-muted">You can select multiple images</small>
                                        @error('gallery_images.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-save"></i> Create Gift
                                </button>
                                <a href="{{ route('admin.gifts.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
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
        // Auto-generate slug from name
        $('#name').on('input', function() {
            if ($('#slug').val() === '') {
                let slug = $(this).val().toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('#slug').val(slug);
            }
        });

        // Toggle customization cost field
        $('#customizable').change(function() {
            if ($(this).val() == '1') {
                $('#customization-cost-group').show();
            } else {
                $('#customization-cost-group').hide();
                $('#customization_cost').val('');
            }
        });

        // Check initial state
        if ($('#customizable').val() == '1') {
            $('#customization-cost-group').show();
        }

        // Initialize Summernote
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });
</script>
@endpush