@extends('layouts.user')

@section('title', 'Reseller Store')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reseller Store</h1>
                <p class="text-gray-600 mt-1">Exclusive products for verified resellers</p>
            </div>
        </div>
    </div>

    @if(!$isReseller)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-user-tag text-yellow-600 mt-0.5"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-lg font-medium text-yellow-800">Reseller Access Required</h4>
                    <p class="text-sm text-yellow-700 mt-1">You are not a reseller yet. Submit a request to gain access to reseller products.</p>
                    <form id="resellerRequestForm" method="POST" action="{{ route('user.reseller.request') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Request Reseller Access
                        </button>
                    </form>
                    <div class="mt-3 text-sm text-yellow-700">
                        Or contact us on WhatsApp to speed up the process:
                        <a href="https://wa.me/2348164622735?text={{ urlencode('Hello, I want to become a reseller. My account email is ' . (auth()->user()->email ?? '')) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors ml-2">
                            <i class="fab fa-whatsapp mr-2"></i>
                            Chat on WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($products as $product)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:bg-gray-50">
                @if($product->image)
                    <div class="mb-3">
                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-32 object-cover rounded-lg">
                    </div>
                @endif
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h3>
                    <span class="text-sm font-medium text-gray-500">Stock: {{ $product->available_stock }}</span>
                </div>
                <p class="text-sm text-gray-600 mb-4">{!! Str::limit($product->description, 120) !!}</p>
                <div class="flex items-center justify-between">
                    <span class="text-xl font-bold text-slate-800">₦{{ number_format($product->price) }}</span>
                    @if($product->available_stock > 0)
                    <button class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm"
                            onclick="openPurchaseModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->available_stock }})">
                        <i class="fas fa-shopping-cart mr-1"></i>Buy
                    </button>
                    @else
                    <button disabled class="bg-gray-400 text-white px-4 py-2 rounded-lg text-sm cursor-not-allowed">
                        Out of Stock
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="md:col-span-3">
                <div class="bg-gray-50 rounded-lg p-8 text-center text-gray-500">
                    No reseller products available.
                </div>
            </div>
            @endforelse
        </div>
    @endif

    <!-- Purchase Modal -->
    <div id="resellerPurchaseModal" style="display: none;" class="z-50">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 w-full h-full" style="z-index: 999;"></div>
        <div class="fixed inset-0 flex items-center justify-center" style="z-index: 1000;" onclick="closeResellerPurchaseModal()">
            <div class="relative mx-auto p-6 border w-11/12 max-w-md shadow-lg rounded-md bg-white" onclick="event.stopPropagation()">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-medium text-gray-900">Purchase <span id="rpProductName"></span></h3>
                        <button onclick="closeResellerPurchaseModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <div class="flex items-center space-x-4">
                                <button id="rpDecreaseBtn" class="w-10 h-10 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-minus text-gray-600"></i>
                                </button>
                                <input type="number" id="rpQuantity" value="1" min="1" class="w-20 text-center border border-gray-300 rounded-lg py-2 font-semibold">
                                <button id="rpIncreaseBtn" class="w-10 h-10 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-plus text-gray-600"></i>
                                </button>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">Max available: <span id="rpMaxStock"></span></p>
                        </div>
                        <div class="border-t pt-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Unit Price:</span>
                                <span id="rpUnitPrice" class="font-semibold"></span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t pt-2">
                                <span class="text-gray-900">Total:</span>
                                <span id="rpTotalPrice" class="text-slate-800"></span>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end space-x-3">
                            <button type="button" onclick="closeResellerPurchaseModal()" 
                                    class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                                Cancel
                            </button>
                            <button type="button" onclick="confirmResellerPurchase()" 
                                    class="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors">
                                <i class="fas fa-credit-card mr-2"></i>
                                Purchase
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let rpProductId = null;
let rpProductName = '';
let rpUnitPriceValue = 0;
let rpMaxStockValue = 0;

function openPurchaseModal(productId, productName, unitPrice, maxStock) {
    rpProductId = productId;
    rpProductName = productName;
    rpUnitPriceValue = unitPrice;
    rpMaxStockValue = maxStock;

    document.getElementById('rpProductName').textContent = productName;
    document.getElementById('rpUnitPrice').textContent = `₦${unitPrice.toLocaleString()}`;
    document.getElementById('rpMaxStock').textContent = maxStock;
    document.getElementById('rpQuantity').value = 1;
    updateResellerTotal();

    document.getElementById('resellerPurchaseModal').style.display = 'block';

    document.getElementById('rpDecreaseBtn').onclick = function() {
        const inp = document.getElementById('rpQuantity');
        const currentValue = parseInt(inp.value);
        if (currentValue > 1) {
            inp.value = currentValue - 1;
            updateResellerTotal();
        }
    };
    document.getElementById('rpIncreaseBtn').onclick = function() {
        const inp = document.getElementById('rpQuantity');
        const currentValue = parseInt(inp.value);
        if (currentValue < rpMaxStockValue) {
            inp.value = currentValue + 1;
            updateResellerTotal();
        }
    };
    document.getElementById('rpQuantity').addEventListener('input', function() {
        let value = parseInt(this.value);
        if (value < 1) this.value = 1;
        if (value > rpMaxStockValue) this.value = rpMaxStockValue;
        updateResellerTotal();
    });
}

function closeResellerPurchaseModal() {
    document.getElementById('resellerPurchaseModal').style.display = 'none';
}

function updateResellerTotal() {
    const qty = parseInt(document.getElementById('rpQuantity').value);
    const total = qty * rpUnitPriceValue;
    document.getElementById('rpTotalPrice').textContent = `₦${total.toLocaleString()}`;
}

function confirmResellerPurchase() {
    const qty = parseInt(document.getElementById('rpQuantity').value);
    setLoadingState(true);
    fetch('{{ route('reseller.purchase') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ product_id: rpProductId, quantity: qty })
    }).then(r => r.json()).then(data => {
        setLoadingState(false);
        if (data.success) {
            notify('success', data.message);
            closeResellerPurchaseModal();
            setTimeout(() => {
                showResellerPurchaseDetails(data.data);
            }, 1200);
        } else {
            notify('error', data.message || 'Purchase failed. Please try again.');
        }
    }).catch(err => {
        console.error(err);
        setLoadingState(false);
        notify('error', 'An unexpected error occurred. Please try again.');
    });
}

function setLoadingState(loading) {
    // Could adapt button state; for simplicity, we use toast
}

function showResellerPurchaseDetails(data) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    const quantity = (data?.order?.quantity) ?? ((data?.order?.logs && data.order.logs.length) ? data.order.logs.length : 0);
    const total = Number(data?.total_amount || 0);
    const remaining = Number(data?.remaining_balance || 0);
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-green-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Purchase Successful!</h3>
                <p class="text-gray-600">Your reseller logs have been added to your account.</p>
            </div>
            <div class="border-t pt-4 mb-4">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-600">Product:</span><span class="font-medium">${data.product_name}</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Quantity:</span><span class="font-medium">${quantity}</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Total Paid:</span><span class="font-medium">₦${total.toLocaleString()}</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Remaining Balance:</span><span class="font-medium">₦${remaining.toLocaleString()}</span></div>
                </div>
            </div>
            <div class="flex space-x-3">
                <button onclick="closeModal(this)" class="flex-1 bg-gray-200 text-gray-800 py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors">Close</button>
                <a href="{{ route('user.order-history') }}" class="flex-1 bg-slate-800 text-white py-2 px-4 rounded-lg hover:bg-slate-900 transition-colors text-center">View Orders</a>
            </div>
        </div>`;
    document.body.appendChild(modal);
}

function closeModal(button) {
    const modal = button.closest('.fixed');
    document.body.removeChild(modal);
}
</script>
@endpush