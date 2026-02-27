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
use App\Models\SocialMediaOrder;
use App\Models\DaisyOrder;
use App\Models\ResellerOrder; // added
use App\Models\ResellerRequest; // added
use App\Services\DaisySmsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    protected $daisySmsService;
    
    public function __construct(DaisySmsService $daisySmsService)
    {
        $this->daisySmsService = $daisySmsService;
    }



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

        // Social Media Boosting Revenue
        $todaysSocialRevenue = SocialMediaOrder::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        $monthSocialRevenue = SocialMediaOrder::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('total_amount');

        $yearSocialRevenue = SocialMediaOrder::where('status', 'completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        // Daisy Orders Revenue
        $todaysDaisyRevenue = DaisyOrder::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->sum('price');

        $monthDaisyRevenue = DaisyOrder::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('price');

        $yearDaisyRevenue = DaisyOrder::where('status', 'completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('price');

        // Reseller Orders Revenue (completed orders)
        $todaysResellerRevenue = ResellerOrder::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        $monthResellerRevenue = ResellerOrder::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('total_amount');

        $yearResellerRevenue = ResellerOrder::where('status', 'completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        // Combined Revenue Totals (updated to include reseller revenue)
        $todaysRevenue = $todaysSmsRevenue + $todaysDigitalRevenue + $todaysGiftRevenue + $todaysSocialRevenue + $todaysDaisyRevenue + $todaysResellerRevenue;
        $monthRevenue = $monthSmsRevenue + $monthDigitalRevenue + $monthGiftRevenue + $monthSocialRevenue + $monthDaisyRevenue + $monthResellerRevenue;
        $yearRevenue = $yearSmsRevenue + $yearDigitalRevenue + $yearGiftRevenue + $yearSocialRevenue + $yearDaisyRevenue + $yearResellerRevenue;

        $totalServices = Service::where('status', 'active')->count();
        $totalUsers = User::where('role', 'client')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalResellers = User::where('is_reseller', true)->orWhere('role', 'reseller')->count(); // added
        
        $pendingReviews = ReviewQueue::count();
        $pendingResellerRequests = ResellerRequest::pending()->count(); // added
        
        // Recent orders for SMS verification
        $recentOrders = Order::with(['user', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $apiBalances = Cache::remember('admin_api_balances', 600, function () {
            // Fetch Daisy Balance
            $daisyBalance = 'N/A';
            try {
                $daisyResponse = $this->daisySmsService->getBalance();
                if ($daisyResponse['success']) {
                    $daisyBalance = '$' . number_format($daisyResponse['balance'], 2);
                }
            } catch (\Exception $e) {
                // Log error silently
            }

            return [
                'daisy' => $daisyBalance,
            ];
        });


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
            
            // Social Media Boosting Orders
            'social_total_orders' => SocialMediaOrder::count(),
            'social_completed_orders' => SocialMediaOrder::where('status', 'completed')->count(),
            'social_pending_orders' => SocialMediaOrder::where('status', 'pending')->count(),
            'social_processing_orders' => SocialMediaOrder::where('status', 'processing')->count(),
            'social_cancelled_orders' => SocialMediaOrder::where('status', 'cancelled')->count(),
            'social_total_revenue' => SocialMediaOrder::where('status', 'completed')->sum('total_amount'),
            'social_today_revenue' => $todaysSocialRevenue,
            'social_month_revenue' => $monthSocialRevenue,
            'social_year_revenue' => $yearSocialRevenue,
            'social_today_orders' => SocialMediaOrder::whereDate('created_at', Carbon::today())->count(),
            
            // Daisy Orders
            'daisy_total_orders' => DaisyOrder::count(),
            'daisy_completed_orders' => DaisyOrder::where('status', 'completed')->count(),
            'daisy_pending_orders' => DaisyOrder::where('status', 'pending')->count(),
            'daisy_processing_orders' => DaisyOrder::where('status', 'processing')->count(),
            'daisy_cancelled_orders' => DaisyOrder::where('status', 'cancelled')->count(),
            'daisy_failed_orders' => DaisyOrder::where('status', 'failed')->count(),
            'daisy_total_revenue' => DaisyOrder::where('status', 'completed')->sum('price'),
            'daisy_today_revenue' => $todaysDaisyRevenue,
            'daisy_month_revenue' => $monthDaisyRevenue,
            'daisy_year_revenue' => $yearDaisyRevenue,
            'daisy_today_orders' => DaisyOrder::whereDate('created_at', Carbon::today())->count(),

            // Reseller Orders (new)
            'reseller_total_orders' => ResellerOrder::count(),
            'reseller_completed_orders' => ResellerOrder::where('status', 'completed')->count(),
            'reseller_pending_orders' => ResellerOrder::where('status', 'pending')->count(),
            'reseller_failed_orders' => ResellerOrder::where('status', 'failed')->count(),
            'reseller_total_revenue' => ResellerOrder::where('status', 'completed')->sum('total_amount'),
            'reseller_today_revenue' => $todaysResellerRevenue,
            'reseller_month_revenue' => $monthResellerRevenue,
            'reseller_year_revenue' => $yearResellerRevenue,

            // Users & Resellers
            'total_resellers' => $totalResellers,
            'pending_reseller_requests' => $pendingResellerRequests,
            
            // Combined Revenue Totals
            'total_revenue_today' => $todaysRevenue,
            'total_revenue_month' => $monthRevenue,
            'total_revenue_year' => $yearRevenue,
            'total_revenue_all_time' => Order::where('status', 'completed')->sum('final_price') + 
                                      DigitalProductOrder::where('status', 'completed')->sum('total_amount') + 
                                      GiftOrder::where('payment_status', 'paid')->sum('total_amount') + 
                                      SocialMediaOrder::where('status', 'completed')->sum('total_amount') + 
                                      DaisyOrder::where('status', 'completed')->sum('price') +
                                      ResellerOrder::where('status', 'completed')->sum('total_amount'),
            
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

        // Recent Social Media Purchases (Last 24 hours)
        $recentSocialPurchases = SocialMediaOrder::with(['user', 'product'])
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent Daisy Orders (Last 24 hours)
        $recentDaisyOrders = DaisyOrder::with(['user', 'service'])
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();


        // Pending Orders Data
        $pendingGiftOrders = GiftOrder::with(['user', 'gift'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $pendingSocialOrders = SocialMediaOrder::with(['user', 'product'])
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
         
        // get the count of processing orders 
        $processingSocialOrders = SocialMediaOrder::whereIn('status',['processing'])
            ->count();

        // Pending Daisy Orders
        $pendingDaisyOrders = DaisyOrder::with(['user', 'service'])
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Processing Daisy Orders count
        $processingDaisyOrders = DaisyOrder::whereIn('status', ['processing'])
            ->count();

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
            'todaysSocialRevenue',
            'monthSocialRevenue',
            'yearSocialRevenue',
            'todaysResellerRevenue',
            'monthResellerRevenue',
            'yearResellerRevenue',
            'todaysDaisyRevenue',
            'monthDaisyRevenue',
            'yearDaisyRevenue',
            'totalServices',
            'totalUsers',
            'totalAdmins',
            'pendingReviews',
            'recentOrders',
            'popularServices',
            'stats',
            'recentDigitalPurchases',
            'popularDigitalProducts',
            'recentSocialPurchases',
            'pendingGiftOrders',
            'pendingSocialOrders',
            'processingSocialOrders',
            'recentDaisyOrders',
            'pendingDaisyOrders',
            'processingDaisyOrders',
            'apiBalances'
        ));
    }

    // admin login
    public function login(){

        return view('admin.auth.login');
    } 

}
