@extends('admin.layouts.master')

@section('title', 'Add Reseller Logs')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Add Reseller Logs</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.reseller-product-logs.index') }}">Reseller Product
                        Logs</a></div>
                <div class="breadcrumb-item">Add Logs</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Multiple Log Items</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.reseller-product-logs.store-multiple') }}">
                                @csrf
                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Product <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-12 col-md-7">
                                        <select name="product_id" class="form-control select2" {{ request('product_id') ? 'disabled' : '' }}>
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ (old('product_id', $selectedProductId ?? null) == $product->id) ? 'selected' : '' }}>{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                        @if(request('product_id'))
                                            <input type="hidden" name="product_id" value="{{ request('product_id') }}">
                                        @endif
                                        @error('product_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Log Items <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-12 col-md-7">
                                        <div id="logItemsContainer">
                                            <div class="log-item-row mb-3" data-index="0">
                                                <div class="row">
                                                    <div class="col-11">
                                                        <textarea class="form-control summernote-log-item"
                                                            name="log_items[]" rows="3"
                                                            placeholder="Enter log item (e.g., username:password, API key, etc.)"></textarea>
                                                    </div>
                                                    <div class="col-1 d-flex align-items-start">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm remove-log-item mt-1">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @error('log_items')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
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
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Details
                                        (optional)</label>
                                    <div class="col-sm-12 col-md-7">
                                        <textarea name="details" class="form-control"
                                            rows="3">{{ old('details') }}</textarea>
                                        @error('details')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Status <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-12 col-md-7">
                                        <select name="status" class="form-control" required>
                                            <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>
                                                Available</option>
                                            <option value="sold" {{ old('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                                        </select>
                                        @error('status')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                    <div class="col-sm-12 col-md-7 text-right">
                                        <a href="{{ route('admin.reseller-product-logs.index') }}"
                                            class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Add Logs</button>
                                    </div>
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