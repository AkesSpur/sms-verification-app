@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>DaisySMS Services Management</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">DaisySMS Services</div>
        </div>
    </div>

    <div class="section-body">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="far fa-list-alt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Services</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['total_services'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="far fa-check-circle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Active Services</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['active_services'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="far fa-star"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Popular Services</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['popular_services'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="far fa-money-bill-alt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Active Prices</h4>
                        </div>
                        <div class="card-body">
                            {{ $stats['active_prices'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Sync Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-sync-alt"></i> Bulk Price Synchronization</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Bulk Sync:</strong> This will fetch all services from DaisySMS API and update prices for services that exist in your database. Exchange rates and markup percentages will be applied automatically.
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <p class="mb-2"><strong>What this does:</strong></p>
                                <ul class="mb-3">
                                    <li>Fetches all available services from DaisySMS API</li>
                                    <li>Checks which services exist in your database</li>
                                    <li>Updates prices for existing services only</li>
                                    <li>Applies current exchange rate and markup settings</li>
                                    <li>Skips services not found in your database</li>
                                </ul>
                            </div>
                            <div class="col-md-4 text-right">
                                <button type="button" class="btn btn-warning btn-lg" id="bulkSyncBtn">
                                    <i class="fas fa-sync-alt"></i> Start Bulk Sync
                                </button>
                            </div>
                        </div>
                        
                        <!-- Progress and Results -->
                        <div id="syncProgress" class="mt-3" style="display: none;">
                            <div class="progress mb-3">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                            <div id="syncStatus" class="text-center">
                                <i class="fas fa-spinner fa-spin"></i> Initializing sync...
                            </div>
                        </div>
                        
                        <div id="syncResults" class="mt-3" style="display: none;">
                            <div class="alert" id="syncAlert"></div>
                            <div id="syncDetails"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>All Services</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.daisy-services.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Service
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter Form -->
                        <form method="GET" action="{{ route('admin.daisy-services.index') }}" class="mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" name="search" class="form-control" placeholder="Search services..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select name="popular" class="form-control">
                                            <option value="">All Services</option>
                                            <option value="1" {{ request('popular') == '1' ? 'selected' : '' }}>Popular Only</option>
                                            <option value="0" {{ request('popular') == '0' ? 'selected' : '' }}>Non-Popular</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                    <a href="{{ route('admin.daisy-services.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-redo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Bulk Actions -->
                        <form id="bulk-action-form" method="POST" action="{{ route('admin.daisy-services.bulk-action') }}">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select name="action" class="form-control" required>
                                            <option value="">Select Bulk Action</option>
                                            <option value="activate">Activate Selected</option>
                                            <option value="deactivate">Deactivate Selected</option>
                                            <option value="mark_popular">Mark as Popular</option>
                                            <option value="unmark_popular">Remove from Popular</option>
                                            <option value="delete">Delete Selected</option>
                                        </select>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary" onclick="return confirmBulkAction()">
                                                <i class="fas fa-check"></i> Apply
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped" id="services-table">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="select-all">
                                            </th>
                                            <th>Code</th>
                                            <th>Service Name</th>
                                            <th>Description</th>
                                            <th>Prices</th>
                                            <th>Sort Order</th>
                                            <th>Status</th>
                                            <th>Popular</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($services as $service)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="services[]" value="{{ $service->id }}" class="service-checkbox">
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $service->code }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($service->icon)
                                                        <i class="{{ $service->icon }} mr-2"></i>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $service->name }}</strong>
                                                        @if($service->is_popular)
                                                            <span class="badge badge-warning ml-1">Popular</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ Str::limit($service->description, 50) }}</span>
                                            </td>
                                            <td>
                                                @if($service->servicePrices->count() > 0)
                                                    <span class="badge badge-success">{{ $service->servicePrices->count() }} countries</span>
                                                    <br>
                                                    <small class="text-muted">
                                                        ₦{{ number_format($service->servicePrices->min('price_naira'), 2) }} - 
                                                        ₦{{ number_format($service->servicePrices->max('price_naira'), 2) }}
                                                    </small>
                                                @else
                                                    <span class="badge badge-danger">No prices</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $service->sort_order }}</span>
                                            </td>
                                            <td>
                                                @if($service->status)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($service->is_popular)
                                                    <span class="badge badge-warning">Popular</span>
                                                @else
                                                    <span class="badge badge-light">Regular</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.daisy-services.show', $service) }}" class="btn btn-sm btn-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.daisy-services.edit', $service) }}" class="btn btn-sm btn-primary" title="Edit Service">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.daisy-services.manage-prices', $service) }}" class="btn btn-sm btn-warning" title="Manage Prices">
                                                        <i class="fas fa-dollar-sign"></i>
                                                    </a>
                                                    <a href="{{ route('admin.daisy-services.toggle-status', $service) }}" class="btn btn-sm {{ $service->status ? 'btn-danger' : 'btn-success' }}" title="{{ $service->status ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fas {{ $service->status ? 'fa-times' : 'fa-check' }}"></i>
                                                    </a>
                                                    <a href="{{ route('admin.daisy-services.toggle-popular', $service) }}" class="btn btn-sm {{ $service->is_popular ? 'btn-outline-warning' : 'btn-warning' }}" title="{{ $service->is_popular ? 'Remove from Popular' : 'Mark as Popular' }}">
                                                        <i class="fas fa-star"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteService({{ $service->id }})" title="Delete Service">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <div class="empty-state" data-height="400">
                                                    <div class="empty-state-icon">
                                                        <i class="fas fa-question"></i>
                                                    </div>
                                                    <h2>No services found</h2>
                                                    <p class="lead">You haven't created any services yet.</p>
                                                    <a href="{{ route('admin.daisy-services.create') }}" class="btn btn-primary mt-4">
                                                        <i class="fas fa-plus"></i> Create Your First Service
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </form>

                        <!-- Pagination -->
                        @if($services->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $services->appends(request()->query())->links() }}
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
        // Select all checkbox functionality
        $('#select-all').change(function() {
            $('.service-checkbox').prop('checked', this.checked);
        });

        // Update select all when individual checkboxes change
        $('.service-checkbox').change(function() {
            if ($('.service-checkbox:checked').length === $('.service-checkbox').length) {
                $('#select-all').prop('checked', true);
            } else {
                $('#select-all').prop('checked', false);
            }
        });
    });

    function confirmBulkAction() {
        const selectedServices = $('.service-checkbox:checked').length;
        if (selectedServices === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Selection',
                text: 'Please select at least one service.',
                confirmButtonColor: '#3085d6'
            });
            return false;
        }
        
        const action = $('select[name="action"]').val();
        if (!action) {
            Swal.fire({
                icon: 'warning',
                title: 'No Action Selected',
                text: 'Please select an action.',
                confirmButtonColor: '#3085d6'
            });
            return false;
        }
        
        const actionText = $('select[name="action"] option:selected').text();
        
        Swal.fire({
            title: 'Are you sure?',
            text: `You want to ${actionText.toLowerCase()} ${selectedServices} service(s)?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, proceed!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#bulk-action-form').submit();
            }
        });
        
        return false; // Prevent default form submission
    }

    function deleteService(serviceId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This service will be deleted permanently. This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/daisy-services/${serviceId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire({
                            title: 'Error!',
                            text: response ? response.message : 'An error occurred while deleting the service.',
                            icon: 'error',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            }
        });
    }

    // Bulk Sync Functionality
    $('#bulkSyncBtn').click(function() {
        const btn = $(this);
        const originalText = btn.html();
        
        // Show confirmation
        Swal.fire({
            title: 'Start Bulk Price Sync?',
            text: 'This will sync prices for all services that exist in your database. This may take a few minutes.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f39c12',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, start sync!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                startBulkSync();
            }
        });
    });

    function startBulkSync() {
        const btn = $('#bulkSyncBtn');
        const progressDiv = $('#syncProgress');
        const resultsDiv = $('#syncResults');
        const progressBar = $('.progress-bar');
        const statusDiv = $('#syncStatus');
        
        // Reset UI
        resultsDiv.hide();
        progressDiv.show();
        progressBar.css('width', '10%');
        statusDiv.html('<i class="fas fa-spinner fa-spin"></i> Starting bulk sync...');
        
        // Disable button
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Syncing...');
        
        // Start sync
        $.ajax({
            url: '{{ route("admin.daisy-services.bulk-sync-prices") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                progressBar.css('width', '100%');
                statusDiv.html('<i class="fas fa-check-circle text-success"></i> Sync completed!');
                
                setTimeout(() => {
                    progressDiv.hide();
                    showSyncResults(response);
                }, 1000);
            },
            error: function(xhr) {
                progressBar.css('width', '100%').removeClass('progress-bar-animated').addClass('bg-danger');
                statusDiv.html('<i class="fas fa-exclamation-triangle text-danger"></i> Sync failed!');
                
                setTimeout(() => {
                    progressDiv.hide();
                    const errorResponse = xhr.responseJSON;
                    showSyncResults({
                        success: false,
                        message: errorResponse ? errorResponse.message : 'An error occurred during sync'
                    });
                }, 1000);
            },
            complete: function() {
                // Re-enable button
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Start Bulk Sync');
            }
        });
    }

    function showSyncResults(response) {
        const resultsDiv = $('#syncResults');
        const alertDiv = $('#syncAlert');
        const detailsDiv = $('#syncDetails');
        
        if (response.success) {
            alertDiv.removeClass('alert-danger').addClass('alert-success');
            alertDiv.html(`<i class="fas fa-check-circle"></i> <strong>Success!</strong> ${response.message}`);
            
            if (response.summary) {
                let detailsHtml = `
                    <div class="row text-center mb-3">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="text-primary">${response.summary.total_services}</h5>
                                    <small>Total Services</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="text-success">${response.summary.synced_count}</h5>
                                    <small>Synced</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="text-info">0</h5>
                                    <small>Created</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="text-danger">${response.summary.error_count}</h5>
                                    <small>Errors</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                if (response.results && response.results.length > 0) {
                    detailsHtml += `
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Service Code</th>
                                        <th>Service Name</th>
                                        <th>Status</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    response.results.forEach(result => {
                        let statusBadge = '';
                        let details = '';
                        
                        switch(result.status) {
                            case 'synced':
                                statusBadge = '<span class="badge badge-success">Synced</span>';
                                details = result.message || 'Price updated successfully';
                                break;
                            case 'created':
                                statusBadge = '<span class="badge badge-info">Created</span>';
                                details = result.reason || 'New service created';
                                break;
                            case 'skipped':
                                statusBadge = '<span class="badge badge-warning">Skipped</span>';
                                details = result.reason || 'Service not in database';
                                break;
                            case 'error':
                                statusBadge = '<span class="badge badge-danger">Error</span>';
                                details = result.reason || 'Unknown error';
                                break;
                        }
                        
                        detailsHtml += `
                            <tr>
                                <td><code>${result.service_code}</code></td>
                                <td>${result.service_name}</td>
                                <td>${statusBadge}</td>
                                <td><small>${details}</small></td>
                            </tr>
                        `;
                    });
                    
                    detailsHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                }
                
                detailsDiv.html(detailsHtml);
            }
            
            // Page refresh removed - results are displayed without auto-refresh
            
        } else {
            alertDiv.removeClass('alert-success').addClass('alert-danger');
            alertDiv.html(`<i class="fas fa-exclamation-triangle"></i> <strong>Error!</strong> ${response.message}`);
            detailsDiv.html('');
        }
        
        resultsDiv.show();
    }
</script>
@endpush