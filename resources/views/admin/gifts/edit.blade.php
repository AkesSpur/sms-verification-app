@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Edit Gift</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.gifts.index') }}">Gifts</a></div>
            <div class="breadcrumb-item">Edit</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Gift: {{ $gift->name }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.gifts.update', $gift->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Gift Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $gift->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="slug">Slug</label>
                                        <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                               id="slug" name="slug" value="{{ old('slug', $gift->slug) }}">
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
                                                   id="price" name="price" value="{{ old('price', $gift->price) }}" 
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
                                               id="sort_order" name="sort_order" value="{{ old('sort_order', $gift->sort_order) }}" min="0">
                                        @error('sort_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="1" {{ old('status', $gift->status) == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status', $gift->status) == '0' ? 'selected' : '' }}>Inactive</option>
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
                                            <option value="0" {{ old('customizable', $gift->customizable) == '0' ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('customizable', $gift->customizable) == '1' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                        @error('customizable')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group" id="customization-cost-group" 
                                         style="display: {{ old('customizable', $gift->customizable) == '1' ? 'block' : 'none' }};">
                                        <label for="customization_cost">Customization Cost</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{{ $settings->currency_icon }}</span>
                                            </div>
                                            <input type="number" class="form-control @error('customization_cost') is-invalid @enderror" 
                                                   id="customization_cost" name="customization_cost" 
                                                   value="{{ old('customization_cost', $gift->customization_cost) }}" step="0.01" min="0">
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
                                          id="description" name="description">{{ old('description', $gift->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="featured_image">Featured Image</label>
                                        @if($gift->featured_image)
                                            <div class="mb-2">
                                                <img src="{{ asset($gift->featured_image) }}" alt="Current featured image" 
                                                     class="img-thumbnail" style="max-width: 200px;">
                                                <p class="text-muted small mt-1">Current featured image</p>
                                            </div>
                                        @endif
                                        <input type="file" class="form-control-file @error('featured_image') is-invalid @enderror" 
                                               id="featured_image" name="featured_image" accept="image/*">
                                        <small class="form-text text-muted">Recommended size: 800x600px. Leave empty to keep current image.</small>
                                        @error('featured_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gallery_images">Add More Gallery Images</label>
                                        <input type="file" class="form-control-file @error('gallery_images.*') is-invalid @enderror" 
                                               id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                                        <small class="form-text text-muted">You can select multiple images to add to the gallery</small>
                                        @error('gallery_images.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            @if($gift->images->count() > 0)
                            <div class="form-group">
                                <label>Current Gallery Images</label>
                                <div class="row">
                                    @foreach($gift->images as $image)
                                    <div class="col-md-3 mb-3" id="image-{{ $image->id }}">
                                        <div class="card">
                                            <img src="{{ asset($image->image_path) }}" class="card-img-top" 
                                                 style="height: 150px; object-fit: cover;" alt="Gallery image">
                                            <div class="card-body p-2">
                                                <div class="btn-group btn-group-sm w-100" role="group">
                                                    @if(!$image->is_featured)
                                                    <button type="button" class="btn btn-outline-primary set-featured" 
                                                            data-image-id="{{ $image->id }}" title="Set as Featured">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                    @else
                                                    <button type="button" class="btn btn-primary unset-featured" 
                                                            data-image-id="{{ $image->id }}" title="Remove Featured">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                    @endif
                                                    <button type="button" class="btn btn-outline-danger delete-image" 
                                                            data-image-id="{{ $image->id }}" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                @if($image->is_featured)
                                                <small class="text-primary d-block text-center mt-1">Featured</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-save"></i> Update Gift
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
            let slug = $(this).val().toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            $('#slug').val(slug);
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

        // Delete image
        $('.delete-image').click(function() {
            const imageId = $(this).data('image-id');
            
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
                        url: `/admin/gifts/images/${imageId}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                $(`#image-${imageId}`).fadeOut(300, function() {
                                    $(this).remove();
                                });
                                toastr.success('Image deleted successfully!');
                            }
                        },
                        error: function() {
                            toastr.error('Error deleting image!');
                        }
                    });
                }
            });
        });

        // Set featured image
        $('.set-featured').click(function() {
            const imageId = $(this).data('image-id');
            
            $.ajax({
                url: `/admin/gifts/images/${imageId}/set-featured`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Remove featured status from all images
                        $('.set-featured, .unset-featured').removeClass('btn-primary unset-featured')
                            .addClass('btn-outline-primary set-featured')
                            .attr('title', 'Set as Featured');
                        $('.card-body small').remove();
                        
                        // Set this image as featured
                        $(`[data-image-id="${imageId}"]`).removeClass('btn-outline-primary set-featured')
                            .addClass('btn-primary unset-featured')
                            .attr('title', 'Remove Featured');
                        $(`#image-${imageId} .card-body`).append('<small class="text-primary d-block text-center mt-1">Featured</small>');
                        
                        toastr.success('Featured image updated successfully!');
                    }
                },
                error: function() {
                    toastr.error('Error updating featured image!');
                }
            });
        });

        // Unset featured image
        $(document).on('click', '.unset-featured', function() {
            const imageId = $(this).data('image-id');
            
            $.ajax({
                url: `/admin/gifts/images/${imageId}/unset-featured`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Convert featured button back to set-featured
                        $(`[data-image-id="${imageId}"]`).removeClass('btn-primary unset-featured')
                            .addClass('btn-outline-primary set-featured')
                            .attr('title', 'Set as Featured');
                        $(`#image-${imageId} .card-body small`).remove();
                        
                        toastr.success('Featured status removed successfully!');
                    }
                },
                error: function() {
                    toastr.error('Error removing featured status!');
                }
            });
        });
    });
</script>
@endpush