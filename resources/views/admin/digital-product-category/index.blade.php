@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Digital Product Categories</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Digital Product Categories</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>All Categories</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.digital-product-categories.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create New
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Subcategories</th>
                                        <th>Status</th>
                                        <th>Sort Order</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $category->name }}</strong>
                                            @if($category->description)
                                                <br><small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td><code>{{ $category->slug }}</code></td>
                                        <td>
                                            <span class="badge badge-info">{{ $category->subcategories->count() }} subcategories</span>
                                        </td>
                                        <td>
                                            @if($category->status)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $category->sort_order }}</td>
                                        <td>
                                            <a href="{{ route('admin.digital-product-categories.edit', $category) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.digital-product-subcategories.index', ['category_id' => $category->id]) }}" class="btn btn-info btn-sm" title="View Subcategories">
                                                <i class="fas fa-list"></i>
                                            </a>
                                            <form action="{{ route('admin.digital-product-categories.destroy', $category) }}" method="POST" class="d-inline delete-item">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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
        // Replace the existing DataTable initialization with:
        if ($.fn.DataTable.isDataTable('#table-1')) {
            $('#table-1').DataTable().destroy();
        }
        
        $('#table-1').DataTable({
            "columnDefs": [
                { "sortable": false, "targets": [5] }
            ],
            "order": [[ 4, "asc" ]] // Sort by sort order
        });
    });
</script>
@endpush