@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Service Details: {{ $daisyService->name }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="{{ route('admin.daisy-services.index') }}">DaisySMS Services</a></div>
            <div class="breadcrumb-item">{{ $daisyService->name }}</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <!-- Service Information -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Service Information</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.daisy-services.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Services
                            </a>
                            <a href="{{ route('admin.daisy-services.edit', $daisyService) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Service
                            </a>
                            <a href="{{ route('admin.daisy-services.manage-prices', $daisyService) }}" class="btn btn-warning">
                                <i class="fas fa-dollar-sign"></i> Manage Prices
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="font-weight-bold">Service Code:</td>
                                        <td><span class="badge badge-info badge-lg">{{ $daisyService->code }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Service Name:</td>
                                        <td>
                                            @if($daisyService->icon)
                                                <i class="{{ $daisyService->icon }} mr-2"></i>
                                            @endif
                                            {{ $daisyService->name }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Description:</td>
                                        <td>{{ $daisyService->description ?: 'No description provided' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Sort Order:</td>
                                        <td><span class="badge badge-secondary">{{ $daisyService->sort_order }}</span></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="font-weight-bold">Status:</td>
                                        <td>
                                            @if($daisyService->status)
                                                <span class="badge badge-success badge-lg">Active</span>
                                            @else
                                                <span class="badge badge-danger badge-lg">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Popular:</td>
                                        <td>
                                            @if($daisyService->is_popular)
                                                <span class="badge badge-warning badge-lg">Popular</span>
                                            @else
                                                <span class="badge badge-light badge-lg">Regular</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Created:</td>
                                        <td>{{ $daisyService->created_at->format('M d, Y H:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">Last Updated:</td>
                                        <td>{{ $daisyService->updated_at->format('M d, Y H:i A') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($daisyService->meta_data && is_array($daisyService->meta_data) && count($daisyService->meta_data) > 0)
                            <hr>
                            <h6>Additional Metadata:</h6>
                            <div class="row">
                                @foreach($daisyService->meta_data as $meta)
                                    <div class="col-md-6 mb-2">
                                        <strong>{{ $meta['key'] ?? 'Unknown' }}:</strong> {{ $meta['value'] ?? 'N/A' }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Service Prices -->
                <div class="card">
                    <div class="card-header">
                        <h4>Service Prices by Country</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.daisy-services.manage-prices', $daisyService) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Manage Prices
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($daisyService->servicePrices->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Country</th>
                                            <th>Code</th>
                                            <th>Price (USD)</th>
                                            <th>Price (NGN)</th>
                                            <th>Markup</th>
                                            <th>Status</th>
                                            <th>Updated</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($daisyService->servicePrices->sortBy('country_name') as $price)
                                        <tr>
                                            <td>
                                                <span class="flag-icon flag-icon-{{ strtolower($price->country_code) }} mr-1"></span>
                                                {{ $price->country_name }}
                                            </td>
                                            <td><span class="badge badge-secondary">{{ strtoupper($price->country_code) }}</span></td>
                                            <td>${{ number_format($price->price_usd, 3) }}</td>
                                            <td>₦{{ number_format($price->price_naira, 2) }}</td>
                                            <td>
                                                @if($price->markup_percentage > 0)
                                                    <span class="badge badge-info">+{{ $price->markup_percentage }}%</span>
                                                @else
                                                    <span class="badge badge-light">0%</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($price->status)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $price->updated_at->diffForHumans() }}</small>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state" data-height="300">
                                <div class="empty-state-icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <h2>No prices configured</h2>
                                <p class="lead">This service doesn't have any country-specific prices yet.</p>
                                <a href="{{ route('admin.daisy-services.manage-prices', $daisyService) }}" class="btn btn-primary mt-4">
                                    <i class="fas fa-plus"></i> Add Prices
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Stats -->
                <div class="card">
                    <div class="card-header">
                        <h4>Quick Statistics</h4>
                    </div>
                    <div class="card-body">
                        <div class="summary">
                            <div class="summary-info">
                                <h4 class="text-primary">{{ $daisyService->servicePrices->count() }}</h4>
                                <span class="text-muted">Total Countries</span>
                            </div>
                            <div class="summary-item">
                                <h6>Active Prices</h6>
                                <span class="text-success">{{ $daisyService->servicePrices->where('status', true)->count() }}</span>
                            </div>
                            <div class="summary-item">
                                <h6>Inactive Prices</h6>
                                <span class="text-danger">{{ $daisyService->servicePrices->where('status', false)->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Range -->
                @if($daisyService->servicePrices->where('status', true)->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h4>Price Range (Active)</h4>
                    </div>
                    <div class="card-body">
                        <div class="summary">
                            <div class="summary-info">
                                <h5 class="text-success">₦{{ number_format($daisyService->servicePrices->where('status', true)->min('price_naira'), 2) }}</h5>
                                <span class="text-muted">Cheapest Price</span>
                            </div>
                            <div class="summary-info">
                                <h5 class="text-warning">₦{{ number_format($daisyService->servicePrices->where('status', true)->max('price_naira'), 2) }}</h5>
                                <span class="text-muted">Most Expensive</span>
                            </div>
                            <div class="summary-info">
                                <h5 class="text-info">₦{{ number_format($daisyService->servicePrices->where('status', true)->avg('price_naira'), 2) }}</h5>
                                <span class="text-muted">Average Price</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Service Actions -->
                <div class="card">
                    <div class="card-header">
                        <h4>Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.daisy-services.edit', $daisyService) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-edit"></i> Edit Service
                            </a>
                            <a href="{{ route('admin.daisy-services.manage-prices', $daisyService) }}" class="btn btn-warning btn-block">
                                <i class="fas fa-dollar-sign"></i> Manage Prices
                            </a>
                            <a href="{{ route('admin.daisy-services.toggle-status', $daisyService) }}" class="btn {{ $daisyService->status ? 'btn-danger' : 'btn-success' }} btn-block">
                                <i class="fas {{ $daisyService->status ? 'fa-times' : 'fa-check' }}"></i> 
                                {{ $daisyService->status ? 'Deactivate' : 'Activate' }} Service
                            </a>
                            <a href="{{ route('admin.daisy-services.toggle-popular', $daisyService) }}" class="btn {{ $daisyService->is_popular ? 'btn-outline-warning' : 'btn-warning' }} btn-block">
                                <i class="fas fa-star"></i> 
                                {{ $daisyService->is_popular ? 'Remove from Popular' : 'Mark as Popular' }}
                            </a>
                            <button type="button" class="btn btn-danger btn-block" onclick="deleteService({{ $daisyService->id }})">
                                <i class="fas fa-trash"></i> Delete Service
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Service Usage Stats (if available) -->
                @if(isset($statistics) && is_array($statistics))
                <div class="card">
                    <div class="card-header">
                        <h4>Usage Statistics</h4>
                    </div>
                    <div class="card-body">
                        <div class="summary">
                            @foreach($statistics as $key => $value)
                            <div class="summary-item">
                                <h6>{{ ucwords(str_replace('_', ' ', $key)) }}</h6>
                                <span class="text-primary">{{ is_numeric($value) ? number_format($value) : $value }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    function deleteService(serviceId) {
        Swal.fire({
            title: 'Delete Service?',
            text: 'This action cannot be undone and will also delete all associated prices.',
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
                                window.location.href = '{{ route('admin.daisy-services.index') }}';
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
</script>
@endpush