<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaCategory;
use App\Models\SocialMediaOrder;
use App\Models\SocialMediaProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Facades\Activity;

class SocialMediaOrderController extends Controller
{
    /**
     * Display a listing of social media orders.
     */
    public function index(Request $request)
    {
        $query = SocialMediaOrder::with(['user', 'product.category']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order number or social media link
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('social_media_link', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $users = User::select('id', 'name', 'email')->get();
        $products = SocialMediaProduct::with('category')->get();
        $categories = SocialMediaCategory::get();

        // Get statistics
        $stats = [
            'total' => SocialMediaOrder::count(),
            'pending' => SocialMediaOrder::where('status', 'pending')->count(),
            'processing' => SocialMediaOrder::where('status', 'processing')->count(),
            'completed' => SocialMediaOrder::where('status', 'completed')->count(),
            'cancelled' => SocialMediaOrder::where('status', 'cancelled')->count(),
            'total_revenue' => SocialMediaOrder::where('status', 'completed')->sum('total_amount')
        ];

        return view('admin.social-media-orders.index', compact(
            'orders', 
            'users',
            'products',
            'categories',
            'stats'));
    }

    /**
     * Display the specified order.
     */
    public function show(SocialMediaOrder $socialMediaOrder)
    {
        $socialMediaOrder->load(['user', 'product.category']);
        $order = $socialMediaOrder; // For view compatibility
        return view('admin.social-media-orders.show', compact('order'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, SocialMediaOrder $socialMediaOrder)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $socialMediaOrder->status;
        $newStatus = $request->status;

        // Update the order
        $socialMediaOrder->update([
            'status' => $newStatus,
            'admin_notes' => $request->admin_notes,
            'payment_status' => $newStatus === 'completed' ? 'paid' : ($newStatus === 'cancelled' ? 'failed' : $socialMediaOrder->payment_status)
        ]);

        // Status change logged successfully

        toastr("Order status updated to {$newStatus} successfully!", 'success');
        return redirect()->back();
    }

    /**
     * Bulk update order status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:social_media_orders,id',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $updatedCount = SocialMediaOrder::whereIn('id', $request->order_ids)
            ->update([
                'status' => $request->status,
                'admin_notes' => $request->admin_notes,
                'payment_status' => $request->status === 'completed' ? 'paid' : ($request->status === 'cancelled' ? 'failed' : DB::raw('payment_status'))
            ]);

        toastr("{$updatedCount} orders updated successfully!", 'success');
        return redirect()->back();
    }

    /**
     * Export orders to CSV.
     */
    public function export(Request $request)
    {
        $query = SocialMediaOrder::with(['user', 'product.category']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $filename = 'social_media_orders_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Order Number',
                'User Name',
                'User Email',
                'Category',
                'Product',
                'Social Media Link',
                'Quantity',
                'Unit Price',
                'Total Amount',
                'Status',
                'Payment Status',
                'Order Date',
                'Admin Notes'
            ]);

            // CSV data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->user->name,
                    $order->user->email,
                    $order->product->category->name,
                    $order->product->name,
                    $order->social_media_link,
                    $order->quantity,
                    $order->unit_price,
                    $order->total_amount,
                    $order->status,
                    $order->payment_status,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->admin_notes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}