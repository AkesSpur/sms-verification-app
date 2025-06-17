@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Product Details</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.digital-products.index') }}">Digital Products</a></div>
            <div class="breadcrumb-item">{{ $digitalProduct->name }}</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Product Information</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.digital-products.edit', $digitalProduct) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Product
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($digitalProduct->image)
                            <div class="text-center mb-4">
                                <img src="{{ asset($digitalProduct->image) }}" alt="{{ $digitalProduct->name }}" class="img-thumbnail" style="max-width: 300px; max-height: 300px;">
                            </div>
                        @endif
                        
                        <table class="table table-striped">
                            <tr>
                                <td><strong>Name</strong></td>
                                <td>{{ $digitalProduct->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Slug</strong></td>
                                <td><code>{{ $digitalProduct->slug }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Category</strong></td>
                                <td><span class="badge badge-primary">{{ $digitalProduct->subcategory->category->name }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Subcategory</strong></td>
                                <td><span class="badge badge-info">{{ $digitalProduct->subcategory->name }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Price</strong></td>
                                <td><strong class="text-success">{{ $settings->currency_icon }}{{ number_format($digitalProduct->price, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Available Stock</strong></td>
                                <td>
                                    @if($digitalProduct->available_stock > 0)
                                        <span class="badge badge-success">{{ $digitalProduct->available_stock }} available</span>
                                    @else
                                        <span class="badge badge-danger">Out of stock</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Total Logs</strong></td>
                                <td><span class="badge badge-secondary">{{ $digitalProduct->logs->count() }} total</span></td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>
                                    @if($digitalProduct->status)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Sort Order</strong></td>
                                <td>{{ $digitalProduct->sort_order }}</td>
                            </tr>
                            <tr>
                                <td><strong>Created</strong></td>
                                <td>{{ $digitalProduct->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Updated</strong></td>
                                <td>{{ $digitalProduct->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                        </table>
                        
                        @if($digitalProduct->description)
                            <div class="mt-4">
                                <h6><strong>Description</strong></h6>
                                <p class="text-muted">{!! $digitalProduct->description !!}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Product Logs</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.digital-product-logs.create', ['product_id' => $digitalProduct->id]) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Add Log
                            </a>
                            <a href="{{ route('admin.digital-product-logs.index', ['product_id' => $digitalProduct->id]) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-list"></i> Manage All
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($digitalProduct->logs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Log Item</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($digitalProduct->logs->take(10) as $log)
                                        <tr>
                                            <td>
                                                <small><strong>{!! Str::limit($log->log_item, 30) !!}</strong></small>
                                            @if($log->details)
                                                <br><small class="text-muted">{!! Str::limit($log->details, 40) !!}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->status === 'available')
                                                    <span class="badge badge-success">Available</span>
                                                @else
                                                    <span class="badge badge-danger">Sold</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $log->created_at->format('M d, Y') }}</small>
                                                @if($log->sold_at)
                                                    <br><small class="text-muted">Sold: {{ $log->sold_at->format('M d, Y') }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->status === 'sold')
                                                    <form action="{{ route('admin.digital-product-logs.mark-available', $log) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm" title="Mark as Available">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('admin.digital-product-logs.edit', $log) }}" class="btn btn-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            @if($digitalProduct->logs->count() > 10)
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.digital-product-logs.index', ['product_id' => $digitalProduct->id]) }}" class="btn btn-outline-primary btn-sm">
                                        View All {{ $digitalProduct->logs->count() }} Logs
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No logs found for this product.</p>
                                <a href="{{ route('admin.digital-product-logs.create', ['product_id' => $digitalProduct->id]) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add First Log
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h4>Quick Stats</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="card card-statistic-1">
                                    <div class="card-icon bg-success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Available</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $digitalProduct->logs->where('status', 'available')->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card card-statistic-1">
                                    <div class="card-icon bg-danger">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Sold</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $digitalProduct->logs->where('status', 'sold')->count() }}
                                        </div>
                                    </div>
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