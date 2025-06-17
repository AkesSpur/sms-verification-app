@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Digital Product Subcategories</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Digital Product Subcategories</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>All Subcategories</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.digital-product-subcategories.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create New
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Slug</th>
                                        <th>Products</th>
                                        <th>Status</th>
                                        <th>Sort Order</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subcategories as $subcategory)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            @if($subcategory->image)
                                                <img src="{{ asset($subcategory->image) }}" alt="{{ $subcategory->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 4px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $subcategory->name }}</strong>
                                            @if($subcategory->description)
                                                <br><small class="text-muted">{{ Str::limit($subcategory->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $subcategory->category->name }}</span>
                                        </td>
                                        <td><code>{{ $subcategory->slug }}</code></td>
                                        <td>
                                            <span class="badge badge-info">{{ $subcategory->products->count() }} products</span>
                                        </td>
                                        <td>
                                            @if($subcategory->status)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $subcategory->sort_order }}</td>
                                        <td>
                                            <a href="{{ route('admin.digital-product-subcategories.edit', $subcategory) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.digital-products.index', ['subcategory_id' => $subcategory->id]) }}" class="btn btn-info btn-sm" title="View Products">
                                                <i class="fas fa-box"></i>
                                            </a>
                                            <form action="{{ route('admin.digital-product-subcategories.destroy', $subcategory) }}" method="POST" class="d-inline delete-item">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
        $('#table-1').DataTable({
            "columnDefs": [
                { "sortable": false, "targets": [1, 8] }
            ]
        });
    });
</script>
@endpush