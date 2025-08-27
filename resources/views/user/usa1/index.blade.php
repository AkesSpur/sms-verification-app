@extends('layouts.user')

@section('title', 'USA Numbers 1')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">USA Numbers 1</h1>
            <p class="mt-1 text-sm text-gray-500">Get and manage your USA phone numbers</p>
        </div>
    </div>



    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-mobile-alt text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalRentals }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $activeRentalsCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Get New USA Number</h2>
        <form id="rentForm" class="space-y-4">
            @csrf
            <input type="hidden" name="country" value="us">
            
            <!-- Service Selection -->
            <div>
                <label for="serviceSelect" class="block text-sm font-medium text-gray-700 mb-2">Select Service</label>
                <div class="relative">
                    <input type="text" id="serviceSearch" placeholder="Search services..." 
                           class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 mb-3 transition-all duration-300">
                    <button type="button" id="clearSearch" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 hidden">
                        <i class="fas fa-times"></i>
                    </button>
                    <select name="service" id="serviceSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-300" required>
                        <option value="">Choose a service...</option>
                        @foreach($services as $service)
                            @php
                                $servicePrice = $service->getPriceForCountry('us');
                                $finalPrice = $servicePrice ? $servicePrice->final_price_naira : 0;
                            @endphp
                            <option value="{{ $service->code }}" data-price="{{ $finalPrice }}">
                                {{ $service->name }} - ₦{{ $servicePrice ? $finalPrice : 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    <div id="noResults" class="hidden mt-2 text-sm text-red-500">No matching services found</div>
                </div>
            </div>
            
            <!-- Price Display -->
            <div id="priceInfo" class="bg-gray-50 rounded-lg p-4 hidden">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Price:</span>
                    <span class="text-lg font-semibold text-gray-900" id="priceAmount"></span>
                </div>
            </div>
            
            <!-- Action Button -->
            <div class="pt-4">
                <button type="submit" id="purchaseBtn" class="w-full px-4 py-2 rounded-lg transition-colors bg-primary-600 text-white font-medium shadow-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2" disabled>
                    <i class="fas fa-shopping-cart mr-2"></i>Purchase
                </button>
            </div>
        </form>
    </div>

    <!-- Information Section -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600 text-lg mt-0.5"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">How USA Numbers Work</h3>
                <div class="text-sm text-blue-800 space-y-1">
                    <p><strong>Timer:</strong> Each order has a countdown timer showing time remaining to receive SMS.</p>
                    <p><strong>Auto-Refund:</strong> If no SMS is received within the time limit, your order will be automatically cancelled and your account will be refunded.</p>
                    <p><strong>Real-time Updates:</strong> Order status and timers update automatically every 30 seconds.</p>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Recent Purchases -->
    <div id="recent-purchases" class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Recent Purchases</h2>
                <button onclick="refreshOrders()" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                    <i class="fas fa-sync-alt mr-1"></i>Refresh
                </button>
            </div>
        </div>
        <div class="p-6">
            @if($rentals->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-primary-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-primary-900 uppercase tracking-wider">@lang('Phone Number')</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-primary-900 uppercase tracking-wider">@lang('Service')</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-primary-900 uppercase tracking-wider">@lang('Price')</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-primary-900 uppercase tracking-wider">@lang('Status')</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-primary-900 uppercase tracking-wider">@lang('SMS Code')</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-primary-900 uppercase tracking-wider">@lang('Date')</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-primary-900 uppercase tracking-wider">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($rentals as $rental)
                                <tr class="hover:bg-gray-50" data-status="{{ $rental->status }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-semibold text-gray-900">{{ formatPhoneNumber($rental->phone_number) }}</span>
                                            <button type="button" class="text-gray-400 hover:text-gray-600 p-1 rounded" onclick="copyToClipboard('{{ $rental->phone_number }}')" title="Copy phone number">
                                                <i class="fas fa-copy text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-blue-800" style="background-color: #dbeafe;">{{ ucfirst($rental->service_name) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-semibold text-gray-900">₦{{ number_format($rental->price,2) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($rental->status == 'pending')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-yellow-800" style="background-color: #fef3c7;">@lang('Pending')</span>
                                        @elseif($rental->status == 'active')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-blue-800" style="background-color: #dbeafe;">@lang('Active')</span>
                                            @if($rental->expires_at)
                                                <div class="countdown-timer mt-1" data-expires="{{ $rental->expires_at->toISOString() }}">
                                                    <small class="text-muted">Expires in: <span class="countdown-display">--:--</span></small>
                                                </div>
                                            @endif
                                        @elseif($rental->status == 'completed')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-green-800" style="background-color: #dcfce7;">@lang('Completed')</span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-red-800" style="background-color: #fecaca;">@lang('Cancelled')</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($rental->sms_code)
                                            <div class="flex items-center space-x-2">
                                                <span class="font-semibold text-green-600">{{ $rental->sms_code }}</span>
                                                <button type="button" class="text-gray-400 hover:text-gray-600 p-1 rounded" onclick="copyToClipboard('{{ $rental->sms_code }}')" title="Copy SMS code">
                                                    <i class="fas fa-copy text-sm"></i>
                                                </button>
                                            </div>
                                        @elseif($rental->status == 'cancelled')
                                            <span class="text-red-500">@lang('Cancelled')</span>
                                        @else
                                            <span class="text-gray-500">@lang('Waiting...')</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-gray-900">{{ showDateTime($rental->created_at, 'd M Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ diffForHumans($rental->created_at) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            @if(in_array($rental->status, ['pending', 'active']))
                                                <button type="button" class="bg-primary-600 hover:bg-primary-700 text-white px-3 py-2 text-sm rounded-lg font-medium transition-colors checkCodeBtn" data-id="{{ $rental->id }}">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                                <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 text-sm rounded-lg font-medium transition-colors cancelBtn" data-id="{{ $rental->id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                            <a href="{{ route('user.sms.rental.details', $rental->id) }}" class="bg-primary-600 hover:bg-primary-700 text-white px-3 py-2 text-sm rounded-lg font-medium transition-colors inline-flex items-center">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-4">
                    @foreach($rentals as $rental)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm" data-status="{{ $rental->status }}">
                            <!-- Header with Phone Number and Status -->
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <div class="flex items-center space-x-2 mb-1">
                                        <h3 class="font-semibold text-gray-900 text-lg">{{ formatPhoneNumber($rental->phone_number) }}</h3>
                                        <button type="button" class="text-gray-400 hover:text-gray-600 p-1 rounded" onclick="copyToClipboard('{{ $rental->phone_number }}')" title="Copy phone number">
                                            <i class="fas fa-copy text-sm"></i>
                                        </button>
                                    </div>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-blue-800" style="background-color: #dbeafe;">{{ ucfirst($rental->service_name) }}</span>
                                </div>
                                <div class="text-right">
                                    @if($rental->status == 'pending')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-yellow-800" style="background-color: #fef3c7;">@lang('Pending')</span>
                                    @elseif($rental->status == 'active')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-blue-800" style="background-color: #dbeafe;">@lang('Active')</span>
                                        @if($rental->expires_at)
                                            <div class="countdown-timer mt-1" data-expires="{{ $rental->expires_at->toISOString() }}">
                                                <small class="text-muted">Expires in: <span class="countdown-display">--:--</span></small>
                                            </div>
                                        @endif
                                    @elseif($rental->status == 'completed')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-green-800" style="background-color: #dcfce7;">@lang('Completed')</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-red-800" style="background-color: #fecaca;">@lang('Cancelled')</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Details Grid -->
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">@lang('Price')</p>
                                    <p class="font-semibold text-gray-900">₦{{ number_format($rental->price,2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">@lang('SMS Code')</p>
                                    @if($rental->sms_code)
                                        <div class="flex items-center space-x-2">
                                            <p class="font-semibold text-green-600">{{ $rental->sms_code }}</p>
                                            <button type="button" class="text-gray-400 hover:text-gray-600 p-1 rounded" onclick="copyToClipboard('{{ $rental->sms_code }}')" title="Copy SMS code">
                                                <i class="fas fa-copy text-sm"></i>
                                            </button>
                                        </div>
                                    @elseif($rental->status == 'cancelled')
                                        <p class="text-red-500">@lang('Cancelled')</p>
                                    @else
                                        <p class="text-gray-500">@lang('Waiting...')</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Date -->
                            <div class="mb-4">
                                <p class="text-xs text-gray-500 uppercase tracking-wider">@lang('Date')</p>
                                <p class="text-gray-900">{{ showDateTime($rental->created_at, 'd M Y') }}</p>
                                <p class="text-sm text-gray-500">{{ diffForHumans($rental->created_at) }}</p>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2 pt-3 border-t border-gray-100">
                                @if(in_array($rental->status, ['pending', 'active']))
                                    <button type="button" class="flex-1 text-white px-3 py-2 text-sm rounded-md font-medium checkCodeBtn" style="background-color: #2563eb;" onmouseover="this.style.backgroundColor='#1d4ed8'" onmouseout="this.style.backgroundColor='#2563eb'" data-id="{{ $rental->id }}">
                                        <i class="fas fa-sync mr-1"></i> @lang('Check Code')
                                    </button>
                                    <button type="button" class="flex-1 text-white px-3 py-2 text-sm rounded-md font-medium cancelBtn" style="background-color: #dc2626;" onmouseover="this.style.backgroundColor='#b91c1c'" onmouseout="this.style.backgroundColor='#dc2626'" data-id="{{ $rental->id }}">
                                        <i class="fas fa-times mr-1"></i> @lang('Cancel')
                                    </button>
                                @endif
                                <a href="{{ route('user.sms.rental.details', $rental->id) }}" class="flex-1 bg-primary-600 px-3 py-2 text-sm text-white rounded-md font-medium text-center">
                                    <i class="fas fa-eye mr-1"></i> @lang('View')
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $emptyMessage }}</h3>
                    <p class="text-gray-500">@lang('Start by purchasing your first number above')</p>
                </div>
            @endif
        </div>
    
    </div>


                    <!-- Pagination -->
            @if($rentals->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 mt-6 rounded-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if($rentals->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $rentals->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    Previous
                                </a>
                            @endif
                            
                            @if($rentals->hasMorePages())
                                <a href="{{ $rentals->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    Next
                                </a>
                            @else
                                <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                    Next
                                </span>
                            @endif
                        </div>
                        
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium">{{ $rentals->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $rentals->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $rentals->total() }}</span> results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    @if($rentals->onFirstPage())
                                        <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    @else
                                        <a href="{{ $rentals->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    @endif
                                    
                                    @foreach($rentals->getUrlRange(1, $rentals->lastPage()) as $page => $url)
                                        @if($page == $rentals->currentPage())
                                            <span class="relative inline-flex items-center px-4 py-2 border border-primary-500 bg-primary-600 text-sm font-medium text-white">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach
                                    
                                    @if($rentals->hasMorePages())
                                        <a href="{{ $rentals->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    @else
                                        <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    @endif
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
</div>

<!-- Cancel Order Modal -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="cancelModalContent">
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-red-100 rounded-full p-2">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">@lang('Cancel Purchase')</h3>
                </div>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeCancelModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Content -->
            <div class="mb-6">
                <p class="text-gray-600 leading-relaxed">
                    @lang('Are you sure you want to cancel this purchase? You may receive a partial refund depending on the current status.')
                </p>
            </div>
            
            <!-- Actions -->
            <div class="flex space-x-3">
                <button type="button" class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors" onclick="closeCancelModal()">
                    @lang('Keep Purchase')
                </button>
                <button type="button" class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-colors" id="confirmCancelBtn">
                    @lang('Cancel Purchase')
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Important Notice Modal -->
<div id="importantNoticeModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-megaphone text-red-500 mr-3"></i>
                    Important Announcement
                </h3>
                <button type="button" onclick="closeImportantNoticeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-bold text-blue-900 mb-3 flex items-center">
                        <i class="fab fa-whatsapp text-green-500 mr-2"></i>
                        WhatsApp & Telegram Guidelines:
                    </h4>
                    <ul class="space-y-3 text-sm text-blue-800">
                        <li class="flex items-start">
                            <i class="fas fa-mobile-alt text-blue-600 mr-3 mt-0.5 flex-shrink-0"></i>
                            <span><strong>Fresh Installation:</strong> Uninstall and reinstall WhatsApp or Telegram before requesting a number for optimal results.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-ban text-red-500 mr-3 mt-0.5 flex-shrink-0"></i>
                            <span><strong>Account Type:</strong> Use regular WhatsApp only. Avoid WhatsApp Business for verification purposes.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-globe text-blue-600 mr-3 mt-0.5 flex-shrink-0"></i>
                            <span><strong>Location Settings:</strong> Make sure your device timezone and VPN location match the country of your purchased number.</span>
                        </li>
                    </ul>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-xs text-yellow-800 flex items-center">
                        <i class="fas fa-lightbulb text-yellow-600 mr-2"></i>
                        Following these guidelines significantly increases your verification success rate.
                    </p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="closeImportantNoticeModal()" 
                        class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-all duration-200">
                    Got it, thanks!
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openImportantNoticeModal() {
    const modal = document.getElementById('importantNoticeModal');
    const content = document.getElementById('modalContent');
    
    modal.classList.remove('hidden');
    
    // Trigger animation
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closeImportantNoticeModal() {
    const modal = document.getElementById('importantNoticeModal');
    const content = document.getElementById('modalContent');
    
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }, 300);
}

// Close modal when clicking outside
document.getElementById('importantNoticeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImportantNoticeModal();
    }
});
</script>

@endsection

@push('scripts')
<script>
(function ($) {
    "use strict";
    
    // Service search functionality
    const serviceSearch = document.getElementById('serviceSearch');
    const serviceSelect = document.getElementById('serviceSelect');
    const clearSearchBtn = document.getElementById('clearSearch');
    const noResultsMsg = document.getElementById('noResults');
    
    // Store all options for filtering
    const allOptions = Array.from(serviceSelect.options).slice(1); // Skip the first 'Select Service' option
    
    // Filter options based on search input
    function filterOptions(searchTerm) {
        searchTerm = searchTerm.toLowerCase().trim();
        
        // Show/hide clear button
        if (clearSearchBtn) {
            clearSearchBtn.classList.toggle('hidden', searchTerm === '');
        }
        
        // Reset select to show all options first
        while (serviceSelect.options.length > 1) {
            serviceSelect.remove(1);
        }
        
        // Filter and add matching options
        let matchCount = 0;
        
        allOptions.forEach(option => {
            const optionText = option.text.toLowerCase();
            if (searchTerm === '' || optionText.includes(searchTerm)) {
                serviceSelect.add(option.cloneNode(true));
                matchCount++;
            }
        });
        
        // Show/hide no results message
        noResultsMsg.classList.toggle('hidden', matchCount > 0);
        
        // Reset selection if no matches
        if (matchCount === 0) {
            serviceSelect.value = '';
            $('#priceInfo').addClass('hidden');
            $('#purchaseBtn').prop('disabled', true);
        }
    }
    
    // Search input event
    if (serviceSearch) {
        serviceSearch.addEventListener('input', function() {
            filterOptions(this.value);
        });
    }
    
    // Clear search button
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            serviceSearch.value = '';
            filterOptions('');
            serviceSearch.focus();
        });
    }
    
    // Service change event
    $('#serviceSelect').on('change', function() {
        const service = $(this).val();
        const selectedOption = $(this).find('option:selected');
        const price = selectedOption.data('price');
        
        if (service && service !== '' && price && price > 0) {
            const priceText = selectedOption.text().split(' - ')[1];
            if (priceText && priceText !== 'N/A') {
                $('#priceAmount').text(priceText);
                $('#priceInfo').removeClass('hidden');
                $('#purchaseBtn').prop('disabled', false);
            } else {
                $('#priceInfo').addClass('hidden');
                $('#purchaseBtn').prop('disabled', true);
            }
        } else {
            $('#priceInfo').addClass('hidden');
            $('#purchaseBtn').prop('disabled', true);
        }
    });
    
    // Rent form submission
    $('#rentForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get service value using multiple methods to ensure we capture it
        let service = $('#serviceSelect').val();
        
        // Fallback: try getting from the form data directly
        if (!service || service === '') {
            service = $(this).find('select[name="service"]').val();
        }
        
        // Fallback: try getting from native DOM
        if (!service || service === '') {
            const serviceElement = document.getElementById('serviceSelect');
            if (serviceElement) {
                service = serviceElement.value;
            }
        }
        
        if (!service || service === '') {
            notify('error', '@lang("Please select a service")');
            return;
        }
        
        const $btn = $('#purchaseBtn');
        const originalText = $btn.html();
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner la-spin"></i> @lang("Processing...")');
        

        // Use FormData to ensure proper serialization
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('service', service);
        formData.append('country', 'us');
        
        $.ajax({
            url: '{{ route("user.sms.rental.rent") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    notify('success', response.message);
                    location.reload();
                } else {
                    notify('error', response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                notify('error', response?.message || '@lang("Something went wrong")');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Auto-check codes for active orders
    function autoCheckCodes() {
        $('.checkCodeBtn').each(function() {
            const $btn = $(this);
            const id = $btn.data('id');
            const $row = $btn.closest('tr, .bg-white');
            const status = $row.data('status') || $row.attr('data-status');
            
            // Only auto-check active and pending orders
            if (status === 'active' || status === 'pending') {
                checkCodeSilently(id);
            }
        });
    }
    
    // Silent check function for auto-checking
    function checkCodeSilently(id) {
        $.ajax({
            url: `{{ route('user.sms.rental.check.code', ':id') }}`.replace(':id', id),
            method: 'GET',
            success: function(response) {
                if (response.success && response.sms_code) {
                    // Only reload if SMS code is received
                    location.reload();
                }
            },
            error: function(xhr) {
                // Silent fail for auto-check
                console.log('Auto-check failed for order:', id);
            }
        });
    }
    
    // Check code button
    $('.checkCodeBtn').on('click', function() {
        const id = $(this).data('id');
        const btn = $(this);
        const originalText = btn.html();
        
        btn.html('<i class="fas fa-spinner la-spin"></i>');
        btn.prop('disabled', true);
        
        $.ajax({
            url: `{{ route('user.sms.rental.check.code', ':id') }}`.replace(':id', id),
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    notify('success', response.message);
                    if (response.reload) {
                        // Update SMS code in place instead of full page reload
                        if (response.sms_code) {
                            updateSmsCodeDisplay(id, response.sms_code);
                            // Hide action buttons for completed rentals
                            const $row = btn.closest('tr, .bg-white');
                            $row.find('.checkCodeBtn, .cancelBtn').hide();
                            // Update status to completed
                            updateStatusDisplay(id, 'completed');
                        } else {
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        }
                    }
                } else {
                    notify('info', response.message);
                    setTimeout(() => {
                                location.reload();
                            }, 1500);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                notify('error', response.message || '@lang("Something went wrong")');
            },
            complete: function() {
                btn.html(originalText);
                btn.prop('disabled', false);
            }
        });
    });
    
    // Cancel button
    let currentCancelId = null;
    let currentCancelBtn = null;
    
    $('.cancelBtn').on('click', function() {
        currentCancelId = $(this).data('id');
        currentCancelBtn = $(this);
        openCancelModal();
    });
    
    // Confirm cancel button in modal
    $('#confirmCancelBtn').on('click', function() {
        if (currentCancelId && currentCancelBtn) {
            const btn = currentCancelBtn;
            const originalText = btn.html();
            
            // Close modal first
            closeCancelModal();
            
            // Show loading state
            btn.html('<i class="fas fa-spinner fa-spin"></i>');
            btn.prop('disabled', true);
            
            $.ajax({
                url: `{{ route('user.sms.rental.cancel', ':id') }}`.replace(':id', currentCancelId),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        notify('success', response.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        notify('error', response.message);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    notify('error', response.message || '@lang("Something went wrong")');
                },
                complete: function() {
                    btn.html(originalText);
                    btn.prop('disabled', false);
                    currentCancelId = null;
                    currentCancelBtn = null;
                }
            });
        }
    });
    

    
})(jQuery);
    // Phone number formatting function
    function formatPhoneNumber(phoneNumber) {
        // Remove any non-digit characters
        const cleaned = phoneNumber.replace(/\D/g, '');
        
        // Format based on length
        if (cleaned.length === 10) {
            // US format: (123) 456-7890
            return `(${cleaned.slice(0, 3)}) ${cleaned.slice(3, 6)}-${cleaned.slice(6)}`;
        } else if (cleaned.length === 11 && cleaned[0] === '1') {
            // US format with country code: +1 (123) 456-7890
            return `+1 (${cleaned.slice(1, 4)}) ${cleaned.slice(4, 7)}-${cleaned.slice(7)}`;
        } else if (cleaned.length > 7) {
            // International format: +XX XXX XXX XXXX
            const countryCode = cleaned.slice(0, -10);
            const areaCode = cleaned.slice(-10, -7);
            const firstPart = cleaned.slice(-7, -4);
            const lastPart = cleaned.slice(-4);
            return `+${countryCode} ${areaCode} ${firstPart} ${lastPart}`;
        }
        
        // Return original if can't format
        return phoneNumber;
    }

    // Copy to clipboard function
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            notify('success', '@lang("Copied successfully!")');
        }).catch(function(err) {
            console.error('Failed to copy: ', err);
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            notify('success', '@lang("Copied successfully!")');
        });
    }

    // Update SMS code display in place
    function updateSmsCodeDisplay(rentalId, smsCode) {
        // Update desktop table view
        $(`.checkCodeBtn[data-id="${rentalId}"]`).closest('tr').find('td:nth-child(5)').html(`
            <div class="flex items-center space-x-2">
                <span class="font-semibold text-green-600">${smsCode}</span>
                <button type="button" class="text-gray-400 hover:text-gray-600 p-1 rounded" onclick="copyToClipboard('${smsCode}')" title="Copy SMS code">
                    <i class="fas fa-copy text-sm"></i>
                </button>
            </div>
        `);
        
        // Update mobile card view
        $(`.checkCodeBtn[data-id="${rentalId}"]`).closest('.bg-white').find('.grid .text-xs:contains("SMS Code")').next().html(`
            <div class="flex items-center space-x-2">
                <p class="font-semibold text-green-600">${smsCode}</p>
                <button type="button" class="text-gray-400 hover:text-gray-600 p-1 rounded" onclick="copyToClipboard('${smsCode}')" title="Copy SMS code">
                    <i class="fas fa-copy text-sm"></i>
                </button>
            </div>
        `);
    }

    // Update status display in place
    function updateStatusDisplay(rentalId, status) {
        const statusHtml = '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-green-800" style="background-color: #dcfce7;">@lang("Completed")</span>';
        
        // Update desktop table view
        $(`.checkCodeBtn[data-id="${rentalId}"]`).closest('tr').find('td:nth-child(4)').html(statusHtml);
        
        // Update mobile card view
        $(`.checkCodeBtn[data-id="${rentalId}"]`).closest('.bg-white').find('.text-right span').replaceWith(statusHtml);
    }



    // Countdown timer functionality
    function initCountdownTimers() {
        $('.countdown-timer').each(function() {
            const $timer = $(this);
            const expiresAt = new Date($timer.data('expires'));
            const $display = $timer.find('.countdown-display');
            
            function updateCountdown() {
                const now = new Date();
                const timeLeft = expiresAt - now;
                
                if (timeLeft <= 0) {
                    $display.text('Expired');
                    $timer.addClass('text-danger');
                    // Auto-cancel if expired
                    autoCancel($timer.closest('.purchase-item'));
                    return;
                }
                
                const minutes = Math.floor(timeLeft / 60000);
                const seconds = Math.floor((timeLeft % 60000) / 1000);
                $display.text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
                
                // Change color when less than 2 minutes left
                if (timeLeft < 120000) {
                    $timer.addClass('text-warning');
                }
            }
            
            updateCountdown();
            const interval = setInterval(updateCountdown, 1000);
            
            // Store interval for cleanup
            $timer.data('interval', interval);
        });
    }
    
    // Auto-cancel expired purchases
    function autoCancel($purchaseItem) {
        const purchaseId = $purchaseItem.find('.cancelBtn').data('id');
        if (!purchaseId) return;
        
        // Mark as auto-cancelling to prevent user actions
        $purchaseItem.addClass('auto-cancelling');
        
        $.ajax({
            url: `{{ route('user.sms.rental.index') }}/auto-cancel/${purchaseId}`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    notify('info', 'Purchase automatically cancelled due to expiration');
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
            },
            error: function() {
                // Silent fail for auto-cancel
                console.log('Auto-cancel failed for purchase:', purchaseId);
            }
        });
    }
    
    // Auto-check codes for active/pending orders
    function autoCheckCodes() {
        // Find all active and pending orders
        const activeOrders = $('[data-status="active"], [data-status="pending"]');
        
        activeOrders.each(function() {
            const $row = $(this);
            const checkBtn = $row.find('.checkCodeBtn');
            
            if (checkBtn.length > 0) {
                const rentalId = checkBtn.data('id');
                if (rentalId) {
                    checkCodeSilently(rentalId);
                }
            }
        });
    }
    
    // Silent check for SMS codes (no user feedback)
    function checkCodeSilently(rentalId) {
        $.ajax({
            url: `{{ route('user.sms.rental.check.code', ':id') }}`.replace(':id', rentalId),
            method: 'GET',
            success: function(response) {
                if (response.success && response.sms_code) {
                    // SMS code received - refresh the page to show updated status
                    location.reload();
                }
                // If no SMS code yet, do nothing (silent check)
            },
            error: function() {
                // Silent fail - don't show error messages for auto-checks
                console.log('Auto-check failed for rental:', rentalId);
            }
        });
    }

    // Refresh orders function
    function refreshOrders() {
        // Show loading state
        const refreshBtn = $('button[onclick="refreshOrders()"]');
        const originalHtml = refreshBtn.html();
        refreshBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Refreshing...');
        refreshBtn.prop('disabled', true);
        
        // Reload the page after a short delay
        setTimeout(() => {
            location.reload();
        }, 500);
    }
    
    // Initialize countdown timers on page load
    $(document).ready(function() {
        initCountdownTimers();
        // Update countdown every second
        setInterval(initCountdownTimers, 1000);
        
        // Start auto-checking codes every 30 seconds
        setInterval(autoCheckCodes, 30000);
    });
    
    // Modal functions
    function openCancelModal() {
        const modal = $('#cancelModal');
        const content = $('#cancelModalContent');
        
        modal.removeClass('hidden');
        
        // Trigger animation
        setTimeout(() => {
            content.removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
        }, 10);
        
        // Prevent body scroll
        $('body').addClass('overflow-hidden');
        
        // Close on backdrop click
        modal.on('click', function(e) {
            if (e.target === this) {
                closeCancelModal();
            }
        });
        
        // Close on escape key
        $(document).on('keydown.cancelModal', function(e) {
            if (e.key === 'Escape') {
                closeCancelModal();
            }
        });
    }
    
    function closeCancelModal() {
        const modal = $('#cancelModal');
        const content = $('#cancelModalContent');
        
        content.removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        
        setTimeout(() => {
            modal.addClass('hidden');
            $('body').removeClass('overflow-hidden');
        }, 300);
        
        // Remove event listeners
        modal.off('click');
        $(document).off('keydown.cancelModal');
        
        // Reset variables
        currentCancelId = null;
        currentCancelBtn = null;
    }
</script>
@endpush