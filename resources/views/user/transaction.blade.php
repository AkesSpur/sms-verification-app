@extends('layouts.user')

@section('title', 'Transaction History')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <!-- Add Funds Section -->
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Account Funding</h3>
                    <p class="text-sm text-gray-500 mt-1">Add funds to your account to purchase services</p>
                </div>
                <button id="addFundsBtn" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Add Funds
                </button>
            </div>
        </div>
    </div>

    <!-- Add Funds Modal -->
    <div id="addFundsModal" style="display: none;" class="z-50">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 w-full h-full" style="z-index: 999;"></div>
        <div class="fixed inset-0 flex items-center justify-center" style="z-index: 1000;" onclick="closeAddFundsModal()">
            <div class="relative mx-auto p-6 border w-11/12 max-w-md shadow-lg rounded-md bg-white" onclick="event.stopPropagation()">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-medium text-gray-900">Add Funds to Account</h3>
                        <button onclick="closeAddFundsModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <!-- Payment Method Tabs -->
                    <div class="mb-6">
                        <div class="flex border-b border-gray-200">
                            @if($etegramSetting && $etegramSetting->status)
                            <button type="button" onclick="switchPaymentTab('etegram')" id="etegramTab" 
                                    class="px-4 py-2 text-sm font-medium text-primary-600 border-b-2 border-primary-600 bg-white">
                                <i class="fas fa-paper-plane mr-2"></i>Instant Paymentt
                            </button>
                            @endif
                            @if($localbankSetting && $localbankSetting->status)
                            <button type="button" onclick="switchPaymentTab('localbank')" id="localbankTab" 
                                    class="px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300">
                                <i class="fas fa-university mr-2"></i>Bank Transfer
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- Local Bank Transfer Section -->
                     @if($localbankSetting && $localbankSetting->status)
                     <div id="localbankSection" class="payment-section" style="display: none;">
                         <div class="space-y-6 max-h-96 overflow-y-auto pr-2">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-yellow-800">Manual Processing</h4>
                                        <p class="text-xs text-yellow-700 mt-1">After making the transfer, contact support for manual account funding</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">
                                    <i class="fas fa-university mr-2 text-primary-600"></i>
                                    Bank Account Details
                                </h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                        <span class="text-sm font-medium text-gray-600">Account Name:</span>
                                        <span class="text-sm text-gray-900 font-medium">{{ $localbankSetting->account_name }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                        <span class="text-sm font-medium text-gray-600">Account Number:</span>
                                        <span class="text-sm text-gray-900 font-mono font-bold">{{ $localbankSetting->account_number }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                        <span class="text-sm font-medium text-gray-600">Bank Name:</span>
                                        <span class="text-sm text-gray-900 font-medium">{{ $localbankSetting->bank_name }}</span>
                                    </div>
                                </div>
                                
                                @if($localbankSetting->extra_info)
                                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                    <h5 class="text-sm font-medium text-blue-800 mb-2">Additional Information:</h5>
                                    <div class="text-sm text-blue-700">{!! $localbankSetting->extra_info !!}</div>
                                </div>
                                @endif
                            </div>
                            
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle text-red-600 mt-0.5"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-red-800">Important Instructions</h4>
                                        <ul class="text-xs text-red-700 mt-1 list-disc list-inside space-y-1">
                                            <li>Make the transfer to the account details above</li>
                                            <li>Contact our support team with your transfer receipt</li>
                                            <li>Include your username and transfer amount in your message</li>
                                            <li>Your account will be funded manually after verification</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-8 flex justify-end space-x-3">
                            <button type="button" onclick="closeAddFundsModal()" 
                                    class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                                Close
                            </button>
                           
                        </div>
                    </div>
                    @endif

                    <!-- Etegram Section -->
                    @if($etegramSetting && $etegramSetting->status)
                    <div id="etegramSection" class="payment-section">
                        <!-- Warning Message -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-red-800">Service Temporarily Unavailable</h4>
                                    <p class="text-xs text-red-700 mt-1">Etegram payment service is currently down for maintenance. Please use alternative payment methods or try again later.</p>
                                </div>
                            </div>
                        </div>
                        
                        <form action="{{ route('user.etegram.redirect') }}" method="POST" class="space-y-6">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            
                            <div>
                                <label for="etegramAmountInput" class="block text-sm font-medium text-gray-700 mb-2">Amount (₦)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 ml-3 text-sm">₦</span>
                                    </div>
                                    <input type="number" id="etegramAmountInput" name="amount" min="100" max="1000000" step="0.01"
                                           class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                                           placeholder="  Enter amount" required oninput="calculateEtegramFees()">
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Minimum: ₦100 • Maximum: ₦1,000,000
                                </p>
                            </div>
                            
                            <!-- Fee Breakdown Section -->
                            <div id="etegramFeeBreakdown" class="bg-blue-50 border border-blue-200 rounded-lg p-4" style="display: none;">
                                <h4 class="text-sm font-medium text-blue-800 mb-3">
                                    <i class="fas fa-calculator mr-2"></i>Payment Breakdown
                                </h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-blue-700">Amount:</span>
                                        <span class="font-medium text-blue-900" id="etegramBaseAmount">₦0.00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-700">Transaction Fee:</span>
                                        <span class="font-medium text-blue-900" id="etegramFeeAmount">₦0.00</span>
                                    </div>
                                    <hr class="border-blue-200">
                                    <div class="flex justify-between font-semibold">
                                        <span class="text-blue-800">Total to Pay:</span>
                                        <span class="text-blue-900" id="etegramTotalAmount">₦0.00</span>
                                    </div>
                                </div>
                                <div class="mt-3 text-xs text-blue-600">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Fee: 1.5% + ₦100 (₦100 waived for amounts under ₦2,500, capped at ₦2,000)
                                </div>
                            </div>
                            
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-paper-plane text-green-600 mt-0.5"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-green-800">Instant Payment</h4>
                                        <p class="text-xs text-green-700 mt-1">Fast and secure online payment processing</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-8 flex justify-end space-x-3">
                                <button type="button" onclick="closeAddFundsModal()" 
                                        class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Proceed with Etegram
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Transactions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-receipt text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Transactions</p>
                    <p class="text-2xl font-bold text-gray-900" id="totalTransactions">{{ $totalTransactions ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Total Spent -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Spent</p>
                    <p class="text-2xl font-bold text-gray-900" id="totalSpent">₦{{ number_format($totalSpent ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Refunds -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Refunds</p>
                    <p class="text-2xl font-bold text-gray-900" id="totalRefunds">₦{{ number_format($totalRefunds ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Amount -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-gray-900" id="pendingAmount">₦{{ number_format($pendingAmount ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="transactionsTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>

                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="transactionsTableBody">
                   
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6" id="paginationContainer" style="display: none;">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button id="prevPageMobile" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <button id="nextPageMobile" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700" id="paginationInfo">
                            Showing <span class="font-medium" id="showingFrom">0</span> to <span class="font-medium" id="showingTo">0</span> of <span class="font-medium" id="totalResults">0</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination" id="paginationNav">
                            <!-- Pagination buttons will be dynamically generated -->
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Website Builder Contact -->
<div class="py-3 text-center text-sm text-gray-700 border-t border-gray-200 mt-6">
    <div class="flex items-center justify-center space-x-2 scale-90 hover:scale-100 transition-transform duration-300">
        <i class="fas fa-mobile-alt text-blue-600 animate-pulse"></i>
        <p>
            Need a custom website? <a href="https://wa.link/18c124 class="text-blue-600 hover:text-blue-800 font-medium transition-colors relative group">
                Contact the developer
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"></span>
            </a>
        </p>
        <i class="fas fa-code text-blue-600 animate-bounce"></i>
    </div>
</div>

    </div>
</div>
@endsection

@push('scripts')
<!-- Custom Styles -->
<style>
    /* Use consistent primary colors that match the sidebar theme */
    .primary-600 { color: #1e293b; }
    .primary-700 { background-color: #0f172a; }
    .primary-500 { border-color: #334155; }
    .primary-50 { background-color: #f8fafc; }
    .bg-primary-600 { background-color: #1e293b; }
    .bg-primary-700 { background-color: #0f172a; }
    .focus\:ring-primary-500:focus { --tw-ring-color: #334155; }
    .border-primary-500 { border-color: #334155; }
    .text-primary-600 { color: #1e293b; }
    .text-primary-700 { color: #0f172a; }
    .hover\:text-primary-700:hover { color: #0f172a; }
</style>


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
                // Handle authentication error with JSON response instead of redirect
                const response = {
                    success: false,
                    message: 'Authentication required. Please log in to continue.',
                    error: 'Unauthorized',
                    status: 401
                };
                
                // Show error message using toastr or alert
                if (typeof toastr !== 'undefined') {
                    toastr.error(response.message, 'Authentication Error');
                } else {
                    alert(response.message);
                }
                
                // Optionally redirect to login after showing the message
                setTimeout(function() {
                    window.location.href = '/login';
                }, 2000);
                
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
                    updateTransactionsTable(response.data.transactions);
                    updateStats(response.data.stats);
                    updatePagination(response.data.pagination);
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load transactions:', error);
                // Error handling is already set up in ajaxSetup
            }
        });
    }
    
    function updateTransactionsTable(transactions) {
        const tbody = $('#transactionsTableBody');
        tbody.empty();
        
        if (transactions && transactions.length > 0) {
            transactions.forEach(function(transaction) {
                const row = createTransactionRow(transaction);
                tbody.append(row);
            });
        } else {
            tbody.append('<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No transactions found</td></tr>');
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
            <button class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 ${prevDisabled ? 'opacity-50 cursor-not-allowed' : ''}" 
                    onclick="${prevDisabled ? '' : 'changePage(' + (currentPage - 1) + ')'}" ${prevDisabled ? 'disabled' : ''}>
                <i class="fas fa-chevron-left"></i>
            </button>
        `);
        
        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);
        
        // First page if not in range
        if (startPage > 1) {
            nav.append(`
                <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50" 
                        onclick="changePage(1)">1</button>
            `);
            if (startPage > 2) {
                nav.append('<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>');
            }
        }
        
        // Page range
        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === currentPage;
            nav.append(`
                <button class="${isActive ? 'bg-primary-50 border-primary-500 text-primary-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'} relative inline-flex items-center px-4 py-2 border text-sm font-medium" 
                        onclick="${isActive ? '' : 'changePage(' + i + ')'}" ${isActive ? 'disabled' : ''}>
                    ${i}
                </button>
            `);
        }
        
        // Last page if not in range
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                nav.append('<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>');
            }
            nav.append(`
                <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50" 
                        onclick="changePage(${totalPages})">${totalPages}</button>
            `);
        }
        
        // Next button
        const nextDisabled = currentPage >= totalPages;
        nav.append(`
            <button class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 ${nextDisabled ? 'opacity-50 cursor-not-allowed' : ''}" 
                    onclick="${nextDisabled ? '' : 'changePage(' + (currentPage + 1) + ')'}" ${nextDisabled ? 'disabled' : ''}>
                <i class="fas fa-chevron-right"></i>
            </button>
        `);
    }
    
    // Global function to change page
    window.changePage = function(page) {
        if (page >= 1 && page <= totalPages && page !== currentPage) {
            loadTransactions(page);
        }
    };
    
    // Mobile pagination event handlers
    $('#prevPageMobile').on('click', function() {
        if (currentPage > 1) {
            changePage(currentPage - 1);
        }
    });
    
    $('#nextPageMobile').on('click', function() {
        if (currentPage < totalPages) {
            changePage(currentPage + 1);
        }
    });
    
    function createTransactionRow(transaction) {
        const statusClass = getStatusClass(transaction.status);
        const typeClass = getTypeClass(transaction.transaction_type);
        const typeIcon = getTypeIcon(transaction.transaction_type);
        const amountDisplay = transaction.transaction_type === 'credit' ? '+₦' : '-₦';
        
        return `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#${transaction.id}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${typeClass}">
                        <i class="${typeIcon} mr-1"></i>
                        ${transaction.type}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${transaction.service || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium ${transaction.transaction_type === 'credit' ? 'text-green-600' : 'text-red-600'}">${amountDisplay}${parseFloat(transaction.amount).toFixed(2)}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                        <i class="fas fa-${transaction.status === 'completed' ? 'check-circle' : transaction.status === 'pending' ? 'clock' : 'times-circle'} mr-1"></i>
                        ${transaction.status}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatDate(transaction.created_at)}</td>

            </tr>
        `;
    }
    
    function getStatusClass(status) {
        switch(status.toLowerCase()) {
            case 'completed': return 'bg-green-100 text-green-800';
            case 'pending': return 'bg-yellow-100 text-yellow-800';
            case 'failed': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }
    
    function getTypeClass(type) {
        switch(type.toLowerCase()) {
            case 'credit': return 'bg-green-100 text-green-800';
            case 'debit': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }
    
    function getTypeIcon(type) {
        switch(type.toLowerCase()) {
            case 'credit': return 'fas fa-arrow-up';
            case 'debit': return 'fas fa-arrow-down';
            default: return 'fas fa-circle';
        }
    }
    
    function updateStats(stats) {
        if (stats) {
            $('#totalTransactions').text(stats.total_transactions || 0);
            $('#totalSpent').text('₦' + parseFloat(stats.total_spent || 0).toFixed(2));
            $('#totalRefunds').text('₦' + parseFloat(stats.total_refunds || 0).toFixed(2));
            $('#pendingAmount').text('₦' + parseFloat(stats.pending_amount || 0).toFixed(2));
        }
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    

    
    // Load transactions on page load
    loadTransactions();
});

// Legacy functions removed - now using closeAddFundsModal()

// Add Funds Modal functionality
document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    document.getElementById('addFundsBtn').addEventListener('click', function() {
        document.getElementById('addFundsModal').style.display = 'block';
    });

    function closeAddFundsModal() {
        document.getElementById('addFundsModal').style.display = 'none';
        // Reset form
        document.getElementById('depositForm').reset();
    }

    // Make closeAddFundsModal globally accessible
    window.closeAddFundsModal = closeAddFundsModal;

    // Close modal with Escape key
     document.addEventListener('keydown', function(e) {
         if (e.key === 'Escape') {
             closeAddFundsModal();
         }
     });
});

// Payment tab switching functionality
function switchPaymentTab(tabName) {
    // Hide all payment sections
    const sections = document.querySelectorAll('.payment-section');
    sections.forEach(section => {
        section.style.display = 'none';
    });
    
    // Remove active classes from all tabs
    const tabs = document.querySelectorAll('[id$="Tab"]');
    tabs.forEach(tab => {
        tab.classList.remove('text-primary-600', 'border-primary-600');
        tab.classList.add('text-gray-500', 'border-transparent');
    });
    
    // Show selected section and activate tab
    if (tabName === 'localbank') {
        document.getElementById('localbankSection').style.display = 'block';
        const localbankTab = document.getElementById('localbankTab');
        localbankTab.classList.remove('text-gray-500', 'border-transparent');
        localbankTab.classList.add('text-primary-600', 'border-primary-600');
    } else if (tabName === 'etegram') {
        document.getElementById('etegramSection').style.display = 'block';
        const etegramTab = document.getElementById('etegramTab');
        etegramTab.classList.remove('text-gray-500', 'border-transparent');
        etegramTab.classList.add('text-primary-600', 'border-primary-600');
    }
}

// Calculate Etegram fees and display breakdown
function calculateEtegramFees() {
    const amountInput = document.getElementById('etegramAmountInput');
    const feeBreakdown = document.getElementById('etegramFeeBreakdown');
    const baseAmountElement = document.getElementById('etegramBaseAmount');
    const feeAmountElement = document.getElementById('etegramFeeAmount');
    const totalAmountElement = document.getElementById('etegramTotalAmount');
    
    const amount = parseFloat(amountInput.value) || 0;
    
    if (amount > 0) {
        // Calculate fee: 1.5% + NGN100
        let percentageFee = amount * 0.015; // 1.5%
        let fixedFee = 100; // NGN100
        
        // NGN100 fee waived for transactions under NGN2500
        if (amount < 2500) {
            fixedFee = 0;
        }
        
        let totalFee = percentageFee + fixedFee;
        
        // Local transactions fees are capped at ₦2000
        if (totalFee > 2000) {
            totalFee = 2000;
        }
        
        const totalAmount = amount + totalFee;
        
        // Format amounts
        baseAmountElement.textContent = `₦${amount.toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        feeAmountElement.textContent = `₦${totalFee.toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        totalAmountElement.textContent = `₦${totalAmount.toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        
        // Show the breakdown
        feeBreakdown.style.display = 'block';
    } else {
        // Hide the breakdown if no amount
        feeBreakdown.style.display = 'none';
    }
}

// Contact support functionality
function contactSupport() {
    // You can customize this based on your support system
    // For now, it will open a mailto link or redirect to support page
    const supportEmail = 'support@yoursite.com'; // Replace with actual support email
    const subject = 'Bank Transfer - Account Funding Request';
    const body = `Hello Support Team,\n\nI have made a bank transfer for account funding.\n\nUsername: {{ Auth::user()->username ?? Auth::user()->email }}\n\nPlease find the transfer receipt attached and fund my account accordingly.\n\nThank you.`;
    
    const mailtoLink = `mailto:${supportEmail}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
    window.location.href = mailtoLink;
    
    // Alternative: You can redirect to a support page or open a chat widget
    // window.open('/support', '_blank');
}
</script>


@endpush
