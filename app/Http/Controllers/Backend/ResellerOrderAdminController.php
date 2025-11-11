<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ResellerOrder;
use App\Models\ResellerProductLog;
use Illuminate\Http\Request;

class ResellerOrderAdminController extends Controller
{
    /** List all reseller orders with pagination and filters */
    public function index(Request $request)
    {
        $query = ResellerOrder::with(['user', 'product'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('id', 'like', "%{$search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $orders = $query->paginate(50);
        return view('admin.reseller-orders.index', compact('orders'));
    }

    /** Show a single reseller order with logs */
    public function show(ResellerOrder $order)
    {
        $order->load(['user', 'product', 'logs']);
        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }
}