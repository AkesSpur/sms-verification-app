@extends('admin.layouts.master')

@section('title', 'Reseller Product Logs')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Reseller Product Logs</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Reseller Product Logs</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Filter Logs</h4>
                        <div class="card-header-action">
                            @if(request('product_id'))
                                <a href="{{ route('admin.reseller-product-logs.add-logs', ['product_id' => request('product_id')]) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Logs
                                </a>
                            @else
                                <a href="{{ route('admin.reseller-product-logs.add-logs') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Logs
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3 mb-1">
                <select class="form-control select2" id="productFilter">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-1">
                <select class="form-control select2" id="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-secondary" id="clearFilters">
                    <i class="fas fa-times"></i> Clear Filters
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Product</th>
                        <th>Status</th>
                        <th>Sold To</th>
                        <th>Sold At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="text-center">{{ ($logs->currentPage() - 1) * $logs->perPage() + $loop->iteration }}</td>
                            <td>{{ $log->product->name ?? 'N/A' }}</td>
                            <td>
                                @if($log->status == 'available')
                                    <span class="badge badge-info">Available</span>
                                @else
                                    <span class="badge badge-success">Sold</span>
                                @endif
                            </td>
                            <td>{{ $log->soldToUser->name ?? '-' }}</td>
                            <td>{{ $log->sold_at ? $log->sold_at->format('Y-m-d H:i') : '-' }}</td>
                            <td>
                                <a href="{{ route('admin.reseller-product-logs.edit', $log) }}" class="btn btn-primary btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($log->status == 'available')
                                <form method="POST" action="{{ route('admin.reseller-product-logs.destroy', $log) }}" class="d-inline delete-item" onsubmit="return confirm('Delete this log?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-3">
                {{ $logs->links() }}
            </div>
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
        // Initialize Select2 for filters
        $('.select2').select2();
 
        // Filter functionality
        $('#productFilter, #statusFilter').on('change', function() {
            applyFilters();
        });
        
        $('#clearFilters').on('click', function() {
            $('#productFilter').val('');
            $('#statusFilter').val('');
            applyFilters();
        });
        
        function applyFilters() {
            let params = new URLSearchParams();
            
            let productId = $('#productFilter').val();
            let status = $('#statusFilter').val();
            
            if (productId) params.append('product_id', productId);
            if (status) params.append('status', status);
            
            let url = '{{ route("admin.reseller-product-logs.index") }}';
            if (params.toString()) {
                url += '?' + params.toString();
            }
            
            window.location.href = url;
        }
    });
</script>
@endpush
