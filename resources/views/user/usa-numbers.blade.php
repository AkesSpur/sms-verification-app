@extends('layouts.user')

@section('title', 'USA Numbers')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">USA Numbers</h1>
            <p class="mt-1 text-sm text-gray-500">Get and manage your USA phone numbers</p>
        </div>
    </div>

    <!-- Purchase Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Get New USA Number</h2>
        <form id="usaForm" class="space-y-4" x-data="{ selectedService: '', servicePrice: 0, statusChecked: false }">
            @csrf
            <input type="hidden" name="country" value="us">
            
            <!-- Service Selection -->
            <div>
                <label for="service" class="block text-sm font-medium text-gray-700 mb-2">Select Service</label>
                <select id="service" name="service" x-model="selectedService" @change="servicePrice = $event.target.options[$event.target.selectedIndex].dataset.price; statusChecked = false" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Choose a service...</option>
                    <option value="whatsapp" data-price="2.50">WhatsApp - $2.50</option>
                    <option value="telegram" data-price="1.80">Telegram - $1.80</option>
                    <option value="discord" data-price="3.00">Discord - $3.00</option>
                    <option value="instagram" data-price="2.20">Instagram - $2.20</option>
                    <option value="facebook" data-price="2.80">Facebook - $2.80</option>
                </select>
            </div>

            <!-- Price Display -->
            <div x-show="selectedService" x-cloak class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Price:</span>
                    <span class="text-lg font-semibold text-gray-900" x-text="'$' + servicePrice"></span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div x-show="selectedService" x-cloak class="flex space-x-3">
                <button type="button" @click="checkStatus()" 
                        class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-search mr-2"></i>Check Status
                </button>
                <button type="submit" :disabled="!statusChecked" 
                        :class="statusChecked ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                        class="flex-1 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-shopping-cart mr-2"></i>Purchase
                </button>
            </div>

            <!-- Status Result -->
            <div id="status-result"></div>
        </form>
    </div>

    <!-- Numbers History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Your USA Numbers</h2>
        </div>
        
        <div class="p-6">
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SMS Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Sample Data -->
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">+1 (555) 123-4567</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">WhatsApp</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center space-x-2">
                                    <span class="font-mono font-bold text-green-600">123456</span>
                                    <button onclick="copyToClipboard('123456')" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Dec 15, 2023 14:30</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-primary-600 hover:text-primary-900 mr-3" onclick="refreshNumber(1)">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <button class="text-gray-600 hover:text-gray-900" onclick="showDetails(1)">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">+1 (555) 987-6543</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Telegram</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Waiting</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Pending...</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Dec 15, 2023 15:45</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-primary-600 hover:text-primary-900 mr-3" onclick="refreshNumber(2)">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <button class="text-gray-600 hover:text-gray-900" onclick="showDetails(2)">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-flag-usa text-blue-600"></i>
                            <span class="font-medium text-gray-900">+1 (555) 123-4567</span>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500">Service:</span>
                            <span class="ml-1 font-medium">WhatsApp</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Created:</span>
                            <span class="ml-1 text-gray-500">Dec 15, 14:30</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500">SMS Code:</span>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="font-mono font-bold text-green-600">123456</span>
                                <button onclick="copyToClipboard('123456')" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2 mt-3">
                        <button onclick="refreshNumber(1)" class="flex-1 bg-primary-100 text-primary-700 px-3 py-2 rounded-lg text-sm hover:bg-primary-200 transition-colors">
                            <i class="fas fa-sync-alt mr-1"></i>Refresh
                        </button>
                        <button onclick="showDetails(1)" class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors">
                            <i class="fas fa-info-circle mr-1"></i>Details
                        </button>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-flag-usa text-blue-600"></i>
                            <span class="font-medium text-gray-900">+1 (555) 987-6543</span>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Waiting</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500">Service:</span>
                            <span class="ml-1 font-medium">Telegram</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Created:</span>
                            <span class="ml-1 text-gray-500">Dec 15, 15:45</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500">SMS Code:</span>
                            <span class="ml-1 text-gray-500">Pending...</span>
                        </div>
                    </div>
                    <div class="flex space-x-2 mt-3">
                        <button onclick="refreshNumber(2)" class="flex-1 bg-primary-100 text-primary-700 px-3 py-2 rounded-lg text-sm hover:bg-primary-200 transition-colors">
                            <i class="fas fa-sync-alt mr-1"></i>Refresh
                        </button>
                        <button onclick="showDetails(2)" class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors">
                            <i class="fas fa-info-circle mr-1"></i>Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <!-- Uncomment this section if no numbers exist
            <div class="text-center py-12">
                <i class="fas fa-mobile-alt text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No USA Numbers Yet</h3>
                <p class="text-gray-500 mb-4">Get your first USA number using the form above.</p>
            </div>
            -->
        </div>
    </div>
</div>

<script>
function checkStatus() {
    const resultDiv = document.getElementById('status-result');
    
    // Show loading
    resultDiv.innerHTML = `
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-spinner fa-spin text-blue-600 mr-3"></i>
                <span class="text-blue-800">Checking availability...</span>
            </div>
        </div>
    `;
    
    // Simulate API call
    setTimeout(() => {
        const isAvailable = Math.random() > 0.3; // 70% chance of availability
        
        if (isAvailable) {
            resultDiv.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-3"></i>
                        <span class="text-green-800 font-medium">Available! USA numbers are ready for this service.</span>
                    </div>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                        <span class="text-yellow-800 font-medium">Limited availability. Try again in a few minutes.</span>
                    </div>
                </div>
            `;
        }
    }, 2000);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('SMS code copied to clipboard!', 'success');
    }, function(err) {
        console.error('Could not copy text: ', err);
        showToast('Failed to copy SMS code', 'error');
    });
}

function refreshNumber(orderId) {
    showToast('Refreshing number status...', 'info');
    
    setTimeout(() => {
        // Simulate random SMS code arrival
        if (Math.random() > 0.5) {
            showToast('SMS code received! Page will refresh.', 'success');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast('No SMS received yet. Try again in a few moments.', 'warning');
        }
    }, 2000);
}

function showDetails(orderId) {
    showToast('Order details: #' + orderId, 'info');
}

// Form submission handler
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('usaForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
        
        // Simulate purchase
        setTimeout(() => {
            showToast('USA number purchased successfully!', 'success');
            this.reset();
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-shopping-cart mr-2"></i>Purchase';
            
            // Reset status
            const resultDiv = document.getElementById('status-result');
            if (resultDiv) resultDiv.innerHTML = '';
            
            // Refresh page after 2 seconds
            setTimeout(() => {
                location.reload();
            }, 2000);
        }, 2000);
    });
});

// Auto-refresh for pending numbers
setInterval(() => {
    const pendingElements = document.querySelectorAll('.bg-yellow-100');
    if (pendingElements.length > 0) {
        // Simulate random SMS arrival
        if (Math.random() > 0.95) { // 5% chance every 30 seconds
            location.reload();
        }
    }
}, 30000); // Check every 30 seconds
</script>
@endsection