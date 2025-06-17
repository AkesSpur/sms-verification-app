@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Balance Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Balance</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format(auth()->user()->balance ?? 0, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wallet text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Numbers Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Numbers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $activeNumbers ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-phone text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Numbers Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Numbers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalNumbers ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200" x-data="{ activeTab: 'usa' }">
        <!-- Tab Headers -->
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button @click="activeTab = 'usa'" 
                        :class="activeTab === 'usa' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-flag-usa mr-2"></i>
                    USA Numbers
                </button>
                <button @click="activeTab = 'all'" 
                        :class="activeTab === 'all' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-globe mr-2"></i>
                    All Countries
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- USA Numbers Tab -->
            <div x-show="activeTab === 'usa'" x-cloak>
                <!-- Purchase Form -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Get USA Number</h3>
                    <form id="usaForm" class="space-y-4" x-data="{ selectedService: '', servicePrice: 0, statusChecked: false }">
                        @csrf
                        <input type="hidden" name="country" value="us">
                        
                        <!-- Service Selection -->
                        <div>
                            <label for="usa_service" class="block text-sm font-medium text-gray-700 mb-2">Select Service</label>
                            <select id="usa_service" name="service" x-model="selectedService" @change="servicePrice = $event.target.options[$event.target.selectedIndex].dataset.price; statusChecked = false" 
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
                            <button type="button" @click="checkStatus('usa')" 
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
                        <div id="usa-status-result"></div>
                    </form>
                </div>

                <!-- Recent USA Numbers -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent USA Numbers</h3>
                    <div class="space-y-3">
                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SMS Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button class="text-primary-600 hover:text-primary-900 mr-3">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
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
                                    <button class="flex-1 bg-primary-100 text-primary-700 px-3 py-2 rounded-lg text-sm hover:bg-primary-200 transition-colors">
                                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                                    </button>
                                    <button class="flex-1 bg-red-100 text-red-700 px-3 py-2 rounded-lg text-sm hover:bg-red-200 transition-colors">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- All Countries Tab -->
            <div x-show="activeTab === 'all'" x-cloak>
                <!-- Purchase Form -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Get International Number</h3>
                    <form id="allForm" class="space-y-4" x-data="{ selectedCountry: '', selectedService: '', servicePrice: 0, statusChecked: false }">
                        @csrf
                        
                        <!-- Country Selection -->
                        <div>
                            <label for="all_country" class="block text-sm font-medium text-gray-700 mb-2">Select Country</label>
                            <select id="all_country" name="country" x-model="selectedCountry" @change="statusChecked = false" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Choose a country...</option>
                                <option value="us">🇺🇸 United States</option>
                                <option value="uk">🇬🇧 United Kingdom</option>
                                <option value="ca">🇨🇦 Canada</option>
                                <option value="de">🇩🇪 Germany</option>
                                <option value="fr">🇫🇷 France</option>
                                <option value="au">🇦🇺 Australia</option>
                                <option value="jp">🇯🇵 Japan</option>
                            </select>
                        </div>

                        <!-- Service Selection -->
                        <div>
                            <label for="all_service" class="block text-sm font-medium text-gray-700 mb-2">Select Service</label>
                            <select id="all_service" name="service" x-model="selectedService" @change="servicePrice = $event.target.options[$event.target.selectedIndex].dataset.price; statusChecked = false" 
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
                        <div x-show="selectedCountry && selectedService" x-cloak class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Price:</span>
                                <span class="text-lg font-semibold text-gray-900" x-text="'$' + servicePrice"></span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div x-show="selectedCountry && selectedService" x-cloak class="flex space-x-3">
                            <button type="button" @click="checkStatus('all')" 
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
                        <div id="all-status-result"></div>
                    </form>
                </div>

              
            </div>
        </div>
    </div>
</div>

<script>
function checkStatus(type) {
    const resultDiv = document.getElementById(type + '-status-result');
    const form = document.getElementById(type + 'Form');
    
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
                        <span class="text-green-800 font-medium">Available! Numbers are ready for this service.</span>
                    </div>
                </div>
            `;
            // Enable purchase button
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = false;
            submitBtn.className = submitBtn.className.replace('bg-gray-300 text-gray-500 cursor-not-allowed', 'bg-primary-600 hover:bg-primary-700 text-white');
            
            // Update Alpine.js data
            if (type === 'usa') {
                Alpine.store('usaForm', { statusChecked: true });
            } else {
                Alpine.store('allForm', { statusChecked: true });
            }
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

// Form submission handlers
document.addEventListener('DOMContentLoaded', function() {
    const usaForm = document.getElementById('usaForm');
    const allForm = document.getElementById('allForm');
    
    [usaForm, allForm].forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
            
            // Simulate purchase
            setTimeout(() => {
                showToast('Number purchased successfully!', 'success');
                this.reset();
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-shopping-cart mr-2"></i>Purchase';
                
                // Reset status
                const resultDiv = this.querySelector('[id$="-status-result"]');
                if (resultDiv) resultDiv.innerHTML = '';
            }, 2000);
        });
    });
});
</script>
@endsection