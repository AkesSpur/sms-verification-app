@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Create Banner</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Create New Banner</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" id="title" name="title" 
                                                   value="{{ old('title') }}" placeholder="Enter banner title">
                                            @error('title')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sort_order">Sort Order</label>
                                            <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                                   value="{{ old('sort_order', 0) }}" min="0" placeholder="0">
                                            @error('sort_order')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" 
                                              placeholder="Enter banner description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="link_url">Link URL</label>
                                    <input type="url" class="form-control" id="link_url" name="link_url" 
                                           value="{{ old('link_url') }}" placeholder="https://example.com">
                                    @error('link_url')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="image">Banner Image <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                    <small class="form-text text-muted">
                                        Accepted formats: JPEG, JPG, PNG, WEBP. Max size: 2MB. Recommended size: 1920x600px
                                    </small>
                                    @error('image')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Create Banner</button>
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