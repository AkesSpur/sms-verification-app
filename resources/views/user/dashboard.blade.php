@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    {{-- ============================================================
         TOP ROW: Balance card + Virtual Account card
         ============================================================ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

        {{-- Balance + Fund Wallet --}}
        <div class="relative overflow-hidden rounded-2xl p-6 text-white"
             style="background: linear-gradient(135deg, #10b981 0%, #059669 60%, #047857 100%);
                    box-shadow: 0 8px 32px rgba(16,185,129,0.35), 0 2px 8px rgba(16,185,129,0.15);">
            {{-- Decorative circles --}}
            <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/10 pointer-events-none"></div>
            <div class="absolute -bottom-4 -left-4 w-20 h-20 rounded-full bg-white/5 pointer-events-none"></div>

            <div class="relative z-10">
                <p class="text-emerald-100 text-xs uppercase tracking-widest font-medium mb-1">Wallet Balance</p>
                <p class="text-3xl font-bold tracking-tight mb-5">&#8358;{{ number_format($balance, 2) }}</p>
                <a href="{{ route('user.transaction') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/20 hover:bg-white/30 text-white rounded-xl text-sm font-semibold transition-all duration-200 backdrop-blur-sm border border-white/20">
                    <i class="ri-arrow-up-circle-line text-base"></i>
                    Fund Wallet
                </a>
            </div>
        </div>

        {{-- Virtual Account area --}}
        <div>
            {{-- Has account: shiny card --}}
            <div id="vaDetails" class="hidden h-full">
                <div class="relative overflow-hidden rounded-2xl p-6 text-white h-full"
                     style="background: linear-gradient(135deg, #475569 0%, #1e293b 55%, #0f172a 100%);
                            box-shadow: 0 8px 32px rgba(71,85,105,0.45), 0 2px 8px rgba(71,85,105,0.2);">
                    {{-- Decorative circles --}}
                    <div class="absolute -top-8 -right-8 w-44 h-44 rounded-full bg-white/10 pointer-events-none"></div>
                    <div class="absolute -bottom-6 -left-6 w-28 h-28 rounded-full bg-white/5 pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-white/60 text-xs uppercase tracking-widest">Virtual Account</p>
                                <p class="text-white font-semibold text-base mt-0.5" id="vaBankName">-</p>
                            </div>
                            {{-- Card chip --}}
                            <svg width="38" height="28" viewBox="0 0 38 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="38" height="28" rx="4" fill="rgba(255,255,255,0.18)"/>
                                <rect x="4" y="7" width="30" height="14" rx="2" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.3)" stroke-width="0.6"/>
                                <line x1="19" y1="7" x2="19" y2="21" stroke="rgba(255,255,255,0.25)" stroke-width="0.6"/>
                                <line x1="4" y1="14" x2="34" y2="14" stroke="rgba(255,255,255,0.25)" stroke-width="0.6"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-white/50 text-xs uppercase tracking-wider mb-1">Account Number</p>
                            <div class="flex items-center gap-3 mb-3">
                                <p class="text-xl font-mono font-bold tracking-widest" id="vaAccountNumber">-</p>
                                <button onclick="copyToClipboard(document.getElementById('vaAccountNumber').textContent)"
                                        class="p-1.5 rounded-lg bg-white/10 hover:bg-white/25 transition-colors text-white/70 hover:text-white">
                                    <i class="ri-file-copy-line text-sm"></i>
                                </button>
                            </div>
                            <p class="text-white/50 text-xs uppercase tracking-wider mb-0.5">Account Name</p>
                            <p class="text-white font-medium text-sm" id="vaAccountName">-</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- No account: balance card with create button --}}
            <div id="noVaCard" class="hidden h-full">
                <div class="flex flex-col justify-between h-full bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-start gap-4 mb-5">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background: rgba(71,85,105,0.1);">
                            <i class="ri-bank-card-2-line text-primary-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Virtual Account</p>
                            <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">Link a virtual bank account to fund your wallet instantly with a direct bank transfer.</p>
                        </div>
                    </div>
                    <button id="createVaBtn"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all btn-glow"
                            style="background: linear-gradient(135deg, #475569, #1e293b);">
                        <i class="ri-add-circle-line text-base"></i>
                        Create Virtual Account
                    </button>
                </div>
            </div>

            {{-- Loading state (initial) --}}
            <div id="vaLoading" class="h-full bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-center justify-center">
                <i class="ri-loader-4-line text-primary-400 text-2xl animate-spin"></i>
            </div>
        </div>
    </div>

    {{-- ============================================================
         QUICK ACTIONS
         ============================================================ --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-bold uppercase tracking-[0.1em] text-gray-400 mb-4">Quick Actions</p>
        <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-7 gap-3">

            <a href="{{ route('user.sms.rental.index') }}"
               class="flex flex-col items-center gap-2.5 p-4 rounded-2xl border border-gray-100 hover:border-primary-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform"
                     style="background: rgba(71,85,105,0.1);">
                    <i class="ri-sim-card-line text-primary-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-600 text-center leading-tight">USA Numbers 1</span>
            </a>

            <a href="{{ route('user.usa-numbers') }}"
               class="flex flex-col items-center gap-2.5 p-4 rounded-2xl border border-gray-100 hover:border-blue-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform"
                     style="background: rgba(59,130,246,0.1);">
                    <i class="ri-smartphone-line text-blue-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-600 text-center leading-tight">USA Numbers 2</span>
            </a>

            <a href="{{ route('user.all-countries') }}"
               class="flex flex-col items-center gap-2.5 p-4 rounded-2xl border border-gray-100 hover:border-emerald-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform"
                     style="background: rgba(16,185,129,0.1);">
                    <i class="ri-earth-line text-emerald-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-600 text-center leading-tight">All Countries</span>
            </a>

            <a href="{{ route('user.reseller') }}"
               class="flex flex-col items-center gap-2.5 p-4 rounded-2xl border border-gray-100 hover:border-amber-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform"
                     style="background: rgba(245,158,11,0.1);">
                    <i class="ri-price-tag-3-line text-amber-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-600 text-center leading-tight">Reseller Store</span>
            </a>

            <a href="{{ route('home') }}"
               class="flex flex-col items-center gap-2.5 p-4 rounded-2xl border border-gray-100 hover:border-sky-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform"
                     style="background: rgba(14,165,233,0.1);">
                    <i class="ri-archive-drawer-line text-sky-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-600 text-center leading-tight">Logs Store</span>
            </a>

            <a href="{{ route('all-gifts') }}"
               class="flex flex-col items-center gap-2.5 p-4 rounded-2xl border border-gray-100 hover:border-pink-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform"
                     style="background: rgba(236,72,153,0.1);">
                    <i class="ri-gift-line text-pink-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-600 text-center leading-tight">Gift Store</span>
            </a>

            <a href="{{ route('user.social-media-boosting.index') }}"
               class="flex flex-col items-center gap-2.5 p-4 rounded-2xl border border-gray-100 hover:border-violet-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform"
                     style="background: rgba(139,92,246,0.1);">
                    <i class="ri-rocket-line text-violet-600 text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-600 text-center leading-tight">Boosting</span>
            </a>

        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const vaDetails  = document.getElementById('vaDetails');
    const noVaCard   = document.getElementById('noVaCard');
    const vaLoading  = document.getElementById('vaLoading');
    const createBtn  = document.getElementById('createVaBtn');

    function showCard(account) {
        document.getElementById('vaAccountNumber').textContent = account.account_number;
        document.getElementById('vaAccountName').textContent   = account.account_name;
        document.getElementById('vaBankName').textContent      = account.bank_name;
        vaLoading.classList.add('hidden');
        noVaCard.classList.add('hidden');
        vaDetails.classList.remove('hidden');
    }

    function showNoVa() {
        vaLoading.classList.add('hidden');
        vaDetails.classList.add('hidden');
        noVaCard.classList.remove('hidden');
    }

    function loadVA() {
        fetch('{{ route('api.user.virtual-account.get') }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.has_account) {
                showCard(data.account);
            } else {
                showNoVa();
            }
        })
        .catch(() => showNoVa());
    }

    createBtn && createBtn.addEventListener('click', function() {
        createBtn.disabled = true;
        createBtn.innerHTML = '<i class="ri-loader-4-line animate-spin text-base"></i> Creating...';
        fetch('{{ route('api.user.virtual-account.create') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showCard(data.account);
            } else {
                notify('error', data.message || 'Failed to create virtual account');
                createBtn.disabled = false;
                createBtn.innerHTML = '<i class="ri-add-circle-line text-base"></i> Create Virtual Account';
            }
        })
        .catch(() => {
            notify('error', 'An error occurred');
            createBtn.disabled = false;
            createBtn.innerHTML = '<i class="ri-add-circle-line text-base"></i> Create Virtual Account';
        });
    });

    loadVA();
});
</script>
@endpush
</div>

@endsection
