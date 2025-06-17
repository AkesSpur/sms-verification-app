@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Bulk Add Product Logs</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.digital-product-logs.index') }}">Product Logs</a></div>
            <div class="breadcrumb-item">Bulk Add</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Add Multiple Logs</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.digital-product-logs.index', request()->only('product_id')) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Logs
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.digital-product-logs.store-multiple') }}" method="POST">
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
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Log Items <span class="text-danger">*</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <div id="logItemsContainer">
                                        <div class="log-item-row mb-3" data-index="0">
                                            <div class="row">
                                                <div class="col-11">
                                                    <textarea class="form-control summernote-log-item" name="log_items[]" 
                                                              placeholder="Enter log item (e.g., username:password, API key, etc.)" rows="3"></textarea>
                                                </div>
                                                <div class="col-1 d-flex align-items-start">
                                                    <button type="button" class="btn btn-danger btn-sm remove-log-item mt-1">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                <div class="col-sm-12 col-md-7">
                                    <button type="button" class="btn btn-success btn-sm" id="addLogItem">
                                        <i class="fas fa-plus"></i> Add Another Log Item
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" id="addMultipleItems">
                                        <i class="fas fa-layer-group"></i> Add Multiple (5)
                                    </button>
                                    <span class="ml-3 text-muted" id="itemCount">1 item</span>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Common Details</label>
                                <div class="col-sm-12 col-md-7">
                                    <textarea class="form-control summernote" name="details" rows="3" placeholder="Optional: Common details that will be applied to all log items">{{ old('details') }}</textarea>
                                    <small class="form-text text-muted">This text will be added as details to all created log items.</small>
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
                                    <small class="form-text text-muted">All created log items will have this status.</small>
                                    @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                <div class="col-sm-12 col-md-7">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus-circle"></i> Create All Logs
                                    </button>
                                    <button type="button" class="btn btn-info ml-2" id="previewBtn">
                                        <i class="fas fa-eye"></i> Preview
                                    </button>
                                    <a href="{{ route('admin.digital-product-logs.index', request()->only('product_id')) }}" class="btn btn-secondary ml-2">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Preview Section -->
                <div class="card" id="previewCard" style="display: none;">
                    <div class="card-header">
                        <h4>Preview Log Items</h4>
                    </div>
                    <div class="card-body">
                        <div id="previewContent">
                            <!-- Preview content will be populated by JavaScript -->
                        </div>
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
                                                <td>${{ number_format($selectedProduct->price, 2) }}</td>
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
        // Initialize Select2
        $('.select2').select2();
        
        // Initialize Summernote for existing textareas
        $('.summernote').summernote({
            height: 120,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
        
        // Initialize Summernote for log item textareas with reduced height
        $('.summernote-log-item').summernote({
            height: 100,
            toolbar: [
                ['font', ['bold', 'italic']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ]
        });
        
        // Preview functionality
        $('#previewBtn').on('click', function() {
            const logItemTextareas = $('textarea[name="log_items[]"]');
            const details = $('textarea[name="details"]').summernote('code');
            const status = $('select[name="status"]').val();
            
            let logItems = [];
            logItemTextareas.each(function() {
                const content = $(this).summernote('code').trim();
                if (content && content !== '<p><br></p>') {
                    logItems.push(content);
                }
            });
            
            if (logItems.length === 0) {
                alert('Please enter some log items to preview.');
                return;
            }
            
            let previewHtml = `
                <div class="alert alert-info">
                    <strong>Preview:</strong> ${logItems.length} log item(s) will be created with status: <span class="badge badge-${status === 'available' ? 'success' : 'danger'}">${status}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Log Item</th>
                                <th>Details</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            logItems.forEach((item, index) => {
                previewHtml += `
                    <tr>
                        <td>${index + 1}</td>
                        <td><div>${item}</div></td>
                        <td>${details && details !== '<p><br></p>' ? '<div>' + details + '</div>' : '<span class="text-muted">-</span>'}</td>
                        <td><span class="badge badge-${status === 'available' ? 'success' : 'danger'}">${status}</span></td>
                    </tr>
                `;
            });
            
            previewHtml += `
                        </tbody>
                    </table>
                </div>
            `;
            
            $('#previewContent').html(previewHtml);
            $('#previewCard').show();
            
            // Scroll to preview
            $('html, body').animate({
                scrollTop: $('#previewCard').offset().top - 100
            }, 500);
        });
    });

    $(document).ready(function() {
    let itemIndex = 1;
    
    // Add single log item
    $('#addLogItem').on('click', function() {
        addLogItemRow();
    });
    
    // Add multiple log items
    $('#addMultipleItems').on('click', function() {
        for(let i = 0; i < 5; i++) {
            addLogItemRow();
        }
    });
    
    // Remove log item
    $(document).on('click', '.remove-log-item', function() {
        if ($('.log-item-row').length > 1) {
            const $row = $(this).closest('.log-item-row');
            const $textarea = $row.find('.summernote-log-item');
            $textarea.summernote('destroy');
            $row.remove();
            updateItemCount();
        }
    });
    
    function addLogItemRow() {
        const newRow = `
            <div class="log-item-row mb-3" data-index="${itemIndex}">
                <div class="row">
                    <div class="col-11">
                        <textarea class="form-control summernote-log-item" name="log_items[]" 
                                  placeholder="Enter log item (e.g., username:password, API key, etc.)" rows="3"></textarea>
                    </div>
                    <div class="col-1 d-flex align-items-start">
                        <button type="button" class="btn btn-danger btn-sm remove-log-item mt-1">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#logItemsContainer').append(newRow);
        
        // Initialize Summernote for the new textarea
        const $newTextarea = $(`.log-item-row[data-index="${itemIndex}"] .summernote-log-item`);
        $newTextarea.summernote({
            height: 100,
            toolbar: [
                ['font', ['bold', 'italic']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ]
        });
        
        itemIndex++;
        updateItemCount();
    }
    
    function updateItemCount() {
        const count = $('.log-item-row').length;
        $('#itemCount').text(count + (count === 1 ? ' item' : ' items'));
    }
});
</script>
@endpush