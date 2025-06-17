@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Add New Product Log</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.digital-product-logs.index') }}">Product Logs</a></div>
            <div class="breadcrumb-item">Add New</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Log Information</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.digital-product-logs.index', request()->only('product_id')) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Logs
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.digital-product-logs.store') }}" method="POST">
                            @csrf
                            
                            @if(request('product_id'))
                                <input type="hidden" name="product_id" value="{{ request('product_id') }}">
                            @endif
                            
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Product <span class="text-danger">*</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control select2" name="product_id" {{ request('product_id') ? 'disabled' : '' }}>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ (old('product_id', request('product_id')) == $product->id) ? 'selected' : '' }}>
                                                {{ $product->name }} 
                                                
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Log Item <span class="text-danger">*</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <textarea class="form-control summernote" name="log_item" rows="3" placeholder="Enter the log item content (e.g., account credentials, access codes, etc.)">{!! old('log_item') !!}</textarea>
                                    <small class="form-text text-muted">This is the actual content that will be delivered to the customer when they purchase the product.</small>
                                    @error('log_item')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Details</label>
                                <div class="col-sm-12 col-md-7">
                                    <textarea class="form-control summernote" name="details" rows="3" placeholder="Additional details or notes about this log item (optional)">{!! old('details') !!}</textarea>
                                    <small class="form-text text-muted">Optional additional information about this log item.</small>
                                    @error('details')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Status <span class="text-danger">*</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control" name="status">
                                        <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="sold" {{ old('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                                    </select>
                                    <small class="form-text text-muted">Set to 'Available' for new log items that can be sold.</small>
                                    @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Conditional fields for sold status -->
                            <div id="soldFields" style="display: none;">
                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Sold Date</label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="datetime-local" class="form-control" name="sold_at" value="{{ old('sold_at') }}">
                                        <small class="form-text text-muted">When was this item sold? Leave empty for current time.</small>
                                        @error('sold_at')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Sold To User</label>
                                    <div class="col-sm-12 col-md-7">
                                        <select class="form-control select2" name="sold_to_user_id">
                                            <option value="">Select User (Optional)</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('sold_to_user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Which user purchased this item?</small>
                                        @error('sold_to_user_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                <div class="col-sm-12 col-md-7">
                                    <button type="submit" class="btn btn-primary me-2 mb-2">
                                        <i class="fas fa-save"></i> Create Log
                                    </button>
                                    <a href="{{ route('admin.digital-product-logs.index', request()->only('product_id')) }}" class="btn btn-secondary mb-2">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                @if(request('product_id'))
                    @php
                        $selectedProduct = $products->find(request('product_id'));
                    @endphp
                    @if($selectedProduct)
                        <div class="card">
                            <div class="card-header">
                                <h4>Product Information</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-striped">
                                            <tr>
                                                <td><strong>Product Name</strong></td>
                                                <td>{{ $selectedProduct->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Category</strong></td>
                                                <td>{{ $selectedProduct->subcategory->category->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Subcategory</strong></td>
                                                <td>{{ $selectedProduct->subcategory->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Price</strong></td>
                                                <td>{{ $settings->currency_icon }}{{ number_format($selectedProduct->price, 2) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-striped">
                                            <tr>
                                                <td><strong>Current Stock</strong></td>
                                                <td>
                                                    @if($selectedProduct->available_stock > 0)
                                                        <span class="badge badge-success">{{ $selectedProduct->available_stock }} available</span>
                                                    @else
                                                        <span class="badge badge-danger">Out of stock</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Total Logs</strong></td>
                                                <td>{{ $selectedProduct->logs->count() }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Available Logs</strong></td>
                                                <td>{{ $selectedProduct->logs->where('status', 'available')->count() }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Sold Logs</strong></td>
                                                <td>{{ $selectedProduct->logs->where('status', 'sold')->count() }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
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
            height: 150,
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
        
        // Initialize Select2
        $('.select2').select2();
        
        // Show/hide sold fields based on status
        function toggleSoldFields() {
            if ($('select[name="status"]').val() === 'sold') {
                $('#soldFields').show();
            } else {
                $('#soldFields').hide();
            }
        }
        
        // Initial check
        toggleSoldFields();
        
        // Listen for status changes
        $('select[name="status"]').on('change', function() {
            toggleSoldFields();
        });
    });
</script>
@endpush