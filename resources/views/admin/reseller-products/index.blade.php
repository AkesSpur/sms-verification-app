@extends('admin.layouts.master')

@section('title', 'Reseller Products')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Reseller Products</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Reseller Products</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>All Products</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.reseller-products.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Product
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="table-1">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Sort Order</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                @if($product->description)
                                    <br><small class="text-muted">{!! Str::limit($product->description, 50) !!}</small>
                                @endif
                            </td>
                            <td>
                                @if($product->image)
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-height: 60px;">
                                @else
                                    <span class="text-muted">No image</span>
                                @endif
                            </td>
                            <td>₦{{ number_format($product->price, 2) }}</td>
                            <td>{{ $product->available_stock }}</td>
                            <td>
                                @if($product->status)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $product->sort_order }}</td>
                            <td>
                                <a href="{{ route('admin.reseller-products.edit', $product) }}" class="btn btn-primary btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.reseller-product-logs.add-logs', ['product_id' => $product->id]) }}" class="btn btn-info btn-sm" title="Add Logs">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.reseller-products.destroy', $product) }}" class="d-inline delete-item" onsubmit="return confirm('Delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No reseller products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $products->links() }}
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
        // Initialize DataTable if needed
        if ($.fn.DataTable.isDataTable('#table-1')) {
            $('#table-1').DataTable().destroy();
        }
        
        $('#table-1').DataTable({
            "columnDefs": [
                { "sortable": false, "targets": [7] } // Disable sorting on Action column
            ],
            "order": [[ 0, "asc" ]] // Sort by first column (iteration)
        });
    });
</script>
@endpush