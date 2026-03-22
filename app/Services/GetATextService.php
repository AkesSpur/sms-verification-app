<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetATextService
{
    private string $apiKey  = '';
    private string $baseUrl = 'https://getatext.com';
    private int    $timeout = 30;

    public function __construct()
    {
        $this->apiKey  = (string) config('services.getatext.api_key', '');
        $this->baseUrl = (string) config('services.getatext.base_url', 'https://getatext.com');
        $this->timeout = (int)    config('services.getatext.timeout', 30);
    }

    // ─────────────────────────────────────────────────────────────
    // HTTP helpers
    // ─────────────────────────────────────────────────────────────

    private function getatextGet(string $endpoint): array
    {
        Log::channel('getatext')->info('GET request', ['endpoint' => $endpoint]);

        $response = Http::timeout($this->timeout)
            ->withHeaders(['Auth' => $this->apiKey, 'Accept' => 'application/json'])
            ->get($this->baseUrl . $endpoint);

        Log::channel('getatext')->info('GET response', [
            'endpoint'    => $endpoint,
            'status_code' => $response->status(),
            'response'    => $response->json(),
        ]);

        if ($response->successful()) {
            return $response->json() ?? [];
        }

        $decoded     = $response->json();
        $userMessage = $this->resolveClientErrorMessage($response->status(), $decoded);

        Log::channel('getatext')->error('GET client error', [
            'endpoint'    => $endpoint,
            'status_code' => $response->status(),
            'response'    => $decoded,
        ]);

        throw new Exception($userMessage);
    }

    private function getatextPost(string $endpoint, array $body = []): array
    {
        Log::channel('getatext')->info('POST request', [
            'endpoint' => $endpoint,
            'payload'  => $body,
        ]);

        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'Auth'         => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])
            ->post($this->baseUrl . $endpoint, $body);

        Log::channel('getatext')->info('POST response', [
            'endpoint'    => $endpoint,
            'status_code' => $response->status(),
            'response'    => $response->json(),
        ]);

        if ($response->successful()) {
            return $response->json() ?? [];
        }

        $decoded     = $response->json();
        $userMessage = $this->resolveClientErrorMessage($response->status(), $decoded);

        Log::channel('getatext')->error('POST client error', [
            'endpoint'    => $endpoint,
            'status_code' => $response->status(),
            'response'    => $decoded,
        ]);

        throw new Exception($userMessage);
    }

    private function resolveClientErrorMessage(int $statusCode, ?array $response): string
    {
        $message = strtolower($response['message'] ?? $response['errors'] ?? '');

        return match ($statusCode) {
            400 => str_contains($message, 'stock') || str_contains($message, 'available')
                ? 'No numbers available for this service at the moment.'
                : (str_contains($message, 'fund') || str_contains($message, 'balance')
                    ? 'Insufficient balance. Please contact support.'
                    : (str_contains($message, 'price') || str_contains($message, 'maximum')
                        ? 'The actual price exceeds the maximum you set. Please try without a max price.'
                        : 'Service request failed. Please try again.')),
            403 => 'Service authentication error. Please contact support.',
            404 => 'Service not found. Please try a different option.',
            429 => 'Too many requests. Please wait a moment and try again.',
            503 => 'Service is under maintenance. Please try again later.',
            default => 'Provider error (' . $statusCode . '). Please try again.',
        };
    }

    // ─────────────────────────────────────────────────────────────
    // Public API methods
    // ─────────────────────────────────────────────────────────────

    /**
     * Fetch account balance.
     * Returns ['success' => true, 'balance' => float] or ['success' => false, 'error' => string].
     */
    public function getBalance(): array
    {
        try {
            $data = $this->getatextGet('/api/v1/balance');

            return [
                'success' => true,
                'balance' => (float) ($data['balance'] ?? 0),
            ];
        } catch (Exception $e) {
            Log::channel('getatext')->error('getBalance failed', ['error' => $e->getMessage()]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Fetch available services from the GetAText prices-info endpoint,
     * cached for 1 hour. Returns an array of service objects:
     *   ['name', 'short_name', 'cost' (USD float), 'count' (int), 'price' (USD float)]
     *
     * Priority order: whatsapp → telegram → paypal first.
     * In-stock services sorted before out-of-stock ones.
     */
    public function getServices(): array
    {
        return Cache::remember('getatext-get-services', 900, function () {
            try {
                $raw   = $this->getatextGet('/api/v1/prices-info');
                $items = $raw['prices'] ?? $raw;

                if (!is_array($items) || empty($items)) {
                    return [];
                }

                $services = [];

                foreach ($items as $item) {
                    if (!isset($item['service_name'], $item['api_name'], $item['price'])) {
                        continue;
                    }

                    $services[] = [
                        'name'       => $item['service_name'],
                        'short_name' => $item['api_name'],
                        'cost'       => (float) $item['price'],
                        'count'      => (int) ($item['stock'] ?? 0),
                        'price'      => (float) $item['price'],
                        'ttl'        => (int) ($item['ttl'] ?? 7),
                    ];
                }

                // Pin priority services to the front
                $priorityOrder = ['whatsapp', 'telegram', 'paypal'];

                foreach (array_reverse($priorityOrder) as $apiName) {
                    foreach ($services as $i => $service) {
                        if (strtolower($service['short_name']) === $apiName) {
                            $pinned = $services[$i];
                            array_splice($services, $i, 1);
                            array_unshift($services, $pinned);
                            break;
                        }
                    }
                }

                // In-stock first, out-of-stock last (stable sort)
                usort($services, fn ($a, $b) => ($a['count'] === 0) - ($b['count'] === 0));

                return array_values($services);

            } catch (Exception $e) {
                Log::channel('getatext')->error('getServices failed', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Rent a phone number.
     * Returns:
     *   ['success'=>true, 'rental_id'=>int, 'phone_number'=>string, 'service_name'=>string, 'expires_at'=>Carbon, 'price_usd'=>float]
     *   ['success'=>false, 'error'=>string]
     */
    public function rentNumber(string $service, ?float $maxPrice = null): array
    {
        try {
            $body = ['service' => $service];

            if ($maxPrice !== null) {
                $body['max_price'] = $maxPrice;
            }

            $data = $this->getatextPost('/api/v1/rent-a-number', $body);

            if (empty($data['id']) || empty($data['number'])) {
                $error = $data['errors'] ?? 'No number returned by provider.';
                Log::channel('getatext')->error('rentNumber unexpected response', ['data' => $data]);

                return ['success' => false, 'error' => is_string($error) ? $error : 'Provider error. Please try again.'];
            }

            // Compute expires_at from now + TTL to avoid timezone ambiguity.
            // GetAText returns end_time in their server timezone (US Eastern),
            // so we derive duration ourselves using ttl (minutes) when available.
            $ttlMinutes = isset($data['ttl']) && $data['ttl'] > 0
                ? (int) $data['ttl']
                : 7;

            return [
                'success'      => true,
                'rental_id'    => (int) $data['id'],
                'phone_number' => (string) $data['number'],
                'service_name' => (string) ($data['service_name'] ?? $service),
                'expires_at'   => now()->addMinutes($ttlMinutes),
                'price_usd'    => (float) ($data['price'] ?? 0),
            ];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Cancel a rental (GetAText requires ≥1 minute after creation before cancelling).
     * Returns ['success'=>true] or ['success'=>false, 'error'=>string].
     */
    public function cancelRental(int $rentalId): array
    {
        try {
            $data = $this->getatextPost('/api/v1/cancel-rental', ['id' => $rentalId]);

            return ['success' => true, 'data' => $data];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Mark a rental as completed (best-effort, errors are swallowed).
     */
    public function markCompleted(int $rentalId): void
    {
        try {
            $this->getatextPost("/api/v1/rental-status/{$rentalId}/completed");
        } catch (Exception $e) {
            Log::channel('getatext')->debug('markCompleted failed (non-critical)', [
                'rental_id' => $rentalId,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get rental status from the API.
     * Returns ['success'=>true, 'status'=>string, ...] or ['success'=>false, 'error'=>string].
     */
    public function getRentalStatus(int $rentalId): array
    {
        try {
            $data = $this->getatextPost('/api/v1/rental-status', ['id' => $rentalId]);

            return [
                'success' => true,
                'status'  => $data['status'] ?? 'unknown',
                'number'  => $data['number'] ?? null,
                'data'    => $data,
            ];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
