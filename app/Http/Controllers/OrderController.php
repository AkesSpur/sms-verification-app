<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Services\SmsActivateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class OrderController extends Controller
{
    protected $smsService;

    public function __construct(SmsActivateService $smsService)
    {
        $this->smsService = $smsService;
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

        try {
            $response = $this->smsService->getNumber($service['code'], $request->country);
        
            // Log the raw response for debugging
            Log::info('SMS Activate Raw Response:', ['response' => $response, 'type' => gettype($response)]);

            // Ensure the response is an array and contains the necessary keys
            if (is_array($response) && isset($response['id']) && isset($response['number'])) {
                $activationId = $response['id']; // Correct way to extract activation ID
                $phoneNumber = $response['number']; // Correct way to extract phone number
            } 
            elseif (is_string($response) && preg_match('/\\\\u[0-9a-fA-F]{4}/', $response)) {
                    // Handle Unicode-encoded error messages
                    $decodedResponse = json_decode('"' . $response . '"');
                    throw new \Exception($decodedResponse);
            }
            else {
                throw new \Exception("Unexpected response format from SMS Activate API.");
            }

            // Save the order to the database
            $order = Order::create([
                'user_id' => Auth::id(),
                'service_id' => $request->service,
                'phone_number' => $phoneNumber,
                'activation_id' => $activationId,
                'expires_at' => now()->addMinutes(20),
            ]);
            
            // Flash success message
            toastr()->success('Order created successfully!');
            return redirect()->route('user.number');
        
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