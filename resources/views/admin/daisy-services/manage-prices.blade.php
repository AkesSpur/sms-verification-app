@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Manage Prices: {{ $daisyService->name }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="{{ route('admin.daisy-services.index') }}">DaisySMS Services</a></div>
            <div class="breadcrumb-item active"><a href="{{ route('admin.daisy-services.show', $daisyService) }}">{{ $daisyService->name }}</a></div>
            <div class="breadcrumb-item">Manage Prices</div>
        </div>
    </div>

    <div class="section-body">
        <!-- Service Info Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-0">
                                    @if($daisyService->icon)
                                        <i class="{{ $daisyService->icon }} mr-2"></i>
                                    @endif
                                    {{ $daisyService->name }} 
                                    <span class="badge badge-info ml-2">{{ $daisyService->code }}</span>
                                </h5>
                                <p class="text-muted mb-0">{{ $daisyService->description ?: 'No description available' }}</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ route('admin.daisy-services.show', $daisyService) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Service
                                </a>
                                <button type="button" class="btn btn-info" onclick="syncPricesFromAPI()">
                                    <i class="fas fa-sync"></i> Sync from API
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Prices Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Country Prices</h4>
                        <div class="card-header-action">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search countries..." id="searchCountries">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="pricesTable">
                                <thead>
                                    <tr>
                                        <th>Country</th>
                                        <th>Code</th>
                                        <th>Original Price (USD)</th>
                                        <th>Markup %</th>
                                        <th>Final Price (USD)</th>
                                        <th>Price (NGN)</th>
                                        <th>Status</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($prices as $price)
                                    <tr data-country-id="{{ $price->id }}">
                                        <td>
                                            <span class="flag-icon flag-icon-{{ strtolower($price->country_code) }} mr-2"></span>
                                            {{ $price->country_name }}
                                        </td>
                                        <td><span class="badge badge-secondary">{{ strtoupper($price->country_code) }}</span></td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm original-price" 
                                                   value="{{ $price->original_price_usd }}" 
                                                   step="0.001" 
                                                   data-price-id="{{ $price->id }}" 
                                                   style="width: 100px;">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm markup-percentage" 
                                                   value="{{ $price->markup_percentage }}" 
                                                   step="0.1" 
                                                   data-price-id="{{ $price->id }}" 
                                                   style="width: 80px;">
                                        </td>
                                        <td>
                                            <span class="final-price-usd" data-price-id="{{ $price->id }}">
                                                ${{ number_format($price->price_usd, 3) }}
                                            </span>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm price-naira" 
                                                   value="{{ $price->price_naira }}" 
                                                   step="0.01" 
                                                   data-price-id="{{ $price->id }}" 
                                                   style="width: 120px;"
                                                   placeholder="Naira price">
                                        </td>
                                        <td>
                                            <div class="custom-switch custom-switch-text custom-switch-color custom-control">
                                                <input type="checkbox" class="custom-control-input status-toggle" 
                                                       id="switch-{{ $price->id }}" 
                                                       data-price-id="{{ $price->id }}" 
                                                       {{ $price->status ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="switch-{{ $price->id }}">
                                                    <span class="switch-text-left">Active</span>
                                                    <span class="switch-text-right">Inactive</span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $price->updated_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-success save-price" data-price-id="{{ $price->id }}">
                                                <i class="fas fa-save"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger ml-1 delete-price" data-price-id="{{ $price->id }}" data-country-name="{{ $price->country_name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <div class="empty-state" data-height="200">
                                                <div class="empty-state-icon">
                                                    <i class="fas fa-dollar-sign"></i>
                                                </div>
                                                <h2>No prices found</h2>
                                                <p class="lead">Click "Sync from API" to fetch prices from DaisySMS.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
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
        // Initialize DataTable
        $('#pricesTable').DataTable({
            "columnDefs": [
                { "orderable": false, "targets": [8] }
            ],
            "order": [[ 0, "asc" ]],
            "pageLength": 25,
            "searching": false // We'll use custom search
        });

        // Custom search functionality
        $('#searchCountries').on('keyup', function() {
            $('#pricesTable').DataTable().search(this.value).draw();
        });



        // Real-time price calculation
        $('.original-price, .markup-percentage').on('input', function() {
            const priceId = $(this).data('price-id');
            calculatePrice(priceId);
        });
        
        // Real-time Naira price calculation
        $('.price-naira').on('input', function() {
            const priceId = $(this).data('price-id');
            calculateFromNaira(priceId);
        });

        // Status toggle
        $('.status-toggle').change(function() {
            const priceId = $(this).data('price-id');
            const status = $(this).is(':checked') ? 1 : 0;
            
            updatePriceStatus(priceId, status);
        });

        // Save individual price
        $('.save-price').click(function() {
            const priceId = $(this).data('price-id');
            savePriceChanges(priceId);
        });

        // Delete individual price
        $('.delete-price').click(function() {
            const priceId = $(this).data('price-id');
            const countryName = $(this).data('country-name');
            deletePriceConfirm(priceId, countryName);
        });
    });

    function calculatePrice(priceId) {
        const originalPrice = parseFloat($(`.original-price[data-price-id="${priceId}"]`).val()) || 0;
        const markupPercentage = parseFloat($(`.markup-percentage[data-price-id="${priceId}"]`).val()) || 0;
        
        const finalPriceUsd = originalPrice * (1 + markupPercentage / 100);
        const finalPriceNaira = finalPriceUsd * {{ config('app.usd_to_naira_rate', 1650) }}; // You can make this dynamic
        
        $(`.final-price-usd[data-price-id="${priceId}"]`).text('$' + finalPriceUsd.toFixed(3));
        $(`.price-naira[data-price-id="${priceId}"]`).val(finalPriceNaira.toFixed(2));
    }
    
    function calculateFromNaira(priceId) {
        const nairaPrice = parseFloat($(`.price-naira[data-price-id="${priceId}"]`).val()) || 0;
        const markupPercentage = parseFloat($(`.markup-percentage[data-price-id="${priceId}"]`).val()) || 0;
        const exchangeRate = {{ config('app.usd_to_naira_rate', 1650) }};
        
        const finalPriceUsd = nairaPrice / exchangeRate;
        const originalPrice = finalPriceUsd / (1 + markupPercentage / 100);
        
        $(`.final-price-usd[data-price-id="${priceId}"]`).text('$' + finalPriceUsd.toFixed(3));
        $(`.original-price[data-price-id="${priceId}"]`).val(originalPrice.toFixed(4));
    }

    function savePriceChanges(priceId) {
        const originalPrice = $(`.original-price[data-price-id="${priceId}"]`).val();
        const markupPercentage = $(`.markup-percentage[data-price-id="${priceId}"]`).val();
        const nairaPrice = $(`.price-naira[data-price-id="${priceId}"]`).val();
        const status = $(`.status-toggle[data-price-id="${priceId}"]`).is(':checked') ? 1 : 0;
        
        $.ajax({
            url: '{{ route("admin.daisy-services.update-price", $daisyService) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                price_id: priceId,
                original_price_usd: originalPrice,
                price_naira: nairaPrice,
                markup_percentage: markupPercentage,
                status: status
            },
            success: function(response) {
                if (response.success === true) {
                    toastr.success('Price updated successfully!');
                    // Update the displayed values with server response
                    if (response.data) {
                        $(`.original-price[data-price-id="${priceId}"]`).val(response.data.original_price_usd);
                        $(`.final-price-usd[data-price-id="${priceId}"]`).text('$' + response.data.final_price_usd.toFixed(3));
                        $(`.price-naira[data-price-id="${priceId}"]`).val(response.data.final_price_naira.toFixed(2));
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response ? response.message : 'An error occurred while updating the price.');
            }
        });
    }

    function updatePriceStatus(priceId, status) {
        $.ajax({
            url: '{{ route("admin.daisy-services.toggle-price-status", $daisyService) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                price_id: priceId,
                status: status
            },
            success: function(response) {
                if (response.success === true) {
                    toastr.success('Status updated successfully!');
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response ? response.message : 'An error occurred while updating the status.');
            }
        });
    }



    function syncPricesFromAPI() {
        Swal.fire({
            title: 'Sync Prices from API?',
            text: 'This will fetch the latest prices from DaisySMS API and may overwrite existing prices. Continue?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, sync prices!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.daisy-services.sync-prices", $daisyService) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        toastr.info('Syncing prices from API...');
                    },
                    success: function(response) {
                        if (response.success === true) {
                            Swal.fire({
                                title: 'Success!',
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
                            text: response ? response.message : 'An error occurred while syncing prices.',
                            icon: 'error',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            }
        });
    }
        function deletePriceConfirm(priceId, countryName) {
            Swal.fire({
                title: 'Delete Price?',
                text: `Are you sure you want to delete the price for ${countryName}? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deletePrice(priceId);
                }
            });
        }

        function deletePrice(priceId) {
            $.ajax({
                url: '{{ route("admin.daisy-services.delete-price", ":priceId") }}'.replace(':priceId', priceId),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success === true) {
                        toastr.success(response.message);
                        // Remove the row from the table
                        $(`tr[data-country-id="${priceId}"]`).fadeOut(300, function() {
                            $(this).remove();
                            // Update DataTable if no rows left
                            if ($('#pricesTable tbody tr:visible').length === 0) {
                                location.reload();
                            }
                        });
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response ? response.message : 'An error occurred while deleting the price.');
                }
            });
        }
    </script>
@endpush