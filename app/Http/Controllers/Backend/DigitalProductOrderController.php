<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DigitalProductOrder;
use App\Models\User;
use App\Models\DigitalProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DigitalProductOrderController extends Controller
{
    /**
     * Display a listing of digital product orders.
     */
    public function index(Request $request)
    {
        $query = DigitalProductOrder::with(['user', 'product', 'log'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('purchased_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('purchased_at', '<=', $request->date_to);
        }

        // Search by order number or user email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('email', 'like', "%{$search}%")
                               ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(20);        

        return view('admin.digital-product-orders.index', compact('orders'));
    }

    /**
     * Display the specified digital product order.
     */
    public function show(string $id)
    {
        $order = DigitalProductOrder::with(['user', 'product', 'log'])
            ->findOrFail($id);

        return view('admin.digital-product-orders.show', compact('order'));
    }

    /**
     * Update the order status.
     */
    public function updateStatus(Request $request, DigitalProductOrder $digitalProductOrder)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,failed',
            'payment_status' => 'sometimes|in:pending,paid,failed',
            'log_content' => 'nullable|string'
        ]);

        $digitalProductOrder->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status ?? $digitalProductOrder->payment_status
        ]);

        // Update log content if provided
        if ($request->has('log_content') && $digitalProductOrder->log) {
            $digitalProductOrder->log->update([
                'log_item' => $request->log_content
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);
    }



    /**
     * Delete the specified order.
     */
    public function destroy(string $id)
    {
        $order = DigitalProductOrder::findOrFail($id);
        $order->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Order deleted successfully!'
        ]);
    }

    /**
     * Export orders to CSV.
     */
    public function export(Request $request)
    {
        $query = DigitalProductOrder::with(['user', 'product'])
            ->latest();

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('purchased_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('purchased_at', '<=', $request->date_to);
        }

        $orders = $query->get();

        $filename = 'digital_product_orders_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Order ID',
                'Customer Name',
                'Customer Email',
                'Product Name',
                'Quantity',
                'Unit Price',
                'Total Amount',
                'Status',
                'Payment Status',
                'Payment Method',
                'Purchase Date',
                'Notes'
            ]);

            // CSV data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->user->name ?? 'N/A',
                    $order->user->email ?? 'N/A',
                    $order->product->name ?? 'N/A',
                    $order->quantity,
                    $order->unit_price,
                    $order->total_amount,
                    ucfirst($order->status),
                    ucfirst($order->payment_status),
                    ucfirst($order->payment_method),
                    $order->purchased_at->format('Y-m-d H:i:s'),
                    $order->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}