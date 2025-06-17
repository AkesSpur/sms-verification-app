@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Edit Product Log</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.digital-product-logs.index') }}">Product Logs</a></div>
            <div class="breadcrumb-item">Edit Log</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Log Information</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.digital-product-logs.show', $digitalProductLog) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <a href="{{ route('admin.digital-product-logs.index', ['product_id' => $digitalProductLog->product_id]) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Logs
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.digital-product-logs.update', $digitalProductLog) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Product <span class="text-danger">*</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control select2" name="product_id">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ (old('product_id', $digitalProductLog->product_id) == $product->id) ? 'selected' : '' }}>
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
                                    <textarea class="form-control summernote" name="log_item" rows="3" placeholder="Enter the log item content (e.g., account credentials, access codes, etc.)">{!! old('log_item', $digitalProductLog->log_item) !!}</textarea>
                                    <small class="form-text text-muted">This is the actual content that will be delivered to the customer when they purchase the product.</small>
                                    @error('log_item')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Details</label>
                                <div class="col-sm-12 col-md-7">
                                    <textarea class="form-control summernote" name="details" rows="3" placeholder="Additional details or notes about this log item (optional)">{!! old('details', $digitalProductLog->details) !!}</textarea>
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
                                        <option value="available" {{ old('status', $digitalProductLog->status) == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="sold" {{ old('status', $digitalProductLog->status) == 'sold' ? 'selected' : '' }}>Sold</option>
                                    </select>
                                    <small class="form-text text-muted">Change status to manage availability.</small>
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
                                        <input type="datetime-local" class="form-control" name="sold_at" value="{{ old('sold_at', $digitalProductLog->sold_at ? $digitalProductLog->sold_at->format('Y-m-d\TH:i') : '') }}">
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
                                                <option value="{{ $user->id }}" {{ old('sold_to_user_id', $digitalProductLog->sold_to_user_id) == $user->id ? 'selected' : '' }}>
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
                                        <i class="fas fa-save"></i> Update Log
                                    </button>
                                    <a href="{{ route('admin.digital-product-logs.show', $digitalProductLog) }}" class="btn btn-info me-2 mb-2">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    <a href="{{ route('admin.digital-product-logs.index', ['product_id' => $digitalProductLog->product_id]) }}" class="btn btn-secondary mb-2">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h4>Log Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-striped">
                                    <tr>
                                        <td><strong>Log ID</strong></td>
                                        <td>#{{ $digitalProductLog->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Product</strong></td>
                                        <td>
                                            <a href="{{ route('admin.digital-products.show', $digitalProductLog->product) }}" class="text-decoration-none">
                                                {{ $digitalProductLog->product->name }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Category</strong></td>
                                        <td>{{ $digitalProductLog->product->subcategory->category->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Subcategory</strong></td>
                                        <td>{{ $digitalProductLog->product->subcategory->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Product Price</strong></td>
                                        <td>{{ $settings->currency_icon }}{{ number_format($digitalProductLog->product->price, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-striped">
                                    <tr>
                                        <td><strong>Current Status</strong></td>
                                        <td>
                                            @if($digitalProductLog->status === 'available')
                                                <span class="badge badge-success">Available</span>
                                            @else
                                                <span class="badge badge-danger">Sold</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created</strong></td>
                                        <td>{{ $digitalProductLog->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last Updated</strong></td>
                                        <td>{{ $digitalProductLog->updated_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    @if($digitalProductLog->sold_at)
                                        <tr>
                                            <td><strong>Sold Date</strong></td>
                                            <td>{{ $digitalProductLog->sold_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    @endif
                                    @if($digitalProductLog->soldToUser)
                                        <tr>
                                            <td><strong>Sold To</strong></td>
                                            <td>{{ $digitalProductLog->soldToUser->name }} ({{ $digitalProductLog->soldToUser->email }})</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
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
        // Initialize Select2
        $('.select2').select2();
        
        // Initialize Summernote
        $('.summernote').summernote({
            height: 150,
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
        
        function toggleSoldFields() {
            if ($('select[name="status"]').val() === 'sold') {
                $('#soldFields').show();
            } else {
                $('#soldFields').hide();
                // Clear sold fields when hiding
                $('input[name="sold_at"]').val('');
                $('select[name="sold_to_user_id"]').val('').trigger('change');
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