@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Service Details</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Services</a></div>
            <div class="breadcrumb-item">{{ $service->name }}</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Service Information</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Service
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Name:</strong></td>
                                <td>{{ $service->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Code:</strong></td>
                                <td><code>{{ $service->code }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Base Price:</strong></td>
                                <td>${{ number_format($service->price, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Allow Refunds:</strong></td>
                                <td>
                                    @if($service->allow_refunds)
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Yes</span>
                                    @else
                                        <span class="badge badge-danger"><i class="fas fa-times"></i> No</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($service->status === 'active')
                                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Active</span>
                                    @else
                                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Created:</strong></td>
                                <td>{{ $service->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Last Updated:</strong></td>
                                <td>{{ $service->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Statistics</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="card card-statistic-1">
                                    <div class="card-icon bg-primary">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Total Orders</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $service->orders()->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card card-statistic-1">
                                    <div class="card-icon bg-success">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Countries</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $service->countries()->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card card-statistic-1">
                                    <div class="card-icon bg-warning">
                                        <i class="fas fa-ban"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Blacklisted Numbers</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $service->blacklistedNumbers()->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card card-statistic-1">
                                    <div class="card-icon bg-info">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Active Countries</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $service->activeCountries()->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if($service->countries()->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Country-Specific Pricing</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.country-service.index') }}" class="btn btn-primary">
                                <i class="fas fa-cog"></i> Manage Pricing
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Country</th>
                                        <th>Country Code</th>
                                        <th>Custom Price</th>
                                        <th>Status</th>
                                        <th>Last Updated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($service->countries as $country)
                                        <tr>
                                            <td>{{ $country->name }}</td>
                                            <td><code>{{ $country->code }}</code></td>
                                            <td>${{ number_format($country->pivot->price, 2) }}</td>
                                            <td>
                                                @if($country->pivot->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $country->pivot->updated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        @if($service->orders()->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Recent Orders</h4>
                        <div class="card-header-action">
                            <small class="text-muted">Showing last 10 orders</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Country</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($service->orders()->with(['user', 'country'])->latest()->limit(10)->get() as $order)
                                        <tr>
                                            <td><code>#{{ $order->id }}</code></td>
                                            <td>{{ $order->user->name ?? 'N/A' }}</td>
                                            <td>{{ $order->country->name ?? 'N/A' }}</td>
                                            <td>${{ number_format($order->price, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Service
                            </a>
                            
                            <form action="{{ route('admin.services.toggle-status', $service) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn {{ $service->status === 'active' ? 'btn-warning' : 'btn-success' }}">
                                    <i class="fas {{ $service->status === 'active' ? 'fa-pause' : 'fa-play' }}"></i>
                                    {{ $service->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            
                            @if($service->orders()->count() == 0 && $service->countries()->count() == 0)
                                <form action="{{ route('admin.services.destroy', $service) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this service? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Delete Service
                                    </button>
                                </form>
                            @endif
                        </div>
                        
                        @if($service->orders()->count() > 0 || $service->countries()->count() > 0)
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Note:</strong> This service cannot be deleted because it has associated orders or country pricing configurations.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection