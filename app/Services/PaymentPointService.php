<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class PaymentPointService
{
    protected string $baseUrl = 'https://api.paymentpoint.co/api/v1';

    public function createVirtualAccount(User $user): array
    {
        $apiKey = config('services.paymentpoint.api_key');
        $apiSecret = config('services.paymentpoint.api_secret');
        $businessId = config('services.paymentpoint.business_id');
        $bankCodes = config('services.paymentpoint.bank_codes');

        $payload = [
            'email' => $user->email,
            'name' => $user->name ?? $user->email,
            // Phone is optional in our payload if null
            'phoneNumber' => $user->phone,
            'bankCode' => $bankCodes,
            'businessId' => $businessId,
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $apiSecret,
            'api-key' => $apiKey,
        ];

        $response = Http::withHeaders($headers)
            ->acceptJson()
            ->post($this->baseUrl . '/createVirtualAccount', $payload);

        if (!$response->successful()) {
            return [
                'success' => false,
                'message' => $response->json('message') ?? 'PaymentPoint API error',
                'status' => $response->status(),
                'data' => $response->json(),
            ];
        }

        $data = $response->json();

        $status = $data['status'] ?? null;
        $accounts = $data['bankAccounts'] ?? [];

        if ($status !== 'success' || empty($accounts)) {
            return [
                'success' => false,
                'message' => $data['message'] ?? 'No bank accounts returned',
                'data' => $data,
            ];
        }

        return [
            'success' => true,
            'message' => $data['message'] ?? 'Success',
            'data' => $data,
        ];
    }
}