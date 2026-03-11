@extends('layouts.app')

@section('title', 'All Countries Numbers')


@section('content')
<div class="space-y-5 max-w-4xl mx-auto">

    {{-- ── Purchase form ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="mb-4">
            <h1 class="text-sm font-bold text-gray-900">All Countries Numbers</h1>
            <p class="text-[11px] text-gray-400 mt-0.5">Get an international phone number for SMS verification</p>
        </div>

        <form id="internationalForm">
            @csrf
            <input type="hidden" id="country" name="country">
            <input type="hidden" id="service" name="service">

            <div class="space-y-3">

                {{-- ── Country picker ── --}}
                <div class="relative" id="countryDropdownWrap">
                    <button type="button" id="countryTrigger"
                            class="w-full flex items-center justify-between gap-3 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm hover:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200 transition-all">
                        <span class="flex items-center gap-2 min-w-0">
                            <i class="ri-earth-line text-gray-400 flex-shrink-0"></i>
                            <span id="countryLabel" class="truncate text-xs font-medium text-gray-400">Select a country...</span>
                        </span>
                        <i class="ri-arrow-down-s-line text-gray-400 text-sm"></i>
                    </button>
                    <div id="countryPanel" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl border border-gray-100 shadow-2xl z-[100] overflow-hidden">
                        <div class="p-2.5 border-b border-gray-50">
                            <div class="relative">
                                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                <input type="text" id="countrySearch" placeholder="Search countries..." autocomplete="off"
                                       class="w-full pl-8 pr-3 py-2 text-xs bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-transparent placeholder-gray-400 transition-all">
                            </div>
                        </div>
                        <div class="max-h-56 overflow-y-auto py-1" id="countryList">
                            @foreach($countries->sortBy('name') as $c)
                                <div class="country-option flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-primary-50 cursor-pointer transition-colors"
                                     data-value="{{ $c->code }}" data-name="{{ $c->name }}">{{ $c->name }}</div>
                            @endforeach
                            <div id="countryEmpty" class="hidden px-4 py-5 text-center text-xs text-gray-400">No results found</div>
                        </div>
                    </div>
                </div>

                {{-- ── Service picker (hidden until country selected) ── --}}
                <div class="relative hidden" id="serviceDropdownWrap">
                    <button type="button" id="serviceTrigger"
                            class="w-full flex items-center justify-between gap-3 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm hover:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200 transition-all">
                        <span class="flex items-center gap-2 min-w-0">
                            <i class="ri-apps-2-line text-gray-400 flex-shrink-0"></i>
                            <span id="serviceLabel" class="truncate text-xs font-medium text-gray-400">Select a service...</span>
                        </span>
                        <i class="ri-arrow-down-s-line text-gray-400 text-sm"></i>
                    </button>
                    <div id="servicePanel" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl border border-gray-100 shadow-2xl z-[100] overflow-hidden">
                        <div class="p-2.5 border-b border-gray-50">
                            <div class="relative">
                                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                <input type="text" id="serviceSearch" placeholder="Search services..." autocomplete="off"
                                       class="w-full pl-8 pr-3 py-2 text-xs bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-transparent placeholder-gray-400 transition-all">
                            </div>
                        </div>
                        <div class="max-h-56 overflow-y-auto py-1" id="serviceList">
                            @foreach($services->sortBy('name') as $s)
                                <div class="service-option flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-primary-50 cursor-pointer transition-colors"
                                     data-value="{{ $s->code }}" data-name="{{ $s->name }}">{{ $s->name }}</div>
                            @endforeach
                            <div id="serviceEmpty" class="hidden px-4 py-5 text-center text-xs text-gray-400">No results found</div>
                        </div>
                    </div>
                </div>

                {{-- ── Check availability (hidden until both selected) ── --}}
                <div class="hidden pt-3 border-t border-gray-100 space-y-2.5" id="checkRow">
                    <div class="hidden flex items-center gap-2 text-xs text-red-600 bg-red-50 border border-red-100 rounded-xl px-3 py-2" id="availError">
                        <i class="ri-error-warning-line flex-shrink-0"></i>
                        <span id="availErrorText"></span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <p class="text-xs text-gray-400">Check if this service is available in the selected country</p>
                        <button type="button" id="checkAvailBtn"
                                class="w-full sm:w-auto flex items-center justify-center gap-1.5 px-5 py-1.5 rounded-lg text-xs font-bold text-white bg-slate-700 hover:bg-slate-800 disabled:opacity-60 transition-all">
                            <i class="ri-search-line text-sm" id="checkAvailIcon"></i>
                            <span id="checkAvailText">Check Availability</span>
                        </button>
                    </div>
                </div>

                {{-- ── Price + purchase (hidden until available) ── --}}
                <div class="hidden pt-3 border-t border-gray-100" id="priceRow">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-[10px] uppercase tracking-widest text-gray-400 font-semibold">Price</span>
                            <span class="text-base font-bold text-gray-900" id="priceDisplay"></span>
                        </div>
                        <button type="submit" id="purchaseBtn"
                                class="w-full sm:w-auto flex items-center justify-center gap-1.5 px-5 py-1.5 rounded-lg text-xs font-bold text-white transition-all btn-glow"
                                style="background: linear-gradient(135deg, #475569 0%, #1e293b 100%);">
                            <i class="ri-shopping-bag-2-line text-sm"></i>
                            Purchase Number
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- ── Info notice ── --}}
    <div class="flex items-start gap-3 bg-primary-50 border border-primary-100 rounded-2xl p-4">
        <i class="ri-shield-check-line text-primary-500 flex-shrink-0 mt-0.5"></i>
        <p class="text-xs text-primary-700 leading-relaxed">
            <span class="font-semibold">Auto-refund enabled.</span>
            Each number has a 20-minute window to receive SMS. If no code arrives, your order is cancelled and balance refunded automatically.
        </p>
    </div>

    {{-- ── Active orders ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-50">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Active Orders</p>
            <button onclick="refreshAllOrders()"
                    class="flex items-center gap-1.5 text-xs text-primary-500 hover:text-primary-700 font-medium transition-colors">
                <i class="ri-refresh-line text-sm" id="refreshIcon"></i> Refresh
            </button>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($activeOrders as $order)
            <div class="p-4" id="order-{{ $order->id }}" data-order-id="{{ $order->id }}" data-status="{{ $order->status }}">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 mb-1">
                            <span class="font-mono font-bold text-gray-800 text-sm">{{ $order->phone_number }}</span>
                            <button onclick="copyToClipboard('{{ $order->phone_number }}')"
                                    class="text-gray-300 hover:text-primary-400 transition-colors">
                                <i class="ri-file-copy-line text-xs"></i>
                            </button>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 text-xs rounded-md bg-primary-50 text-primary-700 font-medium">
                                {{ $order->service->name ?? 'Unknown' }}
                            </span>
                            @if($order->country)
                            <span class="text-xs text-gray-400">
                                {{ $order->country->flag ?? '🌍' }} {{ $order->country->name ?? '' }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                        <span id="status-{{ $order->id }}" class="status-cell">
                            @if($order->status === 'pending')
                                <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 text-xs font-medium">Pending</span>
                            @elseif($order->status === 'active')
                                <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-xs font-medium">Active</span>
                            @elseif($order->status === 'completed')
                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-xs font-medium">Completed</span>
                            @else
                                <span class="px-2 py-0.5 rounded-md bg-red-50 text-red-500 text-xs font-medium">{{ ucfirst($order->status) }}</span>
                            @endif
                        </span>
                        @if($order->sms_window_expires_at && $order->sms_window_expires_at->isFuture())
                        <span id="timer-{{ $order->id }}" class="text-xs font-mono text-amber-500 font-semibold">
                            <span class="timer-display" data-expires="{{ $order->sms_window_expires_at->toISOString() }}"></span>
                        </span>
                        @endif
                    </div>
                </div>

                <div id="sms-code-{{ $order->id }}" class="sms-code-cell text-xs mb-2">
                    @if($order->sms_code)
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] uppercase tracking-wider text-gray-400">SMS Code:</span>
                        <span class="font-mono font-bold text-emerald-600">{{ $order->sms_code }}</span>
                        <button onclick="copyToClipboard('{{ $order->sms_code }}')"
                                class="text-gray-300 hover:text-emerald-500 transition-colors">
                            <i class="ri-file-copy-line"></i>
                        </button>
                    </div>
                    @else
                    <span class="text-gray-400">Waiting for SMS…</span>
                    @endif
                </div>

                <div class="flex items-center gap-2 pt-2.5 border-t border-gray-50" id="ao-actions-{{ $order->id }}">
                    <button onclick="checkOrderStatus({{ $order->id }})"
                            class="check-btn flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-50 hover:bg-primary-100 text-primary-600 text-xs font-medium transition-colors">
                        <i class="ri-refresh-line"></i> Check
                    </button>
                    @if(in_array($order->status, ['pending', 'active']))
                    <button onclick="cancelOrder({{ $order->id }})"
                            class="cancel-btn flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 text-xs font-medium transition-colors">
                        <i class="ri-close-line"></i> Cancel
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center py-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                    <i class="ri-earth-line text-gray-200 text-3xl"></i>
                </div>
                <p class="text-sm font-semibold text-gray-400">No active orders</p>
                <p class="text-xs text-gray-300 mt-1">Purchase a number to get started</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── Order history ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Order History</p>
        </div>

        @if($orders->count() > 0)

        {{-- Desktop table --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left font-semibold text-gray-400 uppercase tracking-wider">Phone</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-400 uppercase tracking-wider">Service</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-400 uppercase tracking-wider">Country</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-400 uppercase tracking-wider">SMS Code</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($orders as $order)
                    @php
                        $statusMap = [
                            'completed' => 'bg-emerald-50 text-emerald-700',
                            'cancelled' => 'bg-red-50 text-red-500',
                            'expired'   => 'bg-amber-50 text-amber-700',
                            'pending'   => 'bg-blue-50 text-blue-700',
                            'active'    => 'bg-primary-50 text-primary-700',
                        ];
                        $sc = $statusMap[$order->status] ?? 'bg-gray-100 text-gray-500';
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-5 py-3">
                            @if($order->phone_number)
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-semibold text-gray-800">{{ $order->phone_number }}</span>
                                <button onclick="copyToClipboard('{{ $order->phone_number }}')"
                                        class="text-gray-300 hover:text-primary-400 transition-colors">
                                    <i class="ri-file-copy-line"></i>
                                </button>
                            </div>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-md bg-primary-50 text-primary-700 font-medium">
                                {{ $order->service->name ?? 'Unknown' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            @if($order->country)
                                {{ $order->country->flag ?? '' }} {{ $order->country->name }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if($order->sms_code)
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-bold text-emerald-600">{{ $order->sms_code }}</span>
                                <button onclick="copyToClipboard('{{ $order->sms_code }}')"
                                        class="text-gray-300 hover:text-emerald-500 transition-colors">
                                    <i class="ri-file-copy-line"></i>
                                </button>
                            </div>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-md font-medium {{ $sc }}">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-500">
                            <div>{{ $order->created_at->format('d M Y') }}</div>
                            <div class="text-gray-400 text-[10px]">{{ $order->created_at->diffForHumans() }}</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="lg:hidden divide-y divide-gray-50">
            @foreach($orders as $order)
            @php
                $statusMap = [
                    'completed' => 'bg-emerald-50 text-emerald-700',
                    'cancelled' => 'bg-red-50 text-red-500',
                    'expired'   => 'bg-amber-50 text-amber-700',
                    'pending'   => 'bg-blue-50 text-blue-700',
                    'active'    => 'bg-indigo-50 text-indigo-700',
                ];
                $sc = $statusMap[$order->status] ?? 'bg-gray-100 text-gray-500';
            @endphp
            <div class="p-4">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        @if($order->phone_number)
                        <div class="flex items-center gap-1.5 mb-1">
                            <span class="font-mono font-bold text-gray-800 text-sm">{{ $order->phone_number }}</span>
                            <button onclick="copyToClipboard('{{ $order->phone_number }}')"
                                    class="text-gray-300 hover:text-indigo-400 transition-colors">
                                <i class="ri-file-copy-line text-xs"></i>
                            </button>
                        </div>
                        @endif
                        <span class="px-2 py-0.5 text-xs rounded-md bg-indigo-50 text-indigo-700 font-medium">
                            {{ $order->service->name ?? 'Unknown' }}
                        </span>
                    </div>
                    <span class="px-2 py-0.5 text-xs rounded-md font-medium {{ $sc }}">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Country</p>
                        <p class="text-gray-600">
                            @if($order->country)
                                {{ $order->country->flag ?? '' }} {{ $order->country->name }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Date</p>
                        <p class="text-gray-600">{{ $order->created_at->format('d M Y') }}</p>
                    </div>
                    @if($order->sms_code)
                    <div class="col-span-2">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">SMS Code</p>
                        <div class="flex items-center gap-1">
                            <span class="font-mono font-bold text-emerald-600">{{ $order->sms_code }}</span>
                            <button onclick="copyToClipboard('{{ $order->sms_code }}')" class="text-gray-300 hover:text-emerald-500 transition-colors">
                                <i class="ri-file-copy-line"></i>
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        @else
        <div class="flex flex-col items-center py-14 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                <i class="ri-file-list-3-line text-gray-200 text-3xl"></i>
            </div>
            <p class="text-sm font-semibold text-gray-400">No order history</p>
            <p class="text-xs text-gray-300 mt-1">Your past orders will appear here</p>
        </div>
        @endif

        <x-pagination :paginator="$orders" />
    </div>

</div>

{{-- ── Cancel modal ── --}}
<div id="cancelOrderModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full transform transition-all duration-300 scale-95 opacity-0" id="cancelOrderModalContent">
        <div class="p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                    <i class="ri-alert-line text-red-500"></i>
                </div>
                <h3 class="text-sm font-bold text-gray-900 flex-1">Cancel Order</h3>
                <button onclick="hideCancelOrderModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="ri-close-line text-lg"></i>
                </button>
            </div>
            <p class="text-xs text-gray-500 mb-5 leading-relaxed">
                Are you sure you want to cancel? Any refund will be processed according to our policy.
            </p>
            <div class="flex gap-2.5">
                <button id="cancelOrderCancel"
                        class="flex-1 py-2.5 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-semibold transition-colors">
                    Keep Order
                </button>
                <button id="cancelOrderConfirm"
                        class="flex-1 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-xs font-bold transition-colors">
                    Yes, Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
window.copyToClipboard = function(text) {
    navigator.clipboard.writeText(text)
        .then(() => notify('success', 'Copied!'))
        .catch(() => {
            const t = document.createElement('textarea');
            t.value = text; document.body.appendChild(t); t.select();
            document.execCommand('copy'); document.body.removeChild(t);
            notify('success', 'Copied!');
        });
};

// ── Vanilla JS Dropdowns (same pattern as sample project) ────────────────
(function() {
    let currentCountry = '', currentService = '';

    function setupDropdown(triggerId, panelId, searchId, listId, optClass, emptyId, onSelect) {
        const trigger = document.getElementById(triggerId);
        const panel   = document.getElementById(panelId);
        const search  = document.getElementById(searchId);
        const list    = document.getElementById(listId);
        const empty   = document.getElementById(emptyId);
        if (!trigger || !panel) return;

        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            const opening = panel.classList.contains('hidden');
            document.getElementById('countryPanel').classList.add('hidden');
            document.getElementById('servicePanel').classList.add('hidden');
            if (opening) {
                panel.classList.remove('hidden');
                search.value = '';
                search.focus();
                list.querySelectorAll('.' + optClass).forEach(el => el.classList.remove('hidden'));
                empty.classList.add('hidden');
            }
        });

        search.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            let visible = 0;
            list.querySelectorAll('.' + optClass).forEach(el => {
                const match = !term || el.textContent.toLowerCase().includes(term);
                el.classList.toggle('hidden', !match);
                if (match) visible++;
            });
            empty.classList.toggle('hidden', visible > 0);
        });

        list.addEventListener('click', function(e) {
            const opt = e.target.closest('.' + optClass);
            if (!opt) return;
            const value = opt.dataset.value;
            const name  = opt.dataset.name || opt.textContent.trim();
            document.getElementById(triggerId.replace('Trigger', 'Label')).textContent = name;
            panel.classList.add('hidden');
            onSelect(value, name);
        });

        panel.addEventListener('click', function(e) { e.stopPropagation(); });
    }

    document.addEventListener('click', function() {
        ['countryPanel', 'servicePanel'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.add('hidden');
        });
    });

    setupDropdown('countryTrigger', 'countryPanel', 'countrySearch', 'countryList', 'country-option', 'countryEmpty',
        function(value) {
            currentCountry = value;
            document.getElementById('country').value = value;
            // Show service picker, reset downstream
            document.getElementById('serviceDropdownWrap').classList.remove('hidden');
            document.getElementById('serviceLabel').textContent = 'Select a service...';
            document.getElementById('service').value = '';
            currentService = '';
            document.getElementById('checkRow').classList.add('hidden');
            document.getElementById('priceRow').classList.add('hidden');
            document.getElementById('availError').classList.add('hidden');
        }
    );

    setupDropdown('serviceTrigger', 'servicePanel', 'serviceSearch', 'serviceList', 'service-option', 'serviceEmpty',
        function(value) {
            currentService = value;
            document.getElementById('service').value = value;
            // Show check availability row
            document.getElementById('checkRow').classList.remove('hidden');
            document.getElementById('priceRow').classList.add('hidden');
            document.getElementById('availError').classList.add('hidden');
        }
    );

    // Check availability button
    document.getElementById('checkAvailBtn').addEventListener('click', function() {
        if (!currentCountry || !currentService) return;
        const btn  = this;
        const icon = document.getElementById('checkAvailIcon');
        const text = document.getElementById('checkAvailText');
        btn.disabled = true;
        icon.className = 'ri-loader-4-line animate-spin text-sm';
        text.textContent = 'Checking...';
        document.getElementById('availError').classList.add('hidden');
        document.getElementById('priceRow').classList.add('hidden');

        fetch('/user/international/check-availability', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ country: currentCountry, service: currentService })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.available) {
                document.getElementById('priceDisplay').textContent = '₦' + Number(data.price).toLocaleString();
                document.getElementById('priceRow').classList.remove('hidden');
            } else {
                document.getElementById('availErrorText').textContent = data.message || 'Service not available in this country';
                document.getElementById('availError').classList.remove('hidden');
            }
        })
        .catch(() => {
            document.getElementById('availErrorText').textContent = 'Error checking availability. Please try again.';
            document.getElementById('availError').classList.remove('hidden');
        })
        .finally(() => {
            btn.disabled = false;
            icon.className = 'ri-search-line text-sm';
            text.textContent = 'Check Availability';
        });
    });
})();

// ── Cancel modal ─────────────────────────────────────────────────────────
function showCancelOrderModal(orderId) {
    const modal   = document.getElementById('cancelOrderModal');
    const content = document.getElementById('cancelOrderModalContent');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    });
    document.body.style.overflow = 'hidden';
    document.getElementById('cancelOrderConfirm').onclick = function() { hideCancelOrderModal(); performCancelOrder(orderId); };
    document.getElementById('cancelOrderCancel').onclick  = hideCancelOrderModal;
    modal.onclick = function(e) { if (e.target === modal) hideCancelOrderModal(); };
    document.addEventListener('keydown', function esc(e) { if (e.key === 'Escape') { hideCancelOrderModal(); document.removeEventListener('keydown', esc); } });
}

function hideCancelOrderModal() {
    const modal   = document.getElementById('cancelOrderModal');
    const content = document.getElementById('cancelOrderModalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function cancelOrder(orderId) { showCancelOrderModal(orderId); }

// ── Form submission ───────────────────────────────────────────────────────
document.getElementById('internationalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const country = document.getElementById('country').value;
    const service = document.getElementById('service').value;

    if (!country || !service) {
        notify('error', 'Please select both country and service');
        return;
    }

    const submitBtn = this.querySelector('button[type="submit"]');
    const origHtml  = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="ri-loader-4-line animate-spin mr-1.5"></i> Processing…';

    fetch('/user/international/purchase', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept':       'application/json'
        },
        body: JSON.stringify({ country, service })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            notify('success', data.message || 'Number purchased successfully!');
            setTimeout(() => location.reload(), 1200);
        } else {
            notify('error', data.message || 'Failed to purchase number');
        }
    })
    .catch(() => notify('error', 'An error occurred while purchasing the number'))
    .finally(() => { submitBtn.disabled = false; submitBtn.innerHTML = origHtml; });
});

// ── Check order status (DOM update — never reloads) ──────────────────────
function checkOrderStatus(orderId) {
    const orderEl = document.querySelector(`[data-order-id="${orderId}"]`);
    const checkBtn = orderEl?.querySelector('.check-btn');
    if (checkBtn) {
        checkBtn.disabled = true;
        checkBtn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i>';
    }

    fetch(`/user/international/order/${orderId}/status`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept':       'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.order) {
            updateActiveOrderUI(orderId, data.order);
            if (data.order.sms_code) {
                notify('success', `SMS Code received: ${data.order.sms_code}`);
            }
            // Silent when no sms_code yet — no notification spam
        }
        // Silent on non-success — background check, no error toast
    })
    .catch(() => {}) // Silent fail for background checks
    .finally(() => {
        if (checkBtn) {
            checkBtn.disabled = false;
            checkBtn.innerHTML = '<i class="ri-refresh-line"></i> Check';
        }
    });
}

function updateActiveOrderUI(orderId, order) {
    const orderEl = document.querySelector(`[data-order-id="${orderId}"]`);
    if (!orderEl) return;

    orderEl.setAttribute('data-status', order.status);

    // Update status badge
    const statusEl = document.getElementById(`status-${orderId}`);
    if (statusEl) {
        const map = {
            pending:   'bg-amber-50 text-amber-700',
            active:    'bg-blue-50 text-blue-700',
            completed: 'bg-emerald-50 text-emerald-700',
            cancelled: 'bg-red-50 text-red-500',
        };
        const cls   = map[order.status] || 'bg-gray-100 text-gray-500';
        const label = order.status.charAt(0).toUpperCase() + order.status.slice(1);
        statusEl.innerHTML = `<span class="px-2 py-0.5 rounded-md text-xs font-medium ${cls}">${label}</span>`;
    }

    // Update SMS code display
    const smsEl = document.getElementById(`sms-code-${orderId}`);
    if (smsEl && order.sms_code) {
        smsEl.innerHTML = `
            <div class="flex items-center gap-1.5">
                <span class="text-[10px] uppercase tracking-wider text-gray-400">SMS Code:</span>
                <span class="font-mono font-bold text-emerald-600">${order.sms_code}</span>
                <button onclick="copyToClipboard('${order.sms_code}')" class="text-gray-300 hover:text-emerald-500 transition-colors">
                    <i class="ri-file-copy-line"></i>
                </button>
            </div>`;
    }

    // Hide cancel button once order is no longer active
    if (!['pending', 'active'].includes(order.status)) {
        const actionsEl = document.getElementById(`ao-actions-${orderId}`);
        actionsEl?.querySelector('.cancel-btn')?.remove();
    }
}

// ── Cancel order ──────────────────────────────────────────────────────────
function performCancelOrder(orderId) {
    notify('success', 'Cancelling order…');
    fetch(`/user/international/order/${orderId}/cancel`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept':       'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            notify('success', data.message || 'Order cancelled successfully');
            setTimeout(() => location.reload(), 1200);
        } else {
            notify('error', data.message || 'Failed to cancel order');
        }
    })
    .catch(() => notify('error', 'Error cancelling order'));
}

// ── Refresh all — clicks every individual Check button ───────────────────
function refreshAllOrders() {
    const icon = document.getElementById('refreshIcon');
    if (icon) icon.classList.add('animate-spin');
    document.querySelectorAll('.check-btn:not([disabled])').forEach(btn => btn.click());
    setTimeout(() => { if (icon) icon.classList.remove('animate-spin'); }, 2000);
}

// ── Countdown timers ──────────────────────────────────────────────────────
function startAllTimers() {
    document.querySelectorAll('.timer-display[data-expires]').forEach(el => {
        if (el.dataset.ticking) return;
        el.dataset.ticking = '1';
        const expires = new Date(el.getAttribute('data-expires'));
        setInterval(function() {
            const left = expires - Date.now();
            if (left <= 0) { el.textContent = 'Expired'; return; }
            const m = Math.floor(left / 60000);
            const s = Math.floor((left % 60000) / 1000);
            el.textContent = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        }, 1000);
    });
}

// ── Auto-check pending/active orders every 10 s (no page reload) ─────────
document.addEventListener('DOMContentLoaded', function() {
    startAllTimers();
    setInterval(function() {
        document.querySelectorAll('[data-status="pending"], [data-status="active"]').forEach(el => {
            const id = el.getAttribute('data-order-id');
            if (id) checkOrderStatus(id);
        });
    }, 10000);
});
</script>

@endsection
