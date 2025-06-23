<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\GiftOrder;
use App\Models\User;
use App\Models\Gift;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiftOrderController extends Controller
{
    use ImageUploadTrait;
    /**
     * Display a listing of gift orders.
     */
    public function index(Request $request)
    {
        $query = GiftOrder::with(['user', 'gift'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('sender_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('gift', function ($giftQuery) use ($search) {
                      $giftQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(20);

        // Get statistics
        $stats = [
            'total_orders' => GiftOrder::count(),
            'pending_orders' => GiftOrder::where('status', 'pending')->count(),
            'confirmed_orders' => GiftOrder::where('status', 'confirmed')->count(),
            'cancelled_orders' => GiftOrder::where('status', 'cancelled')->count(),
            'total_revenue' => GiftOrder::where('payment_status', 'paid')->sum('total_amount'),
            'pending_revenue' => GiftOrder::where('payment_status', 'pending')->sum('total_amount')
        ];

        return view('admin.gift-orders.index', compact('orders', 'stats'));
    }

    /**
     * Display the specified gift order.
     */
    public function show(GiftOrder $giftOrder)
    {
        $order = $giftOrder->load(['user', 'gift']);
        
        return view('admin.gift-orders.show', compact('order'));
    }

    /**
     * Update the status of the specified gift order.
     */
    public function updateStatus(Request $request, GiftOrder $giftOrder)
    {
        $request->validate([
            'status' => 'required|in:confirmed,cancelled',
            'tracking_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $giftOrder->status;
        $newStatus = $request->status;

        DB::beginTransaction();
        
        try {
            // Update based on status
            switch ($newStatus) {
                case 'confirmed':
                    $giftOrder->update([
                        'status' => 'confirmed',
                        'tracking_number' => $request->tracking_number,
                        'notes' => $request->notes,
                        'confirmed_at' => now()
                    ]);
                    break;
                case 'cancelled':
                    $giftOrder->update([
                        'status' => 'cancelled',
                        'notes' => $request->notes,
                        'cancelled_at' => now()
                    ]);
                    // Refund user if payment was made
                    if ($giftOrder->payment_status == 'paid') {
                        $giftOrder->user->addBalance(
                            $giftOrder->total_amount,
                            'gift_refund',
                            "Admin refund for gift order: {$giftOrder->gift->name}",
                            $giftOrder,
                            auth()->user()
                        );
                        $giftOrder->update(['payment_status' => 'refunded']);
                    }
                    break;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Order status updated from '{$oldStatus}' to '{$newStatus}' successfully."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified gift order from storage.
     */
    public function destroy(GiftOrder $giftOrder)
    {
        try {
            // Only allow deletion of cancelled orders
            if ($giftOrder->status !== 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only cancelled orders can be deleted.'
                ], 400);
            }

             // Delete image if exists
        if ($giftOrder->custom_image) {
            $this->deleteImage($giftOrder->custom_image);
        }
            $giftOrder->delete();


            return response()->json([
                'success' => true,
                'message' => 'Gift order deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export gift orders to CSV.
     */
    public function export(Request $request)
    {
        $query = GiftOrder::with(['user', 'gift']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $filename = 'gift_orders_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Order ID',
                'Gift Name',
                'Customer Name',
                'Customer Email',
                'Recipient Name',
                'Recipient Phone',
                'Sender Name',
                'Sender Email',
                'Quantity',
                'Unit Price',
                'Customization Cost',
                'Total Amount',
                'Status',
                'Payment Status',
                'Is Customized',
                'Delivery Address',
                'Delivery City',
                'Delivery State',
                'Delivery Country',
                'Tracking Number',
                'Ordered At',
                'Shipped At',
                'Delivered At'
            ]);

            // CSV data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->gift->name,
                    $order->user->name,
                    $order->user->email,
                    $order->recipient_name,
                    $order->recipient_phone,
                    $order->sender_name,
                    $order->sender_email,
                    $order->quantity,
                    $order->unit_price,
                    $order->customization_cost,
                    $order->total_amount,
                    $order->status,
                    $order->payment_status,
                    $order->is_customized ? 'Yes' : 'No',
                    $order->delivery_address,
                    $order->delivery_city,
                    $order->delivery_state,
                    $order->delivery_country,
                    $order->tracking_number,
                    $order->ordered_at?->format('Y-m-d H:i:s'),
                    $order->shipped_at?->format('Y-m-d H:i:s'),
                    $order->delivered_at?->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}