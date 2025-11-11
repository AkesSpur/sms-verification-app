@extends('admin.layouts.master')

@section('title', 'Edit Reseller Product')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Edit Reseller Product</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.reseller-products.index') }}">Reseller Products</a></div>
            <div class="breadcrumb-item">Edit</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Product Details</h4>
                    </div>
                    <div class="card-body">
        <form method="POST" action="{{ route('admin.reseller-products.update', $resellerProduct) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ $resellerProduct->name }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Slug (optional)</label>
                <input type="text" name="slug" class="form-control" value="{{ $resellerProduct->slug }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                @if($resellerProduct->image)
                    <div class="mt-2">
                        <img src="{{ asset($resellerProduct->image) }}" alt="Current Image" class="img-thumbnail" style="max-height: 150px;">
                    </div>
                @endif
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="4">{{ $resellerProduct->description }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="number" step="0.01" name="price" class="form-control" value="{{ $resellerProduct->price }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="1" {{ $resellerProduct->status ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$resellerProduct->status ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ $resellerProduct->sort_order }}">
            </div>
            <div class="text-end">
                <a href="{{ route('admin.reseller-products.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Product</button>
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
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script>
    $(document).ready(function() {
        $('#description').summernote({
            height: 200,
            placeholder: 'Enter product description...',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });
</script>
@endpush