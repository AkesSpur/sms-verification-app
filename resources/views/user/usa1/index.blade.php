@extends('layouts.app')

@section('title', 'USA Numbers 1')

@php
$_rate   = (float) ($settings->usd_to_ngn_rate ?? 1600);
$_markup = (float) ($settings->api_price_markup_percentage ?? 0);

$servicesData = collect($services)->map(function($s) use ($_rate, $_markup) {
    $priceNaira = (float) ceil(($s['cost'] * $_rate * (1 + $_markup / 100)) / 10) * 10;
    return [
        'code'     => $s['short_name'],
        'name'     => $s['name'],
        'price'    => $priceNaira,
        'label'    => $priceNaira > 0 ? '₦' . number_format($priceNaira) : null,
        'count'    => (int) ($s['count'] ?? 0),
        'in_stock' => (int) ($s['count'] ?? 0) > 0,
    ];
})->values();
@endphp

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">

    {{-- ── Purchase form ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        {{-- Title moved inside the form card --}}
        <div class="mb-4">
            <h1 class="text-sm font-bold text-gray-900">USA Numbers 1</h1>
            <p class="text-[11px] text-gray-400 mt-0.5">Get a US phone number for SMS verification</p>
        </div>

        <form id="rentForm">
            @csrf
            <input type="hidden" name="country" value="us">
            <input type="hidden" name="service" id="serviceCode">

            {{-- Custom service dropdown (vanilla JS) --}}
            <div class="relative" id="serviceDropdownWrap">
                <button type="button" id="serviceTrigger"
                        class="w-full flex items-center justify-between gap-3 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm hover:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200 transition-all">
                    <span class="flex items-center gap-2 min-w-0">
                        <i class="ri-apps-2-line text-gray-400 flex-shrink-0"></i>
                        <span id="serviceLabel" class="truncate text-xs font-medium text-gray-400">Select a service...</span>
                    </span>
                    <span class="flex items-center gap-1.5 flex-shrink-0">
                        <span id="selectedPriceBadge" style="display:none"
                              class="text-xs font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded-md"></span>
                        <span id="clearService" style="display:none"
                              class="cursor-pointer text-gray-300 hover:text-gray-500 transition-colors leading-none">
                            <i class="ri-close-circle-line text-sm"></i>
                        </span>
                        <i class="ri-arrow-down-s-line text-gray-400 text-sm transition-transform duration-200" id="dropdownArrow"></i>
                    </span>
                </button>

                <div id="servicePanel" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl border border-gray-100 shadow-2xl z-[100] overflow-hidden">
                    <div class="p-2.5 border-b border-gray-50">
                        <div class="relative">
                            <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            <input type="text" id="serviceSearch" placeholder="Search services..." autocomplete="off"
                                   class="w-full pl-8 pr-3 py-2 text-xs bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-transparent placeholder-gray-400 transition-all">
                        </div>
                    </div>
                    <ul class="max-h-60 overflow-y-auto py-1" id="serviceList">
                        @foreach($servicesData as $s)
                            <li class="service-option flex items-center justify-between px-4 py-2.5 transition-colors {{ $s['in_stock'] ? 'cursor-pointer hover:bg-primary-50' : 'cursor-not-allowed opacity-50 bg-gray-50' }}"
                                data-value="{{ $s['code'] }}"
                                data-name="{{ $s['name'] }}"
                                data-label="{{ $s['label'] }}"
                                data-in-stock="{{ $s['in_stock'] ? '1' : '0' }}">
                                <span class="text-sm text-gray-700">{{ $s['name'] }}</span>
                                <span class="ml-3 flex-shrink-0 flex items-center gap-1.5">
                                    @if($s['in_stock'])
                                        <span class="text-xs font-bold text-primary-600">{{ $s['label'] }}</span>
                                        <span class="text-[10px] text-gray-400 tabular-nums">({{ $s['count'] }})</span>
                                    @else
                                        <span class="text-[10px] text-gray-400 italic">Out of stock</span>
                                    @endif
                                </span>
                            </li>
                        @endforeach
                        <li id="serviceEmpty" class="hidden px-4 py-5 text-center text-xs text-gray-400">
                            <i class="ri-search-line text-2xl block mb-1 text-gray-200"></i>
                            No results found
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Price + Purchase row (shown when service selected) --}}
            <div id="priceRow" class="hidden mt-4 pt-4 border-t border-gray-100">
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
        </form>
    </div>

    {{-- ── Info notice ── --}}
    <div class="flex items-start gap-3 bg-primary-50 border border-primary-100 rounded-2xl p-4">
        <i class="ri-shield-check-line text-primary-500 flex-shrink-0 mt-0.5"></i>
        <p class="text-xs text-primary-700 leading-relaxed">
            <span class="font-semibold">Auto-refund enabled.</span>
            If no SMS arrives within the time limit, your order is cancelled and your balance is refunded automatically.
        </p>
    </div>

    {{-- ── Recent orders ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-50">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Recent Orders</p>
            <button onclick="refreshOrders()"
                    class="flex items-center gap-1.5 text-xs text-primary-500 hover:text-primary-700 font-medium transition-colors">
                <i class="ri-refresh-line text-sm" id="refreshIcon"></i> Refresh
            </button>
        </div>

        @if($rentals->count() > 0)

            {{-- Desktop table --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-5 py-3 text-left font-semibold text-gray-400 uppercase tracking-wider">Phone</th>
                            <th class="px-5 py-3 text-left font-semibold text-gray-400 uppercase tracking-wider">Service</th>
                            <th class="px-5 py-3 text-left font-semibold text-gray-400 uppercase tracking-wider">Price</th>
                            <th class="px-5 py-3 text-left font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 text-left font-semibold text-gray-400 uppercase tracking-wider">SMS Code</th>
                            <th class="px-5 py-3 text-left font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-5 py-3 text-left font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($rentals as $rental)
                        <tr class="hover:bg-gray-50/60 transition-colors" data-status="{{ $rental->status }}">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-mono font-semibold text-gray-800">{{ formatPhoneNumber($rental->phone_number) }}</span>
                                    <button onclick="copyToClipboard('{{ $rental->phone_number }}')"
                                            class="text-gray-300 hover:text-primary-400 transition-colors">
                                        <i class="ri-file-copy-line"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-md bg-primary-50 text-primary-700 font-medium">{{ ucfirst($rental->service_name) }}</span>
                            </td>
                            <td class="px-5 py-3 font-bold text-gray-800">&#8358;{{ number_format($rental->price, 2) }}</td>
                            <td class="px-5 py-3">
                                @if($rental->status == 'pending')
                                    <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-medium">Pending</span>
                                @elseif($rental->status == 'active')
                                    <div>
                                        <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-medium">Active</span>
                                        @if($rental->expires_at)
                                            <div class="countdown-timer mt-1" data-expires="{{ $rental->expires_at->toISOString() }}">
                                                <span class="text-gray-400">Exp: <span class="countdown-display font-mono font-semibold text-amber-500">--:--</span></span>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($rental->status == 'completed')
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-medium">Completed</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md bg-red-50 text-red-500 font-medium">Cancelled</span>
                                @endif
                            </td>
                            <td class="px-5 py-3" id="sms-cell-{{ $rental->id }}">
                                @if($rental->sms_code)
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono font-bold text-emerald-600">{{ $rental->sms_code }}</span>
                                        <button onclick="copyToClipboard('{{ $rental->sms_code }}')"
                                                class="text-gray-300 hover:text-emerald-500 transition-colors">
                                            <i class="ri-file-copy-line"></i>
                                        </button>
                                    </div>
                                @elseif($rental->status == 'cancelled')
                                    <span class="text-red-400">—</span>
                                @else
                                    <span class="text-gray-400">Waiting…</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-500">
                                <div>{{ showDateTime($rental->created_at, 'd M Y') }}</div>
                                <div class="text-gray-400">{{ diffForHumans($rental->created_at) }}</div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-1.5">
                                    @if(in_array($rental->status, ['pending', 'active']))
                                        <button class="checkCodeBtn px-3 py-1.5 rounded-lg bg-primary-50 hover:bg-primary-100 text-primary-600 font-medium transition-colors"
                                                data-id="{{ $rental->id }}">
                                            <i class="ri-refresh-line"></i>
                                        </button>
                                        <button onclick="prepareCancel({{ $rental->id }})"
                                                class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 font-medium transition-colors">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="lg:hidden divide-y divide-gray-50">
                @foreach($rentals as $rental)
                <div class="p-4" data-status="{{ $rental->status }}">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="flex items-center gap-1.5 mb-1">
                                <span class="font-mono font-bold text-gray-800 text-sm">{{ formatPhoneNumber($rental->phone_number) }}</span>
                                <button onclick="copyToClipboard('{{ $rental->phone_number }}')"
                                        class="text-gray-300 hover:text-primary-400 transition-colors">
                                    <i class="ri-file-copy-line text-xs"></i>
                                </button>
                            </div>
                            <span class="px-2 py-0.5 text-xs rounded-md bg-primary-50 text-primary-700 font-medium">{{ ucfirst($rental->service_name) }}</span>
                        </div>
                        <div class="text-right">
                            @if($rental->status == 'pending')
                                <span class="px-2 py-0.5 text-xs rounded-md bg-amber-50 text-amber-700 font-medium">Pending</span>
                            @elseif($rental->status == 'active')
                                <span class="px-2 py-0.5 text-xs rounded-md bg-blue-50 text-blue-700 font-medium">Active</span>
                                @if($rental->expires_at)
                                    <div class="countdown-timer mt-1" data-expires="{{ $rental->expires_at->toISOString() }}">
                                        <span class="text-xs text-gray-400">Exp: <span class="countdown-display font-mono font-semibold text-amber-500">--:--</span></span>
                                    </div>
                                @endif
                            @elseif($rental->status == 'completed')
                                <span class="px-2 py-0.5 text-xs rounded-md bg-emerald-50 text-emerald-700 font-medium">Completed</span>
                            @else
                                <span class="px-2 py-0.5 text-xs rounded-md bg-red-50 text-red-500 font-medium">Cancelled</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-3 text-xs">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Price</p>
                            <p class="font-bold text-gray-800">&#8358;{{ number_format($rental->price, 2) }}</p>
                        </div>
                        <div id="sms-card-{{ $rental->id }}">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">SMS Code</p>
                            @if($rental->sms_code)
                                <div class="flex items-center gap-1">
                                    <span class="font-mono font-bold text-emerald-600">{{ $rental->sms_code }}</span>
                                    <button onclick="copyToClipboard('{{ $rental->sms_code }}')"
                                            class="text-gray-300 hover:text-emerald-500 transition-colors">
                                        <i class="ri-file-copy-line text-xs"></i>
                                    </button>
                                </div>
                            @elseif($rental->status == 'cancelled')
                                <p class="text-red-400">—</p>
                            @else
                                <p class="text-gray-400">Waiting…</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Date</p>
                            <p class="text-gray-600">{{ showDateTime($rental->created_at, 'd M Y') }}</p>
                        </div>
                    </div>

                    @if(in_array($rental->status, ['pending', 'active']))
                    <div class="flex items-center gap-2 pt-3 border-t border-gray-50">
                        <button class="checkCodeBtn flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl bg-primary-50 text-primary-600 text-xs font-semibold hover:bg-primary-100 transition-colors"
                                data-id="{{ $rental->id }}">
                            <i class="ri-refresh-line"></i> Check Code
                        </button>
                        <button onclick="prepareCancel({{ $rental->id }})"
                                class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl bg-red-50 text-red-500 text-xs font-semibold hover:bg-red-100 transition-colors">
                            <i class="ri-close-line"></i> Cancel
                        </button>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

        @else
            <div class="flex flex-col items-center py-14 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                    <i class="ri-sim-card-line text-gray-200 text-3xl"></i>
                </div>
                <p class="text-sm font-semibold text-gray-400">{{ $emptyMessage }}</p>
                <p class="text-xs text-gray-300 mt-1">Purchase your first number using the form above</p>
            </div>
        @endif

        {{-- Pagination --}}
        <x-pagination :paginator="$rentals" />
    </div>

</div>

{{-- ── Cancel modal ── --}}
<div id="cancelModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full transform transition-all duration-300 scale-95 opacity-0" id="cancelModalContent">
        <div class="p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                    <i class="ri-alert-line text-red-500"></i>
                </div>
                <h3 class="text-sm font-bold text-gray-900 flex-1">Cancel Purchase</h3>
                <button class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeCancelModal()">
                    <i class="ri-close-line text-lg"></i>
                </button>
            </div>
            <p class="text-xs text-gray-500 mb-5 leading-relaxed">
                Are you sure you want to cancel? You may receive a partial refund depending on the current status of the order.
            </p>
            <div class="flex gap-2.5">
                <button onclick="closeCancelModal()"
                        class="flex-1 py-2.5 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-semibold transition-colors">
                    Keep Order
                </button>
                <button id="confirmCancelBtn"
                        class="flex-1 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-xs font-bold transition-colors">
                    Yes, Cancel
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Important Notice modal ── --}}
<div id="importantNoticeModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <div class="p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <i class="ri-megaphone-line text-amber-500"></i> Important Guidelines
                </h3>
                <button onclick="closeImportantNoticeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="ri-close-line text-lg"></i>
                </button>
            </div>
            <div class="space-y-3">
                <div class="bg-primary-50 rounded-xl p-4 space-y-2.5 text-xs">
                    <p class="font-bold text-primary-800 mb-2">WhatsApp &amp; Telegram:</p>
                    <div class="flex items-start gap-2 text-primary-700">
                        <i class="ri-smartphone-line mt-0.5 flex-shrink-0"></i>
                        <span><strong>Fresh install:</strong> Reinstall the app before requesting a number for best results.</span>
                    </div>
                    <div class="flex items-start gap-2 text-primary-700">
                        <i class="ri-forbid-line mt-0.5 flex-shrink-0"></i>
                        <span><strong>Regular only:</strong> Avoid WhatsApp Business for verification.</span>
                    </div>
                    <div class="flex items-start gap-2 text-primary-700">
                        <i class="ri-earth-line mt-0.5 flex-shrink-0"></i>
                        <span><strong>Location:</strong> Set VPN and timezone to match the number's country.</span>
                    </div>
                </div>
                <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 flex items-center gap-2 text-xs text-amber-700">
                    <i class="ri-lightbulb-line flex-shrink-0"></i>
                    Following these steps significantly increases your verification success rate.
                </div>
            </div>
            <div class="mt-5 flex justify-end">
                <button onclick="closeImportantNoticeModal()"
                        class="px-6 py-2.5 rounded-xl text-xs font-bold text-white transition-all btn-glow"
                        style="background:linear-gradient(135deg,#475569,#1e293b);">
                    Got it, thanks!
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ── Modal helpers ──────────────────────────────────────────────────────────────
function openImportantNoticeModal() {
    const modal = document.getElementById('importantNoticeModal');
    const content = document.getElementById('modalContent');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    });
    document.body.style.overflow = 'hidden';
}

function closeImportantNoticeModal() {
    const modal = document.getElementById('importantNoticeModal');
    const content = document.getElementById('modalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('importantNoticeModal').addEventListener('click', function(e) {
    if (e.target === this) closeImportantNoticeModal();
});

// Auto-show on load, then every 30 minutes
document.addEventListener('DOMContentLoaded', function() {
    openImportantNoticeModal();
    setInterval(openImportantNoticeModal, 30 * 60 * 1000);
});
</script>

@endsection

@push('scripts')
<script>
// ── Service dropdown (vanilla JS) ──────────────────────────────────────────
(function() {
    const trigger      = document.getElementById('serviceTrigger');
    const panel        = document.getElementById('servicePanel');
    const search       = document.getElementById('serviceSearch');
    const list         = document.getElementById('serviceList');
    const empty        = document.getElementById('serviceEmpty');
    const lbl          = document.getElementById('serviceLabel');
    const arrow        = document.getElementById('dropdownArrow');
    const clearBtn     = document.getElementById('clearService');
    const priceBadge   = document.getElementById('selectedPriceBadge');
    const priceRow     = document.getElementById('priceRow');
    const priceDisplay = document.getElementById('priceDisplay');
    if (!trigger) return;

    function openPanel() {
        panel.classList.remove('hidden');
        arrow.classList.add('rotate-180');
        search.value = '';
        list.querySelectorAll('.service-option').forEach(el => el.classList.remove('hidden'));
        empty.classList.add('hidden');
        search.focus();
    }

    function closePanel() {
        panel.classList.add('hidden');
        arrow.classList.remove('rotate-180');
    }

    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        panel.classList.contains('hidden') ? openPanel() : closePanel();
    });

    search.addEventListener('input', function() {
        const term = this.value.toLowerCase().trim();
        let visible = 0;
        list.querySelectorAll('.service-option').forEach(el => {
            const match = !term || el.dataset.name.toLowerCase().includes(term);
            el.classList.toggle('hidden', !match);
            if (match) visible++;
        });
        empty.classList.toggle('hidden', visible > 0);
    });

    list.addEventListener('click', function(e) {
        const opt = e.target.closest('.service-option');
        if (!opt || opt.dataset.inStock !== '1') return;
        document.getElementById('serviceCode').value = opt.dataset.value;
        lbl.textContent = opt.dataset.name;
        lbl.classList.replace('text-gray-400', 'text-gray-800');
        priceBadge.textContent = opt.dataset.label;
        priceBadge.style.display = '';
        clearBtn.style.display = '';
        priceDisplay.textContent = opt.dataset.label;
        priceRow.classList.remove('hidden');
        closePanel();
    });

    clearBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('serviceCode').value = '';
        lbl.textContent = 'Select a service...';
        lbl.classList.replace('text-gray-800', 'text-gray-400');
        priceBadge.style.display = 'none';
        clearBtn.style.display = 'none';
        priceRow.classList.add('hidden');
    });

    panel.addEventListener('click', e => e.stopPropagation());
    document.addEventListener('click', closePanel);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closePanel(); });
})();
</script>

<script>
(function ($) {
    "use strict";

    // ── Form submission ─────────────────────────────────────────────────────
    $('#rentForm').on('submit', function(e) {
        e.preventDefault();

        const service = document.getElementById('serviceCode').value;
        if (!service) {
            notify('error', 'Please select a service');
            return;
        }

        const $btn = $('#purchaseBtn');
        const origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin mr-1.5"></i> Processing…');

        const fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fd.append('service', service);
        fd.append('country', 'us');

        $.ajax({
            url: '{{ route("user.sms.rental.rent") }}',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success) { notify('success', res.message); location.reload(); }
                else notify('error', res.message);
            },
            error: function(xhr) { notify('error', xhr.responseJSON?.message || 'Something went wrong'); },
            complete: function() { $btn.prop('disabled', false).html(origHtml); }
        });
    });

    // Track the last seen code per order to avoid redundant DOM updates
    const lastSeenCode = {};

    // ── Check code button ───────────────────────────────────────────────────
    function performCheck(id, $btn = null, silent = false) {
        if ($btn) {
            $btn.html('<i class="ri-loader-4-line animate-spin"></i>').prop('disabled', true);
        }

        $.ajax({
            url: `{{ route('user.sms.rental.check.code', ':id') }}`.replace(':id', id),
            method: 'GET',
            success: function(res) {
                if (res.success) {
                    if (res.sms_code) {
                        // Only update the DOM if the code actually changed
                        if (res.sms_code !== lastSeenCode[id]) {
                            lastSeenCode[id] = res.sms_code;
                            updateSmsCodeDisplay(id, res.sms_code);
                            if (!silent) notify('success', res.message);
                        }
                        // Leave status badge, action buttons, and data-status unchanged
                    } else if (!silent) {
                        notify('info', res.message);
                    }
                } else {
                    // Terminal states (cancelled/expired) — reload to reflect final status
                    if (res.status === 'cancelled' || res.status === 'expired') {
                        setTimeout(() => location.reload(), 800);
                    } else if (!silent) {
                        notify('info', res.message);
                    }
                }
            },
            error: function(xhr) {
                if (!silent) notify('error', xhr.responseJSON?.message || 'Something went wrong');
            },
            complete: function() {
                if ($btn) {
                    $btn.html('<i class="ri-refresh-line"></i> Check Code').prop('disabled', false);
                    // Shorter text for desktop table button
                    if ($btn.parents('table').length) {
                        $btn.html('<i class="ri-refresh-line"></i>');
                    }
                }
            }
        });
    }

    // Click handler
    $('.checkCodeBtn').on('click', function() {
        performCheck($(this).data('id'), $(this), false);
    });

    // ── Cancel button ───────────────────────────────────────────────────────
    let currentCancelId = null;

    window.prepareCancel = function(id) {
        currentCancelId = id;
        openCancelModal();
    };

    $('#confirmCancelBtn').on('click', function() {
        if (!currentCancelId) return;
        const $btn = $(this);
        const origHtml = $btn.html();
        
        // Show loading state on the confirm button
        $btn.html('<i class="ri-loader-4-line animate-spin"></i> Processing...').prop('disabled', true);

        // Use direct URL construction to ensure ID is passed correctly
        const url = `/user/sms-rental/cancel/${currentCancelId}`;

        $.ajax({
            url: url,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                if (res.success) { 
                    notify('success', res.message); 
                    closeCancelModal();
                    setTimeout(() => location.reload(), 1000); 
                } else { 
                    notify('error', res.message);
                    $btn.html(origHtml).prop('disabled', false);
                    closeCancelModal();
                }
            },
            error: function(xhr) { 
                notify('error', xhr.responseJSON?.message || 'Something went wrong'); 
                $btn.html(origHtml).prop('disabled', false);
                closeCancelModal();
            }
        });
    });

    // ── Silent auto-check every 10 s ────────────────────────────────────────
    function autoCheckCodes() {
        const seen = new Set();
        $('[data-status="active"], [data-status="pending"]').each(function() {
            const id = $(this).find('.checkCodeBtn').first().data('id');
            if (id && !seen.has(id)) {
                seen.add(id);
                performCheck(id, null, true);
            }
        });
    }

    // ── Countdown timers ────────────────────────────────────────────────────
    function initCountdownTimers() {
        $('.countdown-timer').each(function() {
            const $el = $(this);
            if ($el.data('ticking')) return;
            $el.data('ticking', true);
            const expires = new Date($el.data('expires'));
            const $disp = $el.find('.countdown-display');

            function tick() {
                const left = expires - Date.now();
                if (left <= 0) { $disp.text('Expired'); return; }
                const m = Math.floor(left / 60000);
                const s = Math.floor((left % 60000) / 1000);
                $disp.text(`${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`);
            }

            tick(); // show immediately on load
            setInterval(tick, 1000);
        });
    }

    // ── DOM helpers ─────────────────────────────────────────────────────────
    function updateSmsCodeDisplay(id, code) {
        const copyBtn = `<button onclick="copyToClipboard('${code}')" class="text-gray-300 hover:text-emerald-500 transition-colors"><i class="ri-file-copy-line"></i></button>`;
        const codeHtml = `<div class="flex items-center gap-1.5"><span class="font-mono font-bold text-emerald-600">${code}</span>${copyBtn}</div>`;

        // Desktop — target the specific TD by unique ID
        const $cell = $(`#sms-cell-${id}`);
        if ($cell.length) $cell.html(codeHtml);

        // Mobile — target the specific card container by unique ID, preserve the label
        const $card = $(`#sms-card-${id}`);
        if ($card.length) {
            $card.html(
                `<p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">SMS Code</p>${codeHtml}`
            );
        }
    }

    function updateStatusDisplay(id, status) {
        const label = status.charAt(0).toUpperCase() + status.slice(1);
        const badge = `<span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-medium">${label}</span>`;
        
        // Update desktop table
        $(`.checkCodeBtn[data-id="${id}"]`).closest('tr').find('td:nth-child(4)').html(badge);
        
        // Update mobile card
        $(`.checkCodeBtn[data-id="${id}"]`).closest('.p-4').find('.text-right').html(badge);
    }

    window.refreshOrders = function() {
        const icon = document.getElementById('refreshIcon');
        if (icon) icon.classList.add('animate-spin');
        
        // Trigger click on all check buttons
        $('.checkCodeBtn:not(:disabled)').each(function() {
            $(this).click();
        });
        
        setTimeout(() => { if (icon) icon.classList.remove('animate-spin'); }, 1000);
    };

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

    // ── Cancel modal helpers ────────────────────────────────────────────────
    window.openCancelModal = function() {
        const $modal = $('#cancelModal'), $content = $('#cancelModalContent');
        $modal.removeClass('hidden');
        setTimeout(() => $content.removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'), 10);
        $('body').addClass('overflow-hidden');
        $modal.on('click.cancel', function(e) { if (e.target === this) closeCancelModal(); });
        $(document).on('keydown.cancel', function(e) { if (e.key === 'Escape') closeCancelModal(); });
    };

    window.closeCancelModal = function() {
        const $modal = $('#cancelModal'), $content = $('#cancelModalContent');
        $content.removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(() => { $modal.addClass('hidden'); $('body').removeClass('overflow-hidden'); }, 300);
        $modal.off('click.cancel');
        $(document).off('keydown.cancel');
        currentCancelId = null;
    };

    // ── Init ────────────────────────────────────────────────────────────────
    $(document).ready(function() {
        initCountdownTimers();
        setInterval(autoCheckCodes, 10000);
    });

})(jQuery);
</script>
@endpush
