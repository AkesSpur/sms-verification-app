<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DigitalProductOrder;
use App\Models\Order;
use App\Models\User;
use App\Models\Service;
use App\Models\ReviewQueue;
use App\Models\GiftOrder;
use App\Models\Transaction;
use App\Models\DigitalProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(){
        // SMS Verification Orders Statistics
        $todaysOrders = Order::whereDate('created_at', Carbon::today())->count();
        $todaysPendingOrders = Order::whereDate('created_at', Carbon::today())
            ->where('status', 'pending')->count();
        $totalOrders = Order::count();
        $totalPendingOrders = Order::where('status', 'pending')->count();
        $totalCompletedOrders = Order::where('status', 'completed')->count();
        $totalFailedOrders = Order::where('status', 'failed')->count();

        // SMS Orders Revenue (using final_price - actual amount paid by users)
        $todaysSmsRevenue = Order::where('status','completed')
            ->whereDate('created_at', Carbon::today())
            ->sum('final_price');

        $monthSmsRevenue = Order::where('status','completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('final_price');

        $yearSmsRevenue = Order::where('status','completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('final_price');

        // Digital Products Revenue
        $todaysDigitalRevenue = DigitalProductOrder::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        $monthDigitalRevenue = DigitalProductOrder::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('total_amount');

        $yearDigitalRevenue = DigitalProductOrder::where('status', 'completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        // Gift Orders Revenue
        $todaysGiftRevenue = GiftOrder::where('payment_status', 'paid')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        $monthGiftRevenue = GiftOrder::where('payment_status', 'paid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('total_amount');

        $yearGiftRevenue = GiftOrder::where('payment_status', 'paid')
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        // Combined Revenue Totals
        $todaysRevenue = $todaysSmsRevenue + $todaysDigitalRevenue + $todaysGiftRevenue;
        $monthRevenue = $monthSmsRevenue + $monthDigitalRevenue + $monthGiftRevenue;
        $yearRevenue = $yearSmsRevenue + $yearDigitalRevenue + $yearGiftRevenue;

        $totalServices = Service::where('status', 'active')->count();
        $totalUsers = User::where('role', 'client')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        
        $pendingReviews = ReviewQueue::count();
        
        // Recent orders for SMS verification
        $recentOrders = Order::with(['user', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Most used services in last 24 hours
        $twentyFourHoursAgo = Carbon::now()->subDay();
        $popularServices = DB::table('orders')
            ->join('services', 'orders.service_id', '=', 'services.id')
            ->select('services.name as service_name', 'services.code', DB::raw('COUNT(*) as usage_count'))
            ->where('orders.created_at', '>=', $twentyFourHoursAgo)
            ->groupBy('services.id', 'services.name', 'services.code')
            ->orderByDesc('usage_count')
            ->limit(5)
            ->get();

        // Comprehensive Statistics
        $stats = [
            // SMS Orders Revenue Breakdown
            'sms_today_revenue' => $todaysSmsRevenue,
            'sms_month_revenue' => $monthSmsRevenue,
            'sms_year_revenue' => $yearSmsRevenue,
            'sms_total_revenue' => Order::where('status', 'completed')->sum('final_price'),
            
            // Digital Product Orders
            'digital_total_orders' => DigitalProductOrder::count(),
            'digital_completed_orders' => DigitalProductOrder::where('status', 'completed')->count(),
            'digital_pending_orders' => DigitalProductOrder::where('status', 'pending')->count(),
            'digital_failed_orders' => DigitalProductOrder::where('status', 'failed')->count(),
            'digital_total_revenue' => DigitalProductOrder::where('status', 'completed')->sum('total_amount'),
            'digital_today_revenue' => $todaysDigitalRevenue,
            'digital_month_revenue' => $monthDigitalRevenue,
            'digital_year_revenue' => $yearDigitalRevenue,
            'digital_today_orders' => DigitalProductOrder::whereDate('created_at', Carbon::today())->count(),
            
            // Gift Orders
            'gift_total_orders' => GiftOrder::count(),
            'gift_pending_orders' => GiftOrder::where('status', 'pending')->count(),
            'gift_confirmed_orders' => GiftOrder::where('status', 'confirmed')->count(),
            'gift_cancelled_orders' => GiftOrder::where('status', 'cancelled')->count(),
            'gift_total_revenue' => GiftOrder::where('payment_status', 'paid')->sum('total_amount'),
            'gift_today_revenue' => $todaysGiftRevenue,
            'gift_month_revenue' => $monthGiftRevenue,
            'gift_year_revenue' => $yearGiftRevenue,
            'gift_pending_revenue' => GiftOrder::where('payment_status', 'pending')->sum('total_amount'),
            'gift_today_orders' => GiftOrder::whereDate('created_at', Carbon::today())->count(),
            
            // Combined Revenue Totals
            'total_revenue_today' => $todaysRevenue,
            'total_revenue_month' => $monthRevenue,
            'total_revenue_year' => $yearRevenue,
            'total_revenue_all_time' => Order::where('status', 'completed')->sum('final_price') + 
                                      DigitalProductOrder::where('status', 'completed')->sum('total_amount') + 
                                      GiftOrder::where('payment_status', 'paid')->sum('total_amount'),
            
            // Transactions
            'total_transactions' => Transaction::count(),
            'total_credits' => Transaction::where('type', 'credit')->sum('amount'),
            'total_debits' => Transaction::where('type', 'debit')->sum('amount'),
            'today_transactions' => Transaction::whereDate('created_at', Carbon::today())->count(),
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
            'completed_transactions' => Transaction::where('status', 'completed')->count(),
            'failed_transactions' => Transaction::where('status', 'failed')->count(),
        ];

        // Recent Digital Product Purchases (Last 24 hours)
        $recentDigitalPurchases = DigitalProductOrder::with(['user', 'product'])
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Most purchased digital products in last 24 hours
        $popularDigitalProducts = DB::table('digital_product_orders')
            ->join('digital_products', 'digital_product_orders.product_id', '=', 'digital_products.id')
            ->select('digital_products.name as product_name', DB::raw('SUM(digital_product_orders.quantity) as total_quantity'), DB::raw('COUNT(*) as order_count'))
            ->where('digital_product_orders.created_at', '>=', $twentyFourHoursAgo)
            ->groupBy('digital_products.id', 'digital_products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return view('admin.dashboard',compact(
            'todaysOrders',
            'todaysPendingOrders',
            'totalOrders',
            'totalPendingOrders',
            'totalCompletedOrders',
            'totalFailedOrders',
            'todaysRevenue',
            'monthRevenue',
            'yearRevenue',
            'todaysSmsRevenue',
            'monthSmsRevenue',
            'yearSmsRevenue',
            'todaysDigitalRevenue',
            'monthDigitalRevenue',
            'yearDigitalRevenue',
            'todaysGiftRevenue',
            'monthGiftRevenue',
            'yearGiftRevenue',
            'totalServices',
            'totalUsers',
            'totalAdmins',
            'pendingReviews',
            'recentOrders',
            'popularServices',
            'stats',
            'recentDigitalPurchases',
            'popularDigitalProducts'
        ));
    }

    // admin login
    public function login(){

        return view('admin.auth.login');
    } 

}
