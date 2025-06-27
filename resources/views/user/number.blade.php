@extends('layouts.users')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">Numbers</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNumberModal">+ Add Number</button>
    </div>
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>Number</th>
                <th>Status</th>
                <th>Code</th>
                <th>Created</th>
                <th>Timer</th>
            </tr>
        </thead>
        <tbody>
            @if (count($orders)==0)
                <tr>
                    <td colspan="4" class="text-center fw-bold">
                        No Data Available
                    </td>
                </tr>
            @else
                @foreach ($orders as $order)
                    <tr>
                        <td class="d-flex align-items-center gap-2">
                            <span id="phone-number-{{ $order->id }}">+{{ $order->phone_number }}</span>
                            <button class="copy-btn btn btn-sm btn-outline-secondary" onclick="copyText('phone-number-{{ $order->id }}')" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy">
                                📋
                            </button>
                        </td>
                        <td class="text-success">{{ $order->status }}</td>
                        <td class="d-flex align-items-center gap-2">
                            @if($order->sms_code)
                                <div class="text-success d-flex align-items-center gap-2 mb-0" id="sms-code-{{ $order->id }}">
                                    <strong>SMS Code:</strong> <span id="sms-code-text-{{ $order->id }}">{{ $order->sms_code }}</span>
                                    <button class="copy-btn btn btn-sm btn-outline-secondary" onclick="copyText('sms-code-text-{{ $order->id }}')" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy">
                                        📋
                                    </button>
                                </div>
                            @else
                                @if($order->status === 'pending')
                                    <div id="sms-status-{{ $order->id }}" class="text-info mb-0 flex-grow-1">
                                        Waiting for SMS...
                                    </div>
                                    <button onclick="checkStatus({{ $order->id }})" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh">
                                        🔄
                                    </button>
                                @else
                                    <div class="text-secondary mb-0 flex-grow-1">
                                        {{ ucfirst($order->status) }}
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td>
                            <span>{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
                        </td>
                        <td>
                            <span id="expires-at-{{ $order->id }}">{{ $order->expires_at }}</span>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    {{ $orders->links() }}
</div>

<!-- Modal for Adding Number -->
<div class="modal fade" id="addNumberModal" tabindex="-1" aria-labelledby="addNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNumberModalLabel">Add New Number</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('order.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="service" class="form-label fw-bold">Select Service</label>
                        <select class="form-select" id="service" name="service" required>
                            <option value="">Choose a service...</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }} - ${{ $service->price }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="country" class="form-label fw-bold">Select Country</label>
                        <select class="form-select" id="country" name="country" required>
                            <option value="">Choose a country...</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->code }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary text-white">Get Number</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Function to start countdown for each order
    function startCountdown(orderId, expiresAt) {
    const timerElement = document.getElementById(`expires-at-${orderId}`);
    const timer = setInterval(() => {
        const now = new Date().getTime();
        const distance = new Date(expiresAt) - now;

        if (distance < 0) {
            clearInterval(timer);
            timerElement.innerHTML = "EXPIRED"; // Update the timer column to "EXPIRED"
            const smsStatusElement = document.getElementById(`sms-status-${orderId}`);
            if (smsStatusElement) {
                smsStatusElement.innerHTML = "Order expired. Please try again.";
            }
            return;
        }

        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        timerElement.innerHTML = `${minutes}m ${seconds}s remaining`;
    }, 1000);
}

    // Function to check the status of an order
    function checkStatus(orderId) {
    fetch(`/user/order/${orderId}/status`)
        .then(response => response.json())
        .then(data => {
            // console.log('API RESPONSE', data);
            if (data.status === 'expired') {
                // Update the UI to show the order has expired
                const smsStatusElement = document.getElementById(`sms-status-${orderId}`);
                const timerElement = document.getElementById(`expires-at-${orderId}`);
                if (smsStatusElement) {
                    smsStatusElement.innerHTML = 
                        `<div class="text-danger mb-0">
                            Order expired. Please try again.
                        </div>`;
                }
                if (timerElement) {
                    timerElement.innerHTML = "EXPIRED"; // Update the timer column
                }
            } else if (data.sms_code) {
                // Update the UI with the SMS code
                const smsStatusElement = document.getElementById(`sms-status-${orderId}`);
                if (smsStatusElement) {
                    smsStatusElement.innerHTML = 
                        `<div class="text-success d-flex align-items-center gap-2 mb-0">
                            <strong>SMS Code:</strong> 
                            <span id="sms-code-text-${orderId}">${data.sms_code}</span>
                            <button class="copy-btn btn btn-sm btn-outline-secondary" onclick="copyText('sms-code-text-${orderId}')" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy">
                                📋
                            </button>
                        </div>`;
                }
            }
        })
        .catch(error => {
            console.error("Error checking status:", error);
        });
}

function initializeOrders() {
    const orders = @json($orders->items()); // Access the items if $orders is paginated

    if (orders && Array.isArray(orders)) {
        orders.forEach(order => {
            if (order.status === 'pending') {
                // Start countdown for each pending order
                startCountdown(order.id, order.expires_at);

                // Periodically check the status of each pending order
                setInterval(() => checkStatus(order.id), 5000); // Check every 5 seconds
            } else if (order.status === 'expired') {
                // If the order is already expired, set the timer to "EXPIRED"
                const timerElement = document.getElementById(`expires-at-${order.id}`);
                if (timerElement) {
                    timerElement.innerHTML = "EXPIRED";
                }
            }
        });
    } else {
        console.error("Orders is not an array:", orders);
    }
}

    // Initialize the orders when the page loads
    document.addEventListener('DOMContentLoaded', initializeOrders);

    function copyText(elementId) {
        let text = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert("Copied: " + text);
        }).catch(err => {
            console.error("Failed to copy text: ", err);
        });
    }
</script>

<!-- Website Builder Contact -->
<div class="py-3 text-center text-sm text-gray-700 border-t border-gray-200 mt-6">
    <div class="flex items-center justify-center space-x-2 scale-90 hover:scale-100 transition-transform duration-300">
        <i class="fas fa-mobile-alt text-blue-600 animate-pulse"></i>
        <p>
            Need a custom SMS platform? <a href="mailto:dev@blizzsms.com" class="text-blue-600 hover:text-blue-800 font-medium transition-colors relative group">
                Contact the developer
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"></span>
            </a>
        </p>
        <i class="fas fa-code text-blue-600 animate-bounce"></i>
    </div>
</div>

@endpush