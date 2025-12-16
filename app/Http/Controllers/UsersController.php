<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Localbank;
use App\Models\Order;
use App\Models\Paystack;
use App\Models\Service;
use App\Models\User;
use App\Services\SmsPoolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $balance = $user->balance;
        
        // Get real statistics for the authenticated user
        $activeNumbers = Order::where('user_id', $user->id)
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_ACTIVE])
            ->count();
            
        $totalNumbers = Order::where('user_id', $user->id)->count();
        
        $completedOrders = Order::where('user_id', $user->id)
            ->where('status', Order::STATUS_COMPLETED)
            ->count();
        
        return view('user.dashboard', compact(
            'balance',
            'activeNumbers',
            'totalNumbers',
            'completedOrders'
        ));
    }
    public function usaNumbers()
    {
        // Redirect to the specialized USA number controller
        return app(UsaNumberController::class)->index();
    }

    public function allCountriesNumbers()
    {
        $usaId = Country::where('code', 'US')->value('id');
        $services = Service::all();
        $countries = $usaId
            ? Country::where('id', '!=', $usaId)->get()
            : Country::where('code', '!=', 'US')->get();

        // Get active international orders (excluding USA orders)
        $activeOrders = Order::where('user_id', Auth::user()->id)
            ->whereNotIn('status', ['completed', 'cancelled', 'expired'])
            ->where('country_id', '!=', $usaId)
            ->with('service')
            ->latest()
            ->get();

        // Get all orders for history (paginated)
        $orders = Order::where('user_id', Auth::user()->id)
            ->where('country_id', '!=', $usaId)
            ->latest()
            ->paginate(10);

        return view('user.all-countries-numbers', compact(
            'services',
            'orders',
            'countries',
            'activeOrders'
        ));
    }

    public function transaction()
    {
        $user = Auth::user();
        
        // Get user's transactions for statistics
        $transactions = $user->transactions;
        
        $totalTransactions = $transactions->count();
        $totalSpent = $transactions->where('type', 'debit')->sum('amount');
        $totalRefunds = $transactions->where('type', 'credit')->whereIn('category', ['gift_refund', 'digital_refund', 'sms_refund'])->sum('amount');
        $pendingAmount = $transactions->where('status', 'pending')->sum('amount');
        
        // Get local bank settings
        $localbankSetting = Localbank::getActive();

        $paystack = Paystack::getActiveConfig();

        
        return view('user.transaction', compact(
            'totalTransactions',
            'totalSpent', 
            'totalRefunds',
            'pendingAmount',
            'paystack',
            'localbankSetting'
        ));
    }
    
    public function orderHistory()
    {
        $user = Auth::user();
        
        // Get user's orders for SMS tab with all necessary relationships
        $smsOrders = Order::where('user_id', $user->id)
            ->with(['service', 'country'])
            ->latest()
            ->paginate(100);
        
        // Get actual digital product orders from database with pagination
        $digitalProducts = auth()->user()->digitalProductOrders()
            ->with(['product.subcategory.category', 'log'])
            ->orderBy('created_at', 'desc')
            ->paginate(100)
            ->through(function ($order) {
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
            ->take(100)
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
            $transactions = $user->transactions()
                ->with(['admin'])
                ->latest()
                ->paginate(10);
            
            $transactionData = $transactions->map(function($transaction) {
                return [
                    'id' => $transaction->transaction_id,
                    'type' => $transaction->category_display,
                    'service' => $this->getServiceFromCategory($transaction->category),
                    'amount' => $transaction->amount,
                    'status' => $transaction->status,
                    'description' => $transaction->description,
                    'transaction_type' => $transaction->type, // credit or debit
                    'created_at' => $transaction->created_at->toISOString()
                ];
            });
            
            $stats = [
                'total_transactions' => $user->transactions()->count(),
                'total_spent' => $user->transactions()->where('type', 'debit')->sum('amount'),
                'total_refunds' => $user->transactions()->where('type', 'credit')->whereIn('category', ['gift_refund', 'digital_refund', 'sms_refund'])->sum('amount'),
                'pending_amount' => $user->transactions()->where('status', 'pending')->sum('amount')
            ];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'transactions' => $transactionData,
                    'stats' => $stats,
                    'pagination' => [
                        'current_page' => $transactions->currentPage(),
                        'last_page' => $transactions->lastPage(),
                        'per_page' => $transactions->perPage(),
                        'total' => $transactions->total()
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
    
    private function getServiceFromCategory($category)
    {
        return match($category) {
            'gift_purchase', 'gift_refund' => 'Gift Service',
            'digital_purchase', 'digital_refund' => 'Digital Products',
            'sms_purchase', 'sms_refund' => 'SMS Service',
            'social_media_purchase', 'social_media_refund' => 'Boosting Service',
            'fund_addition', 'fund_withdrawal' => 'Wallet Management',
            default => 'General'
        };
    }
    
    public function setDepositAmount(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:100|max:1000000'
            ]);
            
            // Store deposit amount in session
            session(['deposit_amount' => $request->amount]);
            
            return response()->json([
                'success' => true,
                'message' => 'Deposit amount set successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set deposit amount: ' . $e->getMessage()
            ], 400);
        }
    }
    

}
