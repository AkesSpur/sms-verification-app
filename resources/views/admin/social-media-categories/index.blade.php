@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Social Media Categories</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Social Media Categories</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>All Categories</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.social-media-categories.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Category
                            </a>
                        </div>
                    </div>
                <div class="card-body">
                    @if($categories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="25%">Name</th>
                                        <th width="20%">Slug</th>
                                        <th width="30%">Description</th>
                                        <th width="10%">Status</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $category->name }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    {{ count($category->products) ?? 0 }} products
                                                </small>
                                            </td>
                                            <td>
                                                <code>{{ $category->slug }}</code>
                                            </td>
                                            <td>
                                                @if($category->description)
                                                    {{ Str::limit($category->description, 100) }}
                                                @else
                                                    <span class="text-muted">No description</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($category->status == 1)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.social-media-categories.show', $category->id) }}" 
                                                       class="btn btn-sm btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.social-media-categories.edit', $category->id) }}" 
                                                       class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="Delete"
                                                            onclick="deleteCategory({{ $category->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($categories->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $categories->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Categories Found</h5>
                            <p class="text-muted">Start by creating your first social media category.</p>
                            <a href="{{ route('admin.social-media-categories.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create First Category
                            </a>
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
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this category? This action cannot be undone.</p>
                <p class="text-warning"><strong>Warning:</strong> All products in this category will also be affected.</p>
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
@endsection

@push('scripts')
<script>
function deleteCategory(categoryId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/admin/social-media-categories/${categoryId}`;
    $('#deleteModal').modal('show');
}
</script>
@endpush