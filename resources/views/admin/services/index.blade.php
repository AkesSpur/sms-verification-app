@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Services Management</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Services</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>All Services</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Service
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($services->count() > 0)
                            <form id="bulkActionForm" method="POST" action="{{ route('admin.services.bulk-action') }}">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <select name="action" class="form-control" required>
                                                <option value="">Select Bulk Action</option>
                                                <option value="activate">Activate Selected</option>
                                                <option value="deactivate">Deactivate Selected</option>
                                                <option value="delete">Delete Selected</option>
                                            </select>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to perform this action?')">Apply</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button type="button" class="btn btn-secondary btn-sm" id="selectAll">Select All</button>
                                        <button type="button" class="btn btn-secondary btn-sm" id="deselectAll">Deselect All</button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <input type="checkbox" id="masterCheckbox">
                                                </th>
                                                <th>Name</th>
                                                <th class="text-center">Code</th>
                                                <th class="text-center">Base Price</th>
                                                <th class="text-center">Allow Refunds</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Orders Count</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($services as $service)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" class="service-checkbox">
                                                    </td>
                                                    <td class="text-center"><strong>{{ $service->name }}</strong></td>
                                                    <td class="text-center"><code>{{ $service->code }}</code></td>
                                                    <td class="text-center">${{ number_format($service->price, 2) }}</td>
                                                    <td class="text-center">
                                                        @if($service->allow_refunds)
                                                            <span class="badge badge-success">Yes</span>
                                                        @else
                                                            <span class="badge badge-danger">No</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($service->status === 'active')
                                                        <label class="custom-switch mt-2">
                                                            <input type="checkbox" checked name="custom-switch-checkbox" data-id="{{$service->id}}" class="custom-switch-input change-status" >
                                                            <span class="custom-switch-indicator"></span>
                                                        </label>
                                                    @else 
                                                        <label class="custom-switch mt-2">
                                                            <input type="checkbox" name="custom-switch-checkbox" data-id="{{$service->id}}" class="custom-switch-input change-status">
                                                            <span class="custom-switch-indicator"></span>
                                                        </label>
                                                    @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-info">{{ $service->orders_count ?? 0 }}</span>
                                                    </td>                                                
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('admin.services.show', $service) }}" class="btn btn-sm btn-info" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-primary" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                           
                                                            @if($service->orders_count == 0)
                                                                <form action="{{ route('admin.services.destroy', $service) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this service?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </form>

                            <div class="d-flex justify-content-center">
                                {{ $services->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No services found</h5>
                                <p class="text-muted">Get started by creating your first service.</p>
                                <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add New Service
                                </a>
                            </div>
                        @endif
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
    // Master checkbox functionality
    $('#masterCheckbox').change(function() {
        $('.service-checkbox').prop('checked', $(this).is(':checked'));
    });

    // Individual checkbox change
    $('.service-checkbox').change(function() {
        if ($('.service-checkbox:checked').length === $('.service-checkbox').length) {
            $('#masterCheckbox').prop('checked', true);
        } else {
            $('#masterCheckbox').prop('checked', false);
        }
    });

    // Select all button
    $('#selectAll').click(function() {
        $('.service-checkbox').prop('checked', true);
        $('#masterCheckbox').prop('checked', true);
    });

    // Deselect all button
    $('#deselectAll').click(function() {
        $('.service-checkbox').prop('checked', false);
        $('#masterCheckbox').prop('checked', false);
    });

    // Handle status toggle switch
    $('body').on('click', '.change-status', function(){
        let isChecked = $(this).is(':checked');
        let id = $(this).data('id');
        let switchElement = $(this);

        $.ajax({
            url: "/admin/services/" + id + "/toggle-status",
            method: 'PATCH',
            data: {
                _token: "{{ csrf_token() }}",
                status: isChecked
            },
            success: function(data){
                if(data.status == 'success'){
                    toastr.success(data.message);
                } else {
                    // Revert the switch if failed
                    switchElement.prop('checked', !isChecked);
                    toastr.error(data.message || 'An error occurred.');
                }
            },
            error: function(xhr, status, error){
                // Revert the switch on error
                switchElement.prop('checked', !isChecked);
                toastr.error('An error occurred: ' + error);
            }
        });
    });

    // Bulk action form validation - only for the specific bulk form
    $('#bulkActionForm').on('submit', function(e) {
        if ($('.service-checkbox:checked').length === 0) {
            e.preventDefault();
            toastr.error('Please select at least one service.');
            return false;
        }
        
        if (!$('select[name="action"]').val()) {
            e.preventDefault();
            toastr.error('Please select an action.');
            return false;
        }
    });
});
</script>
@endpush