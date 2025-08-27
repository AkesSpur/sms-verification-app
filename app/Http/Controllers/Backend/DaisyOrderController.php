<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DaisyOrder;
use App\Models\User;
use App\Services\DaisySmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DaisyOrderController extends Controller
{
    protected $daisySmsService;

    public function __construct(DaisySmsService $daisySmsService)
    {
        $this->daisySmsService = $daisySmsService;
    }

    /**
     * Display a listing of daisy orders
     */
    public function index(Request $request)
    {
        $query = DaisyOrder::with(['user', 'service', 'transaction'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_code')) {
            $query->where('service_code', $request->service_code);
        }

        if ($request->filled('country_code')) {
            $query->where('country_code', $request->country_code);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('rental_id', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('trx', 'like', "%{$search}%")
                  ->orWhere('sms_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(25);

        // Get statistics
        $stats = DaisyOrder::getStatistics();

        // Get filter options
        $services = DaisyOrder::select('service_code', 'service_name')
            ->distinct()
            ->orderBy('service_name')
            ->get();

        $countries = DaisyOrder::select('country_code', 'country_name')
            ->distinct()
            ->orderBy('country_name')
            ->get();

        $users = User::select('id', 'name', 'email')
            ->whereHas('daisyOrders')
            ->orderBy('name')
            ->get();

        return view('admin.daisy-orders.index', compact(
            'orders', 'stats', 'services', 'countries', 'users'
        ));
    }

    /**
     * Display the specified order
     */
    public function show(DaisyOrder $daisyOrder)
    {
        $daisyOrder->load(['user', 'service', 'transaction']);
        
        // Get SMS status from API if order is active
        $smsStatus = null;
        if ($daisyOrder->isActive()) {
            try {
                $smsStatus = $this->daisySmsService->getCode($daisyOrder->rental_id);
            } catch (\Exception $e) {
                // Log error but don't break the page
                Log::error('Failed to get SMS status for order ' . $daisyOrder->id . ': ' . $e->getMessage());
            }
        }

        return view('admin.daisy-orders.show', compact('daisyOrder', 'smsStatus'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, DaisyOrder $daisyOrder)
    {
        $request->validate([
            'status' => 'required|in:pending,active,completed,cancelled,expired',
            'sms_code' => 'nullable|string',
            'sms_text' => 'nullable|string',
            'reason' => 'nullable|string'
        ]);

        $oldStatus = $daisyOrder->status;
        $newStatus = $request->status;

        DB::beginTransaction();
        try {
            // Update order status
            $daisyOrder->status = $newStatus;
            
            if ($request->filled('sms_code')) {
                $daisyOrder->sms_code = $request->sms_code;
            }
            
            if ($request->filled('sms_text')) {
                $daisyOrder->sms_text = $request->sms_text;
            }

            $daisyOrder->save();

            // Handle status-specific logic
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                // Refund user if order was cancelled
                $this->processRefund($daisyOrder, $request->reason ?? 'Order cancelled by admin');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => [
                    'status' => $daisyOrder->status,
                    'status_badge' => $daisyOrder->status_badge,
                    'sms_code' => $daisyOrder->sms_code,
                    'sms_text' => $daisyOrder->sms_text
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh SMS status from API
     */
    public function refreshSmsStatus(DaisyOrder $daisyOrder)
    {
        if (!$daisyOrder->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Can only refresh SMS status for active orders'
            ], 400);
        }

        try {
            $smsStatus = $this->daisySmsService->getCode($daisyOrder->rental_id);
            
            // Update order if SMS received
            if (isset($smsStatus['sms']) && $smsStatus['sms']) {
                $daisyOrder->sms_code = $smsStatus['sms'];
                $daisyOrder->sms_text = $smsStatus['full_sms'] ?? null;
                $daisyOrder->status = DaisyOrder::STATUS_COMPLETED;
                $daisyOrder->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'SMS status refreshed successfully',
                'data' => [
                    'sms_status' => $smsStatus,
                    'order_status' => $daisyOrder->status,
                    'sms_code' => $daisyOrder->sms_code,
                    'sms_text' => $daisyOrder->sms_text
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh SMS status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel order and process refund
     */
    public function cancelOrder(Request $request, DaisyOrder $daisyOrder)
    {
        // No validation needed for simple cancellation

        if ($daisyOrder->isCompleted() || $daisyOrder->isCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel completed or already cancelled orders'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Cancel order in API if it's active
            if ($daisyOrder->isActive()) {
                try {
                    $this->daisySmsService->cancelRental($daisyOrder->rental_id);
                } catch (\Exception $e) {
                    // Log but don't fail the cancellation
                    Log::warning('Failed to cancel rental in API: ' . $e->getMessage());
                }
            }

            // Update order status
            $daisyOrder->markAsCancelled();

            // Process refund
            $this->processRefund($daisyOrder, $request->reason ?? 'Order cancelled by admin');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled and refund processed successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update orders
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'action' => 'required|in:cancel,mark_completed,mark_expired',
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:daisy_orders,id',
            'reason' => 'nullable|string'
        ]);

        $orderIds = $request->order_ids;
        $action = $request->action;
        $reason = $request->reason ?? 'Bulk action by admin';

        DB::beginTransaction();
        try {
            $orders = DaisyOrder::whereIn('id', $orderIds)->get();
            $processed = 0;

            foreach ($orders as $order) {
                switch ($action) {
                    case 'cancel':
                        if (!$order->isCompleted() && !$order->isCancelled()) {
                            $order->markAsCancelled();
                            $this->processRefund($order, $reason);
                            $processed++;
                        }
                        break;
                    case 'mark_completed':
                        if ($order->isActive()) {
                            $order->markAsCompleted();
                            $processed++;
                        }
                        break;
                    case 'mark_expired':
                        if ($order->isActive()) {
                            $order->markAsExpired();
                            $processed++;
                        }
                        break;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully processed {$processed} orders"
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process bulk update: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $query = DaisyOrder::with(['user', 'service'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // ... (apply other filters)

        $orders = $query->get();

        $filename = 'daisy_orders_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'User', 'Email', 'Rental ID', 'Phone Number', 'Service', 
                'Country', 'Price (NGN)', 'Status', 'SMS Code', 'Created At', 'Expires At'
            ]);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->user->name ?? 'N/A',
                    $order->user->email ?? 'N/A',
                    $order->rental_id,
                    $order->phone_number,
                    $order->service_name,
                    $order->country_name,
                    $order->price,
                    ucfirst($order->status),
                    $order->sms_code ?? 'N/A',
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->expires_at ? $order->expires_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Process refund for cancelled order
     */
    private function processRefund(DaisyOrder $order, $reason)
    {
        if (!$order->transaction) {
            return;
        }

        // Create refund transaction
        $order->user->transactions()->create([
            'type' => 'credit',
            'amount' => $order->price,
            'description' => "Refund for cancelled Daisy SMS order #{$order->id}",
            'reference' => 'REFUND_' . $order->trx,
            'status' => 'completed',
            'metadata' => [
                'original_order_id' => $order->id,
                'reason' => $reason
            ]
        ]);

        // Update user balance
        $order->user->increment('balance', $order->price);
    }
}