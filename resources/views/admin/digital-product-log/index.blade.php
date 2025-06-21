@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Digital Product Logs</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            @if(request('product_id'))
                <div class="breadcrumb-item"><a href="{{ route('admin.digital-products.index') }}">Digital Products</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.digital-products.show', request('product_id')) }}">{{ $product->name ?? 'Product' }}</a></div>
            @endif
            <div class="breadcrumb-item">Product Logs</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Manage Product Logs</h4>
                        <div class="card-header-action">
                            @if(request('product_id'))
                                <a href="{{ route('admin.digital-product-logs.create', ['product_id' => request('product_id')]) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add New Log
                                </a>
                                <a href="{{ route('admin.digital-product-logs.add-logs', ['product_id' => request('product_id')]) }}" class="btn btn-success">
                                    <i class="fas fa-plus-circle"></i> Bulk Add Logs
                                </a>
                            @else
                                <a href="{{ route('admin.digital-product-logs.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add New Log
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-3 mb-1">
                                <select class="form-control select2" id="productFilter">
                                    <option value="">All Products</option>
                                    @foreach($products as $prod)
                                        <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>
                                            {{ $prod->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-1">
                                <select class="form-control" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-1">
                                <input type="date" class="form-control" id="dateFilter" value="{{ request('date') }}" placeholder="Filter by date">
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-secondary" id="clearFilters">
                                    <i class="fas fa-times"></i> Clear Filters
                                </button>
                            </div>
                        </div>

                        @if($logs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-1">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Product</th>
                                            <th>Log Item</th>
                                            <th>Details</th>
                                            <th>Status</th>
                                            <th>Sold Date</th>
                                            <th>Sold To</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($logs as $log)
                                        <tr>
                                            <td class="text-center">{{ $log->id }}</td>
                                            <td>
                                                <a href="{{ route('admin.digital-products.show', $log->product) }}" class="text-decoration-none">
                                                    <strong>{{ $log->product->name }}</strong>
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $log->product->subcategory->name }}</small>
                                            </td>
                                            <td>
                                                <strong>{!! Str::limit(strip_tags($log->log_item), 50) !!}</strong>
                                                @if(strlen(strip_tags($log->log_item)) > 50)
                                                    <br><small class="text-muted" title="{{ strip_tags($log->log_item) }}">{!! Str::limit(strip_tags($log->log_item), 100) !!}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->details)
                                                    <span title="{{ strip_tags($log->details) }}">{!! Str::limit(strip_tags($log->details), 50) !!}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->status == 'available')
                                                    <span class="badge badge-success">Available</span>
                                                @else
                                                    <span class="badge badge-danger">Sold</span>
                                                @endif
                                            </td>
                                            {{-- <td>
                                                <span title="{{ $log->created_at->format('M d, Y H:i:s') }}">
                                                    {{ $log->created_at->format('M d, Y') }}
                                                </span>
                                            </td> --}}
                                            <td>
                                                @if($log->sold_at)
                                                    <span title="{{ $log->sold_at->format('M d, Y H:i:s') }}">
                                                        {{ $log->sold_at->format('M d, Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->soldToUser)
                                                    <span title="{{ $log->soldToUser->email ?? 'N/A' }}">
                                                        {{ $log->soldToUser->name ?? 'Unknown User' }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group-vertical btn-group-sm d-block d-md-none" role="group">
                                                    <a href="{{ route('admin.digital-product-logs.show', $log) }}" class="btn btn-primary btn-sm mb-1" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.digital-product-logs.edit', $log) }}" class="btn btn-warning btn-sm mb-1" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($log->status == 'sold')
                                                        <form action="{{ route('admin.digital-product-logs.mark-available', $log) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm mb-1" title="Mark as Available" onclick="return confirm('Are you sure you want to mark this log as available?')">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('admin.digital-product-logs.destroy', $log) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this log?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="btn-group d-none d-md-block" role="group">
                                                    <a href="{{ route('admin.digital-product-logs.show', $log) }}" class="btn btn-primary mb-1 btn-sm" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.digital-product-logs.edit', $log) }}" class="btn btn-warning mb-1 btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($log->status == 'sold')
                                                        <form action="{{ route('admin.digital-product-logs.mark-available', $log) }}" method="POST" class="d-inline mark-available-form">
                                                            @csrf
                                                            <button type="button" class="btn btn-success btn-sm mb-1 mark-available-btn" title="Mark as Available" data-log-id="{{ $log->id }}">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        @endif
                                                        <form action="{{ route('admin.digital-product-logs.destroy', $log) }}" method="POST" class="d-inline delete-log-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-danger btn-sm delete-log-btn" title="Delete" data-log-id="{{ $log->id }}">
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
                                                    
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-4x text-muted mb-4"></i>
                                <h5 class="text-muted">No logs found</h5>
                                <p class="text-muted">No product logs match your current filters.</p>
                                @if(request('product_id'))
                                    <a href="{{ route('admin.digital-product-logs.create', ['product_id' => request('product_id')]) }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add First Log
                                    </a>
                                @else
                                    <a href="{{ route('admin.digital-product-logs.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add New Log
                                    </a>
                                @endif
                            </div>
                        @endif
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
        // Check if DataTable is already initialized, if so destroy it first
        if ($.fn.DataTable.isDataTable('#table-1')) {
            $('#table-1').DataTable().destroy();
        }
        
        // Initialize DataTable with your custom settings
        $("#table-1").DataTable({
            "columnDefs": [
                { "sortable": false, "targets": [7] }
            ],
            "order": [[ 0, "desc" ]], // Sort by created date descending
            "pageLength": 25
        });
        
        // Filter functionality
        $('#productFilter, #statusFilter, #dateFilter').on('change', function() {
            applyFilters();
        });
        
        $('#clearFilters').on('click', function() {
            $('#productFilter').val('');
            $('#statusFilter').val('');
            $('#dateFilter').val('');
            applyFilters();
        });
        
        function applyFilters() {
            let params = new URLSearchParams();
            
            let productId = $('#productFilter').val();
            let status = $('#statusFilter').val();
            let date = $('#dateFilter').val();
            
            if (productId) params.append('product_id', productId);
            if (status) params.append('status', status);
            if (date) params.append('date', date);
            
            let url = '{{ route("admin.digital-product-logs.index") }}';
            if (params.toString()) {
                url += '?' + params.toString();
            }
            
            window.location.href = url;
        }
        // Modal logic
        function showModal(modalId, confirmCallback) {
            $(modalId).modal('show');
            $(modalId + ' .confirm-btn').off('click').on('click', function() {
                confirmCallback();
                $(modalId).modal('hide');
            });
            $(modalId + ' .cancel-btn').off('click').on('click', function() {
                $(modalId).modal('hide');
            });
        }
        $('.delete-log-btn').on('click', function() {
            var form = $(this).closest('form');
            showModal('#deleteLogModal', function() {
                form.submit();
            });
        });
        $('.mark-available-btn').on('click', function() {
            var form = $(this).closest('form');
            showModal('#markAvailableModal', function() {
                form.submit();
            });
        });
    });
</script>
<!-- Delete Log Modal -->
<div class="modal fade" id="deleteLogModal" tabindex="-1" role="dialog" aria-labelledby="deleteLogModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteLogModalLabel">Confirm Delete</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this log? This action cannot be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary cancel-btn" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger confirm-btn">Delete</button>
      </div>
    </div>
  </div>
</div>
<!-- Mark as Available Modal -->
<div class="modal fade" id="markAvailableModal" tabindex="-1" role="dialog" aria-labelledby="markAvailableModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="markAvailableModalLabel">Confirm Mark as Available</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to mark this log as available?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary cancel-btn" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success confirm-btn">Mark as Available</button>
      </div>
    </div>
  </div>
</div>
@endpush