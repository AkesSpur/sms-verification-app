@extends('layouts.app')

@section('title', 'Transaction History')

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">

    {{-- ── Stat: Total Spent ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-sm font-bold text-gray-900">Wallet & Transactions</h1>
                <p class="text-[11px] text-gray-400 mt-0.5">Manage your balance and view transaction history</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] uppercase tracking-widest text-gray-400 font-semibold mb-0.5">Total Spent</p>
                <p class="text-lg font-bold text-gray-900">&#8358;{{ number_format($totalSpent, 2) }}</p>
            </div>
        </div>
    </div>

    {{-- ── Add Funds (inline, no modal) ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-4">Add Funds</p>

        <div>
            {{-- Virtual Account Section --}}
            <div id="virtualAccountSection" class="payment-section">
                <div class="flex items-start gap-2 bg-primary-50 border border-primary-100 rounded-xl px-3 py-2 mb-4 text-xs text-primary-700">
                    <i class="ri-information-line flex-shrink-0 mt-0.5"></i>
                    <span>Create your virtual bank account and fund it by transfer. Your wallet will be credited automatically.</span>
                </div>
                <div id="vaCreateContainer">
                    <button id="createVaBtnModal" type="button"
                            class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white transition-all btn-glow"
                            style="background:linear-gradient(135deg,#475569,#1e293b)">
                        <i class="ri-bank-card-line"></i> Create Virtual Account
                    </button>
                </div>
                <div id="vaCardModal" class="hidden">
                    <div class="rounded-2xl p-6 text-white relative overflow-hidden" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); box-shadow: 0 10px 30px -10px rgba(0,0,0,0.3);">
                        <!-- Background Pattern -->
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-5 rounded-full blur-xl"></div>
                        <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-20 h-20 bg-primary-500 opacity-10 rounded-full blur-xl"></div>
                        
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Bank Name</p>
                                    <p class="font-bold text-lg text-white tracking-wide" id="vaBankNameModal">—</p>
                                </div>
                                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center backdrop-blur-sm">
                                    <i class="ri-bank-line text-white"></i>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Account Number</p>
                                <div class="flex items-center gap-3 group">
                                    <p class="font-mono font-bold text-2xl text-white tracking-widest" id="vaAccountNumberModal">—</p>
                                    <button onclick="copyToClipboard(document.getElementById('vaAccountNumberModal').textContent)"
                                            class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 transform translate-y-1 group-hover:translate-y-0">
                                        <i class="ri-file-copy-line text-sm"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Account Name</p>
                                <p class="font-medium text-sm text-gray-200" id="vaAccountNameModal">—</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Transactions List (Div-based) ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Recent Transactions</p>
        </div>
        
        <div id="transactionsContainer">
            {{-- Transactions will be loaded here via AJAX --}}
        </div>

        {{-- Pagination --}}
        <div class="px-5 py-3 border-t border-gray-50" id="paginationContainer" style="display: none;">
            <div class="flex items-center justify-between">
                <div class="flex gap-2 sm:hidden">
                    <button id="prevPageMobile" class="px-3 py-1.5 text-xs border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">Previous</button>
                    <button id="nextPageMobile" class="px-3 py-1.5 text-xs border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">Next</button>
                </div>
                <div class="hidden sm:flex sm:items-center sm:justify-between w-full">
                    <p class="text-xs text-gray-400" id="paginationInfo">
                        Showing <span class="font-medium text-gray-600" id="showingFrom">0</span>–<span class="font-medium text-gray-600" id="showingTo">0</span> of <span class="font-medium text-gray-600" id="totalResults">0</span>
                    </p>
                    <nav class="flex items-center gap-1" id="paginationNav"></nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- JavaScript for enhanced functionality -->
<script>
$(document).ready(function() {
    // Handle authentication errors with JSON response
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        error: function(xhr, status, error) {
            if (xhr.status === 401) {
                window.location.href = '/login';
                return false;
            }
        }
    });
    
    // Global pagination state
    let currentPage = 1;
    let totalPages = 1;
    
    // Load transactions data via AJAX
    function loadTransactions(page = 1) {
        $.ajax({
            url: '/api/user/transactions',
            method: 'GET',
            data: { page: page },
            success: function(response) {
                if (response.success) {
                    updateTransactionsList(response.data.transactions);
                    updateStats(response.data.stats);
                    updatePagination(response.data.pagination);
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load transactions:', error);
            }
        });
    }
    
    function updateTransactionsList(transactions) {
        const container = $('#transactionsContainer');
        container.empty();
        
        if (transactions && transactions.length > 0) {
            transactions.forEach(function(transaction) {
                const row = createTransactionRow(transaction);
                container.append(row);
            });
        } else {
            container.append(`
                <div class="flex flex-col items-center py-14 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mb-3">
                        <i class="ri-file-list-3-line text-gray-200 text-3xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-400">No transactions found</p>
                </div>
            `);
        }
    }
    
    function updatePagination(pagination) {
        currentPage = pagination.current_page;
        totalPages = pagination.last_page;
        
        // Update pagination info
        const from = ((currentPage - 1) * pagination.per_page) + 1;
        const to = Math.min(currentPage * pagination.per_page, pagination.total);
        
        $('#showingFrom').text(pagination.total > 0 ? from : 0);
        $('#showingTo').text(to);
        $('#totalResults').text(pagination.total);
        
        // Show/hide pagination container
        if (totalPages > 1) {
            $('#paginationContainer').show();
            generatePaginationButtons(pagination);
        } else {
            $('#paginationContainer').hide();
        }
        
        // Update mobile pagination buttons
        $('#prevPageMobile').prop('disabled', currentPage <= 1);
        $('#nextPageMobile').prop('disabled', currentPage >= totalPages);
    }
    
    function generatePaginationButtons(pagination) {
        const nav = $('#paginationNav');
        nav.empty();
        
        // Previous button
        const prevDisabled = currentPage <= 1;
        nav.append(`
            <button class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors text-gray-500 disabled:opacity-50 disabled:cursor-not-allowed" 
                    onclick="${prevDisabled ? '' : 'changePage(' + (currentPage - 1) + ')'}" ${prevDisabled ? 'disabled' : ''}>
                <i class="ri-arrow-left-s-line"></i>
            </button>
        `);
        
        // Page numbers logic
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);
        
        if (startPage > 1) {
            nav.append(`<button class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-xs font-medium text-gray-600" onclick="changePage(1)">1</button>`);
            if (startPage > 2) nav.append(`<span class="px-1 text-gray-400">...</span>`);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === currentPage;
            nav.append(`
                <button class="w-8 h-8 rounded-lg border ${isActive ? 'border-primary-600 bg-primary-600 text-white' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50'} flex items-center justify-center text-xs font-medium transition-colors" 
                        onclick="${isActive ? '' : 'changePage(' + i + ')'}" ${isActive ? 'disabled' : ''}>
                    ${i}
                </button>
            `);
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) nav.append(`<span class="px-1 text-gray-400">...</span>`);
            nav.append(`<button class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-xs font-medium text-gray-600" onclick="changePage(${totalPages})">${totalPages}</button>`);
        }
        
        // Next button
        const nextDisabled = currentPage >= totalPages;
        nav.append(`
            <button class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors text-gray-500 disabled:opacity-50 disabled:cursor-not-allowed" 
                    onclick="${nextDisabled ? '' : 'changePage(' + (currentPage + 1) + ')'}" ${nextDisabled ? 'disabled' : ''}>
                <i class="ri-arrow-right-s-line"></i>
            </button>
        `);
    }
    
    window.changePage = function(page) {
        if (page >= 1 && page <= totalPages && page !== currentPage) {
            loadTransactions(page);
        }
    };
    
    // Mobile pagination handlers
    $('#prevPageMobile').on('click', function() { if (currentPage > 1) changePage(currentPage - 1); });
    $('#nextPageMobile').on('click', function() { if (currentPage < totalPages) changePage(currentPage + 1); });
    
    function createTransactionRow(transaction) {
        const isCredit = transaction.transaction_type.toLowerCase() === 'credit';
        const amountDisplay = isCredit ? '+₦' : '-₦';
        const amountClass = isCredit ? 'text-emerald-600' : 'text-red-600';
        const iconBg = isCredit ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600';
        const icon = isCredit ? 'ri-arrow-left-down-line' : 'ri-arrow-right-up-line';
        
        // Format date to "time ago"
        const timeAgoStr = timeAgo(transaction.created_at);

        // Status badge style
        let statusClass = 'bg-gray-100 text-gray-600';
        let statusIcon = 'ri-checkbox-circle-line';
        if (transaction.status === 'completed') {
            statusClass = 'bg-emerald-50 text-emerald-600';
            statusIcon = 'ri-checkbox-circle-fill';
        } else if (transaction.status === 'pending') {
            statusClass = 'bg-amber-50 text-amber-600';
            statusIcon = 'ri-time-line';
        } else if (transaction.status === 'failed') {
            statusClass = 'bg-red-50 text-red-600';
            statusIcon = 'ri-close-circle-line';
        }

        return `
        <div class="flex items-center gap-4 p-4 border-b border-gray-50 last:border-0 hover:bg-gray-50/60 transition-colors">
            <div class="w-10 h-10 rounded-xl ${iconBg} flex items-center justify-center flex-shrink-0">
                <i class="${icon} text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm truncate">#${transaction.id}</p>
                <p class="text-[11px] text-gray-400 mt-0.5 flex items-center gap-1">
                    <i class="ri-time-line"></i> ${timeAgoStr}
                </p>
            </div>
            <div class="font-medium text-sm ${amountClass}">
                ${amountDisplay}${parseFloat(transaction.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
            </div>
            <div class="flex-shrink-0">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide ${statusClass}">
                    <i class="${statusIcon}"></i> ${transaction.status}
                </span>
            </div>
        </div>
        `;
    }
    
    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        
        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + " years ago";
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + " months ago";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + " days ago";
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + " hours ago";
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + " minutes ago";
        return Math.floor(seconds) + " seconds ago";
    }
    
    function updateStats(stats) {
        // Logic to update stats if needed
    }
    
    // Load transactions on page load
    loadTransactions();
    
    // Initialize Virtual Account Modal Logic immediately
    initVirtualAccountModal();
});

// Virtual Account Logic
function initVirtualAccountModal() {
    const createBtn = document.getElementById('createVaBtnModal');
    const card = document.getElementById('vaCardModal');
    const createContainer = document.getElementById('vaCreateContainer');

    // Fetch existing VA
    fetch('{{ route('api.user.virtual-account.get') }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.has_account) {
            document.getElementById('vaAccountNumberModal').textContent = data.account.account_number;
            document.getElementById('vaAccountNameModal').textContent = data.account.account_name;
            document.getElementById('vaBankNameModal').textContent = data.account.bank_name;
            card.classList.remove('hidden');
            createContainer.classList.add('hidden');
        } else {
            card.classList.add('hidden');
            createContainer.classList.remove('hidden');
        }
    })
    .catch(() => {/* ignore */});

    // Create VA
    if (createBtn && !createBtn.dataset.bound) {
        createBtn.dataset.bound = '1';
        createBtn.addEventListener('click', function() {
            createBtn.disabled = true;
            createBtn.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i>Creating...';
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
                    document.getElementById('vaAccountNumberModal').textContent = data.account.account_number;
                    document.getElementById('vaAccountNameModal').textContent = data.account.account_name;
                    document.getElementById('vaBankNameModal').textContent = data.account.bank_name;
                    card.classList.remove('hidden');
                    createContainer.classList.add('hidden');
                } else {
                    notify('error', data.message || 'Failed to create virtual account');
                    createBtn.disabled = false;
                    createBtn.innerHTML = '<i class="ri-bank-card-line"></i> Create Virtual Account';
                }
            })
            .catch(err => {
                console.error(err);
                notify('error', 'An error occurred');
                createBtn.disabled = false;
                createBtn.innerHTML = '<i class="ri-bank-card-line"></i> Create Virtual Account';
            });
        });
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        if(typeof notify === 'function') notify('success', 'Copied to clipboard!');
        else alert('Copied!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
@endpush
