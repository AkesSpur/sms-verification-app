<?php

namespace App\Http\Controllers;

use App\Models\DaisyOrder;
use App\Services\GetATextService;
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

        // Idempotency guard — already processed
        if ($order->sms_code) {
            Log::channel('getatext')->info('Webhook already processed', [
                'rental_id' => $rentalId,
                'order_id'  => $order->id,
            ]);

            return response()->json(['status' => 'already_set']);
        }

        // Persist the SMS code
        DB::beginTransaction();

        try {
            $order->update([
                'sms_code'     => (string) $smsCode,
                'sms_text'     => $smsText,
                'status'       => DaisyOrder::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            DB::commit();

            Log::channel('getatext')->info('Webhook processed successfully', [
                'rental_id' => $rentalId,
                'order_id'  => $order->id,
                'code'      => $smsCode,
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            Log::channel('getatext')->error('Webhook DB error', [
                'rental_id' => $rentalId,
                'error'     => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error'], 500);
        }

        // Best-effort: mark completed on GetAText side
        try {
            app(GetATextService::class)->markCompleted((int) $rentalId);
        } catch (Exception $e) {
            Log::channel('getatext')->debug('markCompleted after webhook failed (non-critical)', [
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}
