<?php

namespace App\Services;

use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsBowerService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.smsbower.api_key', '');
        $this->baseUrl = config('services.smsbower.base_url', 'https://smsbower.page/stubs/handler_api.php');
    }

    /**
     * Make a GET request to the SmsBower API.
     * All responses are plain text (except getCountries, getServicesList, getPrices, getNumberV2).
     */
    private function request(array $params): string
    {
        $params['api_key'] = $this->apiKey;

        try {
            $response = Http::timeout(15)->get($this->baseUrl, $params);
            return trim($response->body());
        } catch (\Throwable $th) {
            Log::error('[SmsBowerService] Request failed: ' . $th->getMessage(), $params);
            return 'REQUEST_ERROR';
        }
    }

    /**
     * Get current API balance.
     */
    public function getBalance(): array
    {
        $raw = $this->request(['action' => 'getBalance']);

        if (str_starts_with($raw, 'ACCESS_BALANCE:')) {
            $balance = (float) substr($raw, strlen('ACCESS_BALANCE:'));
            return ['success' => true, 'balance' => $balance];
        }

        Log::warning('[SmsBowerService] getBalance unexpected response: ' . $raw);
        return ['success' => false, 'balance' => 0];
    }

    /**
     * Get list of available countries.
     * Cached 6 hours. Returns [numeric_id => country_name].
     */
    public function getCountries(): array
    {
        $cached = Cache::get('smsbower_countries');
        if (is_array($cached) && $cached !== []) {
            return $cached;
        }

        $raw  = $this->request(['action' => 'getCountries']);
        $data = json_decode($raw, true);

        if (!is_array($data)) {
            Log::error('[SmsBowerService] getCountries invalid response: ' . $raw);
            return [];
        }

        $countries = [];
        foreach ($data as $item) {
            if (isset($item['id'], $item['eng'])) {
                $countries[(int) $item['id']] = $item['eng'];
            }
        }

        asort($countries);

        // Only cache successful, non-empty responses
        if ($countries !== []) {
            Cache::put('smsbower_countries', $countries, 21600);
        }

        return $countries;
    }

    /**
     * Get list of available services.
     * Cached 6 hours. Returns [service_code => service_name].
     */
    public function getServices(): array
    {
        $cached = Cache::get('smsbower_services');
        if (is_array($cached) && $cached !== []) {
            return $cached;
        }

        $raw  = $this->request(['action' => 'getServicesList']);
        $data = json_decode($raw, true);

        if (!is_array($data)) {
            Log::error('[SmsBowerService] getServicesList invalid response: ' . $raw);
            return [];
        }

        $items = $data['services'] ?? $data;
        if (!is_array($items)) {
            return [];
        }

        $services = [];
        foreach ($items as $item) {
            $code = $item['code'] ?? $item['Code'] ?? null;
            $name = $item['name'] ?? $item['Value'] ?? null;
            if ($code && $name) {
                $services[$code] = $name;
            }
        }

        asort($services);

        // Only cache successful, non-empty responses
        if ($services !== []) {
            Cache::put('smsbower_services', $services, 21600);
        }

        return $services;
    }

    /**
     * Get price and stock for a country + service combination.
     * Returns ['cost' => float, 'count' => int] or null if unavailable.
     */
    public function getPrice(int $countryId, string $serviceCode): ?array
    {
        $raw = $this->request([
            'action'  => 'getPrices',
            'country' => $countryId,
            'service' => $serviceCode,
        ]);

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return null;
        }

        $countryData = $data[(string) $countryId] ?? $data[$countryId] ?? null;
        $serviceData = $countryData[$serviceCode] ?? null;

        if (!$serviceData || !isset($serviceData['cost'])) {
            return null;
        }

        return [
            'cost'  => (float) $serviceData['cost'],
            'count' => (int) ($serviceData['count'] ?? 0),
        ];
    }

    /**
     * Purchase a phone number via getNumberV2 (returns JSON).
     * Returns ['order_id' => string, 'number' => string, 'cost' => float] on success.
     * Returns ['error' => string] on failure.
     */
    public function purchaseNumber(int $countryId, string $serviceCode): array
    {
        $raw = $this->request([
            'action'  => 'getNumberV2',
            'service' => $serviceCode,
            'country' => $countryId,
        ]);

        $data = json_decode($raw, true);
        if (is_array($data) && isset($data['activationId'], $data['phoneNumber'])) {
            return [
                'order_id' => (string) $data['activationId'],
                'number'   => (string) $data['phoneNumber'],
                'cost'     => (float) ($data['activationCost'] ?? 0),
            ];
        }

        $errors = [
            'NO_NUMBERS'    => 'No numbers available for this service/country.',
            'NO_BALANCE'    => 'Provider balance insufficient. Please contact support.',
            'WRONG_SERVICE' => 'Invalid service selected.',
            'WRONG_COUNTRY' => 'Invalid country selected.',
            'BAD_ACTION'    => 'API error. Please try again.',
            'BAD_KEY'       => 'API configuration error. Please contact support.',
            'ERROR_SQL'     => 'Provider server error. Please try again.',
            'REQUEST_ERROR' => 'Could not connect to service. Please try again.',
        ];

        $message = $errors[$raw] ?? 'Service unavailable. Please try again.';

        Log::error('[SmsBowerService] purchaseNumber failed', [
            'country'  => $countryId,
            'service'  => $serviceCode,
            'response' => $raw,
        ]);

        return ['error' => $message];
    }

    /**
     * Check SMS status for an active order.
     * Returns ['status' => 'waiting|completed|cancelled', 'code' => string|null]
     */
    public function checkSms(string $orderId): array
    {
        $raw = $this->request([
            'action' => 'getStatus',
            'id'     => $orderId,
        ]);

        if (str_starts_with($raw, 'STATUS_OK:')) {
            $code = trim(substr($raw, strlen('STATUS_OK:')));
            return ['status' => 'completed', 'code' => $code];
        }

        if ($raw === 'STATUS_WAIT_CODE' || $raw === 'STATUS_WAIT_RESEND') {
            return ['status' => 'waiting', 'code' => null];
        }

        // STATUS_WAIT_RETRY:lastCode — partial code, still waiting
        if (str_starts_with($raw, 'STATUS_WAIT_RETRY:')) {
            return ['status' => 'waiting', 'code' => null];
        }

        if (in_array($raw, ['STATUS_CANCEL', 'NO_ACTIVATION'])) {
            return ['status' => 'cancelled', 'code' => null];
        }

        Log::warning('[SmsBowerService] checkSms unexpected response', [
            'order_id' => $orderId,
            'response' => $raw,
        ]);

        return ['status' => 'waiting', 'code' => null];
    }

    /**
     * Cancel an active order (setStatus&status=8).
     * Returns ['success' => bool, 'message' => string]
     */
    public function cancelOrder(string $orderId): array
    {
        $raw = $this->request([
            'action' => 'setStatus',
            'id'     => $orderId,
            'status' => 8,
        ]);

        if ($raw === 'ACCESS_CANCEL') {
            return ['success' => true, 'message' => 'Order cancelled successfully.'];
        }

        if ($raw === 'EARLY_CANCEL_DENIED') {
            return ['success' => false, 'early_cancel' => true, 'message' => 'Please wait at least 2 minutes before cancelling.'];
        }

        Log::warning('[SmsBowerService] cancelOrder unexpected response', [
            'order_id' => $orderId,
            'response' => $raw,
        ]);

        return ['success' => false, 'message' => 'Cancellation failed. Please try again shortly.'];
    }

    /**
     * Convert a USD cost to NGN: cost × usd_to_ngn_rate + global order fee.
     */
    public function calculateNairaPrice(float $costUsd): int
    {
        $settings = GeneralSetting::first();
        $rate     = (float) ($settings->usd_to_ngn_rate ?? 1600);
        $fee      = (int) ($settings->global_order_fee ?? 1000);

        return (int) ceil($costUsd * $rate) + $fee;
    }
}
