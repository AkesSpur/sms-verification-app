<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Service;
use App\Models\Country;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Services\SmsActivateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class SmsOrderController extends Controller
{
    protected $smsActivateService;

    public function __construct(SmsActivateService $smsActivateService)
    {
        $this->smsActivateService = $smsActivateService;
    }

    /**
     * Display a listing of SMS orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'service', 'country', 'statusHistory'])
                     ->latest();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('activation_id', 'like', "%{$search}%")
                  ->orWhere('sms_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('service', function($serviceQuery) use ($search) {
                      $serviceQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Country filter
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        // Service filter
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Needs review filter
        if ($request->filled('needs_review')) {
            $query->where('needs_review', true);
        }

        $orders = $query->paginate(20);

        // Get filter options
        $countries = Country::orderBy('name')->get();
        $services = Service::orderBy('name')->get();
        $statuses = [
            Order::STATUS_PENDING => 'Pending',
            Order::STATUS_ACTIVE => 'Active', 
            Order::STATUS_COMPLETED => 'Completed',
            Order::STATUS_EXPIRED => 'Expired',
            Order::STATUS_CANCELLED => 'Cancelled',
            Order::STATUS_FAILED => 'Failed',
            Order::STATUS_REFUNDED => 'Refunded'
        ];

        return view('admin.sms-orders.index', compact('orders', 'countries', 'services', 'statuses'));
    }

    /**
     * Display the specified SMS order
     */
    public function show(Order $order)
    {
        $order->load(['user', 'service', 'country']);
        
        return view('admin.sms-orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', [
                Order::STATUS_PENDING,
                Order::STATUS_ACTIVE,
                Order::STATUS_COMPLETED,
                Order::STATUS_EXPIRED,
                Order::STATUS_CANCELLED,
                Order::STATUS_FAILED,
                Order::STATUS_REFUNDED
            ]),
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            DB::transaction(function () use ($request, $order) {
                $oldStatus = $order->status;
                $newStatus = $request->status;
                $reason = $request->reason;

                // Handle specific status changes
                switch ($newStatus) {
                    case Order::STATUS_CANCELLED:
                        if ($order->canBeCancelled() || $order->shouldBeAutoCancelled()) {
                            $order->cancel($reason, 'admin', auth()->id());
                        } else {
                            throw new \Exception('Order cannot be cancelled in current state');
                        }
                        break;

                    case Order::STATUS_REFUNDED:
                        $order->markAsRefunded($reason);
                        break;

                    case Order::STATUS_FAILED:
                        $order->markAsFailed($reason);
                        break;

                    case Order::STATUS_COMPLETED:
                        if ($request->filled('sms_code')) {
                            $order->markSmsReceived($request->sms_code);
                        } else {
                            $order->update(['status' => $newStatus]);
                            $order->logStatusChange($newStatus, $reason, 'admin', auth()->id());
                        }
                        break;

                    default:
                        $order->update(['status' => $newStatus]);
                        $order->logStatusChange($newStatus, $reason, 'admin', auth()->id());
                        break;
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Order status updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update order status', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update order status: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Manually set SMS code for an order
     */
    public function setSmsCode(Request $request, Order $order)
    {
        $request->validate([
            'sms_code' => 'required|string|max:20'
        ]);

        try {
            $order->markSmsReceived($request->sms_code);

            return response()->json([
                'status' => 'success',
                'message' => 'SMS code set successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to set SMS code: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Retry SMS for an order
     */
    public function retrySms(Order $order)
    {
        try {
            if (!$order->canRetry()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order has reached maximum retry attempts'
                ], 400);
            }

            // Call SMS service to retry
            $result = $this->smsActivateService->getStatus($order->activation_id);
            
            if ($result['status'] === 'OK' && isset($result['code'])) {
                $order->markSmsReceived($result['code']);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'SMS received successfully',
                    'sms_code' => $result['code']
                ]);
            } else {
                $order->incrementRetryAttempts();
                
                return response()->json([
                    'status' => 'info',
                    'message' => 'SMS not yet received. Retry attempt recorded.'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to retry SMS', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retry SMS: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Force cancel an order
     */
    public function forceCancel(Request $request, Order $order)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            DB::transaction(function () use ($request, $order) {
                // Force cancel regardless of normal cancellation rules
                $order->update([
                    'status' => Order::STATUS_CANCELLED,
                    'cancelled_at' => Carbon::now(),
                    'cancellation_reason' => $request->reason,
                    'can_cancel' => false
                ]);

                // Process refund if applicable
                if ($order->shouldRefund()) {
                    $order->processRefund($request->reason, 'admin');
                }

                $order->logStatusChange(Order::STATUS_CANCELLED, $request->reason, 'admin', auth()->id());
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Order force cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to force cancel order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark order for review
     */
    public function markForReview(Request $request, Order $order)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $order->update(['needs_review' => true]);
            
            // Create review queue entry if not exists
            if (!$order->reviewQueue) {
                $order->reviewQueue()->create([
                    'reason' => $request->reason,
                    'created_by' => auth()->id()
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Order marked for review successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark order for review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove from review queue
     */
    public function removeFromReview(Order $order)
    {
        try {
            $order->update(['needs_review' => false]);
            
            if ($order->reviewQueue) {
                $order->reviewQueue->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Order removed from review queue successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to remove order from review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $query = Order::with(['user', 'service', 'country'])
                     ->latest();

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->get();

        $filename = 'sms_orders_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Order ID',
                'User Name',
                'User Email',
                'Service',
                'Country',
                'Phone Number',
                'SMS Code',
                'Status',
                'Price',
                'API Price',
                'Final Price',
                'Refunded',
                'Needs Review',
                'Created At',
                'SMS Received At',
                'Expires At'
            ]);

            // CSV data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->user->name ?? 'N/A',
                    $order->user->email ?? 'N/A',
                    $order->service->name ?? 'N/A',
                    $order->country->name ?? 'N/A',
                    $order->phone_number,
                    $order->sms_code,
                    $order->status,
                    $order->price,
                    $order->api_price,
                    $order->final_price,
                    $order->refunded ? 'Yes' : 'No',
                    $order->needs_review ? 'Yes' : 'No',
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->sms_received_at ? $order->sms_received_at->format('Y-m-d H:i:s') : 'N/A',
                    $order->expires_at ? $order->expires_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Get order statistics
     */
    public function statistics()
    {
        // Get exchange rate from general settings
        $generalSettings = GeneralSetting::first();
        $exchangeRate = $generalSettings->naira_to_dollar_rate ?? 1700.00;
        
        // Calculate financial statistics
        // Final price is already in Naira, API price is in USD
        $totalRevenueNaira = Order::where('status', Order::STATUS_COMPLETED)->sum('final_price');
        $totalApiCostUsd = Order::where('status', Order::STATUS_COMPLETED)->sum('api_price');
        $totalApiCostNaira = $totalApiCostUsd * $exchangeRate;
        $totalProfitNaira = $totalRevenueNaira - $totalApiCostNaira;
        $averageProfitMargin = $totalApiCostNaira > 0 ? ($totalProfitNaira / $totalApiCostNaira) * 100 : 0;
        
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'active_orders' => Order::where('status', Order::STATUS_ACTIVE)->count(),
            'completed_orders' => Order::where('status', Order::STATUS_COMPLETED)->count(),
            'cancelled_orders' => Order::where('status', Order::STATUS_CANCELLED)->count(),
            'expired_orders' => Order::where('status', Order::STATUS_EXPIRED)->count(),
            'failed_orders' => Order::where('status', Order::STATUS_FAILED)->count(),
            'needs_review' => Order::where('needs_review', true)->count(),
            'total_revenue' => Order::where('status', Order::STATUS_COMPLETED)->sum('final_price'),
            'total_refunded' => Order::where('refunded', true)->sum('final_price'),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'this_week_orders' => Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month_orders' => Order::whereMonth('created_at', now()->month)->count(),
            // Financial statistics
            'total_revenue_naira' => number_format($totalRevenueNaira, 2),
            'total_api_cost_usd' => number_format($totalApiCostUsd, 2), // Keep API costs in USD
            'total_profit_naira' => number_format($totalProfitNaira, 2),
            'average_profit_margin' => number_format($averageProfitMargin, 1)
        ];

        return response()->json($stats);
    }

    /**
     * Bulk actions on orders
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:cancel,mark_review,remove_review,export',
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'reason' => 'required_if:action,cancel,mark_review|string|max:500'
        ]);

        try {
            $orders = Order::whereIn('id', $request->order_ids)->get();
            $action = $request->action;
            $reason = $request->reason;

            DB::transaction(function () use ($orders, $action, $reason) {
                foreach ($orders as $order) {
                    switch ($action) {
                        case 'cancel':
                            if ($order->canBeCancelled() || $order->shouldBeAutoCancelled()) {
                                $order->cancel($reason, 'admin', auth()->id());
                            }
                            break;

                        case 'mark_review':
                            $order->update(['needs_review' => true]);
                            if (!$order->reviewQueue) {
                                $order->reviewQueue()->create([
                                    'reason' => $reason,
                                    'created_by' => auth()->id()
                                ]);
                            }
                            break;

                        case 'remove_review':
                            $order->update(['needs_review' => false]);
                            if ($order->reviewQueue) {
                                $order->reviewQueue->delete();
                            }
                            break;
                    }
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Bulk action completed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to perform bulk action: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an order
     */
    public function destroy(Order $order)
    {
        try {
            // Only allow deletion of cancelled, expired, or failed orders
            if (!in_array($order->status, [Order::STATUS_CANCELLED, Order::STATUS_EXPIRED, Order::STATUS_FAILED])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Only cancelled, expired, or failed orders can be deleted'
                ], 400);
            }

            $order->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Order deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete order: ' . $e->getMessage()
            ], 500);
        }
    }
}