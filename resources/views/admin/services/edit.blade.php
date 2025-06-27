@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Edit Service</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Services</a></div>
            <div class="breadcrumb-item">Edit</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Service: {{ $service->name }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.services.update', $service) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="name">Service Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $service->name) }}" 
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
                                       value="{{ old('code', $service->code) }}" 
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
                                        <span class="input-group-text">₦</span>
                                    </div>
                                    <input type="number" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           id="price" 
                                           name="price" 
                                           value="{{ old('price', $service->price) }}" 
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
                                    <option value="active" {{ old('status', $service->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $service->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                           {{ old('allow_refunds', $service->allow_refunds) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="allow_refunds">
                                        Allow Refunds
                                    </label>
                                </div>
                                <small class="form-text text-muted">Check this if customers can request refunds for this service.</small>
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Service
                                </button>
                                <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <a href="{{ route('admin.services.show', $service) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> View Service
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-4 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Service Statistics</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="card card-statistic-1">
                                    <div class="card-icon bg-primary">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Total Orders</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $service->orders()->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card card-statistic-1">
                                    <div class="card-icon bg-success">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Countries</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $service->countries()->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Service Information:</h6>
                            <ul class="mb-0">
                                <li><strong>Created:</strong> {{ $service->created_at->format('M d, Y H:i') }}</li>
                                <li><strong>Last Updated:</strong> {{ $service->updated_at->format('M d, Y H:i') }}</li>
                                <li><strong>Current Status:</strong> 
                                    @if($service->status === 'active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                        
                        @if($service->orders()->count() > 0)
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle"></i> Warning:</h6>
                                <p class="mb-0">This service has {{ $service->orders()->count() }} associated orders. Changing the service code or deactivating it may affect existing orders.</p>
                            </div>
                        @endif
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