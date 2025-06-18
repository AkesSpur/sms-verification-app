@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Edit Banner</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Edit Banner</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" id="title" name="title" 
                                                   value="{{ old('title', $banner->title) }}" placeholder="Enter banner title">
                                            @error('title')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sort_order">Sort Order</label>
                                            <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                                   value="{{ old('sort_order', $banner->sort_order) }}" min="0" placeholder="0">
                                            @error('sort_order')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" 
                                              placeholder="Enter banner description">{{ old('description', $banner->description) }}</textarea>
                                    @error('description')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="link_url">Link URL</label>
                                    <input type="url" class="form-control" id="link_url" name="link_url" 
                                           value="{{ old('link_url', $banner->link_url) }}" placeholder="https://example.com">
                                    @error('link_url')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="image">Banner Image</label>
                                    @if($banner->image_path)
                                        <div class="mb-2">
                                            <img src="{{ $banner->image_url }}" alt="Current Banner" 
                                                 class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                                            <p class="text-muted mt-1">Current Image</p>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <small class="form-text text-muted">
                                        Accepted formats: JPEG, JPG, PNG, WEBP. Max size: 2MB. Recommended size: 1920x600px
                                        <br>Leave empty to keep current image.
                                    </small>
                                    @error('image')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="1" {{ old('status', $banner->status) == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $banner->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Update Banner</button>
                                    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Cancel</a>
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
            // Preview image before upload
            $('#image').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Remove existing preview
                        $('#image-preview').remove();
                        
                        // Add new preview
                        const preview = `
                            <div id="image-preview" class="mt-2">
                                <img src="${e.target.result}" alt="Preview" 
                                     class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                                <p class="text-muted mt-1">New Image Preview</p>
                            </div>
                        `;
                        $('#image').after(preview);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endpush