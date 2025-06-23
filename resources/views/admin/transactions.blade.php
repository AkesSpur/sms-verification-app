@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Transaction Management</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Transactions</div>
        </div>
    </div>

    <div class="section-body">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Transactions</h4>
                        </div>
                        <div class="card-body" id="totalTransactions">
                            {{ $stats['total_transactions'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Credits</h4>
                        </div>
                        <div class="card-body" id="totalCredits">
                            ₦{{ number_format($stats['total_credits'] ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Debits</h4>
                        </div>
                        <div class="card-body" id="totalDebits">
                            ₦{{ number_format($stats['total_debits'] ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Today's Transactions</h4>
                        </div>
                        <div class="card-body" id="todayTransactions">
                            {{ $stats['today_transactions'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Filter Transactions</h4>
                        <div class="card-header-action">
                            <a href="#" onclick="exportTransactions()" class="btn btn-success">
                                <i class="fas fa-download"></i> Export CSV
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="filterForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>User</label>
                                        <input type="text" id="userFilter" class="form-control" placeholder="Search by user name or email...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <select id="typeFilter" class="form-control select2">
                                            <option value="">All Types</option>
                                            <option value="credit">Credit</option>
                                            <option value="debit">Debit</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select id="categoryFilter" class="form-control">
                                            <option value="">All Categories</option>
                                            <option value="fund_addition">Fund Addition</option>
                                            <option value="fund_withdrawal">Fund Withdrawal</option>
                                            <option value="gift_purchase">Gift Purchase</option>
                                            <option value="gift_refund">Gift Refund</option>
                                            <option value="digital_product_purchase">Digital Product Purchase</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select id="statusFilter" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="completed">Completed</option>
                                            <option value="pending">Pending</option>
                                            <option value="failed">Failed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date Range</label>
                                        <div class="input-group">
                                            <input type="date" id="dateFrom" class="form-control" placeholder="From">
                                            <div class="input-group-prepend input-group-append">
                                                <div class="input-group-text">to</div>
                                            </div>
                                            <input type="date" id="dateTo" class="form-control" placeholder="To">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-primary" onclick="applyFilters()">Filter</button>
                                        <button type="button" class="btn btn-secondary" onclick="clearFilters()">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Transactions (<span id="transactionCount">0</span> total)</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#TXN-ID</th>
                                        <th>User</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Amount</th>
                                        <th>Balance Before</th>
                                        <th>Balance After</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="transactionsTableBody">
                                    <!-- Transactions will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div id="paginationContainer" class="d-flex justify-content-center">
                            <!-- Pagination will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="transactionModalBody">
                <!-- Transaction details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentPage = 1;
    let currentFilters = {};

    $(document).ready(function() {
        loadTransactions();
    });

    function loadTransactions(page = 1) {
        currentPage = page;
        
        const params = new URLSearchParams({
            page: page,
            ...currentFilters
        });

        $.ajax({
            url: '{{ route("admin.transactions.data") }}?' + params.toString(),
            method: 'GET',
            success: function(response) {
                updateTransactionsTable(response.data);
                updatePagination(response);
                updateStats(response.stats);
            },
            error: function(xhr) {
                console.error('Error loading transactions:', xhr);
                toastr.error('Failed to load transactions');
            }
        });
    }

    function updateTransactionsTable(transactions) {
        const tbody = $('#transactionsTableBody');
        tbody.empty();

        if (transactions.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <div class="empty-state">
                            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No transactions found</h5>
                            <p class="text-muted">Transactions will appear here when users make payments or transfers.</p>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }

        transactions.forEach(transaction => {
            tbody.append(createTransactionRow(transaction));
        });
    }

    function createTransactionRow(transaction) {
        const statusClass = getStatusClass(transaction.status);
        const typeClass = getTypeClass(transaction.type);
        const typeIcon = getTypeIcon(transaction.type);
        const amountDisplay = transaction.type === 'credit' ? '+₦' : '-₦';
        
        return `
            <tr>
                <td>
                    <strong>${transaction.transaction_id}</strong>
                </td>
                <td>
                    <div>
                        <strong>${transaction.user_name}</strong><br>
                        <small class="text-muted">${transaction.user_email}</small>
                    </div>
                </td>
                <td>
                    <span class="text-white badge ${typeClass}">
                        <i class="${typeIcon} mr-1"></i>
                        ${transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1)}
                    </span>
                </td>
                <td>${transaction.category_display}</td>
                <td>
                    <strong class="${transaction.type === 'credit' ? 'text-success' : 'text-danger'}">
                        ${amountDisplay}${parseFloat(transaction.amount).toFixed(2)}
                    </strong>
                </td>
                <td>₦${parseFloat(transaction.balance_before).toFixed(2)}</td>
                <td>₦${parseFloat(transaction.balance_after).toFixed(2)}</td>
                <td>
                    <span class="badge text-white ${statusClass}">
                        ${transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)}
                    </span>
                </td>
                <td>
                    <div>
                        ${formatDate(transaction.created_at)}<br>
                        <small class="text-muted">${formatTime(transaction.created_at)}</small>
                    </div>
                </td>
                <td>
                    <a class="btn btn-sm btn-outline-primary" href="#" onclick="viewTransaction('${transaction.id}')" title="View Details">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
        `;
    }

    function getStatusClass(status) {
        switch(status.toLowerCase()) {
            case 'completed': return 'bg-success';
            case 'pending': return 'bg-warning';
            case 'failed': return 'bg-danger';
            default: return 'bg-secondary';
        }
    }

    function getTypeClass(type) {
        switch(type.toLowerCase()) {
            case 'credit': return 'bg-success';
            case 'debit': return 'bg-danger';
            default: return 'bg-secondary';
        }
    }

    function getTypeIcon(type) {
        switch(type.toLowerCase()) {
            case 'credit': return 'fas fa-arrow-up';
            case 'debit': return 'fas fa-arrow-down';
            default: return 'fas fa-circle';
        }
    }

    function updatePagination(response) {
        const pagination = $('#transactionPagination');
        const info = $('#transactionInfo');
        
        // Update info
        const start = (response.current_page - 1) * response.per_page + 1;
        const end = Math.min(response.current_page * response.per_page, response.total);
        info.text(`Showing ${start} to ${end} of ${response.total} entries`);
        
        // Update pagination
        pagination.empty();
        
        if (response.last_page > 1) {
            let paginationHtml = '<ul class="pagination pagination-rounded justify-content-end mb-0">';
            
            // Previous button
            if (response.current_page > 1) {
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadTransactions(${response.current_page - 1})">Previous</a></li>`;
            }
            
            // Page numbers
            for (let i = Math.max(1, response.current_page - 2); i <= Math.min(response.last_page, response.current_page + 2); i++) {
                const activeClass = i === response.current_page ? 'active' : '';
                paginationHtml += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="loadTransactions(${i})">${i}</a></li>`;
            }
            
            // Next button
            if (response.current_page < response.last_page) {
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadTransactions(${response.current_page + 1})">Next</a></li>`;
            }
            
            paginationHtml += '</ul>';
            pagination.html(paginationHtml);
        }
    }

    function updateStats(stats) {
        if (stats) {
            $('#totalTransactions').text(stats.total_transactions || 0);
            $('#totalCredits').text('₦' + parseFloat(stats.total_credits || 0).toFixed(2));
            $('#totalDebits').text('₦' + parseFloat(stats.total_debits || 0).toFixed(2));
            $('#todayTransactions').text(stats.today_transactions || 0);
        }
    }

    function applyFilters() {
        currentFilters = {
            user: $('#userFilter').val(),
            type: $('#typeFilter').val(),
            category: $('#categoryFilter').val(),
            status: $('#statusFilter').val(),
            date_from: $('#dateFrom').val(),
            date_to: $('#dateTo').val()
        };
        
        // Remove empty filters
        Object.keys(currentFilters).forEach(key => {
            if (!currentFilters[key]) {
                delete currentFilters[key];
            }
        });
        
        loadTransactions(1);
    }

    function clearFilters() {
        $('#filterForm')[0].reset();
        currentFilters = {};
        loadTransactions(1);
    }

    function viewTransaction(transactionId) {
        $.ajax({
            url: `{{ route('admin.transactions.show', '') }}/${transactionId}`,
            method: 'GET',
            success: function(response) {
                $('#transactionModalBody').html(response);
                $('#transactionModal').modal('show');
            },
            error: function(xhr) {
                console.error('Error loading transaction details:', xhr);
                toastr.error('Failed to load transaction details');
            }
        });
    }

    function exportTransactions() {
        const params = new URLSearchParams({
            export: 'csv',
            ...currentFilters
        });
        
        window.location.href = '{{ route("admin.transactions.export") }}?' + params.toString();
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    }
</script>
@endpush