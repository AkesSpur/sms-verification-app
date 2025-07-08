<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Services\SmsPoolService;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class OrderController extends Controller
{
    protected $smsService;
    protected $pricingService;

    public function __construct(SmsPoolService $smsService, PricingService $pricingService)
    {
        $this->smsService = $smsService;
        $this->pricingService = $pricingService;
    }

    public function create()
    {
        $services = Service::all(); // Fetch available services
        return view('order.create', compact('services'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'service' => 'required|integer',
            'country' => 'required|string',
        ]);
    
        $service = Service::findOrFail($request->service);
        $user = Auth::user();

        try {
            // Get price in Naira using PricingService
            $priceInNaira = $this->pricingService->getServicePrice($service->code, $request->country);
            
            // Check if user has sufficient balance
            if ($user->balance < $priceInNaira) {
                toastr()->error('Insufficient balance. Required: ₦' . number_format($priceInNaira, 2));
                return back();
            }
            
            // Convert Naira price to USD for API call
            $priceInUsd = $this->pricingService->convertNairaToUsd($priceInNaira);
            
            $response = $this->smsService->purchaseNumber($service->code, $user->id, $request->country, $priceInNaira);
        
            // Log the raw response for debugging
            Log::info('SMS Activate Raw Response:', ['response' => $response, 'type' => gettype($response)]);

            // Check if purchase was successful
            if ($response['success']) {
                $order = $response['order'];
                
                toastr()->success('Order created successfully! Price: ₦' . number_format($priceInNaira, 2));
                return redirect()->route('user.number');
            } else {
                 throw new \Exception('Failed to purchase number from SMS Activate API.');
             }
        
        } catch (\Exception $e) {
            // Flash error message
            toastr()->translate()->error($e->getMessage());
            return back();
        }
    }
    

    public function show(Order $order)
    {
        return view('order.show', compact('order'));
    }

    public function checkStatus($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
    
        try {
            $status = $this->smsService->getStatus($order->activation_id);

                
           // Handle JSON response
        if (isset($status['response'])) {
            if ($status['response'] === 'STATUS_CANCEL') {
                $order->update([
                    'status' => 'expired', // Update status to expired
                ]);

                return response()->json([
                    'status' => 'expired',
                    'message' => 'Order has been canceled and marked as expired.',
                ]);
            } elseif (strpos($status['response'], 'STATUS_OK') !== false) {
                $code = explode(':', $status['response'])[1]; // Extract the code
                $order->update([
                    'sms_code' => $code,
                    'status' => 'completed',
                ]);

                return response()->json([
                    'sms_code' => $code,
                    'status' => 'completed',
                ]);
            }
        }

        // Handle colon-separated response
        if (isset($status['status'])) {
            if ($status['status'] === 'STATUS_CANCEL') {
                $order->update([
                    'status' => 'expired', // Update status to expired
                ]);

                return response()->json([
                    'status' => 'expired',
                    'message' => 'Order has been canceled and marked as expired.',
                ]);
            } elseif ($status['status'] === 'STATUS_OK') {
                $order->update([
                    'sms_code' => $status['code'],
                    'status' => 'completed',
                ]);

                return response()->json([
                    'sms_code' => $status['code'],
                    'status' => 'completed',
                ]);
            }
        }

        // Handle other cases
        return response()->json([
            'status' => $order->status,
            'message' => 'No changes made.',
        ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}