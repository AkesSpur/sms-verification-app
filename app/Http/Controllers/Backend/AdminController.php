<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Service;
use App\Models\ReviewQueue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(){

        $todaysOrders = Order::whereDate('created_at', Carbon::today())->count();
        $todaysPendingOrders = Order::whereDate('created_at', Carbon::today())
            ->where('status', 'pending')->count();
        $totalOrders = Order::count();
        $totalPendingOrders = Order::where('status', 'pending')->count();
        $totalCompletedOrders = Order::where('status', 'completed')->count();
        $totalFailedOrders = Order::where('status', 'failed')->count();

        $todaysRevenue = Order::where('orders.status','completed')
            ->whereDate('orders.created_at', Carbon::today())
            ->join('services', 'orders.service_id', '=', 'services.id')
            ->sum('services.price');

        $monthRevenue = Order::where('orders.status','completed')
            ->whereMonth('orders.created_at', Carbon::now()->month)
            ->join('services', 'orders.service_id', '=', 'services.id')
            ->sum('services.price');

        $yearRevenue = Order::where('orders.status','completed')
            ->whereYear('orders.created_at', Carbon::now()->year)
            ->join('services', 'orders.service_id', '=', 'services.id')
            ->sum('services.price');

        $totalServices = Service::where('status', 'active')->count();
        $totalUsers = User::where('role', 'client')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        
        // $pendingReviews = ReviewQueue::where('status', 'pending')->count();
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
            'totalServices',
            'totalUsers',
            'totalAdmins',
            'pendingReviews',
            'recentOrders',
            'popularServices'
        ));
    }

    // admin login
    public function login(){

        return view('admin.auth.login');
    } 

}
