@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Order Details #{{ $daisyOrder->id }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.daisy-orders.index') }}">Daisy SMS Orders</a></div>
                <div class="breadcrumb-item">Order #{{ $daisyOrder->id }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <!-- Order Information -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Order Information</h4>
                            <div class="card-header-action">
                                <span class="badge badge-{{ $daisyOrder->status_badge }}">
                                    {{ ucfirst($daisyOrder->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Order ID:</strong></td>
                                            <td>#{{ $daisyOrder->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Transaction ID:</strong></td>
                                            <td>{{ $daisyOrder->trx }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Rental ID:</strong></td>
                                            <td>{{ $daisyOrder->rental_id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone Number:</strong></td>
                                            <td><strong>{{ $daisyOrder->phone_number }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Service:</strong></td>
                                            <td>{{ $daisyOrder->service_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Country:</strong></td>
                                            <td>{{ $daisyOrder->country_name }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Price:</strong></td>
                                            <td><strong>₦{{ number_format($daisyOrder->price, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $daisyOrder->created_at->format('M d, Y H:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Expires:</strong></td>
                                            <td>
                                                @if($daisyOrder->expires_at)
                                                    {{ $daisyOrder->expires_at->format('M d, Y H:i A') }}
                                                    @if($daisyOrder->expires_at->isPast())
                                                        <span class="badge badge-danger ml-2">Expired</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Updated:</strong></td>
                                            <td>{{ $daisyOrder->updated_at->format('M d, Y H:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Duration:</strong></td>
                                            <td>{{ $daisyOrder->duration }} minutes</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Max SMS:</strong></td>
                                            <td>{{ $daisyOrder->max_sms }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SMS Information -->
                    <div class="card">
                        <div class="card-header">
                            <h4>SMS Information</h4>
                            <div class="card-header-action">
                                @if($daisyOrder->isActive())
                                    <button type="button" class="btn btn-primary" id="refreshSmsBtn">
                                        <i class="fas fa-sync"></i> Refresh SMS Status
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>SMS Code:</strong></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="smsCode" value="{{ $daisyOrder->sms_code }}" readonly>
                                            @if($daisyOrder->sms_code)
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('#smsCode')">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                        @if(!$daisyOrder->sms_code && !in_array($daisyOrder->status, ['expired', 'cancelled']))
                                            <small class="text-muted">Waiting for SMS...</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>SMS Status:</strong></label>
                                        <div id="smsStatusContainer">
                                            @if($smsStatus)
                                                @if(isset($smsStatus['sms']) && $smsStatus['sms'])
                                                    <span class="badge badge-success">SMS Received</span>
                                                @else
                                                    <span class="badge badge-warning">Waiting for SMS</span>
                                                @endif
                                            @else
                                                <span class="badge badge-secondary">Unknown</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($daisyOrder->sms_text)
                                <div class="form-group">
                                    <label><strong>Full SMS Text:</strong></label>
                                    <div class="alert alert-info">
                                        {{ $daisyOrder->sms_text }}
                                    </div>
                                </div>
                            @endif

                            @if($smsStatus && isset($smsStatus['status']))
                                <div class="form-group">
                                    <label><strong>API Status Details:</strong></label>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            @foreach($smsStatus as $key => $value)
                                                <tr>
                                                    <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong></td>
                                                    <td>
                                                        @if(is_bool($value))
                                                            <span class="badge badge-{{ $value ? 'success' : 'danger' }}">
                                                                {{ $value ? 'Yes' : 'No' }}
                                                            </span>
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions & User Info -->
                <div class="col-lg-4">
                    <!-- User Information -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Customer Information</h4>
                        </div>
                        <div class="card-body">
                            @if($daisyOrder->user)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-lg mr-3">
                                        <img alt="image" src="{{ $daisyOrder->user->avatar ?? asset('admin/img/avatar/avatar-1.png') }}" class="rounded-circle">
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $daisyOrder->user->name }}</h6>
                                        <small class="text-muted">{{ $daisyOrder->user->email }}</small>
                                    </div>
                                </div>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td><strong>User ID:</strong></td>
                                        <td>#{{ $daisyOrder->user->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Balance:</strong></td>
                                        <td>₦{{ number_format($daisyOrder->user->balance, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge badge-{{ $daisyOrder->user->status == 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($daisyOrder->user->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Joined:</strong></td>
                                        <td>{{ $daisyOrder->user->created_at->format('M d, Y') }}</td>
                                    </tr>
                                </table>
                                <div class="mt-3">
                                    <a href="{{ route('admin.customer.index', ['search' => $daisyOrder->user->email]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-user"></i> View Customer
                                    </a>
                                </div>
                            @else
                                <div class="text-center text-muted">
                                    <i class="fas fa-user-slash fa-2x mb-2"></i>
                                    <p>User not found</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Order Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Order Actions</h4>
                        </div>
                        <div class="card-body">
                            <!-- Status Update -->
                            <div class="form-group">
                                <label>Update Status</label>
                                <select class="form-control" id="statusSelect">
                                    <option value="pending" {{ $daisyOrder->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="active" {{ $daisyOrder->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="completed" {{ $daisyOrder->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $daisyOrder->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="expired" {{ $daisyOrder->status == 'expired' ? 'selected' : '' }}>Expired</option>
                                </select>
                            </div>

                            <!-- Manual SMS Code -->
                            <div class="form-group">
                                <label>Manual SMS Code</label>
                                <input type="text" class="form-control" id="manualSmsCode" placeholder="Enter SMS code manually...">
                            </div>

                            <!-- Manual SMS Text -->
                            <div class="form-group">
                                <label>Manual SMS Text</label>
                                <textarea class="form-control" id="manualSmsText" rows="3" placeholder="Enter full SMS text..."></textarea>
                            </div>

                            <div class="form-group">
                                <button type="button" class="btn btn-primary btn-block" id="updateStatusBtn">
                                    <i class="fas fa-save"></i> Update Order
                                </button>
                            </div>

                            <hr>

                            @if(!$daisyOrder->isCompleted() && !$daisyOrder->isCancelled())
                                <div class="form-group">
                                    <button type="button" class="btn btn-danger btn-block" id="cancelOrderBtn">
                                        <i class="fas fa-times"></i> Cancel Order
                                    </button>
                                </div>
                            @endif

                            <div class="form-group">
                                <a href="{{ route('admin.daisy-orders.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-arrow-left"></i> Back to Orders
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Information -->
                    @if($daisyOrder->transaction)
                        <div class="card">
                            <div class="card-header">
                                <h4>Transaction Details</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td><strong>Transaction ID:</strong></td>
                                        <td>#{{ $daisyOrder->transaction->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Amount:</strong></td>
                                        <td>₦{{ number_format($daisyOrder->transaction->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Type:</strong></td>
                                        <td>
                                            <span class="badge badge-{{ $daisyOrder->transaction->type == 'credit' ? 'success' : 'danger' }}">
                                                {{ ucfirst($daisyOrder->transaction->type) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge badge-{{ $daisyOrder->transaction->status == 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($daisyOrder->transaction->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                                <div class="mt-3">
                                    <a href="{{ route('admin.transactions.show', $daisyOrder->transaction) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-receipt"></i> View Transaction
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Order</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="cancelOrderForm">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> This action will cancel the order and process a refund to the user's account.
                    </div>
                    <p>Are you sure you want to cancel this order?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update order status
    $('#updateStatusBtn').click(function() {
        const status = $('#statusSelect').val();
        const smsCode = $('#manualSmsCode').val();
        const smsText = $('#manualSmsText').val();
        
        // Auto-set status to completed if SMS code is manually provided
        let finalStatus = status;
        if (smsCode && smsCode.trim() !== '' && status !== 'completed') {
            finalStatus = 'completed';
            $('#statusSelect').val('completed');
        }
        
        const button = $(this);
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        
        $.ajax({
            url: '{{ route("admin.daisy-orders.update-status", $daisyOrder) }}',
            method: 'POST',
            data: {
                status: finalStatus,
                sms_code: smsCode,
                sms_text: smsText,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Failed to update order status');
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="fas fa-save"></i> Update Order');
            }
        });
    });

    // Refresh SMS status
    $('#refreshSmsBtn').click(function() {
        const button = $(this);
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');
        
        $.ajax({
            url: '{{ route("admin.daisy-orders.refresh-sms", $daisyOrder) }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Failed to refresh SMS status');
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="fas fa-sync"></i> Refresh SMS Status');
            }
        });
    });

    // Cancel order
    $('#cancelOrderBtn').click(function() {
        $('#cancelOrderModal').modal('show');
    });

    // Cancel order form submit
    $('#cancelOrderForm').submit(function(e) {
        e.preventDefault();
        
        const formData = {
            _token: '{{ csrf_token() }}'
        };
        
        $.ajax({
            url: '{{ route("admin.daisy-orders.cancel", $daisyOrder) }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Failed to cancel order');
            }
        });
        
        $('#cancelOrderModal').modal('hide');
    });
});

// Copy to clipboard function
function copyToClipboard(element) {
    const text = $(element).val();
    if (text) {
        navigator.clipboard.writeText(text).then(function() {
            toastr.success('Copied to clipboard!');
        }, function() {
            // Fallback for older browsers
            $(element).select();
            document.execCommand('copy');
            toastr.success('Copied to clipboard!');
        });
    }
}
</script>
@endpush