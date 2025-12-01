<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\RequestError;
use App\Models\Country;
use Exception;

class SmsPoolService
{
    private $apiKey;
    private $baseUrl;
    private $timeout;
    private $maxRetries;
    private $retryDelay;
    
    const USA_COUNTRY_CODE = "US"; // SMSPool uses numeric country codes
    
    // Status mapping for SMSPool responses
    const STATUS_MAPPING = [
        0 => 'pending',
        1 => 'received', 
        2 => 'expired',
        3 => 'completed', // Status 3 with SMS code means completed
        4 => 'cancelled'
    ];
    
    // Error codes that SMSPool might return
    const ERROR_CODES = [
        'INSUFFICIENT_BALANCE',
        'INVALID_SERVICE',
        'INVALID_COUNTRY',
        'NO_NUMBERS_AVAILABLE',
        'INVALID_ORDER_ID',
        'ORDER_NOT_FOUND',
        'INVALID_API_KEY'
    ];
    
    public function __construct()
    {
        $this->apiKey = config('services.smspool.api_key');
        $this->baseUrl = config('services.smspool.base_url', 'https://api.smspool.net');
        $this->timeout = config('services.smspool.timeout', 30);
        $this->maxRetries = config('services.smspool.max_retries', 3);
        $this->retryDelay = config('services.smspool.retry_delay', 1000);
    }
    
    /**
     * Get account balance
     */
    public function getBalance()
    {
        try {
            $response = $this->makeRequest('request/balance', [], 'POST');
            
            if (isset($response['balance'])) {
                return floatval($response['balance']);
            }
            
            throw new RequestError('Invalid balance response');
            
        } catch (Exception $e) {
            Log::error('SMSPool balance check failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Get available countries
     * Since the direct countries endpoint doesn't work, we'll extract countries from success_rate data
     */
    public function getCountries()
    {
        try {
            $cacheKey = 'smspool_countries';
            
            return Cache::remember($cacheKey, 3600, function() {
                // Use success_rate endpoint with a common service (WhatsApp) to get country list
                $response = $this->makeRequest('request/success_rate', ['service' => '1012'], 'POST');
                
                if (is_array($response)) {
                    // Extract unique countries from the response
                    $countries = [];
                    $seenCountries = [];
                    
                    foreach ($response as $item) {
                        if (isset($item['country_id'], $item['name'], $item['short_name']) && 
                            !in_array($item['country_id'], $seenCountries)) {
                            
                            $countries[] = [
                                'ID' => $item['country_id'],
                                'name' => $item['short_name'],
                                'country' => $item['name']
                            ];
                            $seenCountries[] = $item['country_id'];
                        }
                    }
                    
                    // Sort by country name
                    usort($countries, function($a, $b) {
                        return strcmp($a['country'], $b['country']);
                    });
                    
                    return $countries;
                }
                
                throw new RequestError('Invalid countries response');
            });
            
        } catch (Exception $e) {
            Log::error('SMSPool countries fetch failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Get available services for a country
     */
    public function getServices($countryId = null)
    {
        try {
            $countryId = $countryId ?? self::USA_COUNTRY_CODE;
            $cacheKey = "smspool_services_{$countryId}";
            
            return Cache::remember($cacheKey, 1800, function() use ($countryId) {
                $response = $this->makeRequest('request/services', [
                    'country' => $countryId
                ], 'GET');
                
                if (is_array($response)) {
                    return $response;
                }
                
                throw new RequestError('Invalid services response');
            });
            
        } catch (Exception $e) {
            Log::error('SMSPool services fetch failed', [
                'country_id' => $countryId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Check if a service is actually purchasable for a country
     * This validates that the service is not just listed but can be purchased
     */
    public function isServicePurchasable($serviceId, $countryId)
    {
        try {
            // First check if service has a valid price
            $priceResponse = $this->makeRequest('request/price', [
                'country' => $countryId,
                'service' => $serviceId
            ], 'GET');
            
            // If price is 0 or invalid, service is likely not purchasable
            if (!isset($priceResponse['price']) || floatval($priceResponse['price']) <= 0) {
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            // If price check fails, assume service is not purchasable
            Log::warning('Service purchasability check failed', [
                'service_id' => $serviceId,
                'country_id' => $countryId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get only purchasable services for a country
     * This filters out services that are listed but not actually available for purchase
     */
    public function getPurchasableServices($countryId = null)
    {
        try {
            $countryId = $countryId ?? self::USA_COUNTRY_CODE;
            $cacheKey = "smspool_purchasable_services_{$countryId}";
            
            return Cache::remember($cacheKey, 3600, function() use ($countryId) {
                $allServices = $this->getServices($countryId);
                $purchasableServices = [];
                
                foreach ($allServices as $service) {
                    if (isset($service['ID']) && $this->isServicePurchasable($service['ID'], $countryId)) {
                        $purchasableServices[] = $service;
                    }
                }
                
                Log::info('Filtered purchasable services', [
                    'country_id' => $countryId,
                    'total_services' => count($allServices),
                    'purchasable_services' => count($purchasableServices)
                ]);
                
                return $purchasableServices;
            });
            
        } catch (Exception $e) {
            Log::error('SMSPool purchasable services fetch failed', [
                'country_id' => $countryId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Get service price for a specific country and service
     */
    public function getServicePrice($serviceId, $countryId)
    {
        try {
            $response = $this->makeRequest('request/price', [
                'country' => $countryId,
                'service' => $serviceId
            ], 'GET');
            
            if (isset($response['price'])) {
                return floatval($response['price']);
            }
            
            throw new RequestError('Invalid price response');
            
        } catch (Exception $e) {
            Log::error('SMSPool price check failed', [
                'service_id' => $serviceId,
                'country_id' => $countryId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Purchase SMS number with pool fallback mechanism
     */
    private function purchaseNumberWithPoolFallback($serviceId, $countryId, $maxPrice = null)
    {
        // Define pool fallback order based on country
        $poolsToTry = $this->getPoolFallbackOrder($countryId);
        
        $lastException = null;
        
        foreach ($poolsToTry as $pool) {
            try {
                Log::info('Attempting SMS purchase with pool fallback', [
                    'service_id' => $serviceId,
                    'country_id' => $countryId,
                    'pool' => $pool,
                    'attempt' => array_search($pool, $poolsToTry) + 1,
                    'total_pools' => count($poolsToTry)
                ]);
                
                $response = $this->purchaseNumberFromApi(
                    $serviceId, 
                    $countryId, 
                    $pool, 
                    $maxPrice, 
                    0, // pricing_option (0 = cheapest)
                    1  // quantity
                );
                
                if ($response['success']) {
                    Log::info('SMS purchase successful with pool', [
                        'service_id' => $serviceId,
                        'country_id' => $countryId,
                        'successful_pool' => $pool,
                        'order_id' => $response['order_id']
                    ]);
                    return $response;
                }
                
            } catch (Exception $e) {
                $lastException = $e;
                Log::warning('SMS purchase failed with pool, trying next', [
                    'service_id' => $serviceId,
                    'country_id' => $countryId,
                    'failed_pool' => $pool,
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }
        
        // If all pools failed, throw the last exception
        Log::error('SMS purchase failed with all pools', [
            'service_id' => $serviceId,
            'country_id' => $countryId,
            'pools_tried' => $poolsToTry,
            'final_error' => $lastException ? $lastException->getMessage() : 'Unknown error'
        ]);
        
        throw $lastException ?: new \Exception('All pool attempts failed');
    }
    
    /**
     * Get pool fallback order based on country
     */
    private function getPoolFallbackOrder($countryId)
    {
        // Default pool order for most countries
        $defaultPools = [null, 1, 2, 3, 4, 5]; // null = auto-select, then numbered pools
        
        // Special pool orders for specific countries
        $countrySpecificPools = [
            'US' => [null, 'Foxtrot', 7, 1, 2, 3, 4, 5], // US: try auto, then Foxtrot/Pool 7, then others
            'US_V' => [null, 'Foxtrot', 7, 1, 2, 3, 4, 5], // US Virtual: same as US
            'GB' => [null, 'Alpha', 1, 2, 3, 4, 5], // UK: try auto, then Alpha, then others
            'CA' => [null, 'Foxtrot', 7, 1, 2, 3, 4, 5], // Canada: similar to US
        ];
        
        return $countrySpecificPools[$countryId] ?? $defaultPools;
    }
    
    /**
     * Purchase SMS number (low-level API call)
     */
    private function purchaseNumberFromApi($serviceId, $countryId, $pool = null, $maxPrice = null, $pricingOption = 0, $quantity = 1)
    {
        try {
            $params = [
                'country' => $countryId,
                'service' => $serviceId,
                'pricing_option' => $pricingOption,
                'quantity' => $quantity
            ];
            
            if ($pool) {
                $params['pool'] = $pool;
            }
            
            if ($maxPrice) {
                $params['max_price'] = $maxPrice;
            }
            
            // Log the request parameters for debugging
            Log::info('SMSPool purchase request', [
                'endpoint' => 'purchase/sms',
                'params' => $params
            ]);
            
            // For purchase requests, use the special method that returns response even on HTTP errors
            $response = $this->executeRequestForPurchase('purchase/sms', $params, 'POST');
            
            // Log the raw API response for debugging
            Log::info('SMSPool purchase raw response', [
                'service_id' => $serviceId,
                'country_id' => $countryId,
                'raw_response' => $response
            ]);
            
            if (isset($response['success']) && $response['success'] == 1) {
                return [
                    'success' => true,
                    'order_id' => $response['order_id'],
                    'number' => $response['number'],
                    'expires_in' => $response['expires_in'] ?? 600,
                    'expiration' => $response['expiration'] ?? (time() + ($response['expires_in'] ?? 600)),
                    'cost' => $response['cost'] ?? null,
                    'pool' => $response['pool'] ?? null,
                    'message' => $response['message'] ?? null
                ];
            }
            
            // Handle specific API error types
            $errorMessage = $response['message'] ?? 'Purchase failed';
            $errorType = $response['type'] ?? null;
            
            // Log the error details for debugging
            Log::error('SMSPool purchase API error', [
                'service_id' => $serviceId,
                'country_id' => $countryId,
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'full_response' => $response
            ]);
            
            // Provide user-friendly error messages for common issues
            switch ($errorType) {
                case 'SERVICE_NOT_AVAILABLE_FOR_COUNTRY':
                    $errorMessage = 'This service is currently not available for purchase in the selected country. Please try a different service or country.';
                    break;
                case 'INSUFFICIENT_BALANCE':
                    $errorMessage = 'Insufficient balance in SMS provider account. Please contact support.';
                    break;
                case 'SERVICE_TEMPORARILY_UNAVAILABLE':
                    $errorMessage = 'This service is temporarily unavailable. Please try again later.';
                    break;
                case 'COUNTRY_NOT_SUPPORTED':
                    $errorMessage = 'The selected country is not supported by the SMS provider.';
                    break;
                case 'INVALID_SERVICE':
                    $errorMessage = 'The requested service is not valid or has been discontinued.';
                    break;
                default:
                    // Keep original message for unknown error types
                    break;
            }
            
            throw new RequestError($errorMessage);
            
        } catch (Exception $e) {
            Log::error('SMSPool number purchase failed', [
                'service_id' => $serviceId,
                'country_id' => $countryId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Purchase SMS number with full order management
     */
    public function purchaseNumber($serviceCode, $userId, $countryCode = null, $priceInNaira = null, $orderSource = 'web')
    {
        try {
            // Get service and country models
            $service = \App\Models\Service::where('code', $serviceCode)->first();
            if (!$service) {
                throw new \Exception('Service not found: ' . $serviceCode);
            }
            
            $country = \App\Models\Country::where('code', $countryCode ?? self::USA_COUNTRY_CODE)->first();
            if (!$country) {
                throw new \Exception('Country not found: ' . ($countryCode ?? self::USA_COUNTRY_CODE));
            }
            
            $user = \App\Models\User::find($userId);
            if (!$user) {
                throw new \Exception('User not found: ' . $userId);
            }
            
            // Check user balance
            if ($user->balance < $priceInNaira) {
                throw new \Exception('Insufficient balance');
            }
            
            // Get the service price to set as max_price
            $servicePrice = $this->getServicePrice($service->code, $country->code);
            
            // Try different pools with fallback mechanism
            $apiResponse = $this->purchaseNumberWithPoolFallback(
                $service->code, 
                $country->code, 
                $servicePrice
            );
            
            if (!$apiResponse['success']) {
                throw new \Exception('API purchase failed');
            }
            
            // Create order in database
            $order = \App\Models\Order::create([
                'user_id' => $userId,
                'service_id' => $service->id,
                'country_id' => $country->id,
                'phone_number' => $apiResponse['number'],
                'activation_id' => $apiResponse['order_id'],
                'price' => $priceInNaira,
                'api_price' => $apiResponse['cost'] ?? null,
                'final_price' => $priceInNaira,
                'status' => \App\Models\Order::STATUS_PENDING,
                'expires_at' => \Carbon\Carbon::now()->addSeconds($apiResponse['expires_in']),
                'sms_window_expires_at' => \Carbon\Carbon::now()->addSeconds($apiResponse['expires_in']),
                'order_source' => $orderSource,
                'api_provider' => 'smspool',
                'api_response' => json_encode($apiResponse)
            ]);
            
            $user->deductBalance(
                $priceInNaira,
                'sms_purchase',
                "SMS number purchase for {$service->name} ({$country->name})",
                $order
            );
            
            Log::info('SMSPool number purchased successfully', [
                'user_id' => $userId,
                'order_id' => $order->id,
                'service' => $serviceCode,
                'country' => $countryCode,
                'phone_number' => $apiResponse['number'],
                'price' => $priceInNaira
            ]);
            
            return [
                'success' => true,
                'order' => $order,
                'phone_number' => $apiResponse['number'],
                'activation_id' => $apiResponse['order_id'],
                'country' => $country
            ];
            
        } catch (Exception $e) {
            Log::error('SMSPool number purchase failed', [
                'service_code' => $serviceCode,
                'user_id' => $userId,
                'country_code' => $countryCode,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Check SMS status and get code
     */
    public function checkSmsStatus($orderId)
    {
        try {
            $response = $this->makeRequest('sms/check', [
                'orderid' => $orderId
            ], 'POST');
            
            if (isset($response['status'])) {
                $rawStatus = $response['status'];
                $hasSmsCode = !empty($response['sms']) || !empty($response['full_sms']);
                
                // Handle special case: status 3 with SMS code means completed,
                // but status 3 without SMS code means cancelled
                if ($rawStatus == 3) {
                    $status = $hasSmsCode ? 'completed' : 'cancelled';
                } else {
                    $status = $this->parseStatusResponse($rawStatus);
                }
                
                return [
                    'status' => $status,
                    'code' => $response['sms'] ?? null,
                    'full_sms' => $response['full_sms'] ?? null
                ];
            }
            
            throw new RequestError('Invalid status response');
            
        } catch (Exception $e) {
            Log::error('SMSPool SMS status check failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Cancel SMS order
     */
    public function cancelNumber($orderId)
    {
        try {
            $response = $this->makeRequest('sms/cancel', [
                'orderid' => $orderId
            ], 'POST');
            
            if (isset($response['success']) && $response['success'] == 1) {
                return [
                    'success' => true,
                    'message' => 'Order cancelled successfully'
                ];
            }
            
            throw new RequestError($response['message'] ?? 'Cancellation failed');
            
        } catch (Exception $e) {
            Log::error('SMSPool number cancellation failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Request SMS resend
     */
    public function requestSmsRetry($orderId)
    {
        try {
            $response = $this->makeRequest('sms/resend', [
                'orderid' => $orderId
            ], 'POST');
            
            if (isset($response['success']) && $response['success'] == 1) {
                return [
                    'success' => true,
                    'message' => $response['message'] ?? 'SMS resend requested successfully'
                ];
            }
            
            throw new RequestError($response['message'] ?? 'Resend failed');
            
        } catch (Exception $e) {
            Log::error('SMSPool SMS resend failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Get active orders
     */
    public function getActiveOrders()
    {
        try {
            $response = $this->makeRequest('request/active', [], 'POST');
            
            if (is_array($response)) {
                return $response;
            }
            
            throw new RequestError('Invalid active orders response');
            
        } catch (Exception $e) {
            Log::error('SMSPool active orders fetch failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Get order history
     */
    public function getOrderHistory()
    {
        try {
            $response = $this->makeRequest('request/history', [], 'POST');
            
            if (is_array($response)) {
                return $response;
            }
            
            throw new RequestError('Invalid order history response');
            
        } catch (Exception $e) {
            Log::error('SMSPool order history fetch failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Check service availability
     * This method checks if a service is actually purchasable, not just listed
     */
    public function checkAvailability($serviceId, $countryId)
    {
        try {
            // First check if the service exists in the services list
            $services = $this->getServices($countryId);
            $serviceExists = false;
            
            foreach ($services as $service) {
                if ($service['ID'] == $serviceId || $service['name'] == $serviceId) {
                    $serviceExists = true;
                    break;
                }
            }
            
            if (!$serviceExists) {
                return false;
            }
            
            // Now check if the service is actually purchasable
            return $this->isServicePurchasable($serviceId, $countryId);
            
        } catch (Exception $e) {
            Log::error('SMSPool availability check failed', [
                'service_id' => $serviceId,
                'country_id' => $countryId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Make API request with retry logic
     */
    private function makeRequest($endpoint, $params = [], $method = 'GET', $retries = null)
    {
        $retries = $retries ?? $this->maxRetries;
        
        for ($i = 0; $i <= $retries; $i++) {
            try {
                return $this->executeRequest($endpoint, $params, $method);
            } catch (Exception $e) {
                if ($i === $retries) {
                    throw $e;
                }
                
                // Wait before retry
                usleep($this->retryDelay * 1000 * ($i + 1));
                
                Log::warning('SMSPool request retry', [
                    'endpoint' => $endpoint,
                    'attempt' => $i + 1,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    
    /**
     * Execute API request
     */
    private function executeRequest($endpoint, $params = [], $method = 'GET')
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        
        // Add API key to params
        $params['key'] = $this->apiKey;
        
        try {
            if ($method == 'POST') {
                $response = Http::timeout($this->timeout)
                    ->asForm()
                    ->post($url, $params);
            } else {
                $response = Http::timeout($this->timeout)
                    ->get($url, $params);
            }
            
            // Get response body regardless of status code
            $result = $response->json();
            
            // Log the request for debugging (without API key)
            Log::info('SMSPool API request', [
                'endpoint' => $endpoint,
                'method' => $method,
                'params' => array_diff_key($params, ['key' => '']),
                'status_code' => $response->status(),
                'response' => is_array($result) ? array_slice($result, 0, 5, true) : substr(json_encode($result), 0, 400)
            ]);
            
            if (!$response->successful()) {
                // For failed requests, try to get error details from response body
                $errorMessage = 'HTTP request failed: ' . $response->status();
                if ($result && isset($result['message'])) {
                    $errorMessage .= ' - ' . $result['message'];
                }
                
                // Log the full error response for debugging
                Log::error('SMSPool API error response', [
                    'endpoint' => $endpoint,
                    'status_code' => $response->status(),
                    'error_response' => $result,
                    'params' => array_diff_key($params, ['key' => ''])
                ]);
                
                throw new RequestError($errorMessage);
            }
            
            if (!$result) {
                throw new RequestError('Empty response from SMSPool API');
            }
            
            // Check for API errors
            if (isset($result['error'])) {
                throw new RequestError($result['error']);
            }
            
            return $result;
            
        } catch (Exception $e) {
            Log::error('SMSPool API request failed', [
                'endpoint' => $endpoint,
                'method' => $method,
                'params' => array_diff_key($params, ['key' => '']),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Execute API request specifically for purchase endpoint (returns response even on HTTP errors)
     */
    private function executeRequestForPurchase($endpoint, $params = [], $method = 'GET')
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        
        // Add API key to params
        $params['key'] = $this->apiKey;
        
        try {
            if ($method == 'POST') {
                // Use multipart form data for POST requests as shown in SMSPool documentation
                $multipartData = [];
                foreach ($params as $key => $value) {
                    $multipartData[] = [
                        'name' => $key,
                        'contents' => (string) $value
                    ];
                }
                
                $response = Http::timeout($this->timeout)
                    ->asMultipart()
                    ->post($url, $multipartData);
            } else {
                $response = Http::timeout($this->timeout)
                    ->get($url, $params);
            }
            
            // Get response body regardless of status code
            $result = $response->json();
            
            // Log the request for debugging (without API key)
            Log::info('SMSPool API request', [
                'endpoint' => $endpoint,
                'method' => $method,
                'params' => array_diff_key($params, ['key' => '']),
                'status_code' => $response->status(),
                'response' => $result
            ]);
            
            if (!$response->successful()) {
                // Log the full error response for debugging
                Log::error('SMSPool API error response', [
                    'endpoint' => $endpoint,
                    'status_code' => $response->status(),
                    'error_response' => $result,
                    'params' => array_diff_key($params, ['key' => ''])
                ]);
            }
            
            // For purchase endpoint, return the result even if HTTP status indicates error
            // This allows us to process the error details in the calling method
            return $result ?: [];
            
        } catch (Exception $e) {
            Log::error('SMSPool API request failed', [
                'endpoint' => $endpoint,
                'method' => $method,
                'params' => array_diff_key($params, ['key' => '']),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Parse status response from API
     */
    private function parseStatusResponse($status)
    {
        return self::STATUS_MAPPING[$status] ?? 'unknown';
    }
    
    /**
     * Get USA country from database
     */
    public function getUsaCountry()
    {
        return Country::where('code', self::USA_COUNTRY_CODE)->first();
    }
    
    /**
     * Get USA country code
     */
    public static function getUsaCountryCode()
    {
        return self::USA_COUNTRY_CODE;
    }
    
    /**
     * Get country by SMSPool ID
     */
    public function getCountryByCode($code)
    {
        return Country::where('code', $code)->first();
    }
    
    /**
     * Check if a country is supported
     */
    public function isCountrySupported($countryId)
    {
        return Country::where('code', $countryId)->exists();
    }
    
    /**
     * Validate service code
     */
    public function validateServiceCode($serviceCode)
    {
        try {
            $services = $this->getServices();
            
            foreach ($services as $service) {
                if ($service['ID'] == $serviceCode || $service['name'] == $serviceCode) {
                    return true;
                }
            }
            
            return false;
        } catch (Exception $e) {
            Log::error('SMSPool service validation failed', [
                'service_code' => $serviceCode,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get detailed pricing information for order creation
     */
    public function getPricingDetails($service, $country, $pricingService)
    {
        try {
            // Get the final price from PricingService
            $finalPrice = $pricingService->getServicePrice($service->id, $country->id);
            
            // Get API price in USD
            $apiPriceUsd = $this->getServicePrice($service->code, $country->code);
            
            // Get general settings for markup percentage
            $generalSettings = \App\Models\GeneralSetting::first();
            $markupPercentage = $generalSettings->api_price_markup_percentage ?? 20.00;
            
            return [
                'final_price' => $finalPrice,
                'api_price_usd' => $apiPriceUsd,
                'markup_percentage' => $markupPercentage
            ];
        } catch (Exception $e) {
            Log::error('Failed to get SMSPool pricing details', [
                'service_id' => $service->id,
                'country_id' => $country->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'final_price' => null,
                'api_price_usd' => null,
                'markup_percentage' => 20.00
            ];
        }
    }
}
