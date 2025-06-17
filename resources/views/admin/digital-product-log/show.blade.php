@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Product Log Details</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.digital-product-logs.index') }}">Product Logs</a></div>
            <div class="breadcrumb-item">Log #{{ $digitalProductLog->id }}</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12 col-md-8 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Log Information</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.digital-product-logs.edit', $digitalProductLog) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Log
                            </a>
                            @if($digitalProductLog->status === 'sold')
                                <form action="{{ route('admin.digital-product-logs.mark-available', $digitalProductLog) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to mark this log as available?')">
                                        <i class="fas fa-check"></i> Mark as Available
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-striped">
                                    <tr>
                                        <td><strong>Log ID</strong></td>
                                        <td>#{{ $digitalProductLog->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Product</strong></td>
                                        <td>
                                            <a href="{{ route('admin.digital-products.show', $digitalProductLog->product) }}" class="text-decoration-none">
                                                <strong>{{ $digitalProductLog->product->name }}</strong>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Category</strong></td>
                                        <td><span class="badge badge-primary">{{ $digitalProductLog->product->subcategory->category->name }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Subcategory</strong></td>
                                        <td><span class="badge badge-info">{{ $digitalProductLog->product->subcategory->name }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Product Price</strong></td>
                                        <td><strong class="text-success">{{ $settings->currency_icon }}{{ number_format($digitalProductLog->product->price, 2) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-striped">
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td>
                                            @if($digitalProductLog->status === 'available')
                                                <span class="badge badge-success">Available</span>
                                            @else
                                                <span class="badge badge-danger">Sold</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created</strong></td>
                                        <td>{{ $digitalProductLog->created_at->format('M d, Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last Updated</strong></td>
                                        <td>{{ $digitalProductLog->updated_at->format('M d, Y H:i:s') }}</td>
                                    </tr>
                                    @if($digitalProductLog->sold_at)
                                        <tr>
                                            <td><strong>Sold Date</strong></td>
                                            <td>{{ $digitalProductLog->sold_at->format('M d, Y H:i:s') }}</td>
                                        </tr>
                                    @endif
                                    @if($digitalProductLog->soldToUser)
                                        <tr>
                                            <td><strong>Sold To</strong></td>
                                            <td>
                                                <strong>{{ $digitalProductLog->soldToUser->name }}</strong>
                                                <br><small class="text-muted">{{ $digitalProductLog->soldToUser->email }}</small>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h4>Log Content</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label><strong>Log Item:</strong></label>
                            <div class="bg-light p-3 rounded border">
                                <pre class="mb-0">{!! $digitalProductLog->log_item !!}</pre>
                            </div>
                            <small class="text-muted">This is the content that will be delivered to the customer.</small>
                        </div>
                        
                        @if($digitalProductLog->details)
                            <div class="form-group">
                                <label><strong>Additional Details:</strong></label>
                                <div class="bg-light p-3 rounded border">
                                    <p class="mb-0">{!! $digitalProductLog->details !!}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-4 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('admin.digital-product-logs.edit', $digitalProductLog) }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-edit text-warning"></i> Edit Log
                            </a>
                            <a href="{{ route('admin.digital-products.show', $digitalProductLog->product) }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-box text-primary"></i> View Product
                            </a>
                            <a href="{{ route('admin.digital-product-logs.index', ['product_id' => $digitalProductLog->product_id]) }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-list text-info"></i> View All Product Logs
                            </a>
                            @if($digitalProductLog->status === 'sold')
                                <form action="{{ route('admin.digital-product-logs.mark-available', $digitalProductLog) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="list-group-item list-group-item-action text-success" onclick="return confirm('Are you sure you want to mark this log as available?')">
                                        <i class="fas fa-check"></i> Mark as Available
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('admin.digital-product-logs.destroy', $digitalProductLog) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="list-group-item list-group-item-action text-danger" onclick="return confirm('Are you sure you want to delete this log? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i> Delete Log
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h4>Product Statistics</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card card-statistic-1 mb-3">
                                    <div class="card-icon bg-primary">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Total Logs</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $digitalProductLog->product->logs->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card card-statistic-1 mb-3">
                                    <div class="card-icon bg-success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Available</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $digitalProductLog->product->logs->where('status', 'available')->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card card-statistic-1 mb-3">
                                    <div class="card-icon bg-danger">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <div class="card-wrap">
                                        <div class="card-header">
                                            <h4>Sold</h4>
                                        </div>
                                        <div class="card-body">
                                            {{ $digitalProductLog->product->logs->where('status', 'sold')->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($digitalProductLog->soldToUser)
                    <div class="card">
                        <div class="card-header">
                            <h4>Customer Information</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <tr>
                                    <td><strong>Name</strong></td>
                                    <td>{{ $digitalProductLog->soldToUser->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td>{{ $digitalProductLog->soldToUser->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Joined</strong></td>
                                    <td>{{ $digitalProductLog->soldToUser->created_at->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection