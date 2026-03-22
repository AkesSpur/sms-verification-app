<?php

namespace App\Http\Controllers;

use App\Models\DaisyOrder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetATextWebhookController extends Controller
{
    /**
     * Handle an inbound SMS webhook from GetAText.
     *
     * GetAText sends the API key in the same `Auth` header format used for
     * outbound requests, so we can verify authenticity by comparing it.
     *
     * POST /api/webhook/getatext
     */
    public function webhook(Request $request): JsonResponse
    {
        // Log full payload for debugging
        Log::channel('getatext')->info('Webhook received', [
            'headers' => $request->headers->all(),
            'payload' => $request->all(),
        ]);

        // GetAText does not send an Auth header on webhook pushes.
        // Security is provided by the rental_id matching an order in our DB.

        // Extract fields from payload
        $rentalId = $request->input('id');
        $smsCode  = $request->input('code');
        $smsText  = $request->input('message') ?? $request->input('text') ?? $request->input('sms_text');

        if (!$rentalId || !$smsCode) {
            Log::channel('getatext')->warning('Webhook missing required fields', [
                'rental_id' => $rentalId,
                'has_code'  => !empty($smsCode),
            ]);

            return response()->json(['status' => 'missing_fields'], 422);
        }

        // Find the matching order
        $order = DaisyOrder::where('rental_id', (string) $rentalId)->first();

        if (!$order) {
            Log::channel('getatext')->warning('Webhook order not found', ['rental_id' => $rentalId]);

            return response()->json(['status' => 'not_found'], 404);
        }

        // Log if this is a new code replacing a previous one
        if ($order->sms_code && $order->sms_code !== (string) $smsCode) {
            Log::channel('getatext')->info('Webhook updating existing code', [
                'rental_id'    => $rentalId,
                'order_id'     => $order->id,
                'previous_code' => $order->sms_code,
                'new_code'      => $smsCode,
            ]);
        }

        // Persist the SMS code — order stays active so more codes can arrive
        DB::beginTransaction();

        try {
            $order->update([
                'sms_code' => (string) $smsCode,
                'sms_text' => $smsText,
            ]);

            DB::commit();

            Log::channel('getatext')->info('Webhook code saved', [
                'rental_id' => $rentalId,
                'order_id'  => $order->id,
                'code'      => $smsCode,
                'status'    => $order->status,
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            Log::channel('getatext')->error('Webhook DB error', [
                'rental_id' => $rentalId,
                'error'     => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error'], 500);
        }

        return response()->json(['status' => 'ok']);
    }
}
