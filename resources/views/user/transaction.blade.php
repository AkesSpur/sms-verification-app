@extends('layouts.user')

@section('title', 'Transaction History')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Transaction History</h1>
                <p class="text-gray-600 mt-1">View all your transaction records and payment history</p>
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
</script>
@endpush
