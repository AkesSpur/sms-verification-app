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

    <!-- Information Section -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-blue-900">How International Numbers Work</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>Timer:</strong> Each number has a 20-minute window to receive SMS</li>
                        <li><strong>Auto-Refund:</strong> If no SMS is received within the time limit, you'll get an automatic refund</li>
                        <li><strong>Real-time Updates:</strong> The page automatically checks for new SMS codes every 30 seconds</li>
                        <li><strong>Multiple Countries:</strong> Choose from various international locations for your verification needs</li>
                    </ul>
                </div>
            </div>
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
                <div class="relative">
                    <input type="text" id="countrySearch" placeholder="Search countries..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 mb-2">
                    <select id="country" name="country" x-model="selectedCountry" @change="statusChecked = false" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" size="1">
                        <option value="">Choose a country...</option>
                        @foreach($countries->sortBy('name') as $country)
                            <option value="{{ $country->code }}" data-name="{{ strtolower($country->name) }}">
                                {{ $country->flag ?? '🌍' }} {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Service Selection -->
            <div x-show="selectedCountry" x-cloak>
                <label for="service" class="block text-sm font-medium text-gray-700 mb-2">Select Service</label>
                <div class="relative">
                    <input type="text" id="serviceSearch" placeholder="Search services..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 mb-2">
                    <select id="service" name="service" x-model="selectedService" 
                            @change="servicePrice = $event.target.options[$event.target.selectedIndex].dataset.price; statusChecked = false" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" size="1">
                        <option value="">Choose a service...</option>
                        @foreach($services->sortBy('name') as $service)
                            <option value="{{ $service->code }}" data-price="{{ $service->price ?? '0.00' }}" data-name="{{ strtolower($service->name) }}">
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Price Display - Hidden until availability check -->
            <!-- <div x-show="selectedService" x-cloak class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Price:</span>
                    <span class="text-lg font-semibold text-gray-900" x-text="'₦' + servicePrice.toLocaleString()"></span>
                </div>
            </div> -->

            <!-- Action Buttons -->
            <div x-show="selectedService" x-cloak class="flex space-x-3">
                <button type="button" @click="checkAvailability()" 
                        class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-search mr-2"></i>Check Availability
                </button>
                <button type="submit" :disabled="!statusChecked" 
                        :class="statusChecked ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                        class="flex-1 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-shopping-cart mr-2"></i>Purchase
                </button>
            </div>

            <!-- Status Result -->
            <div id="availability-result"></div>
        </form>
    </div>

    <!-- Active Orders Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hidden md:block">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Active International Orders</h2>
        <div id="active-orders-section">
            {{-- Active orders are already passed from the controller --}}
            
            @if($activeOrders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone Number</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SMS Code</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timer</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider justify-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="active-orders-table-body">
                            @foreach($activeOrders as $order)
                                <tr id="order-{{ $order->id }}" data-order-id="{{ $order->id }}" data-status="{{ $order->status }}" data-created-at="{{ $order->created_at->toISOString() }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ $order->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <span class="mr-2">{{ $order->phone_number }}</span>
                                            <button class="text-gray-400 hover:text-gray-600" onclick="copyToClipboard('{{ $order->phone_number }}')" title="Copy phone number">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->service->name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center sms-code-cell" id="sms-code-{{ $order->id }}">
                                            @if($order->sms_code)
                                                <span class="text-sm font-medium text-gray-900 mr-2">{{ $order->sms_code }}</span>
                                                <button class="text-gray-400 hover:text-gray-600" onclick="copyToClipboard('{{ $order->sms_code }}')">
                                                    <i class="far fa-copy"></i>
                                                </button>
                                            @else
                                                <span class="text-sm text-gray-500 italic">Waiting for SMS...</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 created-at-cell">
                                        {{ $order->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" id="timer-{{ $order->id }}">
                                        @if($order->sms_window_expires_at && $order->sms_window_expires_at->isFuture())
                                            <span class="timer-display" data-expires="{{ $order->sms_window_expires_at->toISOString() }}"></span>
                                        @else
                                            <span class="text-red-500">Expired</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap status-cell" id="status-{{ $order->id }}">
                                        <div class="flex flex-col">
                                            @if($order->status === 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                            @elseif($order->status === 'active')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Active</span>
                                            @elseif($order->status === 'completed')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                            @elseif($order->status === 'cancelled')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($order->status) }}</span>
                                            @endif
                                            @if($order->status === 'cancelled' && isset($order->refunded))
                                                @if($order->refunded)
                                                    <span class="text-xs text-green-600 mt-1">
                                                        <i class="fas fa-check-circle mr-1"></i>Refunded
                                                    </span>
                                                @else
                                                    <span class="text-xs text-red-600 mt-1">
                                                        <i class="fas fa-times-circle mr-1"></i>No Refund
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex justify-center space-x-2">
                                            <button class="text-primary-600 hover:text-primary-900 refresh-btn" onclick="checkOrderStatus({{ $order->id }})" title="Refresh Status">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                            @if(in_array($order->status, ['pending', 'active']))
                                                <button class="text-red-600 hover:text-red-900" onclick="cancelOrder({{ $order->id }})" title="Cancel Order">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                            <a href="{{ route('international.order.show', $order->id) }}" class="text-gray-600 hover:text-gray-900" title="View Details">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                </tbody>
             </table>
                 </div>
             @else
                 <!-- Empty state -->
                 <div class="py-12 text-center">
                     <div class="inline-block p-4 rounded-full bg-gray-100 mb-4">
                         <i class="fas fa-phone-slash text-2xl text-gray-400"></i>
                     </div>
                     <h3 class="text-lg font-medium text-gray-900">No active orders</h3>
                     <p class="mt-1 text-sm text-gray-500">Purchase a new international number to get started.</p>
                 </div>
             @endif
         </div>
     </div>

    <!-- Active Orders Cards (Mobile) -->
    <div class="md:hidden space-y-4">
        <h2 class="text-lg font-semibold text-gray-900">Active International Orders</h2>
        <div id="active-orders-mobile" class="space-y-4">
            @if($activeOrders->count() > 0)
                @foreach($activeOrders as $order)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4" id="mobile-order-{{ $order->id }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="flex items-center">
                                    <span class="text-lg font-medium">#{{ $order->id }} - {{ $order->phone_number }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ $order->service->name }}</p>
                            </div>
                            <div class="flex flex-col items-end">
                                @if($order->status === 'pending')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($order->status === 'active')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Active</span>
                                @elseif($order->status === 'completed')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                @elseif($order->status === 'cancelled')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($order->status) }}</span>
                                @endif
                                @if($order->status === 'cancelled' && isset($order->refunded))
                                    @if($order->refunded)
                                        <span class="text-xs text-green-600 mt-1">
                                            <i class="fas fa-check-circle mr-1"></i>Refunded
                                        </span>
                                    @else
                                        <span class="text-xs text-red-600 mt-1">
                                            <i class="fas fa-times-circle mr-1"></i>No Refund
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-xs text-gray-500">SMS Code:</p>
                                    <div class="flex items-center mt-1">
                                        @if($order->sms_code)
                                            <span class="text-sm font-medium mr-2">{{ $order->sms_code }}</span>
                                            <button class="text-gray-400 hover:text-gray-600" onclick="copyToClipboard('{{ $order->sms_code }}')">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        @else
                                            <span class="text-sm text-gray-500 italic">Waiting for SMS...</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Created:</p>
                                    <p class="text-sm">{{ $order->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100 flex justify-end space-x-3">
                            <button class="text-primary-600 hover:text-primary-900 refresh-btn" onclick="checkOrderStatus({{ $order->id }})">
                                <i class="fas fa-sync-alt mr-1"></i> Refresh
                            </button>
                            @if(in_array($order->status, ['pending', 'active']))
                                <button class="text-red-600 hover:text-red-900" onclick="cancelOrder({{ $order->id }})">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </button>
                            @endif
                            <a href="{{ route('international.order.show', $order->id) }}" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-info-circle mr-1"></i> Details
                            </a>
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Empty state for mobile -->
                <div class="py-12 text-center">
                    <div class="inline-block p-4 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-phone-slash text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No active orders</h3>
                    <p class="mt-1 text-sm text-gray-500">Purchase a new international number to get started.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Past Orders Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Order History</h2>
                <p class="mt-1 text-sm text-gray-500">View all your past international number orders</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <select id="historyStatusFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                    <option value="">All Status</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="expired">Expired</option>
                    <option value="refunded">Refunded</option>
                </select>
                <button type="button" onclick="refreshOrderHistory()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm refresh-btn">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block">
            @if($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone Number</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SMS Code</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ $order->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($order->phone_number)
                                            <div class="flex items-center space-x-2">
                                                <span class="font-mono">{{ $order->phone_number }}</span>
                                                <button onclick="copyToClipboard('{{ $order->phone_number }}')" 
                                                        class="text-gray-400 hover:text-gray-600 transition-colors" 
                                                        title="Copy phone number">
                                                    <i class="fas fa-copy text-xs"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->service->name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($order->country)
                                            <div class="flex items-center space-x-2">
                                                <span>{{ $order->country->flag ?? '🌍' }}</span>
                                                <span>{{ $order->country->name ?? 'Unknown' }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400">Unknown</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($order->sms_code)
                                            <div class="flex items-center space-x-2">
                                                <span class="font-mono bg-green-100 text-green-800 px-2 py-1 rounded text-xs">{{ $order->sms_code }}</span>
                                                <button onclick="copyToClipboard('{{ $order->sms_code }}')" 
                                                        class="text-gray-400 hover:text-gray-600 transition-colors" 
                                                        title="Copy SMS code">
                                                    <i class="fas fa-copy text-xs"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-gray-400">No SMS</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>
                                            <div>{{ $order->created_at->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-400">{{ $order->created_at->format('H:i') }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClasses = [
                                                'completed' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                'expired' => 'bg-yellow-100 text-yellow-800',
                                                'pending' => 'bg-blue-100 text-blue-800',
                                                'active' => 'bg-purple-100 text-purple-800',
                                            ];
                                            $statusClass = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        @if($order->refunded)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 ml-1">
                                                Refunded
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('international.order.show', $order->id) }}" 
                                               class="text-primary-600 hover:text-primary-900 transition-colors" 
                                               title="View details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($order->sms_code)
                                                <button onclick="copyToClipboard('{{ $order->sms_code }}')" 
                                                        class="text-green-600 hover:text-green-900 transition-colors" 
                                                        title="Copy SMS code">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 text-center">
                    <div class="inline-block p-4 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-history text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No order history</h3>
                    <p class="mt-1 text-sm text-gray-500">Your past orders will appear here once you make your first purchase.</p>
                </div>
            @endif
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-4">
            @if($orders->count() > 0)
                @foreach($orders as $order)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-medium text-gray-900">#{{ $order->id }}</h3>
                                <p class="text-sm text-gray-500">{{ $order->service->name ?? 'Unknown' }}</p>
                            </div>
                            <div class="text-right">
                                @php
                                    $statusClasses = [
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'expired' => 'bg-yellow-100 text-yellow-800',
                                        'pending' => 'bg-blue-100 text-blue-800',
                                        'active' => 'bg-purple-100 text-purple-800',
                                    ];
                                    $statusClass = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                                @if($order->status === 'cancelled' && isset($order->refunded))
                                    @if($order->refunded)
                                        <span class="text-xs text-green-600 mt-1">
                                            <i class="fas fa-check-circle mr-1"></i>Refunded
                                        </span>
                                    @else
                                        <span class="text-xs text-red-600 mt-1">
                                            <i class="fas fa-times-circle mr-1"></i>No Refund
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        
                        <div class="space-y-2 text-sm">
                            @if($order->phone_number)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Phone:</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="font-mono">{{ $order->phone_number }}</span>
                                        <button onclick="copyToClipboard('{{ $order->phone_number }}')" 
                                                class="text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-copy text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                            
                            @if($order->country)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Country:</span>
                                    <div class="flex items-center space-x-1">
                                        <span>{{ $order->country->flag ?? '🌍' }}</span>
                                        <span>{{ $order->country->name ?? 'Unknown' }}</span>
                                    </div>
                                </div>
                            @endif
                            
                            @if($order->sms_code)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">SMS Code:</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="font-mono bg-green-100 text-green-800 px-2 py-1 rounded text-xs">{{ $order->sms_code }}</span>
                                        <button onclick="copyToClipboard('{{ $order->sms_code }}')" 
                                                class="text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-copy text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="flex justify-between">
                                <span class="text-gray-500">Date:</span>
                                <span>{{ $order->created_at->format('M d, Y H:i') }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-3 pt-3 border-t border-gray-100 flex justify-end">
                            <a href="{{ route('international.order.show', $order->id) }}" 
                               class="text-primary-600 hover:text-primary-900">
                                <i class="fas fa-eye mr-1"></i> View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="py-12 text-center">
                    <div class="inline-block p-4 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-history text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No order history</h3>
                    <p class="mt-1 text-sm text-gray-500">Your past orders will appear here once you make your first purchase.</p>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6 mt-6 rounded-lg shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        @if($orders->onFirstPage())
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                Previous
                            </span>
                        @else
                            <a href="{{ $orders->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                Previous
                            </a>
                        @endif
                        
                        @if($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
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
                                Showing <span class="font-medium">{{ $orders->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $orders->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $orders->total() }}</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                @if($orders->onFirstPage())
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                @else
                                    <a href="{{ $orders->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                @endif
                                
                                @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                                    @if($page == $orders->currentPage())
                                        <span class="relative inline-flex items-center px-4 py-2 border border-primary-500 bg-primary-600 text-sm font-medium text-white">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach
                                
                                @if($orders->hasMorePages())
                                    <a href="{{ $orders->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors">
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
</div>

<script>
    // Global function for Alpine.js to use
    function checkAvailability() {
        const statusResult = document.getElementById('availability-result');
        const country = document.getElementById('country').value;
        const service = document.getElementById('service').value;
        
        if (!country || !service) {
            statusResult.innerHTML = '<div class="mt-3 p-3 bg-red-100 text-red-700 rounded-lg">Please select both country and service</div>';
            return;
        }
        
        // Show loading state
        statusResult.innerHTML = '<div class="mt-3 p-3 bg-gray-100 text-gray-700 rounded-lg">Checking availability... <i class="fas fa-spinner fa-spin ml-2"></i></div>';
        
        // Make AJAX request to check availability
        fetch('/user/international/check-availability', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                country: country,
                service: service
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.available) {
                    statusResult.innerHTML = '<div class="mt-3 p-3 bg-green-100 text-green-700 rounded-lg"><i class="fas fa-check-circle mr-2"></i>Service available Price: ₦' + data.price + '</div>';
                    // Set statusChecked to true in Alpine.js using Alpine's proper API
                    const form = document.getElementById('internationalForm');
                    if (form && form._x_dataStack) {
                        const alpineData = form._x_dataStack[0];
                        alpineData.statusChecked = true;
                        alpineData.servicePrice = parseFloat(data.price);
                    } else if (form && form.__x) {
                        // Fallback for older Alpine versions
                        form.__x.$data.statusChecked = true;
                        form.__x.$data.servicePrice = parseFloat(data.price);
                    }
                } else {
                    statusResult.innerHTML = '<div class="mt-3 p-3 bg-red-100 text-red-700 rounded-lg"><i class="fas fa-times-circle mr-2"></i>Service currently unavailable</div>';
                    // Reset statusChecked when service is not available
                    const form = document.getElementById('internationalForm');
                    if (form && form._x_dataStack) {
                        const alpineData = form._x_dataStack[0];
                        alpineData.statusChecked = false;
                    } else if (form && form.__x) {
                        form.__x.$data.statusChecked = false;
                    }
                }
            } else {
                statusResult.innerHTML = '<div class="mt-3 p-3 bg-red-100 text-red-700 rounded-lg"><i class="fas fa-exclamation-triangle mr-2"></i>' + (data.message || 'Error checking availability') + '</div>';
                // Reset statusChecked on error
                const form = document.getElementById('internationalForm');
                if (form && form._x_dataStack) {
                    const alpineData = form._x_dataStack[0];
                    alpineData.statusChecked = false;
                } else if (form && form.__x) {
                    form.__x.$data.statusChecked = false;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            statusResult.innerHTML = '<div class="mt-3 p-3 bg-red-100 text-red-700 rounded-lg"><i class="fas fa-exclamation-triangle mr-2"></i>Error checking availability</div>';
            // Reset statusChecked on error
            const form = document.getElementById('internationalForm');
            if (form && form._x_dataStack) {
                const alpineData = form._x_dataStack[0];
                alpineData.statusChecked = false;
            } else if (form && form.__x) {
                form.__x.$data.statusChecked = false;
            }
        });
    }

// Search functionality for countries
document.getElementById('countrySearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const select = document.getElementById('country');
    const options = select.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
            return;
        }
        
        const countryName = option.getAttribute('data-name');
        if (countryName && countryName.includes(searchTerm)) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
});

// Search functionality for services
document.getElementById('serviceSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const select = document.getElementById('service');
    const options = select.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
            return;
        }
        
        const serviceName = option.getAttribute('data-name');
        if (serviceName && serviceName.includes(searchTerm)) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
});

// Clear search when country changes
document.getElementById('country').addEventListener('change', function() {
    document.getElementById('serviceSearch').value = '';
    const serviceOptions = document.getElementById('service').querySelectorAll('option');
    serviceOptions.forEach(option => {
        option.style.display = 'block';
    });
});

// Click-away functionality for select dropdowns
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('country');
    const serviceSelect = document.getElementById('service');
    
    // Function to close select when clicking outside
    function closeSelectOnClickAway(selectElement) {
        selectElement.addEventListener('blur', function() {
            // Close the select by removing focus
            this.size = 1;
        });
        
        selectElement.addEventListener('focus', function() {
            // Open the select by setting size
            this.size = 8;
        });
        
        selectElement.addEventListener('change', function() {
            // Close after selection
            this.size = 1;
            this.blur();
        });
    }
    
    // Apply click-away functionality to both selects
    closeSelectOnClickAway(countrySelect);
    closeSelectOnClickAway(serviceSelect);
    
    // Close selects when clicking outside
    document.addEventListener('click', function(event) {
        if (!countrySelect.contains(event.target) && !document.getElementById('countrySearch').contains(event.target)) {
            countrySelect.size = 1;
        }
        if (!serviceSelect.contains(event.target) && !document.getElementById('serviceSearch').contains(event.target)) {
            serviceSelect.size = 1;
        }
    });
 });
    
    // Function to handle form submission
    document.getElementById('internationalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const country = document.getElementById('country').value;
        const service = document.getElementById('service').value;
        
        // Show loading state
        const statusResult = document.getElementById('availability-result');
        statusResult.innerHTML = '<div class="mt-3 p-3 bg-gray-100 text-gray-700 rounded-lg">Processing purchase... <i class="fas fa-spinner fa-spin ml-2"></i></div>';
        
        // Make AJAX request to purchase
        fetch('/user/international/purchase', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                country: country,
                service: service
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                statusResult.innerHTML = '<div class="mt-3 p-3 bg-green-100 text-green-700 rounded-lg">' + (data.message || 'Number purchased successfully!') + '</div>';
                
                // Reset form
                this.reset();
                const form = document.getElementById('internationalForm');
                if (form && form._x_dataStack) {
                    const alpineData = form._x_dataStack[0];
                    alpineData.selectedCountry = '';
                    alpineData.selectedService = '';
                    alpineData.servicePrice = 0;
                    alpineData.statusChecked = false;
                } else if (form && form.__x) {
                    // Fallback for older Alpine versions
                    form.__x.$data.selectedCountry = '';
                    form.__x.$data.selectedService = '';
                    form.__x.$data.servicePrice = 0;
                    form.__x.$data.statusChecked = false;
                }
                
                // Reload page to show new order
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                statusResult.innerHTML = '<div class="mt-3 p-3 bg-red-100 text-red-700 rounded-lg">' + (data.message || 'Failed to purchase number') + '</div>';
                showNotification(data.message || 'Failed to purchase number', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            statusResult.innerHTML = '<div class="mt-3 p-3 bg-red-100 text-red-700 rounded-lg">An error occurred while purchasing the number</div>';
            showNotification('An error occurred while purchasing the number', 'error');
        });
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
        
        // Build query parameters
        const params = new URLSearchParams();
        if (countryFilter) params.append('country', countryFilter);
        if (serviceFilter) params.append('service', serviceFilter);
        if (searchInput) params.append('search', searchInput);
        
        // Reload page with filters
        window.location.href = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
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
                
            }
        }
    }, 10000); // Check every 10 seconds
</script>

<!-- Cancel Order Modal -->
<div id="cancelOrderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="fixed inset-0 flex items-center justify-center p-4" onclick="hideCancelOrderModal()">
        <div class="relative mx-auto border w-full max-w-md shadow-lg rounded-xl bg-white" onclick="event.stopPropagation()">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-medium text-gray-900">Cancel Order</h3>
                    <button onclick="hideCancelOrderModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        Are you sure you want to cancel this order? This action cannot be undone and any refund will be processed according to our policy.
                    </p>
                </div>
                
                <div class="flex space-x-3">
                    <button id="cancelOrderCancel" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>Keep Order
                    </button>
                    <button id="cancelOrderConfirm" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-medium">
                        <i class="fas fa-times mr-2"></i>Cancel Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

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
                showNotification('Processing your request...', 'info');
                
                // In a real app, you would submit this via AJAX
                // For demo purposes, we'll just show a success message after a delay
                setTimeout(function() {
                    showNotification('Number purchased successfully!', 'success');
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
    
    function refreshNumber(orderId) {
        toastr.info('Refreshing order status...');
        
        // Make AJAX request to check order status
        fetch('/user/international/check-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                order_id: orderId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message || 'Status updated successfully');
                // Reload page to show updated status
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                toastr.error(data.message || 'Failed to refresh status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error refreshing status', 'error');
        });
    }
    
    function showDetails(orderId) {
        // Navigate to order details page
        window.location.href = '/user/international/order/' + orderId;
    }
    
    // Timer functionality
    let timerIntervals = new Map();

    function updateTimer(timerElement, expiresAt) {
        const orderId = timerElement.closest('tr').id.replace('order-', '');
        
        // Clear existing interval if any
        if (timerIntervals.has(orderId)) {
            clearInterval(timerIntervals.get(orderId));
        }
        
        const interval = setInterval(() => {
            const now = new Date().getTime();
            const expiry = new Date(expiresAt).getTime();
            const timeLeft = expiry - now;
            
            if (timeLeft <= 0) {
                clearInterval(interval);
                timerIntervals.delete(orderId);
                timerElement.innerHTML = '<span class="text-red-500 font-bold">EXPIRED</span>';
                return;
            }
            
            const minutes = Math.floor(timeLeft / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
            timerElement.innerHTML = `<span class="text-gray-600">${minutes}m ${seconds}s remaining</span>`;
        }, 1000);
        
        timerIntervals.set(orderId, interval);
    }

    function startAllTimers() {
        const timerElements = document.querySelectorAll('.timer-display');
        timerElements.forEach(timerElement => {
            const expiresAt = timerElement.getAttribute('data-expires');
            if (expiresAt) {
                updateTimer(timerElement, expiresAt);
            }
        });
    }

    // Refresh order history
    function refreshOrderHistory() {
        // Add animation to refresh button
        const refreshBtn = event ? event.target.closest('.refresh-btn') : null;
        if (refreshBtn) {
            refreshBtn.classList.add('clicked');
            setTimeout(() => {
                refreshBtn.classList.remove('clicked');
            }, 300);
        }
        
        showNotification('Refreshing order history...', 'info');
        setTimeout(() => {
            window.location.reload();
        }, 300);
    }
    
    // Cancel order function
    function cancelOrder(orderId) {
        // Show custom modal instead of browser confirm
        showCancelOrderModal(orderId);
    }
    
    // Show cancel order modal
    function showCancelOrderModal(orderId) {
        const modal = document.getElementById('cancelOrderModal');
        const confirmButton = document.getElementById('cancelOrderConfirm');
        const cancelButton = document.getElementById('cancelOrderCancel');
        
        // Show modal
        modal.classList.remove('hidden');
        
        // Handle confirm button click
        confirmButton.onclick = function() {
            hideCancelOrderModal();
            performCancelOrder(orderId);
        };
        
        // Handle cancel button click
        cancelButton.onclick = function() {
            hideCancelOrderModal();
        };
        
        // Handle click outside modal to close
        modal.onclick = function(event) {
            if (event.target === modal) {
                hideCancelOrderModal();
            }
        };
        
        // Handle escape key
        document.addEventListener('keydown', function escapeHandler(e) {
            if (e.key === 'Escape') {
                hideCancelOrderModal();
                document.removeEventListener('keydown', escapeHandler);
            }
        });
    }
    
    // Hide cancel order modal
    function hideCancelOrderModal() {
        const modal = document.getElementById('cancelOrderModal');
        modal.classList.add('hidden');
    }
    
    // Perform the actual cancel order operation
    function performCancelOrder(orderId) {
        // Disable all cancel buttons for this order to prevent multiple requests
        const cancelButtons = document.querySelectorAll(`button[onclick*="cancelOrder(${orderId})"]`);
        cancelButtons.forEach(btn => {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Cancelling...';
        });
        
        fetch(`/user/international/order/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Order cancelled successfully', 'success');
                // Update the order row status
                const orderRow = document.getElementById(`order-${orderId}`);
                if (orderRow) {
                    const statusCell = orderRow.querySelector('.status-cell');
                    if (statusCell) {
                        statusCell.innerHTML = '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>';
                    }
                    // Remove cancel button
                    const cancelBtn = orderRow.querySelector('button[onclick*="cancelOrder"]');
                    if (cancelBtn) {
                        cancelBtn.remove();
                    }
                }
                // Also update mobile view if exists
                const mobileOrderCard = document.getElementById(`mobile-order-${orderId}`);
                if (mobileOrderCard) {
                    const mobileStatus = mobileOrderCard.querySelector('.bg-blue-100, .bg-yellow-100');
                    if (mobileStatus) {
                        mobileStatus.className = 'px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800';
                        mobileStatus.textContent = 'Cancelled';
                    }
                    // Remove cancel button from mobile view
                    const mobileCancelBtn = mobileOrderCard.querySelector('button[onclick*="cancelOrder"]');
                    if (mobileCancelBtn) {
                        mobileCancelBtn.remove();
                    }
                }
            } else {
                showNotification(data.message || 'Failed to cancel order', 'error');
            }
        })
        .catch(error => {
            console.error('Error cancelling order:', error);
            showNotification('Error cancelling order', 'error');
            
            // Re-enable cancel buttons on error
            const cancelButtons = document.querySelectorAll(`button[onclick*="cancelOrder(${orderId})"]`);
            cancelButtons.forEach(btn => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-times mr-1"></i>Cancel';
            });
        });
    }

    // Auto-refresh pending numbers every 30 seconds
    let autoRefreshInterval;
    function startAutoRefresh() {
        autoRefreshInterval = setInterval(function() {
            const pendingOrders = document.querySelectorAll('[data-status="pending"], [data-status="active"]');
            
            if (pendingOrders.length > 0) {
                // Check status for all pending/active orders
                pendingOrders.forEach(element => {
                    const orderId = element.dataset.orderId;
                    if (orderId) {
                        checkOrderStatus(orderId);
                    }
                });
            }
        }, 30000);
    }

    // Stop auto-refresh when page is not visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
        } else {
            startAutoRefresh();
        }
    });
    
    // Function to check order status via AJAX (silent check)
    function checkOrderStatus(orderId) {
        // Add animation to refresh button
        const refreshBtn = event ? event.target.closest('.refresh-btn') : null;
        if (refreshBtn) {
            refreshBtn.classList.add('spinning');
        }
        
        fetch(`/user/international/order/${orderId}/status`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update the order row with new data
                const orderRow = document.getElementById(`order-${orderId}`);
                if (orderRow && data.order) {
                    // Update status
                    const statusCell = orderRow.querySelector('.status-cell');
                    if (statusCell && data.order.status) {
                        statusCell.innerHTML = `<span class="px-2 py-1 text-xs font-semibold rounded-full ${
                            data.order.status === 'completed' ? 'bg-green-100 text-green-800' :
                            data.order.status === 'active' ? 'bg-blue-100 text-blue-800' :
                            data.order.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                            'bg-red-100 text-red-800'
                        }">${data.order.status.toUpperCase()}</span>`;
                    }
                    
                    // Update created_at time display (keep original time but update "time ago")
                    const createdAtCell = orderRow.querySelector('.created-at-cell');
                    if (createdAtCell && data.order.created_at) {
                        const createdAt = new Date(data.order.created_at);
                        const now = new Date();
                        const diffInMinutes = Math.floor((now - createdAt) / (1000 * 60));
                        
                        let timeAgoText;
                        if (diffInMinutes < 1) {
                            timeAgoText = 'just now';
                        } else if (diffInMinutes < 60) {
                            timeAgoText = `${diffInMinutes} minute${diffInMinutes > 1 ? 's' : ''} ago`;
                        } else if (diffInMinutes < 1440) {
                            const hours = Math.floor(diffInMinutes / 60);
                            timeAgoText = `${hours} hour${hours > 1 ? 's' : ''} ago`;
                        } else {
                            const days = Math.floor(diffInMinutes / 1440);
                            timeAgoText = `${days} day${days > 1 ? 's' : ''} ago`;
                        }
                        
                        createdAtCell.textContent = timeAgoText;
                    }
                    
                    // Update SMS code if available
                    if (data.order.sms_code) {
                        const smsCell = orderRow.querySelector('.sms-code-cell');
                        if (smsCell) {
                            smsCell.innerHTML = `
                                <span class="text-sm font-medium mr-2">${data.order.sms_code}</span>
                                <button class="text-gray-400 hover:text-gray-600" onclick="copySmsCode('${data.order.sms_code}')">
                                    <i class="far fa-copy"></i>
                                </button>
                            `;
                        }
                    }
                    
                    // Update timer if expires_at changed
                    if (data.order.sms_window_expires_at) {
                        const timerElement = orderRow.querySelector('.timer-display');
                        if (timerElement) {
                            timerElement.setAttribute('data-expires', data.order.sms_window_expires_at);
                            updateTimer(timerElement, data.order.sms_window_expires_at);
                        }
                    }
                }
                
                // Show notification if there's a message
                if (data.message) {
                    showNotification(data.message, 'success');
                }
            }
        })
        .catch(error => {
            console.error('Error checking order status:', error);
            showNotification('Error checking order status', 'error');
        })
        .finally(() => {
            // Remove animation from refresh button
            if (refreshBtn) {
                refreshBtn.classList.remove('spinning');
            }
        });
    }
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

// Enhanced notification function
function showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full ${
        type === 'success' ? 'bg-green-100 border border-green-200 text-green-800' :
        type === 'error' ? 'bg-red-100 border border-red-200 text-red-800' :
        type === 'warning' ? 'bg-yellow-100 border border-yellow-200 text-yellow-800' :
        'bg-blue-100 border border-blue-200 text-blue-800'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
                type === 'success' ? 'fa-check-circle' :
                type === 'error' ? 'fa-times-circle' :
                type === 'warning' ? 'fa-exclamation-triangle' :
                'fa-info-circle'
            } mr-2"></i>
            <span class="flex-1">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, duration);
}

// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Copied to clipboard!', 'success', 2000);
    }).catch(err => {
        console.error('Failed to copy: ', err);
        showNotification('Failed to copy to clipboard', 'error');
    });
}

// Function to update all time displays
function updateAllTimeDisplays() {
    const createdAtCells = document.querySelectorAll('.created-at-cell');
    createdAtCells.forEach(cell => {
        const orderRow = cell.closest('tr');
        if (orderRow) {
            const createdAtData = orderRow.dataset.createdAt;
            if (createdAtData) {
                const createdAt = new Date(createdAtData);
                const now = new Date();
                const diffInMinutes = Math.floor((now - createdAt) / (1000 * 60));
                
                let timeAgoText;
                if (diffInMinutes < 1) {
                    timeAgoText = 'just now';
                } else if (diffInMinutes < 60) {
                    timeAgoText = `${diffInMinutes} minute${diffInMinutes > 1 ? 's' : ''} ago`;
                } else if (diffInMinutes < 1440) {
                    const hours = Math.floor(diffInMinutes / 60);
                    timeAgoText = `${hours} hour${hours > 1 ? 's' : ''} ago`;
                } else {
                    const days = Math.floor(diffInMinutes / 1440);
                    timeAgoText = `${days} day${days > 1 ? 's' : ''} ago`;
                }
                
                cell.textContent = timeAgoText;
            }
        }
    });
    
    // Also update mobile view time displays
    const mobileCards = document.querySelectorAll('[id^="mobile-order-"]');
    mobileCards.forEach(card => {
        const orderId = card.id.replace('mobile-order-', '');
        const orderRow = document.getElementById(`order-${orderId}`);
        if (orderRow) {
            const createdAtData = orderRow.dataset.createdAt;
            if (createdAtData) {
                const createdAt = new Date(createdAtData);
                const now = new Date();
                const diffInMinutes = Math.floor((now - createdAt) / (1000 * 60));
                
                let timeAgoText;
                if (diffInMinutes < 1) {
                    timeAgoText = 'just now';
                } else if (diffInMinutes < 60) {
                    timeAgoText = `${diffInMinutes} minute${diffInMinutes > 1 ? 's' : ''} ago`;
                } else if (diffInMinutes < 1440) {
                    const hours = Math.floor(diffInMinutes / 60);
                    timeAgoText = `${hours} hour${hours > 1 ? 's' : ''} ago`;
                } else {
                    const days = Math.floor(diffInMinutes / 1440);
                    timeAgoText = `${days} day${days > 1 ? 's' : ''} ago`;
                }
                
                const mobileTimeElement = card.querySelector('.text-right p:last-child');
                if (mobileTimeElement) {
                    mobileTimeElement.textContent = timeAgoText;
                }
            }
        }
    });
}

// Initialize timers and auto-refresh on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Start timers and auto-refresh
    startAllTimers();
    startAutoRefresh();
    updateAllTimeDisplays();
    
    // Update time displays every minute
    setInterval(updateAllTimeDisplays, 60000);
});

// Clean up intervals when page is unloaded
window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
    
    // Clear all timer intervals
    timerIntervals.forEach(interval => clearInterval(interval));
    timerIntervals.clear();
});
</script>
@endpush

@push('styles')
<style>
    /* Refresh button animation */
    .refresh-btn {
        transition: all 0.2s ease-in-out;
    }
    
    .refresh-btn:hover {
        transform: scale(1.1);
    }
    
    .refresh-btn.spinning {
        pointer-events: none;
    }
    
    .refresh-btn.spinning i {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    
    /* Pulse animation for clicked state */
    .refresh-btn.clicked {
        animation: pulse 0.3s ease-in-out;
    }
    
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.2);
        }
        100% {
            transform: scale(1);
        }
    }
</style>

<!-- Website Builder Contact -->
<div class="py-3 text-center text-sm text-gray-700 border-t border-gray-200 mt-6">
    <div class="flex items-center justify-center space-x-2 scale-90 hover:scale-100 transition-transform duration-300">
        <i class="fas fa-mobile-alt text-blue-600 animate-pulse"></i>
        <p>
            Need a custom website? <a href="https://wa.link/18c124" class="text-blue-600 hover:text-blue-800 font-medium transition-colors relative group">
                Contact the developer
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"></span>
            </a>
        </p>
        <i class="fas fa-code text-blue-600 animate-bounce"></i>
    </div>
</div>

@endpush

{{-- @endsection --}}