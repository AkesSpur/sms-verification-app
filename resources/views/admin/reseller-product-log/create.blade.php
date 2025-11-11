@extends('admin.layouts.master')

@section('title', 'Create Reseller Log')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Create Reseller Log</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.reseller-product-logs.index') }}">Reseller Product Logs</a></div>
            <div class="breadcrumb-item">Create</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Log Details</h4>
                    </div>
                    <div class="card-body">
        <form method="POST" action="{{ route('admin.reseller-product-logs.store') }}">
            @csrf
            <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Product <span class="text-danger">*</span></label>
                <div class="col-sm-12 col-md-7">
                    <select name="product_id" class="form-control" required>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ ($selectedProductId ?? null) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
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
                    <textarea name="log_item" class="form-control summernote-log-item" rows="6" required>{{ old('log_item') }}</textarea>
                    @error('log_item')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Details (optional)</label>
                <div class="col-sm-12 col-md-7">
                    <textarea name="details" class="form-control summernote" rows="3">{{ old('details') }}</textarea>
                    @error('details')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-group row mb-4">
                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Status <span class="text-danger">*</span></label>
                <div class="col-sm-12 col-md-7">
                    <select name="status" class="form-control" required>
                        <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
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
                    <a href="{{ route('admin.reseller-product-logs.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Log</button>
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
    $('.select2').select2();

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

    $('.summernote-log-item').summernote({
        height: 150,
        toolbar: [
            ['font', ['bold', 'italic']],
            ['para', ['ul', 'ol']],
            ['insert', ['link']],
            ['view', ['codeview']]
        ]
    });
});
</script>
@endpush