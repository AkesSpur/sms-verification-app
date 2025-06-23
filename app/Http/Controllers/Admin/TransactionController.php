<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display the transactions page
     */
    public function index()
    {
        $stats = $this->getTransactionStats();
        
        return view('admin.transactions', compact('stats'));
    }

    /**
     * Get transactions data for AJAX requests
     */
    public function getData(Request $request)
    {
        $query = Transaction::with(['user', 'admin'])
            ->select('transactions.*')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->addSelect('users.name as user_name', 'users.email as user_email');

        // Apply filters
        if ($request->filled('user')) {
            $query->where(function($q) use ($request) {
                $q->where('users.name', 'like', '%' . $request->user . '%')
                  ->orWhere('users.email', 'like', '%' . $request->user . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('transactions.type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('transactions.category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('transactions.status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transactions.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transactions.created_at', '<=', $request->date_to);
        }

        // Order by latest first
        $query->orderBy('transactions.created_at', 'desc');

        // Paginate
        $perPage = $request->get('per_page', 15);
        $transactions = $query->paginate($perPage);

        // Transform data
        $transactions->getCollection()->transform(function ($transaction) {
            return [
                'id' => $transaction->id,
                'transaction_id' => $transaction->transaction_id,
                'user_name' => $transaction->user_name,
                'user_email' => $transaction->user_email,
                'type' => $transaction->type,
                'category' => $transaction->category,
                'category_display' => $this->getCategoryDisplay($transaction->category),
                'amount' => $transaction->amount,
                'balance_before' => $transaction->balance_before,
                'balance_after' => $transaction->balance_after,
                'description' => $transaction->description,
                'status' => $transaction->status,
                'created_at' => $transaction->created_at->toISOString(),
                'admin_name' => $transaction->admin ? $transaction->admin->name : null,
            ];
        });

        return response()->json([
            'data' => $transactions->items(),
            'current_page' => $transactions->currentPage(),
            'last_page' => $transactions->lastPage(),
            'per_page' => $transactions->perPage(),
            'total' => $transactions->total(),
            'stats' => $this->getTransactionStats($request)
        ]);
    }

    /**
     * Show transaction details
     */
    public function show($id)
    {
        $transaction = Transaction::with(['user', 'admin', 'reference'])->findOrFail($id);
        
        $html = view('admin.partials.transaction-details', compact('transaction'))->render();
        
        return response($html);
    }

    /**
     * Export transactions to CSV
     */
    public function export(Request $request)
    {
        $query = Transaction::with(['user', 'admin'])
            ->select('transactions.*')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->addSelect('users.name as user_name', 'users.email as user_email');

        // Apply same filters as getData method
        if ($request->filled('user')) {
            $query->where(function($q) use ($request) {
                $q->where('users.name', 'like', '%' . $request->user . '%')
                  ->orWhere('users.email', 'like', '%' . $request->user . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('transactions.type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('transactions.category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('transactions.status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transactions.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transactions.created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('transactions.created_at', 'desc')->get();

        $filename = 'transactions_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Transaction ID',
                'User Name',
                'User Email',
                'Type',
                'Category',
                'Amount',
                'Balance Before',
                'Balance After',
                'Description',
                'Status',
                'Admin',
                'Date'
            ]);

            // CSV data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_id,
                    $transaction->user_name,
                    $transaction->user_email,
                    $transaction->type,
                    $this->getCategoryDisplay($transaction->category),
                    $transaction->amount,
                    $transaction->balance_before,
                    $transaction->balance_after,
                    $transaction->description,
                    $transaction->status,
                    $transaction->admin ? $transaction->admin->name : '',
                    $transaction->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get transaction statistics
     */
    private function getTransactionStats($request = null)
    {
        // Helper function to apply filters to a query
        $applyFilters = function($query) use ($request) {
            if ($request) {
                if ($request->filled('user')) {
                    $query->whereHas('user', function($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->user . '%')
                          ->orWhere('email', 'like', '%' . $request->user . '%');
                    });
                }

                if ($request->filled('type')) {
                    $query->where('type', $request->type);
                }

                if ($request->filled('category')) {
                    $query->where('category', $request->category);
                }

                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }

                if ($request->filled('date_from')) {
                    $query->whereDate('created_at', '>=', $request->date_from);
                }

                if ($request->filled('date_to')) {
                    $query->whereDate('created_at', '<=', $request->date_to);
                }
            }
            return $query;
        };

        // Create separate query instances for each statistic
        $totalQuery = $applyFilters(Transaction::query());
        $creditQuery = $applyFilters(Transaction::query())->where('type', 'credit');
        $debitQuery = $applyFilters(Transaction::query())->where('type', 'debit');

        $stats = [
            'total_transactions' => $totalQuery->count(),
            'total_credits' => $creditQuery->sum('amount'),
            'total_debits' => $debitQuery->sum('amount'),
            'today_transactions' => Transaction::whereDate('created_at', Carbon::today())->count(),
        ];

        return $stats;
    }

    /**
     * Get display name for category
     */
    private function getCategoryDisplay($category)
    {
        $categories = [
            'fund_addition' => 'Fund Addition',
            'fund_withdrawal' => 'Fund Withdrawal',
            'gift_purchase' => 'Gift Purchase',
            'gift_refund' => 'Gift Refund',
            'digital_product_purchase' => 'Digital Product Purchase',
        ];

        return $categories[$category] ?? ucwords(str_replace('_', ' ', $category));
    }
}