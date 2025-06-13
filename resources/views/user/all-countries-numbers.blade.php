@extends('layouts.user')

@section('title', 'All Countries Numbers')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">All Countries Numbers</h1>
            <p class="mt-1 text-sm text-gray-500">Get and manage international phone numbers</p>
        </div>
    </div>

    <!-- Purchase Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Get New International Number</h2>
        <form id="internationalForm" class="space-y-4" x-data="{ selectedCountry: '', selectedService: '', servicePrice: 0, statusChecked: false }">
            @csrf
            
            <!-- Country Selection -->
            <div>
                <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Select Country</label>
                <select id="country" name="country" x-model="selectedCountry" @change="statusChecked = false" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Choose a country...</option>
                    <option value="uk">🇬🇧 United Kingdom</option>
                    <option value="ca">🇨🇦 Canada</option>
                    <option value="au">🇦🇺 Australia</option>
                    <option value="de">🇩🇪 Germany</option>
                    <option value="fr">🇫🇷 France</option>
                    <option value="it">🇮🇹 Italy</option>
                    <option value="es">🇪🇸 Spain</option>
                    <option value="nl">🇳🇱 Netherlands</option>
                    <option value="se">🇸🇪 Sweden</option>
                    <option value="no">🇳🇴 Norway</option>
                </select>
            </div>
            
            <!-- Service Selection -->
            <div x-show="selectedCountry" x-cloak>
                <label for="service" class="block text-sm font-medium text-gray-700 mb-2">Select Service</label>
                <select id="service" name="service" x-model="selectedService" @change="servicePrice = $event.target.options[$event.target.selectedIndex].dataset.price; statusChecked = false" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Choose a service...</option>
                    <option value="whatsapp" data-price="3.50">WhatsApp - $3.50</option>
                    <option value="telegram" data-price="2.80">Telegram - $2.80</option>
                    <option value="discord" data-price="4.00">Discord - $4.00</option>
                    <option value="instagram" data-price="3.20">Instagram - $3.20</option>
                    <option value="facebook" data-price="3.80">Facebook - $3.80</option>
                    <option value="twitter" data-price="3.60">Twitter - $3.60</option>
                    <option value="tiktok" data-price="4.20">TikTok - $4.20</option>
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

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filter Numbers</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="countryFilter" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                <select id="countryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Countries</option>
                    <option value="uk">United Kingdom</option>
                    <option value="ca">Canada</option>
                    <option value="au">Australia</option>
                    <option value="de">Germany</option>
                    <option value="fr">France</option>
                </select>
            </div>
            <div>
                <label for="serviceFilter" class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                <select id="serviceFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Services</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="telegram">Telegram</option>
                    <option value="discord">Discord</option>
                    <option value="instagram">Instagram</option>
                </select>
            </div>
            <div>
                <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" id="searchInput" placeholder="Search phone numbers..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button type="button" onclick="applyFilters()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-filter mr-2"></i>Apply Filters
            </button>
        </div>
    </div>

    <!-- Numbers Table (Desktop) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hidden md:block">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Your International Numbers</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone Number</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SMS Code</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="numbers-table-body">
                    <!-- Sample data, will be replaced by JavaScript -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">🇬🇧 UK</td>
                        <td class="px-6 py-4 whitespace-nowrap">+44 7700 900123</td>
                        <td class="px-6 py-4 whitespace-nowrap">WhatsApp</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-sm text-gray-900 mr-2">123456</span>
                                <button class="text-gray-400 hover:text-gray-600" onclick="copySmsCode('123456')">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2 mins ago</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-primary-600 hover:text-primary-900 mr-3" onclick="refreshNumber('44770090123')"><i class="fas fa-sync-alt"></i></button>
                            <button class="text-gray-600 hover:text-gray-900" onclick="showDetails('44770090123')"><i class="fas fa-info-circle"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">🇩🇪 Germany</td>
                        <td class="px-6 py-4 whitespace-nowrap">+49 151 23456789</td>
                        <td class="px-6 py-4 whitespace-nowrap">Telegram</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-sm text-gray-500 italic">Waiting...</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">5 mins ago</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-primary-600 hover:text-primary-900 mr-3" onclick="refreshNumber('4915123456789')"><i class="fas fa-sync-alt"></i></button>
                            <button class="text-gray-600 hover:text-gray-900" onclick="showDetails('4915123456789')"><i class="fas fa-info-circle"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Empty state -->
        <div id="empty-state" class="hidden py-12 text-center">
            <div class="inline-block p-4 rounded-full bg-gray-100 mb-4">
                <i class="fas fa-phone-slash text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">No numbers found</h3>
            <p class="mt-1 text-sm text-gray-500">Purchase a new number or try different filters.</p>
        </div>
    </div>

    <!-- Numbers Cards (Mobile) -->
    <div class="md:hidden space-y-4">
        <h2 class="text-lg font-semibold text-gray-900">Your International Numbers</h2>
        <div id="numbers-cards" class="space-y-4">
            <!-- Sample card, will be replaced by JavaScript -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="flex items-center">
                            <span class="text-lg font-medium">🇬🇧 +44 7700 900123</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">WhatsApp</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-500">SMS Code:</p>
                            <div class="flex items-center mt-1">
                                <span class="text-sm font-medium mr-2">123456</span>
                                <button class="text-gray-400 hover:text-gray-600" onclick="copySmsCode('123456')">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Created:</p>
                            <p class="text-sm">2 mins ago</p>
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 flex justify-end space-x-3">
                    <button class="text-primary-600 hover:text-primary-900" onclick="refreshNumber('44770090123')">
                        <i class="fas fa-sync-alt mr-1"></i> Refresh
                    </button>
                    <button class="text-gray-600 hover:text-gray-900" onclick="showDetails('44770090123')">
                        <i class="fas fa-info-circle mr-1"></i> Details
                    </button>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="flex items-center">
                            <span class="text-lg font-medium">🇩🇪 +49 151 23456789</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Telegram</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-500">SMS Code:</p>
                            <div class="flex items-center mt-1">
                                <span class="text-sm text-gray-500 italic">Waiting...</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Created:</p>
                            <p class="text-sm">5 mins ago</p>
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 flex justify-end space-x-3">
                    <button class="text-primary-600 hover:text-primary-900" onclick="refreshNumber('4915123456789')">
                        <i class="fas fa-sync-alt mr-1"></i> Refresh
                    </button>
                    <button class="text-gray-600 hover:text-gray-900" onclick="showDetails('4915123456789')">
                        <i class="fas fa-info-circle mr-1"></i> Details
                    </button>
                </div>
            </div>
        </div>
        <!-- Empty state mobile -->
        <div id="empty-state-mobile" class="hidden py-8 text-center bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="inline-block p-4 rounded-full bg-gray-100 mb-4">
                <i class="fas fa-phone-slash text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">No numbers found</h3>
            <p class="mt-1 text-sm text-gray-500">Purchase a new number or try different filters.</p>
        </div>
    </div>
</div>

<script>
    // Function to check status before purchase
    function checkStatus() {
        const statusResult = document.getElementById('status-result');
        const country = document.getElementById('country').value;
        const service = document.getElementById('service').value;
        
        if (!country || !service) {
            statusResult.innerHTML = '<div class="mt-3 p-3 bg-red-100 text-red-700 rounded-lg">Please select both country and service</div>';
            return;
        }
        
        // Show loading state
        statusResult.innerHTML = '<div class="mt-3 p-3 bg-gray-100 text-gray-700 rounded-lg">Checking availability... <i class="fas fa-spinner fa-spin ml-2"></i></div>';
        
        // Simulate API call with timeout
        setTimeout(() => {
            // Simulate success response
            statusResult.innerHTML = '<div class="mt-3 p-3 bg-green-100 text-green-700 rounded-lg">Service available! You can proceed with purchase.</div>';
            // Set statusChecked to true in Alpine.js
            const form = document.getElementById('internationalForm');
            form.__x.$data.statusChecked = true;
        }, 1500);
    }
    
    // Function to handle form submission
    document.getElementById('internationalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const country = document.getElementById('country').value;
        const service = document.getElementById('service').value;
        
        // Show loading state
        const statusResult = document.getElementById('status-result');
        statusResult.innerHTML = '<div class="mt-3 p-3 bg-gray-100 text-gray-700 rounded-lg">Processing purchase... <i class="fas fa-spinner fa-spin ml-2"></i></div>';
        
        // Simulate API call with timeout
        setTimeout(() => {
            // Simulate success response
            statusResult.innerHTML = '<div class="mt-3 p-3 bg-green-100 text-green-700 rounded-lg">Number purchased successfully! Check the numbers table below.</div>';
            
            // Reset form
            this.reset();
            const form = document.getElementById('internationalForm');
            form.__x.$data.selectedCountry = '';
            form.__x.$data.selectedService = '';
            form.__x.$data.servicePrice = 0;
            form.__x.$data.statusChecked = false;
            
            // Refresh the numbers table (in a real app, you would fetch from the server)
            // For demo, we'll just leave the sample data
        }, 2000);
    });
    
    // Function to copy SMS code to clipboard
    function copySmsCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            // Show a toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg';
            toast.textContent = 'SMS code copied to clipboard!';
            document.body.appendChild(toast);
            
            // Remove toast after 3 seconds
            setTimeout(() => {
                toast.remove();
            }, 3000);
        });
    }
    
    // Function to refresh a number
    function refreshNumber(number) {
        // In a real app, you would make an API call to refresh the number
        // For demo, we'll just show a loading spinner on the button
        const buttons = document.querySelectorAll(`button[onclick="refreshNumber('${number}')"`);
        
        buttons.forEach(button => {
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            button.disabled = true;
            
            // Simulate API call with timeout
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.disabled = false;
                
                // Show a toast notification
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg';
                toast.textContent = 'Number refreshed successfully!';
                document.body.appendChild(toast);
                
                // Remove toast after 3 seconds
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }, 1500);
        });
    }
    
    // Function to show number details
    function showDetails(number) {
        // In a real app, you would make an API call to get the details
        // For demo, we'll just show an alert
        alert(`Details for number ${number}\n\nThis would show a modal or page with detailed information about this number, including full history of SMS messages, timestamps, etc.`);
    }
    
    // Function to apply filters
    function applyFilters() {
        const countryFilter = document.getElementById('countryFilter').value;
        const serviceFilter = document.getElementById('serviceFilter').value;
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        
        // In a real app, you would make an API call to filter the numbers
        // For demo, we'll just show a loading state and then the same data
        
        // Show loading state
        const tableBody = document.getElementById('numbers-table-body');
        const cardsContainer = document.getElementById('numbers-cards');
        const emptyState = document.getElementById('empty-state');
        const emptyStateMobile = document.getElementById('empty-state-mobile');
        
        tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center">Loading... <i class="fas fa-spinner fa-spin ml-2"></i></td></tr>';
        cardsContainer.innerHTML = '<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">Loading... <i class="fas fa-spinner fa-spin ml-2"></i></div>';
        
        // Simulate API call with timeout
        setTimeout(() => {
            // If all filters are empty, show sample data
            if (!countryFilter && !serviceFilter && !searchInput) {
                // Reset to sample data (in a real app, you would fetch from the server)
                // For now, we'll just reload the page to restore the sample data
                location.reload();
                return;
            }
            
            // Simulate empty results for any filter combination
            tableBody.innerHTML = '';
            cardsContainer.innerHTML = '';
            emptyState.classList.remove('hidden');
            emptyStateMobile.classList.remove('hidden');
        }, 1000);
    }
    
    // Set up auto-refresh for pending numbers
    setInterval(() => {
        // In a real app, you would make an API call to check for updates
        // For demo, we'll just simulate a random update occasionally
        if (Math.random() > 0.8) {
            // Simulate a number being updated with an SMS code
            const pendingStatuses = document.querySelectorAll('.bg-yellow-100.text-yellow-800');
            if (pendingStatuses.length > 0) {
                const randomIndex = Math.floor(Math.random() * pendingStatuses.length);
                const statusElement = pendingStatuses[randomIndex];
                
                // Update status to active
                statusElement.classList.remove('bg-yellow-100', 'text-yellow-800');
                statusElement.classList.add('bg-green-100', 'text-green-800');
                statusElement.textContent = 'Active';
                
                // Find the corresponding SMS code element and update it
                const row = statusElement.closest('tr');
                if (row) {
                    const smsCodeCell = row.querySelector('td:nth-child(5) div');
                    if (smsCodeCell) {
                        const randomCode = Math.floor(100000 + Math.random() * 900000).toString();
                        smsCodeCell.innerHTML = `
                            <span class="text-sm text-gray-900 mr-2">${randomCode}</span>
                            <button class="text-gray-400 hover:text-gray-600" onclick="copySmsCode('${randomCode}')">
                                <i class="far fa-copy"></i>
                            </button>
                        `;
                    }
                }
                
                // Also update in mobile view
                const cards = document.querySelectorAll('#numbers-cards > div');
                if (cards.length > randomIndex) {
                    const card = cards[randomIndex];
                    const cardStatus = card.querySelector('.bg-yellow-100.text-yellow-800');
                    if (cardStatus) {
                        cardStatus.classList.remove('bg-yellow-100', 'text-yellow-800');
                        cardStatus.classList.add('bg-green-100', 'text-green-800');
                        cardStatus.textContent = 'Active';
                        
                        const smsCodeDiv = card.querySelector('.flex.items-center.mt-1');
                        if (smsCodeDiv) {
                            const randomCode = Math.floor(100000 + Math.random() * 900000).toString();
                            smsCodeDiv.innerHTML = `
                                <span class="text-sm font-medium mr-2">${randomCode}</span>
                                <button class="text-gray-400 hover:text-gray-600" onclick="copySmsCode('${randomCode}')">
                                    <i class="far fa-copy"></i>
                                </button>
                            `;
                        }
                    }
                }
                
                // Show a toast notification
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg';
                toast.textContent = 'New SMS code received!';
                document.body.appendChild(toast);
                
                // Remove toast after 3 seconds
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }
        }
    }, 10000); // Check every 10 seconds
</script>

@endsection

@push('scripts')
<script>
    // Form submission handling
    document.addEventListener('DOMContentLoaded', function() {
        const internationalForm = document.getElementById('internationalForm');
        if (internationalForm) {
            internationalForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const country = document.getElementById('country').value;
                const service = document.getElementById('service').value;
                
                // Show loading state
                toastr.info('Processing your request...');
                
                // In a real app, you would submit this via AJAX
                // For demo purposes, we'll just show a success message after a delay
                setTimeout(function() {
                    toastr.success('Number purchased successfully!');
                    // Reload the page to show the new number
                    // window.location.reload();
                }, 1500);
            });
        }
    });

    // Check status function for Alpine.js
    function checkStatus() {
        const statusResult = document.getElementById('status-result');
        statusResult.innerHTML = '<div class="mt-3 p-3 bg-blue-50 text-blue-700 rounded-lg"><i class="fas fa-spinner fa-spin mr-2"></i>Checking availability...</div>';
        
        // Simulate API call
        setTimeout(function() {
            statusResult.innerHTML = '<div class="mt-3 p-3 bg-green-50 text-green-700 rounded-lg"><i class="fas fa-check-circle mr-2"></i>Service is available! You can proceed with purchase.</div>';
            
            // Update Alpine.js state
            const form = document.getElementById('internationalForm');
            if (form && form.__x) {
                form.__x.$data.statusChecked = true;
            }
        }, 1500);
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            toastr.success('SMS code copied to clipboard!');
        }, function() {
            toastr.error('Failed to copy SMS code');
        });
    }
    
    function refreshNumber(id) {
        // Simulate refreshing
        toastr.info('Refreshing number...');
        
        // In a real app, you would make an AJAX call to refresh the number
        // For demo purposes, we'll just show a success message after a delay
        setTimeout(function() {
            toastr.success('Number refreshed successfully!');
        }, 1500);
    }
    
    function showDetails(id) {
        // In a real app, you would fetch details via AJAX and show them
        toastr.info('Showing details for number ID: ' + id);
    }
    
    // Auto-refresh pending numbers every 30 seconds
    setInterval(function() {
        // In a real app, you would check for pending numbers and refresh them
        console.log('Auto-refreshing pending numbers...');
    }, 30000);
</script>
@endpush


@push('scripts')
<script>
// Copy text function
function copyText(elementId) {
    const element = document.getElementById(elementId);
    const text = element.textContent;
    navigator.clipboard.writeText(text).then(() => {
        // Show tooltip or notification
        const tooltip = new bootstrap.Tooltip(element.nextElementSibling, {
            title: 'Copied!',
            trigger: 'manual'
        });
        tooltip.show();
        setTimeout(() => tooltip.hide(), 1000);
    });
}

// Check status function
function checkStatus(orderId) {
    fetch(`/order/${orderId}/status`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'expired') {
                const smsStatusElement = document.getElementById(`sms-status-${orderId}`);
                if (smsStatusElement) {
                    smsStatusElement.innerHTML = `<span class="text-danger"><i class="fas fa-times me-1"></i>Expired</span>`;
                }
                const timerElement = document.getElementById(`expires-at-${orderId}`);
                if (timerElement) {
                    timerElement.innerHTML = "EXPIRED";
                }
            } else if (data.sms_code) {
                location.reload(); // Reload to show the SMS code
            }
        })
        .catch(error => {
            console.error("Error checking status:", error);
        });
}

// Apply filters function
function applyFilters() {
    const countryFilter = document.getElementById('countryFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    // Build query string
    const params = new URLSearchParams();
    if (countryFilter) params.append('country', countryFilter);
    if (statusFilter) params.append('status', statusFilter);
    
    // Reload page with filters
    window.location.href = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
{{-- @endsection --}}