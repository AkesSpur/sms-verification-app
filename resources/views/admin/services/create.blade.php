@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Create New Service</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Services</a></div>
            <div class="breadcrumb-item">Create</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Service Information</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.services.store') }}" method="POST">
                            @csrf
                            
                            <div class="form-group">
                                <label for="name">Service Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Enter service name"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Enter a descriptive name for the service (e.g., "WhatsApp", "Telegram", "Instagram")</small>
                            </div>

                            <div class="form-group">
                                <label for="code">Service Code <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code') }}" 
                                       placeholder="Enter service code"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Enter a unique code for the service (e.g., "wa", "tg", "ig"). This should match the API service code.</small>
                            </div>

                            <div class="form-group">
                                <label for="price">Base Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           id="price" 
                                           name="price" 
                                           value="{{ old('price') }}" 
                                           step="0.01" 
                                           min="0" 
                                           placeholder="0.00"
                                           required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Set the base price for this service. This can be overridden by country-specific pricing.</small>
                            </div>

                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Only active services will be available for customers to purchase.</small>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="allow_refunds" 
                                           name="allow_refunds" 
                                           value="1" 
                                           {{ old('allow_refunds') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="allow_refunds">
                                        Allow Refunds
                                    </label>
                                </div>
                                <small class="form-text text-muted">Check this if customers can request refunds for this service.</small>
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Service
                                </button>
                                <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-4 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Service Guidelines</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Important Notes:</h6>
                            <ul class="mb-0">
                                <li><strong>Service Name:</strong> Use clear, recognizable names that customers will understand.</li>
                                <li><strong>Service Code:</strong> Must be unique and should match the SMS provider's API service codes.</li>
                                <li><strong>Base Price:</strong> This is the default price. You can set country-specific pricing later.</li>
                                <li><strong>Status:</strong> Inactive services won't be visible to customers.</li>
                                <li><strong>Refunds:</strong> Enable this for services where refunds are typically allowed.</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> Common Service Codes:</h6>
                            <ul class="mb-0">
                                <li><code>wa</code> - WhatsApp</li>
                                <li><code>tg</code> - Telegram</li>
                                <li><code>ig</code> - Instagram</li>
                                <li><code>fb</code> - Facebook</li>
                                <li><code>tw</code> - Twitter</li>
                                <li><code>go</code> - Google</li>
                            </ul>
                            <small>Check your SMS provider's documentation for exact codes.</small>
                        </div>
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
    // Auto-generate code from name
    $('#name').on('input', function() {
        if ($('#code').val() === '') {
            let code = $(this).val()
                .toLowerCase()
                .replace(/[^a-z0-9]/g, '')
                .substring(0, 10);
            $('#code').val(code);
        }
    });
    
    // Format price input
    $('#price').on('blur', function() {
        let value = parseFloat($(this).val());
        if (!isNaN(value)) {
            $(this).val(value.toFixed(2));
        }
    });
});
</script>
@endpush