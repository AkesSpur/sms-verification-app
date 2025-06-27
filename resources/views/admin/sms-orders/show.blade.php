@extends('admin.layouts.master')

@section('content')
    <!-- Main Content -->
    <section class="section">
        <div class="section-header">
            <h1>SMS Order Details</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.sms-orders.index') }}">SMS Orders</a></div>
                <div class="breadcrumb-item">Order #{{ $order->id }}</div>
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
                                <span class="badge badge-{{ $order->status_color }} badge-lg">
                                    {{ $order->status_text }}
                                </span>
                                @if($order->needs_review)
                                    <span class="badge badge-warning badge-lg ml-2">Needs Review</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="font-weight-bold">Order ID:</td>
                                            <td>#{{ $order->id }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">User:</td>
                                            <td>
                                                @if($order->user)
                                                    <div>
                                                        <strong>{{ $order->user->name }}</strong><br>
                                                        <small class="text-muted">{{ $order->user->email }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">User not found</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Service:</td>
                                            <td>
                                                @if($order->service)
                                                    <div>
                                                        <strong>{{ $order->service->name }}</strong><br>
                                                        <small class="text-muted">Code: {{ $order->service->code }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Service not found</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Country:</td>
                                            <td>
                                                @if($order->country)
                                                    <div class="d-flex align-items-center">
                                                        @if($order->country->flag)
                                                            <img src="{{ $order->country->flag }}" alt="{{ $order->country->name }}" 
                                                                 class="mr-2" style="width: 24px; height: 18px;">
                                                        @endif
                                                        <span>{{ $order->country->name }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Country not found</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Phone Number:</td>
                                            <td>
                                                @if($order->phone_number)
                                                    <div class="d-flex align-items-center">
                                                        <span class="font-weight-bold text-primary">{{ $order->phone_number }}</span>
                                                        <button class="btn btn-sm btn-link p-0 ml-2" 
                                                                onclick="copyToClipboard('{{ $order->phone_number }}')"
                                                                title="Copy phone number">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Not assigned yet</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">SMS Code:</td>
                                            <td>
                                                @if($order->sms_code)
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge badge-success badge-lg">{{ $order->sms_code }}</span>
                                                        <button class="btn btn-sm btn-link p-0 ml-2" 
                                                                onclick="copyToClipboard('{{ $order->sms_code }}')"
                                                                title="Copy SMS code">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Waiting for SMS...</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="font-weight-bold">Created:</td>
                                            <td>{{ $order->created_at ? $order->created_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Updated:</td>
                                            <td>{{ $order->updated_at ? $order->updated_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Expires At:</td>
                                            <td>
                                                @if($order->expires_at)
                                                    <span class="{{ $order->expires_at->isPast() ? 'text-danger' : 'text-warning' }}">
                                                        {{ $order->expires_at->format('M d, Y h:i A') }}
                                                    </span>
                                                    @if($order->expires_at->isPast())
                                                        <small class="text-danger d-block">Expired</small>
                                                    @else
                                                        <small class="text-muted d-block">{{ $order->expires_at->diffForHumans() }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No expiration</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">API Order ID:</td>
                                            <td>
                                                @if($order->api_order_id)
                                                    <code>{{ $order->api_order_id }}</code>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Refunded:</td>
                                            <td>
                                                @if($order->refunded)
                                                    <span class="badge badge-success">Yes</span>
                                                @else
                                                    <span class="badge badge-secondary">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Review Status:</td>
                                            <td>
                                                @if($order->needs_review)
                                                    <span class="badge badge-warning">Needs Review</span>
                                                    @if($order->review_reason)
                                                        <small class="text-muted d-block">{{ $order->review_reason }}</small>
                                                    @endif
                                                @else
                                                    <span class="badge badge-success">No Review Needed</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Information -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Pricing Information</h4>
                        </div>
                        <div class="card-body">
                            @php
                                // Get exchange rate from general settings
                                $exchangeRate = $generalSettings->naira_to_dollar_rate ?? 1700.00;
                                
                                // Final price is already in Naira, convert other USD prices to Naira
                                $originalPriceNaira = $order->original_price * $exchangeRate;
                                $discountAmountNaira = $order->discount_amount * $exchangeRate;
                                $finalPriceNaira = $order->final_price; // Already in Naira
                                
                                // API price is in USD, convert to Naira for comparison
                                $apiPriceUsd = $order->api_price;
                                $apiPriceNaira = $apiPriceUsd * $exchangeRate;
                                
                                // Calculate profit in Naira (Final price in Naira - API price in Naira)
                                $profitNaira = $finalPriceNaira - $apiPriceNaira;
                                $marginPercent = $apiPriceNaira > 0 ? ($profitNaira / $apiPriceNaira) * 100 : 0;
                            @endphp
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="font-weight-bold">Original Price:</td>
                                            <td>₦{{ number_format($originalPriceNaira, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Discount:</td>
                                            <td>
                                                @if($order->discount_amount > 0)
                                                    <span class="text-success">-₦{{ number_format($discountAmountNaira, 2) }}</span>
                                                @else
                                                    <span class="text-muted">No discount</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Final Price (Naira):</td>
                                            <td class="font-weight-bold text-primary">₦{{ number_format($finalPriceNaira, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Exchange Rate:</td>
                                            <td class="text-muted">1 USD = ₦{{ number_format($exchangeRate, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="font-weight-bold">API Price (USD):</td>
                                            <td>${{ number_format($apiPriceUsd, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">API Price (Naira):</td>
                                            <td>₦{{ number_format($apiPriceNaira, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Profit Margin:</td>
                                            <td>
                                                <span class="{{ $profitNaira > 0 ? 'text-success' : 'text-danger' }}">
                                                    ₦{{ number_format($profitNaira, 2) }} ({{ number_format($marginPercent, 1) }}%)
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Payment Status:</td>
                                            <td>
                                                @if($order->refunded)
                                                    <span class="badge badge-info">Refunded</span>
                                                @else
                                                    <span class="badge badge-success">Paid</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    @if($order->cancellation_reason || $order->review_reason || $order->notes)
                        <div class="card">
                            <div class="card-header">
                                <h4>Additional Information</h4>
                            </div>
                            <div class="card-body">
                                @if($order->cancellation_reason)
                                    <div class="mb-3">
                                        <h6 class="text-danger">Cancellation Reason:</h6>
                                        <p class="text-muted">{{ $order->cancellation_reason }}</p>
                                    </div>
                                @endif
                                
                                @if($order->review_reason)
                                    <div class="mb-3">
                                        <h6 class="text-warning">Review Reason:</h6>
                                        <p class="text-muted">{{ $order->review_reason }}</p>
                                    </div>
                                @endif
                                
                                @if($order->notes)
                                    <div class="mb-3">
                                        <h6>Notes:</h6>
                                        <p class="text-muted">{{ $order->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Actions Panel -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Quick Actions</h4>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if(in_array($order->status, ['pending', 'active']))
                                    <button class="btn btn-info btn-block" onclick="retrySms({{ $order->id }})">
                                        <i class="fas fa-redo"></i> Retry SMS
                                    </button>
                                @endif
                                
                                @if($order->canBeCancelled() || $order->shouldBeAutoCancelled())
                                    <button class="btn btn-warning btn-block" onclick="showCancelModal()">
                                        <i class="fas fa-times"></i> Cancel Order
                                    </button>
                                @endif
                                
                                @if(!$order->needs_review)
                                    <button class="btn btn-secondary btn-block" onclick="showReviewModal()">
                                        <i class="fas fa-flag"></i> Mark for Review
                                    </button>
                                @else
                                    <button class="btn btn-success btn-block" onclick="removeFromReview()">
                                        <i class="fas fa-check"></i> Remove from Review
                                    </button>
                                @endif
                                
                                @if($order->sms_code)
                                    <button class="btn btn-primary btn-block" onclick="setSmsCode()">
                                        <i class="fas fa-edit"></i> Update SMS Code
                                    </button>
                                @else
                                    <button class="btn btn-primary btn-block" onclick="setSmsCode()">
                                        <i class="fas fa-plus"></i> Set SMS Code
                                    </button>
                                @endif
                                
                                @if(in_array($order->status, ['cancelled', 'expired', 'failed']))
                                    <button class="btn btn-danger btn-block" onclick="deleteOrder()">
                                        <i class="fas fa-trash"></i> Delete Order
                                    </button>
                                @endif
                                
                                <hr>
                                
                                <a href="{{ route('admin.sms-orders.index') }}" class="btn btn-light btn-block">
                                    <i class="fas fa-arrow-left"></i> Back to Orders
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Order Timeline -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Order Timeline</h4>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Order Created</h6>
                                        <p class="timeline-text">{{ $order->created_at ? $order->created_at->format('M d, Y h:i A') : 'N/A' }}</p>
                                    </div>
                                </div>
                                
                                @if($order->phone_number)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-info"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">Phone Number Assigned</h6>
                                            <p class="timeline-text">{{ $order->phone_number }}</p>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($order->sms_code)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-success"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">SMS Code Received</h6>
                                            <p class="timeline-text">Code: {{ $order->sms_code }}</p>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($order->needs_review)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-warning"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">Marked for Review</h6>
                                            @if($order->review_reason)
                                                <p class="timeline-text">{{ $order->review_reason }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                @if($order->status === 'cancelled')
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-danger"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">Order Cancelled</h6>
                                            @if($order->cancellation_reason)
                                                <p class="timeline-text">{{ $order->cancellation_reason }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                @if($order->status === 'completed')
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-success"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">Order Completed</h6>
                                            <p class="timeline-text">{{ $order->updated_at ? $order->updated_at->format('M d, Y h:i A') : 'N/A' }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Order</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="cancel-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Cancellation Reason</label>
                            <textarea class="form-control" name="reason" rows="3" required 
                                      placeholder="Enter reason for cancellation..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Cancel Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mark for Review</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="review-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Review Reason</label>
                            <textarea class="form-control" name="reason" rows="3" required 
                                      placeholder="Enter reason for review..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Mark for Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SMS Code Modal -->
    <div class="modal fade" id="smsCodeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Set SMS Code</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="sms-code-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>SMS Code</label>
                            <input type="text" class="form-control" name="sms_code" 
                                   value="{{ $order->sms_code }}" required 
                                   placeholder="Enter SMS verification code...">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Set Code</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    
    .timeline-marker {
        position: absolute;
        left: -22px;
        top: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #dee2e6;
    }
    
    .timeline-title {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .timeline-text {
        font-size: 13px;
        color: #6c757d;
        margin: 0;
    }
    
    .d-grid {
        display: grid;
    }
    
    .gap-2 {
        gap: 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Copy to clipboard function
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            toastr.success('Copied to clipboard!');
        });
    }

    // Show cancel modal
    function showCancelModal() {
        $('#cancelModal').modal('show');
    }

    // Show review modal
    function showReviewModal() {
        $('#reviewModal').modal('show');
    }

    // Show SMS code modal
    function setSmsCode() {
        $('#smsCodeModal').modal('show');
    }

    // Cancel order
    $('#cancel-form').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: `/admin/sms-orders/{{ $order->id }}/force-cancel`,
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#cancelModal').modal('hide');
                toastr.success(response.message);
                location.reload();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response.message || 'Failed to cancel order');
            }
        });
    });

    // Mark for review
    $('#review-form').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: `/admin/sms-orders/{{ $order->id }}/mark-review`,
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#reviewModal').modal('hide');
                toastr.success(response.message);
                location.reload();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response.message || 'Failed to mark for review');
            }
        });
    });

    // Set SMS code
    $('#sms-code-form').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: `/admin/sms-orders/{{ $order->id }}/set-sms-code`,
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#smsCodeModal').modal('hide');
                toastr.success(response.message);
                location.reload();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response.message || 'Failed to set SMS code');
            }
        });
    });

    // Remove from review
    function removeFromReview() {
        if (confirm('Are you sure you want to remove this order from review queue?')) {
            $.ajax({
                url: `/admin/sms-orders/{{ $order->id }}/remove-review`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.message);
                    location.reload();
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response.message || 'Failed to remove from review');
                }
            });
        }
    }

    // Retry SMS
    function retrySms(orderId) {
        $.ajax({
            url: `/admin/sms-orders/${orderId}/retry-sms`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                toastr.success(response.message);
                if (response.sms_code) {
                    location.reload();
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response.message || 'Failed to retry SMS');
            }
        });
    }

    // Delete order
    function deleteOrder() {
        if (confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
            $.ajax({
                url: `/admin/sms-orders/{{ $order->id }}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.message);
                    window.location.href = '{{ route("admin.sms-orders.index") }}';
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response.message || 'Failed to delete order');
                }
            });
        }
    }
</script>
@endpush