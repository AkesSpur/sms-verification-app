@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Banners</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>All Banners</h4>
                            <div class="card-header-action">
                                <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create New
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Sort Order</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($banners as $banner)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if($banner->image_path)
                                                        <img src="{{ $banner->image_url }}" alt="Banner" 
                                                             class="img-thumbnail" style="width: 80px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <span class="text-muted">No Image</span>
                                                    @endif
                                                </td>
                                                <td>{{ $banner->title ?? 'N/A' }}</td>
                                                <td>
                                                    @if($banner->description)
                                                        {{ Str::limit($banner->description, 50) }}
                                                    @else
                                                        <span class="text-muted">No Description</span>
                                                    @endif
                                                </td>
                                                <td>{{ $banner->sort_order }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <label class="custom-switch mt-2 me-2">
                                                            <input type="checkbox" 
                                                                   name="custom-switch-checkbox" 
                                                                   data-id="{{ $banner->id }}" 
                                                                   class="custom-switch-input status-toggle"
                                                                   {{ $banner->status ? 'checked' : '' }}>
                                                            <span class="custom-switch-indicator"></span>
                                                        </label>
                                                        <span class="badge mt-2 ml-2 {{ $banner->status ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $banner->status ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.banners.edit', $banner) }}" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.banners.destroy', $banner) }}" 
                                                       class="btn btn-danger btn-sm delete-item">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No banners found</td>
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
            // Handle status toggle
            $('.status-toggle').on('change', function() {
                const bannerId = $(this).data('id');
                const isChecked = $(this).is(':checked');
                const badge = $(this).closest('div').find('.badge');
                const toggleElement = $(this);
                
                $.ajax({
                    url: `/admin/banners/${bannerId}/toggle-status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update badge text and class
                            badge.text(response.status ? 'Active' : 'Inactive');
                            badge.removeClass('bg-success bg-secondary');
                            badge.addClass(response.status ? 'bg-success' : 'bg-secondary');
                            toastr.success(response.message);
                        }
                    },
                    error: function() {
                        // Revert the toggle if error
                        toggleElement.prop('checked', !isChecked);
                        toastr.error('Failed to update status');
                    }
                });
            });
        });
    </script>
@endpush