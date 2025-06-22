<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function dashboard()
    {
        $countries = Country::all();
        $services = Service::all();
        $balance = Auth::user()->balance;
        return view('user.dashboard', compact(
            'services',
            'balance',
            'countries'
        ));
    }
    public function usaNumbers()
    {
        $services = Service::all();
        $countries = Country::all();

        // Filter orders for USA numbers only (assuming country code 7 is USA)
        $orders = Order::where('user_id', Auth::user()->id)
            ->whereHas('service', function($query) {
                // You might need to adjust this based on how you store country info
                // For now, we'll filter by phone number prefix or add country field to orders
            })
            ->latest()
            ->paginate(10);

        return view('user.usa-numbers', compact(
            'services',
            'orders',
            'countries'
        ));
    }

    public function allCountriesNumbers()
    {
        $services = Service::all();
        $countries = Country::all();

        $orders = Order::where('user_id', Auth::user()->id)
            ->latest()
            ->paginate(10);

        return view('user.all-countries-numbers', compact(
            'services',
            'orders',
            'countries'
        ));
    }

    public function transaction()
    {
        $user = Auth::user();
        
        // Get user's transactions/orders for statistics
        $orders = Order::where('user_id', $user->id)->get();
        
        $totalTransactions = $orders->count();
        $totalSpent = $orders->where('status', 'completed')->sum('amount');
        $totalRefunds = $orders->where('status', 'refunded')->sum('amount');
        $pendingAmount = $orders->where('status', 'pending')->sum('amount');
        
        return view('user.transaction', compact(
            'totalTransactions',
            'totalSpent', 
            'totalRefunds',
            'pendingAmount'
        ));
    }
    
    public function orderHistory()
    {
        $user = Auth::user();
        
        // Get user's orders for SMS tab
        $smsOrders = Order::where('user_id', $user->id)
            ->with('service')
            ->latest()
            ->paginate(10);
        
        // Get actual digital product orders from database
        $digitalProducts = auth()->user()->digitalProductOrders()
            ->with(['product.subcategory.category', 'log'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'name' => $order->product->name,
                    'type' => $order->product->subcategory ? $order->product->subcategory->name : 'Digital Product',
                    'details' => $order->log ? $order->log->details : null,
                    'amount' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                    'order_id' => $order->id,
                    'full_log_item' => ($order->log && $order->log->status == 'sold' && $order->log->sold_to_user_id == Auth::id()) ? $order->log->log_item : 'Access to this log has been denied. If you believe this is a mistake, please contact support.',
                ];
            });
       
        // Get actual gift orders from database
        $giftOrders = auth()->user()->giftOrders()
            ->with(['gift'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'item_name' => $order->gift ? $order->gift->name : 'Gift Item',
                    'item_description' => $order->gift ? $order->gift->description : 'Custom Gift',
                    'recipient' => $order->recipient_name,
                    'amount' => $order->total_amount,
                    'status' => $order->status,
                    'tracking_code' => $order->tracking_number,
                    'notes' => $order->notes,
                    'icon' => collect(['gift', 'gifts', 'hand-holding-heart', 'surprise', 'heart', 'star'])->random(),
                    'created_at' => $order->created_at
                ];
            });
        
        return view('user.order-history', compact(
            'smsOrders',
            'digitalProducts',
            'giftOrders'
        ));
    }
    
    public function getTransactions(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required. Please log in to continue.',
                    'error' => 'Unauthorized',
                    'status' => 401
                ], 401);
            }
            
            $user = Auth::user();
            $orders = Order::where('user_id', $user->id)
                ->with('service')
                ->latest()
                ->paginate(10);
            
            $transactions = $orders->map(function($order) {
                return [
                    'id' => 'TXN' . str_pad($order->id, 3, '0', STR_PAD_LEFT),
                    'type' => $this->getTransactionType($order->status),
                    'service' => $order->service->name ?? 'N/A',
                    'amount' => $order->amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at->toISOString()
                ];
            });
            
            $stats = [
                'total_transactions' => $orders->total(),
                'total_spent' => Order::where('user_id', $user->id)->where('status', 'completed')->sum('amount'),
                'total_refunds' => Order::where('user_id', $user->id)->where('status', 'refunded')->sum('amount'),
                'pending_amount' => Order::where('user_id', $user->id)->where('status', 'pending')->sum('amount')
            ];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'transactions' => $transactions,
                    'stats' => $stats,
                    'pagination' => [
                        'current_page' => $orders->currentPage(),
                        'last_page' => $orders->lastPage(),
                        'per_page' => $orders->perPage(),
                        'total' => $orders->total()
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load transactions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function getTransactionType($status)
    {
        switch($status) {
            case 'completed':
                return 'purchase';
            case 'refunded':
                return 'refund';
            case 'pending':
                return 'purchase';
            default:
                return 'purchase';
        }
    }
}
