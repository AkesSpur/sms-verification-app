@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Gift Management</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Gifts</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>All Gifts</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.gifts.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Gift
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Customizable</th>
                                        <th>Status</th>
                                        <th>Sort Order</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($gifts as $gift)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            @if($gift->main_image)
                                                <img src="{{ asset($gift->main_image) }}" alt="{{ $gift->name }}" 
                                                     class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px; border-radius: 4px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $gift->name }}</strong><br>
                                            <small class="text-muted">{{ $gift->slug }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $settings->currency_icon }}{{ number_format($gift->price, 2) }}</strong>
                                            @if($gift->customizable && $gift->customization_cost)
                                                <br><small class="text-info">+{{ $settings->currency_icon }}{{ number_format($gift->customization_cost, 2) }} (custom)</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($gift->customizable)
                                                <span class="badge badge-success">Yes</span>
                                                @if($gift->customization_cost)
                                                    <br><small>{{ $settings->currency_icon }}{{ number_format($gift->customization_cost, 2) }}</small>
                                                @endif
                                            @else
                                                <span class="badge badge-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($gift->status)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $gift->sort_order }}</td>
                                        <td>
                                            <a href="{{ route('admin.gifts.show', $gift->id) }}" 
                                               class="btn btn-info btn-sm mt-1 me-1" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.gifts.edit', $gift->id) }}" 
                                               class="btn btn-primary btn-sm mt-1 me-1" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.gifts.destroy', $gift->id) }}" 
                                               class="btn btn-danger btn-sm mt-1 delete-item" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
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
        $('#table-1').DataTable({
            "columnDefs": [
                { "sortable": false, "targets": [1, 7] }
            ]
        });

        // Delete functionality is handled by master.blade.php
    });
</script>
@endpush