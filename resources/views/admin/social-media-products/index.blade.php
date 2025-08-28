@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Social Media Products</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active">Social Media Products</div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>All Products</h4>
                    <div class="card-header-action">
                        <button type="button" class="btn btn-success me-2" onclick="testOwletConnection()">
                            <i class="fas fa-plug"></i> Test API Connection
                        </button>
                        <button type="button" class="btn btn-info me-2" onclick="syncOwletServices()">
                            <i class="fas fa-sync"></i> Sync Owlet Services
                        </button>
                        <div class="btn-group me-2" id="bulkActions" style="display: none;">
                            <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-edit"></i> Bulk Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="showBulkPriceModal()"><i class="fas fa-dollar-sign"></i> Update Prices</a></li>
                                <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus(1)"><i class="fas fa-check"></i> Activate Selected</a></li>
                                <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus(0)"><i class="fas fa-times"></i> Deactivate Selected</a></li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.social-media-products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Product
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select class="form-control" id="categoryFilter" onchange="filterByCategory()">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" id="statusFilter" onchange="filterByStatus()">
                                <option value="">All Status</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search products..." value="{{ request('search') }}">
                        </div>
                    </div>

                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="3%">
                                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                        </th>
                                        <th width="5%">#</th>
                                        <th width="20%">Product Name</th>
                                        <th width="15%">Category</th>
                                        <th width="10%">Price/1000</th>
                                        <th width="15%">Quantity Range</th>
                                        <th width="10%">Status</th>
                                        <th width="15%">Created</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="product-checkbox" value="{{ $product->id }}" onchange="toggleBulkActions()">
                                            </td>
                                            <td>{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
                                            <td>
                                                <strong>{{ $product->name }}</strong>
                                                <br>
                                                <small class="text-muted">{!! Str::limit($product->description, 50) !!}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $product->category->name }}</span>
                                            </td>
                                            <td>
                                                <strong class="text-success">₦{{ number_format($product->price_per_1000, 0) }}</strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    Min: {{ number_format($product->min_quantity) }}<br>
                                                    Max: {{ number_format($product->max_quantity) }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($product->status == 1)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $product->created_at->format('M d, Y') }}<br>
                                                    {{ $product->created_at->format('h:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.social-media-products.show', $product->id) }}" 
                                                       class="btn btn-sm btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.social-media-products.edit', $product->id) }}" 
                                                       class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="Delete"
                                                            onclick="deleteProduct({{ $product->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($products->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $products->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Products Found</h5>
                            @if(request()->hasAny(['category', 'status', 'search']))
                                <p class="text-muted">No products match your current filters.</p>
                                <a href="{{ route('admin.social-media-products.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear Filters
                                </a>
                            @else
                                <p class="text-muted">Start by creating your first social media product.</p>
                                <a href="{{ route('admin.social-media-products.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Product
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                <p class="text-warning"><strong>Warning:</strong> All orders for this product will be affected.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Price Update Modal -->
<div class="modal fade" id="bulkPriceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Price Update</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkPriceForm">
                    <div class="mb-3">
                        <label class="form-label">Update Type</label>
                        <select class="form-control" id="priceActionType" onchange="togglePriceInputLabel()">
                            <option value="percentage">Increase by Percentage</option>
                            <option value="fixed">Set Fixed Price</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" id="priceValueLabel">Percentage Increase (%)</label>
                        <input type="number" class="form-control" id="priceValue" min="0" step="0.01" required>
                        <small class="form-text text-muted" id="priceHelpText">Enter the percentage to increase prices (e.g., 10 for 10% increase)</small>
                    </div>
                    <div class="alert alert-info">
                        <strong>Selected Products:</strong> <span id="selectedCount">0</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="executeBulkPriceUpdate()">Update Prices</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testOwletConnection() {
        if (confirm('Test connection to Owlet API?')) {
            fetch('{{ route("admin.social-media-products.test-owlet-connection") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    if (response.status === 403) {
                        throw new Error('Access denied. Please check your admin permissions.');
                    } else if (response.status === 500) {
                        throw new Error('Server error occurred. Please check the logs.');
                    }
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    toastr.success(data.message + (data.data ? ' - Balance: ' + JSON.stringify(data.data) : ''));
                } else {
                    toastr.error(data.message || 'API connection test failed');
                }
            })
            .catch(error => {
                console.error('Error details:', error);
                toastr.error('Error: ' + error.message);
            });
        }
    }

    function syncOwletServices() {
        if (confirm('This will sync services from Owlet API and create new products. Continue?')) {
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';
        button.disabled = true;
        
        fetch('{{ route("admin.social-media-products.sync-owlet-services") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                if (response.status === 403) {
                    throw new Error('Access denied. Please ensure you are logged in as an admin.');
                }
                if (response.status === 500) {
                    throw new Error('Server error occurred. Check the Laravel logs for details.');
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                toastr.success(data.message);
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                console.error('Sync failed:', data);
                toastr.error(data.message || 'Failed to sync services');
            }
        })
        .catch(error => {
            console.error('Sync error details:', error);
            console.error('Error stack:', error.stack);
            toastr.error(error.message || 'An error occurred while syncing services');
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}

function deleteProduct(productId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/admin/social-media-products/${productId}`;
    $('#deleteModal').modal('show');
}

function filterByCategory() {
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchInput').value;
    
    updateFilters(category, status, search);
}

function filterByStatus() {
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchInput').value;
    
    updateFilters(category, status, search);
}

function updateFilters(category, status, search) {
    const url = new URL(window.location.href);
    
    if (category) {
        url.searchParams.set('category', category);
    } else {
        url.searchParams.delete('category');
    }
    
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    
    if (search) {
        url.searchParams.set('search', search);
    } else {
        url.searchParams.delete('search');
    }
    
    window.location.href = url.toString();
}

// Search with debounce
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const category = document.getElementById('categoryFilter').value;
        const status = document.getElementById('statusFilter').value;
        const search = this.value;
        
        updateFilters(category, status, search);
    }, 500);
});

// Bulk operations functions
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    toggleBulkActions();
}

function toggleBulkActions() {
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    
    if (checkboxes.length > 0) {
        bulkActions.style.display = 'block';
    } else {
        bulkActions.style.display = 'none';
    }
    
    // Update select all checkbox state
    const allCheckboxes = document.querySelectorAll('.product-checkbox');
    const selectAll = document.getElementById('selectAll');
    selectAll.checked = checkboxes.length === allCheckboxes.length;
}

function getSelectedProductIds() {
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function showBulkPriceModal() {
    const selectedIds = getSelectedProductIds();
    if (selectedIds.length === 0) {
        toastr.warning('Please select at least one product');
        return;
    }
    
    document.getElementById('selectedCount').textContent = selectedIds.length;
    $('#bulkPriceModal').modal('show');
}

function togglePriceInputLabel() {
    const actionType = document.getElementById('priceActionType').value;
    const label = document.getElementById('priceValueLabel');
    const helpText = document.getElementById('priceHelpText');
    const input = document.getElementById('priceValue');
    
    if (actionType === 'percentage') {
        label.textContent = 'Percentage Increase (%)';
        helpText.textContent = 'Enter the percentage to increase prices (e.g., 10 for 10% increase)';
        input.placeholder = 'e.g., 10';
    } else {
        label.textContent = 'Fixed Price (₦)';
        helpText.textContent = 'Enter the new fixed price for all selected products';
        input.placeholder = 'e.g., 1000';
    }
    
    input.value = '';
}

function executeBulkPriceUpdate() {
    const selectedIds = getSelectedProductIds();
    const actionType = document.getElementById('priceActionType').value;
    const value = document.getElementById('priceValue').value;
    
    if (!value || value <= 0) {
        toastr.error('Please enter a valid value');
        return;
    }
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    button.disabled = true;
    
    fetch('{{ route("admin.social-media-products.bulk-update-prices") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            action_type: actionType,
            value: parseFloat(value),
            product_ids: selectedIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            toastr.error(data.message || 'Failed to update prices');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('An error occurred while updating prices');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        $('#bulkPriceModal').modal('hide');
    });
}

function bulkUpdateStatus(status) {
    const selectedIds = getSelectedProductIds();
    if (selectedIds.length === 0) {
        toastr.warning('Please select at least one product');
        return;
    }
    
    const statusText = status == 1 ? 'activate' : 'deactivate';
    if (!confirm(`Are you sure you want to ${statusText} ${selectedIds.length} selected products?`)) {
        return;
    }
    
    fetch('{{ route("admin.social-media-products.bulk-update-status") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            status: status,
            product_ids: selectedIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            toastr.error(data.message || 'Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('An error occurred while updating status');
    });
}
</script>
@endpush