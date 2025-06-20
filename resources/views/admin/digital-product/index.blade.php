@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Digital Products</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Digital Products</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>All Products</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.digital-products.create') }}" class="btn btn-primary">
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
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Subcategory</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Sort Order</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            @if($product->image)
                                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 4px;">
                                                    <i class="fas fa-box text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $product->name }}</strong>
                                            <br><code class="small">{{ $product->slug }}</code>
                                            {{-- @if($product->description)
                                                <br><small class="text-muted">{!! Str::limit($product->description, 50) !!}</small>
                                            @endif --}}
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $product->subcategory->category->name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $product->subcategory->name }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $settings->currency_icon }}{{ number_format($product->price, 2) }}</strong>
                                        </td>
                                        <td>
                                            @if($product->available_stock > 0)
                                                <span class="badge badge-success">{{ $product->available_stock }} available</span>
                                            @else
                                                <span class="badge badge-danger">Out of stock</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->status)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $product->sort_order }}</td>
                                        <td>
                                            <div class="btn-group-vertical btn-group-sm d-md-none" role="group">
                                                <a href="{{ route('admin.digital-products.show', $product) }}" class="btn btn-info btn-sm mb-1" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.digital-products.edit', $product) }}" class="btn btn-primary btn-sm mb-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.digital-product-logs.index', ['product_id' => $product->id]) }}" class="btn btn-warning btn-sm mb-1" title="Manage Logs">
                                                    <i class="fas fa-list-alt"></i>
                                                </a>
                                                <form action="{{ route('admin.digital-products.destroy', $product) }}" method="POST" class="delete-item">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="d-none d-md-block">
                                                <a href="{{ route('admin.digital-products.show', $product) }}" class="btn btn-info btn-sm me-1" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.digital-products.edit', $product) }}" class="btn btn-primary btn-sm me-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.digital-product-logs.index', ['product_id' => $product->id]) }}" class="btn btn-warning btn-sm me-1" title="Manage Logs">
                                                    <i class="fas fa-list-alt"></i>
                                                </a>
                                                <form action="{{ route('admin.digital-products.destroy', $product) }}" method="POST" class="d-inline delete-item">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
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
        // Replace the existing DataTable initialization with:
        if ($.fn.DataTable.isDataTable('#table-1')) {
            $('#table-1').DataTable().destroy();
        }
        
        $('#table-1').DataTable({
            "columnDefs": [
                { "sortable": false, "targets": [8] }
            ],
            "order": [[ 7, "asc" ]] // Sort by sort order
        });
    });
</script>
@endpush