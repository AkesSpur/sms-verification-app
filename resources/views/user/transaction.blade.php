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
            <div class="flex items-center space-x-3">
                <!-- Filter Button -->
                <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-filter mr-2"></i>
                    Filter
                </button>
                <!-- Export Button -->
                <button class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-download mr-2"></i>
                    Export
                </button>
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
                    <p class="text-2xl font-bold text-gray-900" id="totalSpent">${{ number_format($totalSpent ?? 0, 2) }}</p>
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
                    <p class="text-2xl font-bold text-gray-900" id="totalRefunds">${{ number_format($totalRefunds ?? 0, 2) }}</p>
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
                    <p class="text-2xl font-bold text-gray-900" id="pendingAmount">${{ number_format($pendingAmount ?? 0, 2) }}</p>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="transactionsTableBody">
                    <!-- Sample data - will be replaced with dynamic data -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#TXN001</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-shopping-cart mr-1"></i>
                                Purchase
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">WhatsApp</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">$2.50</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Completed
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jan 15, 2024 10:30 AM</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-primary-600 hover:text-primary-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-download"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#TXN002</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-undo mr-1"></i>
                                Refund
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Telegram</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">$1.80</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>
                                Pending
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jan 14, 2024 3:45 PM</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-primary-600 hover:text-primary-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-download"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#TXN003</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-plus mr-1"></i>
                                Top-up
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">-</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">$25.00</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Completed
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jan 13, 2024 9:15 AM</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-primary-600 hover:text-primary-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-download"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </button>
                    <button class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">3</span> of <span class="font-medium">3</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <button class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="bg-primary-50 border-primary-500 text-primary-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                1
                            </button>
                            <button class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .primary-600 { color: #4f46e5; }
    .primary-700 { background-color: #4338ca; }
    .primary-500 { border-color: #6366f1; }
    .primary-50 { background-color: #eef2ff; }
    .bg-primary-600 { background-color: #4f46e5; }
    .bg-primary-700 { background-color: #4338ca; }
    .focus\:ring-primary-500:focus { --tw-ring-color: #6366f1; }
    .border-primary-500 { border-color: #6366f1; }
    .text-primary-600 { color: #4f46e5; }
    .text-primary-900 { color: #312e81; }
    .hover\:text-primary-900:hover { color: #312e81; }
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
    
    // Load transactions data via AJAX
    function loadTransactions() {
        $.ajax({
            url: '/api/user/transactions',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    updateTransactionsTable(response.data.transactions);
                    updateStats(response.data.stats);
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
            tbody.append('<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No transactions found</td></tr>');
        }
    }
    
    function createTransactionRow(transaction) {
        const statusClass = getStatusClass(transaction.status);
        const typeClass = getTypeClass(transaction.type);
        const typeIcon = getTypeIcon(transaction.type);
        
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
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">$${parseFloat(transaction.amount).toFixed(2)}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                        <i class="fas fa-${transaction.status === 'completed' ? 'check-circle' : transaction.status === 'pending' ? 'clock' : 'times-circle'} mr-1"></i>
                        ${transaction.status}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatDate(transaction.created_at)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button class="text-primary-600 hover:text-primary-900 mr-3" onclick="viewTransaction('${transaction.id}')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="text-gray-600 hover:text-gray-900" onclick="downloadReceipt('${transaction.id}')">
                        <i class="fas fa-download"></i>
                    </button>
                </td>
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
            case 'purchase': return 'bg-blue-100 text-blue-800';
            case 'refund': return 'bg-green-100 text-green-800';
            case 'top-up': return 'bg-purple-100 text-purple-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }
    
    function getTypeIcon(type) {
        switch(type.toLowerCase()) {
            case 'purchase': return 'fas fa-shopping-cart';
            case 'refund': return 'fas fa-undo';
            case 'top-up': return 'fas fa-plus';
            default: return 'fas fa-circle';
        }
    }
    
    function updateStats(stats) {
        if (stats) {
            $('#totalTransactions').text(stats.total_transactions || 0);
            $('#totalSpent').text('$' + parseFloat(stats.total_spent || 0).toFixed(2));
            $('#totalRefunds').text('$' + parseFloat(stats.total_refunds || 0).toFixed(2));
            $('#pendingAmount').text('$' + parseFloat(stats.pending_amount || 0).toFixed(2));
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
    
    // Global functions for button actions
    window.viewTransaction = function(id) {
        // Implement view transaction details
        console.log('View transaction:', id);
    };
    
    window.downloadReceipt = function(id) {
        // Implement download receipt
        console.log('Download receipt:', id);
    };
    
    // Load transactions on page load
    // loadTransactions();
});
</script>
@endsection

