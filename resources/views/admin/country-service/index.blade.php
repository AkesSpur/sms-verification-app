@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Country Service Pricing Management</h1>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Manage Service Prices by Country</h4>
                        <div class="card-header-action">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#bulkUpdateModal">
                                <i class="fas fa-edit"></i> Bulk Update
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="countrySelect">Select Country:</label>
                                <select id="countrySelect" class="form-control select2">
                                    <option value="">Choose a country...</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }} ({{ $country->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>&nbsp;</label><br>
                                <button id="loadPricesBtn" class="btn btn-info" disabled>
                                    <i class="fas fa-search"></i> Load Prices
                                </button>
                                <button id="syncApiBtn" class="btn btn-warning" disabled>
                                    <i class="fas fa-sync"></i> Sync from API
                                </button>
                            </div>
                        </div>

                        <div id="pricingTable" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Service</th>
                                            <th>Service Code</th>
                                            <th>Base Price</th>
                                            <th>Current Price</th>
                                            <th>Custom Price</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pricingTableBody">
                                        <!-- Dynamic content will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="loadingSpinner" style="display: none;" class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p>Loading pricing data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Edit Price Modal -->
<div class="modal fade" id="editPriceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Service Price</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editPriceForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_country_id" name="country_id">
                    <input type="hidden" id="edit_service_id" name="service_id">
                    
                    <div class="form-group">
                        <label>Service Name</label>
                        <input type="text" id="edit_service_name" class="form-control" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" id="edit_price" name="price" class="form-control" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" id="edit_is_active" name="is_active" class="custom-control-input" checked>
                            <label class="custom-control-label" for="edit_is_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Price</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Update Prices</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="bulkUpdateForm">
                <div class="modal-body">
                    <div id="bulkUpdateContent">
                        <p>Select a country first to enable bulk update functionality.</p>
                    </div>
                </div>
                <div class="modal-footer" id="bulkUpdateFooter" style="display: none;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update All Prices</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentCountryId = null;
    let pricingData = [];

    // Country selection change
    $('#countrySelect').change(function() {
        currentCountryId = $(this).val();
        if (currentCountryId) {
            $('#loadPricesBtn, #syncApiBtn').prop('disabled', false);
        } else {
            $('#loadPricesBtn, #syncApiBtn').prop('disabled', true);
            $('#pricingTable').hide();
        }
    });

    // Load prices for selected country
    $('#loadPricesBtn').click(function() {
        if (!currentCountryId) return;
        
        $('#loadingSpinner').show();
        $('#pricingTable').hide();
        
        $.get(`/admin/country-service/${currentCountryId}/prices`)
            .done(function(response) {
                pricingData = response.pricing_data;
                renderPricingTable(response.pricing_data);
                $('#pricingTable').show();
            })
            .fail(function() {
                toastr.error('Failed to load pricing data');
            })
            .always(function() {
                $('#loadingSpinner').hide();
            });
    });

    // Sync prices from API
    $('#syncApiBtn').click(function() {
        if (!currentCountryId) return;
        
        if (!confirm('This will sync prices from the API and may overwrite custom prices. Continue?')) {
            return;
        }
        
        $('#loadingSpinner').show();
        
        $.post(`/admin/country-service/${currentCountryId}/sync-api`)
            .done(function(response) {
                toastr.success(response.message);
                $('#loadPricesBtn').click(); // Reload the table
            })
            .fail(function() {
                toastr.error('Failed to sync prices from API');
            })
            .always(function() {
                $('#loadingSpinner').hide();
            });
    });

    // Render pricing table
    function renderPricingTable(data) {
        let tbody = $('#pricingTableBody');
        tbody.empty();
        
        data.forEach(function(item) {
            let statusBadge = item.is_active ? 
                '<span class="badge badge-success">Active</span>' : 
                '<span class="badge badge-danger">Inactive</span>';
            
            let customPriceDisplay = item.has_custom_pricing ? 
                `<span class="text-success">${item.custom_price}</span>` : 
                '<span class="text-muted">API Price</span>';
            
            let row = `
                <tr>
                    <td>${item.service_name}</td>
                    <td>${item.service_code}</td>
                    <td>${item.base_price}</td>
                    <td><strong>${item.current_price}</strong></td>
                    <td>${customPriceDisplay}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-price-btn" 
                                data-service-id="${item.service_id}"
                                data-service-name="${item.service_name}"
                                data-price="${item.custom_price || item.current_price}"
                                data-active="${item.is_active}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        ${item.has_custom_pricing ? `
                        <button class="btn btn-sm btn-danger remove-custom-btn" 
                                data-service-id="${item.service_id}">
                            <i class="fas fa-trash"></i> Remove Custom
                        </button>
                        ` : ''}
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // Edit price button click
    $(document).on('click', '.edit-price-btn', function() {
        let serviceId = $(this).data('service-id');
        let serviceName = $(this).data('service-name');
        let price = $(this).data('price');
        let isActive = $(this).data('active');
        
        $('#edit_country_id').val(currentCountryId);
        $('#edit_service_id').val(serviceId);
        $('#edit_service_name').val(serviceName);
        $('#edit_price').val(price);
        $('#edit_is_active').prop('checked', isActive);
        
        $('#editPriceModal').modal('show');
    });

    // Edit price form submission
    $('#editPriceForm').submit(function(e) {
        e.preventDefault();
        
        let formData = {
            country_id: $('#edit_country_id').val(),
            service_id: $('#edit_service_id').val(),
            price: $('#edit_price').val(),
            is_active: $('#edit_is_active').is(':checked')
        };
        
        $.post('/admin/country-service/update-price', formData)
            .done(function(response) {
                toastr.success(response.message);
                $('#editPriceModal').modal('hide');
                $('#loadPricesBtn').click(); // Reload the table
            })
            .fail(function(xhr) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).forEach(function(errorArray) {
                        errorArray.forEach(function(error) {
                            toastr.error(error);
                        });
                    });
                } else {
                    toastr.error('Failed to update price');
                }
            });
    });

    // Remove custom price
    $(document).on('click', '.remove-custom-btn', function() {
        if (!confirm('Remove custom pricing? This will revert to API pricing.')) {
            return;
        }
        
        let serviceId = $(this).data('service-id');
        
        $.ajax({
            url: '/admin/country-service/remove-price',
            method: 'DELETE',
            data: {
                country_id: currentCountryId,
                service_id: serviceId,
                _token: '{{ csrf_token() }}'
            }
        })
        .done(function(response) {
            toastr.success(response.message);
            $('#loadPricesBtn').click(); // Reload the table
        })
        .fail(function() {
            toastr.error('Failed to remove custom pricing');
        });
    });

    // Handle bulk update modal
    $('#bulkUpdateModal').on('show.bs.modal', function() {
        if (!currentCountryId || pricingData.length === 0) {
            $('#bulkUpdateContent').html('<p>Please select a country and load pricing data first.</p>');
            $('#bulkUpdateFooter').hide();
            return;
        }
        
        let content = `
            <div class="alert alert-info">
                <strong>Bulk Update Instructions:</strong> Modify the prices below and click "Update All Prices" to apply changes to all services.
            </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Current Price</th>
                            <th>New Price</th>
                            <th>Active</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        pricingData.forEach(function(item, index) {
            content += `
                <tr>
                    <td>${item.service_name}</td>
                    <td>${item.current_price}</td>
                    <td>
                        <input type="number" 
                               class="form-control form-control-sm bulk-price-input" 
                               data-service-id="${item.service_id}"
                               value="${item.custom_price || item.current_price}"
                               step="0.01" min="0" required>
                    </td>
                    <td>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" 
                                   class="custom-control-input bulk-active-input" 
                                   data-service-id="${item.service_id}"
                                   id="bulk_active_${item.service_id}" 
                                   ${item.is_active ? 'checked' : ''}>
                            <label class="custom-control-label" for="bulk_active_${item.service_id}"></label>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        content += `
                    </tbody>
                </table>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Apply percentage change to all prices:</label>
                    <div class="input-group">
                        <input type="number" id="percentageChange" class="form-control" placeholder="e.g., 10 for +10%, -5 for -5%" step="0.1">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="applyPercentageBtn">Apply %</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label>Set all services status:</label><br>
                    <button type="button" class="btn btn-sm btn-success" id="activateAllBtn">Activate All</button>
                    <button type="button" class="btn btn-sm btn-danger" id="deactivateAllBtn">Deactivate All</button>
                </div>
            </div>
        `;
        
        $('#bulkUpdateContent').html(content);
        $('#bulkUpdateFooter').show();
    });

    // Apply percentage change
    $(document).on('click', '#applyPercentageBtn', function() {
        let percentage = parseFloat($('#percentageChange').val());
        if (isNaN(percentage)) {
            toastr.error('Please enter a valid percentage');
            return;
        }
        
        $('.bulk-price-input').each(function() {
            let currentPrice = parseFloat($(this).val());
            let newPrice = currentPrice * (1 + percentage / 100);
            $(this).val(newPrice.toFixed(2));
        });
        
        toastr.success(`Applied ${percentage}% change to all prices`);
    });

    // Activate/Deactivate all
    $(document).on('click', '#activateAllBtn', function() {
        $('.bulk-active-input').prop('checked', true);
    });
    
    $(document).on('click', '#deactivateAllBtn', function() {
        $('.bulk-active-input').prop('checked', false);
    });

    // Bulk update form submission
    $('#bulkUpdateForm').submit(function(e) {
        e.preventDefault();
        
        if (!currentCountryId) {
            toastr.error('No country selected');
            return;
        }
        
        let prices = [];
        $('.bulk-price-input').each(function() {
            let serviceId = $(this).data('service-id');
            let price = parseFloat($(this).val());
            let isActive = $(`.bulk-active-input[data-service-id="${serviceId}"]`).is(':checked');
            
            prices.push({
                service_id: serviceId,
                price: price,
                is_active: isActive
            });
        });
        
        if (prices.length === 0) {
            toastr.error('No prices to update');
            return;
        }
        
        $.post('/admin/country-service/bulk-update', {
            country_id: currentCountryId,
            prices: prices,
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            toastr.success(response.message);
            $('#bulkUpdateModal').modal('hide');
            $('#loadPricesBtn').click(); // Reload the table
        })
        .fail(function(xhr) {
            let errors = xhr.responseJSON?.errors;
            if (errors) {
                Object.values(errors).forEach(function(errorArray) {
                    errorArray.forEach(function(error) {
                        toastr.error(error);
                    });
                });
            } else {
                toastr.error('Failed to update prices');
            }
        });
    });
});
</script>
@endpush