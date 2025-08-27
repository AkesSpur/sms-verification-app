@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Create New Service</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="{{ route('admin.daisy-services.index') }}">DaisySMS Services</a></div>
            <div class="breadcrumb-item">Create Service</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Service Information</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.daisy-services.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Services
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.daisy-services.store') }}">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="code">Service Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                               id="code" name="code" value="{{ old('code') }}" 
                                               placeholder="e.g., TG, WA, FB" maxlength="10" required>
                                        <small class="form-text text-muted">Unique service identifier (max 10 characters)</small>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Service Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name') }}" 
                                               placeholder="e.g., Telegram, WhatsApp" maxlength="100" required>
                                        <small class="form-text text-muted">Display name for the service</small>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="icon">Icon Class</label>
                                        <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                               id="icon" name="icon" value="{{ old('icon') }}" 
                                               placeholder="e.g., fab fa-telegram, fab fa-whatsapp">
                                        <small class="form-text text-muted">FontAwesome icon class (optional)</small>
                                        @error('icon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sort_order">Sort Order</label>
                                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                               id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" 
                                               min="0" placeholder="0">
                                        <small class="form-text text-muted">Display order (0 = first)</small>
                                        @error('sort_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Brief description of the service">{{ old('description') }}</textarea>
                                <small class="form-text text-muted">Optional description for the service</small>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        <small class="form-text text-muted">Service availability status</small>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="is_popular">Popular Service <span class="text-danger">*</span></label>
                                        <select class="form-control @error('is_popular') is-invalid @enderror" id="is_popular" name="is_popular" required>
                                            <option value="0" {{ old('is_popular', '0') == '0' ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('is_popular') == '1' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                        <small class="form-text text-muted">Mark as popular service</small>
                                        @error('is_popular')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Meta Data Section -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5>Additional Settings</h5>
                                </div>
                                <div class="card-body">
                                    <div id="meta-data-container">
                                        <div class="form-group">
                                            <label>Meta Data (Key-Value Pairs)</label>
                                            <small class="form-text text-muted mb-2">Add custom metadata for this service</small>
                                            <div id="meta-fields">
                                                <!-- Meta fields will be added here dynamically -->
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-meta-field">
                                                <i class="fas fa-plus"></i> Add Meta Field
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Service
                                </button>
                                <a href="{{ route('admin.daisy-services.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times"></i> Cancel
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
        let metaFieldIndex = 0;

        // Add meta field functionality
        $('#add-meta-field').click(function() {
            const metaField = `
                <div class="row meta-field mb-2" data-index="${metaFieldIndex}">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="meta_data[${metaFieldIndex}][key]" placeholder="Key" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="meta_data[${metaFieldIndex}][value]" placeholder="Value" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-danger remove-meta-field">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#meta-fields').append(metaField);
            metaFieldIndex++;
        });

        // Remove meta field functionality
        $(document).on('click', '.remove-meta-field', function() {
            $(this).closest('.meta-field').remove();
        });

        // Auto-generate service code from name
        $('#name').on('input', function() {
            const name = $(this).val();
            if (name && !$('#code').val()) {
                const code = name.substring(0, 2).toUpperCase();
                $('#code').val(code);
            }
        });

        // Icon preview
        $('#icon').on('input', function() {
            const iconClass = $(this).val();
            const preview = $('#icon-preview');
            
            if (iconClass) {
                if (!preview.length) {
                    $(this).after('<div id="icon-preview" class="mt-2"><i class="' + iconClass + '"></i> Preview</div>');
                } else {
                    preview.html('<i class="' + iconClass + '"></i> Preview');
                }
            } else {
                preview.remove();
            }
        });

        // Form validation
        $('form').on('submit', function(e) {
            const code = $('#code').val().trim();
            const name = $('#name').val().trim();
            
            if (!code || !name) {
                e.preventDefault();
                toastr.error('Please fill in all required fields.');
                return false;
            }
            
            if (code.length > 10) {
                e.preventDefault();
                toastr.error('Service code must be 10 characters or less.');
                return false;
            }
        });
    });
</script>
@endpush