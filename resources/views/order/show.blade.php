// resources/views/order/show.blade.php
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Phone Number:</strong> {{ $order->phone_number }}
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong> 
                        <span id="status" class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'expired' ? 'danger' : 'warning') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Expires At:</strong> 
                        <span id="expires-at">{{ $order->expires_at->format('Y-m-d H:i:s') }}</span>
                    </div>
                    <div id="countdown" class="mb-3"></div>
                    @if($order->sms_code)
                        <div class="alert alert-success">
                            <strong>SMS Code:</strong> {{ $order->sms_code }}
                        </div>
                    @else
                        <div id="sms-status" class="alert alert-info">
                            Waiting for SMS...
                        </div>
                        <button onclick="checkStatus()" class="btn btn-primary w-100">
                            Refresh Status
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function startCountdown(expiresAt) {
    const timer = setInterval(() => {
        const now = new Date().getTime();
        const distance = new Date(expiresAt) - now;

        if (distance < 0) {
            clearInterval(timer);
            document.getElementById('countdown').innerHTML = "EXPIRED";
            document.getElementById('sms-status').innerHTML = "Order expired. Please try again.";
            return;
        }

        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById('countdown').innerHTML = 
            `${minutes}m ${seconds}s remaining`;
    }, 1000);
}

startCountdown("{{ $order->expires_at }}");

function checkStatus() {
    fetch(`/order/{{ $order->id }}/status`)
        .then(response => response.json())
        .then(data => {
            if(data.sms_code) {
                document.getElementById('sms-status').innerHTML = 
                    `SMS Code: ${data.sms_code}`;
                location.reload();
            }
        });
}
</script>
@endsection