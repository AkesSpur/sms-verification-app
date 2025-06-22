@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ route('admin.gift-orders.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Gift Order #{{ $order->id }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="{{ route('admin.gift-orders.index') }}">Gift Orders</a></div>
            <div class="breadcrumb-item">Order Details</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <!-- Order Status Card -->
            <div class="col-12 col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Order Status</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'confirmed' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                $paymentColors = [
                                    'pending' => 'warning',
                                    'paid' => 'success',
                                    'refunded' => 'info'
                                ];
                            @endphp
                            <span class="badge badge-{{ $statusColors[$order->status] ?? 'secondary' }} badge-lg mb-2">
                                {{ ucfirst($order->status) }}
                            </span>
                            <br>
                            <span class="badge badge-{{ $paymentColors[$order->payment_status] ?? 'secondary' }}">
                                Payment: {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-right">
                                    <h6 class="text-muted mb-1">Order Date</h6>
                                    <p class="mb-0">{{ $order->created_at->format('M d, Y') }}</p>
                                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h6 class="text-muted mb-1">Total Amount</h6>
                                <p class="mb-0 font-weight-bold">₦{{ number_format($order->total_amount, 2) }}</p>
                                @if($order->is_customized)
                                    <small class="text-info">Customized</small>
                                @endif
                            </div>
                        </div>
                        
                        @if(in_array($order->status, ['pending', 'confirmed']))
                            <div class="mt-3">
                                <button class="btn btn-primary btn-block" onclick="showStatusModal()">Update Status</button>
                            </div>
                        @endif
                    </div>
                </div>
                @if($order->is_customized)
                <!-- Custom Images Card -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Custom Images</h4>
                            </div>
                            <div class="card-body">
                                @if($order->custom_image)
                                <div class="row">
                                            <div class="card">
                                                <img src="{{ asset($order->custom_image) }}" class="card-img-top" 
                                                     style="height: 200px; object-fit: cover;" alt="Custom Image">
                                                <div class="card-body p-2">
                                                    <button class="btn btn-sm btn-primary btn-block" 
                                                            onclick="downloadImage('{{ asset($order->custom_image) }}', 'custom-image-{{ $order->order_number }}.jpg')">
                                                        <i class="fas fa-download"></i> Download
                                                    </button>
                                                </div>
                                            </div>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <div class="mb-3">
                                        <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="text-muted">No Custom Image Provided</h5>
                                    <p class="text-muted mb-0">The customer did not upload a custom image for this gift order.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            </div>

            <!-- Shipping Information Card -->
            <div class="col-12 col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Shipping Information</h4>
                        <button class="btn btn-sm btn-outline-primary" onclick="copyAllShippingInfo()">
                            <i class="fas fa-copy"></i> Copy All Info
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Recipient Name</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $order->recipient_name }}" readonly id="recipient_name">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" onclick="copyToClipboard('recipient_name')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Recipient Phone</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $order->recipient_phone }}" readonly id="recipient_phone">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" onclick="copyToClipboard('recipient_phone')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Sender Name</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $order->sender_name }}" readonly id="sender_name">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" onclick="copyToClipboard('sender_name')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Sender Phone</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $order->sender_phone }}" readonly id="sender_phone">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" onclick="copyToClipboard('sender_phone')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Sender Email</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $order->sender_email }}" readonly id="sender_email">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" onclick="copyToClipboard('sender_email')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if($order->delivery_apartment)
                                    <div class="form-group">
                                        <label class="font-weight-bold">Apartment/Suite</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $order->delivery_apartment }}" readonly id="delivery_apartment">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" onclick="copyToClipboard('delivery_apartment')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Delivery Address</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $order->delivery_address }}" readonly id="delivery_address">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" onclick="copyToClipboard('delivery_address')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Delivery City</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $order->delivery_city }}" readonly id="delivery_city">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" onclick="copyToClipboard('delivery_city')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Delivery State</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $order->delivery_state }}" readonly id="delivery_state">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" onclick="copyToClipboard('delivery_state')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Delivery Country</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $order->delivery_country }}" readonly id="delivery_country">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" onclick="copyToClipboard('delivery_country')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                @if($order->delivery_zip)
                                    <div class="form-group">
                                        <label class="font-weight-bold">Zip Code</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $order->delivery_zip }}" readonly id="delivery_zip">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" onclick="copyToClipboard('delivery_zip')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <!-- Empty column for layout balance -->
                            </div>
                        </div>
                        
                        @if($order->tracking_number)
                            <div class="form-group">
                                <label class="font-weight-bold">Tracking Number</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $order->tracking_number }}" readonly id="tracking_number">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" onclick="copyToClipboard('tracking_number')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if($order->notes)
                            <div class="form-group">
                                <label class="font-weight-bold">Order Notes</label>
                                <div class="input-group">
                                    <textarea class="form-control" rows="2" readonly id="order_notes">{{ $order->notes }}</textarea>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" onclick="copyToClipboard('order_notes')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Gift Details Card -->
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Gift Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            @if($order->gift->main_image)
                                <img src="{{ asset($order->gift->main_image) }}" alt="{{ $order->gift->name }}" 
                                     class="img-thumbnail me-3" style="width: 80px; height: 80px; object-fit: cover;">
                            @endif
                            <div class="ml-3">
                                <h5 class="mb-1">{{ $order->gift->name }}</h5>
                                <p class="text-muted mb-1">{{ $order->gift->description }}</p>
                                <small class="text-muted">Quantity: {{ $order->quantity }}</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-6">
                                <h6 class="text-muted mb-1">Unit Price</h6>
                                <p class="mb-0">₦{{ number_format($order->unit_price, 2) }}</p>
                            </div>
                            <div class="col-6">
                                <h6 class="text-muted mb-1">Customization Cost</h6>
                                <p class="mb-0">₦{{ number_format($order->customization_cost, 2) }}</p>
                            </div>
                        </div>
                        
                        @if($order->is_customized)
                            <div class="mt-3">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> This is a customized order
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Customer Details Card -->
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Customer Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-muted mb-1">Customer Name</h6>
                                <p class="mb-2">{{ $order->user->name }}</p>
                                
                                <h6 class="text-muted mb-1">Email</h6>
                                <p class="mb-2">{{ $order->user->email }}</p>
                                
                                <h6 class="text-muted mb-1">Customer Balance</h6>
                                <p class="mb-2">₦{{ number_format($order->user->balance, 2) }}</p>
                                
                                <h6 class="text-muted mb-1">Member Since</h6>
                                <p class="mb-0">{{ $order->user->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <button class="btn btn-success mb-2 btn-block" onclick="downloadOrderData()">
                                    <i class="fas fa-download"></i> Download Order Data
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-info mb-2 btn-block" onclick="printOrder()">
                                    <i class="fas fa-print"></i> Print Order
                                </button>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('admin.gift-orders.index') }}" class="btn mb-2 btn-secondary btn-block">
                                    <i class="fas fa-arrow-left"></i> Back to Orders
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>New Status</label>
                        <select class="form-control" id="newStatus" required>
                            <option value="">Select Status</option>
                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Tracking Number (Optional)</label>
                        <input type="text" id="trackingNumber" class="form-control" 
                               placeholder="Enter tracking number" value="{{ $order->tracking_number }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Notes (Optional)</label>
                        <textarea id="statusNotes" class="form-control" rows="3" 
                                  placeholder="Add any notes about this status update">{{ $order->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Copy individual field to clipboard
function copyToClipboard(elementId, buttonElement) {
    const element = document.getElementById(elementId);
    const text = element.value || element.textContent;
    
    // If buttonElement is not provided, find it using the event
    if (!buttonElement && window.event) {
        buttonElement = window.event.target.closest('button');
    }
    
    navigator.clipboard.writeText(text).then(function() {
        // Show success feedback
        if (buttonElement) {
            const originalHtml = buttonElement.innerHTML;
            buttonElement.innerHTML = '<i class="fas fa-check"></i>';
            buttonElement.classList.add('btn-success');
            buttonElement.classList.remove('btn-outline-secondary');
            
            setTimeout(function() {
                buttonElement.innerHTML = originalHtml;
                buttonElement.classList.remove('btn-success');
                buttonElement.classList.add('btn-outline-secondary');
            }, 1500);
        }
        console.log('Copied to clipboard: ' + text);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy to clipboard');
    });
}

// Copy all shipping information
function copyAllShippingInfo() {
    const shippingInfo = `
Recipient: {{ $order->recipient_name }}
Recipient Phone: {{ $order->recipient_phone }}
Sender: {{ $order->sender_name }}
Sender Phone: {{ $order->sender_phone }}
Sender Email: {{ $order->sender_email }}
Delivery Address: {{ $order->delivery_address }}
@if($order->delivery_apartment)Apartment/Suite: {{ $order->delivery_apartment }}@endif
City: {{ $order->delivery_city }}
State: {{ $order->delivery_state }}
Country: {{ $order->delivery_country }}
@if($order->delivery_zip)Zip Code: {{ $order->delivery_zip }}@endif
@if($order->tracking_number)Tracking: {{ $order->tracking_number }}@endif
@if($order->notes)Notes: {{ $order->notes }}@endif
    `.trim();
    
    navigator.clipboard.writeText(shippingInfo).then(function() {
        alert('All shipping information copied to clipboard!');
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy to clipboard');
    });
}

// Download image
function downloadImage(imageUrl, filename) {
    const link = document.createElement('a');
    link.href = imageUrl;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Download order data as JSON
function downloadOrderData() {
    const orderData = {
        order_id: {{ $order->id }},
        gift_name: '{{ addslashes($order->gift->name) }}',
        customer: {
            name: '{{ addslashes($order->user->name) }}',
            email: '{{ addslashes($order->user->email) }}'
        },
        recipient: {
            name: '{{ addslashes($order->recipient_name) }}',
            phone: '{{ addslashes($order->recipient_phone) }}'
        },
        sender: {
            name: '{{ addslashes($order->sender_name) }}',
            email: '{{ addslashes($order->sender_email) }}'
        },
        delivery: {
            address: '{{ addslashes($order->delivery_address) }}',
            city: '{{ addslashes($order->delivery_city) }}',
            state: '{{ addslashes($order->delivery_state) }}',
            country: '{{ addslashes($order->delivery_country) }}'
        },
        order_details: {
            quantity: {{ $order->quantity }},
            unit_price: {{ $order->unit_price }},
            customization_cost: {{ $order->customization_cost }},
            total_amount: {{ $order->total_amount }},
            is_customized: {{ $order->is_customized ? 'true' : 'false' }},
            status: '{{ $order->status }}',
            payment_status: '{{ $order->payment_status }}',
            tracking_number: '{{ $order->tracking_number ?? '' }}',
            notes: '{{ addslashes($order->notes ?? '') }}'
        },
        timestamps: {
            ordered_at: '{{ $order->created_at->toISOString() }}',
            @if($order->shipped_at)shipped_at: '{{ $order->shipped_at->toISOString() }}',@endif
            @if($order->delivered_at)delivered_at: '{{ $order->delivered_at->toISOString() }}'@endif
        }
    };
    
    const dataStr = JSON.stringify(orderData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'gift-order-{{ $order->id }}-data.json';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

// Print order
function printOrder() {
    window.print();
}

// Show status modal
function showStatusModal() {
    $('#statusModal').modal('show');
}

// Handle status form submission
$('#statusForm').on('submit', function(e) {
    e.preventDefault();
    
    const status = $('#newStatus').val();
    const trackingNumber = $('#trackingNumber').val();
    const notes = $('#statusNotes').val();
    
    if (!status) {
        alert('Please select a status');
        return;
    }
    
    $.ajax({
        url: `/admin/gift-orders/{{ $order->id }}/status`,
        method: 'PUT',
        data: {
            _token: '{{ csrf_token() }}',
            status: status,
            tracking_number: trackingNumber,
            notes: notes
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error updating status: ' + response.message);
            }
        },
        error: function() {
            alert('Error updating status. Please try again.');
        }
    });
});
</script>
@endpush

@push('styles')
<style>
@media print {
    .btn, .card-header, .section-header, .breadcrumb {
        display: none !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
        margin-bottom: 20px !important;
    }
    
    .card-body {
        padding: 15px !important;
    }
}

.input-group .btn {
    border-left: 0;
}

.badge-lg {
    font-size: 1rem;
    padding: 0.5rem 1rem;
}
</style>
@endpush
