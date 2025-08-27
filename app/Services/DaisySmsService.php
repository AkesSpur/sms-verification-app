<?php

namespace App\Services;

use App\Lib\CurlRequest;
use App\Models\DaisyService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class DaisySmsService
{
    private $apiKey;
    private $baseUrl;
    private $timeout;
    private $logRequests;

    public function __construct()
    {
        $this->apiKey = config('services.daisysms.api_key');
        $this->baseUrl = config('services.daisysms.base_url', 'https://daisysms.com/stubs/handler_api.php');
        $this->timeout = config('services.daisysms.timeout', 30);
        $this->logRequests = config('services.daisysms.log_requests', true);
    }

    /**
     * Check if API key is configured
     */
    private function isApiKeyConfigured()
    {
        return !empty($this->apiKey);
    }

    /**
     * Get account balance
     */
    public function getBalance()
    {
        // Check if API key is configured
        if (!$this->isApiKeyConfigured()) {
            return [
                'success' => false,
                'error' => 'DaisySMS API key is not configured. Please contact administrator.'
            ];
        }

        try {
            $response = $this->makeRequest([
                'action' => 'getBalance'
            ]);

            if (strpos($response, 'ACCESS_BALANCE:') === 0) {
                return [
                    'success' => true,
                    'balance' => (float) str_replace('ACCESS_BALANCE:', '', $response)
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get balance: ' . $response
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'API Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Rent a phone number
     */
    public function rentNumber($serviceCode, $maxPrice = null, $areaCodes = null, $carriers = null, $specificNumber = null)
    {
        $requestId = uniqid('rent_');
        
        // Enhanced logging for rent request
        Log::info('SMS Rental Request Started', [
            'request_id' => $requestId,
            'service_code' => $serviceCode,
            'max_price' => $maxPrice,
            'area_codes' => $areaCodes,
            'carriers' => $carriers,
            'specific_number' => $specificNumber ? '[MASKED]' : null,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id() ?? 'system'
        ]);
        
        // Check if API key is configured
        if (!$this->isApiKeyConfigured()) {
            Log::error('SMS Rental Failed - API Key Not Configured', [
                'request_id' => $requestId,
                'service_code' => $serviceCode,
                'user_id' => auth()->id() ?? 'system'
            ]);
            
            return [
                'success' => false,
                'error' => 'DaisySMS API key is not configured. Please contact administrator.',
                'request_id' => $requestId
            ];
        }

        try {
            $params = [
                'action' => 'getNumber',
                'service' => $serviceCode
            ];

            if ($maxPrice) {
                $params['max_price'] = $maxPrice;
            }

            if ($areaCodes) {
                $params['areas'] = is_array($areaCodes) ? implode(',', $areaCodes) : $areaCodes;
            }

            if ($carriers) {
                $params['carriers'] = is_array($carriers) ? implode(',', $carriers) : $carriers;
            }

            if ($specificNumber) {
                $params['number'] = $specificNumber;
            }

            Log::info('Making API Request for Number Rental', [
                'request_id' => $requestId,
                'params' => array_merge($params, ['api_key' => '[HIDDEN]']),
                'timestamp' => now()->toISOString()
            ]);

            $response = $this->makeRequest($params);

            // Log the complete raw API response for observation
            Log::info('SMS Rental - RAW API RESPONSE', [
                'request_id' => $requestId,
                'service_code' => $serviceCode,
                'raw_response' => $response,
                'response_length' => strlen($response),
                'response_starts_with' => substr($response, 0, 50),
                'response_ends_with' => strlen($response) > 50 ? substr($response, -50) : '',
                'contains_access_number' => strpos($response, 'ACCESS_NUMBER:') !== false,
                'contains_error' => strpos($response, 'ERROR_') !== false || strpos($response, 'BAD_') !== false,
                'user_id' => auth()->id() ?? 'system',
                'timestamp' => now()->toISOString()
            ]);

            if (strpos($response, 'ACCESS_NUMBER:') === 0) {
                $parts = explode(':', $response);
                
                // Validate response format
                if (count($parts) < 3) {
                    Log::error('Invalid API Response Format for Number Rental', [
                        'request_id' => $requestId,
                        'response' => $response,
                        'parts_count' => count($parts),
                        'service_code' => $serviceCode
                    ]);
                    
                    return [
                        'success' => false,
                        'error' => 'Invalid response format from API',
                        'request_id' => $requestId
                    ];
                }
                
                $result = [
                    'success' => true,
                    'rental_id' => $parts[1],
                    'phone_number' => $parts[2],
                    'price' => $this->getLastRequestPrice(),
                    'request_id' => $requestId
                ];
                
                Log::info('SMS Rental Successful', [
                    'request_id' => $requestId,
                    'rental_id' => $parts[1],
                    'phone_number' => $parts[2],
                    'service_code' => $serviceCode,
                    'user_id' => auth()->id() ?? 'system',
                    'timestamp' => now()->toISOString()
                ]);
                
                return $result;
            }

            // Handle error responses
            $errorMessages = [
                'MAX_PRICE_EXCEEDED' => 'Maximum price exceeded',
                'NO_NUMBERS' => 'No numbers available',
                'TOO_MANY_ACTIVE_RENTALS' => 'Too many active rentals',
                'NO_MONEY' => 'Insufficient balance',
                'BAD_KEY' => 'Invalid API key',
                'BAD_SERVICE' => 'Invalid service code',
                'ERROR_SQL' => 'Database error on provider side',
                'NO_ACTIVATION' => 'No activation available'
            ];

            $errorMessage = $errorMessages[$response] ?? 'Unknown error: ' . $response;
            
            Log::warning('SMS Rental Failed - API Error', [
                'request_id' => $requestId,
                'service_code' => $serviceCode,
                'api_response' => $response,
                'error_message' => $errorMessage,
                'user_id' => auth()->id() ?? 'system',
                'timestamp' => now()->toISOString()
            ]);
            
            return [
                'success' => false,
                'error' => $errorMessage,
                'request_id' => $requestId
            ];
        } catch (Exception $e) {
            Log::error('SMS Rental Failed - Exception', [
                'request_id' => $requestId,
                'service_code' => $serviceCode,
                'exception_message' => $e->getMessage(),
                'exception_type' => get_class($e),
                'stack_trace' => $e->getTraceAsString(),
                'user_id' => auth()->id() ?? 'system',
                'timestamp' => now()->toISOString()
            ]);
            
            return [
                'success' => false,
                'error' => 'API Error: ' . $e->getMessage(),
                'request_id' => $requestId
            ];
        }
    }

    /**
     * Get SMS code for a rental
     */
    public function getCode($rentalId, $includeText = false)
    {
        $requestId = uniqid('code_');
        
        Log::info('SMS Code Request Started', [
            'request_id' => $requestId,
            'rental_id' => $rentalId,
            'include_text' => $includeText,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id() ?? 'system'
        ]);
        
        // Check if API key is configured
        if (!$this->isApiKeyConfigured()) {
            Log::error('SMS Code Request Failed - API Key Not Configured', [
                'request_id' => $requestId,
                'rental_id' => $rentalId,
                'user_id' => auth()->id() ?? 'system'
            ]);
            
            return [
                'success' => false,
                'error' => 'DaisySMS API key is not configured. Please contact administrator.',
                'request_id' => $requestId
            ];
        }

        try {
            $params = [
                'action' => 'getStatus',
                'id' => $rentalId
            ];

            if ($includeText) {
                $params['text'] = 1;
            }

            Log::info('Making API Request for SMS Code', [
                'request_id' => $requestId,
                'rental_id' => $rentalId,
                'params' => array_merge($params, ['api_key' => '[HIDDEN]']),
                'timestamp' => now()->toISOString()
            ]);

            $response = $this->makeRequest($params);

            if (strpos($response, 'STATUS_OK:') === 0) {
                $code = str_replace('STATUS_OK:', '', $response);
                
                // Validate code format
                if (empty($code) || !preg_match('/^[0-9]{4,8}$/', $code)) {
                    Log::warning('SMS Code Retrieved but Format Invalid', [
                        'request_id' => $requestId,
                        'rental_id' => $rentalId,
                        'code_length' => strlen($code),
                        'code_pattern' => preg_match('/^[0-9]+$/', $code) ? 'numeric' : 'non-numeric',
                        'user_id' => auth()->id() ?? 'system'
                    ]);
                }
                
                Log::info('SMS Code Retrieved Successfully', [
                    'request_id' => $requestId,
                    'rental_id' => $rentalId,
                    'code_length' => strlen($code),
                    'user_id' => auth()->id() ?? 'system',
                    'timestamp' => now()->toISOString()
                ]);
                
                return [
                    'success' => true,
                    'status' => 'completed',
                    'code' => $code,
                    'text' => $this->getLastRequestText(),
                    'request_id' => $requestId
                ];
            }

            $statusMessages = [
                'NO_ACTIVATION' => 'Invalid rental ID',
                'STATUS_WAIT_CODE' => 'waiting',
                'STATUS_CANCEL' => 'cancelled',
                'STATUS_ACCESS_READY' => 'waiting',
                'STATUS_ACCESS_ACTIVATION' => 'waiting'
            ];

            $status = $statusMessages[$response] ?? 'unknown';
            
            Log::info('SMS Code Request - Status Response', [
                'request_id' => $requestId,
                'rental_id' => $rentalId,
                'api_response' => $response,
                'status_message' => $status,
                'user_id' => auth()->id() ?? 'system',
                'timestamp' => now()->toISOString()
            ]);
            
            return [
                'success' => true,
                'status' => $status,
                'code' => null,
                'text' => null,
                'request_id' => $requestId
            ];
        } catch (Exception $e) {
            Log::error('SMS Code Request Failed - Exception', [
                'request_id' => $requestId,
                'rental_id' => $rentalId,
                'exception_message' => $e->getMessage(),
                'exception_type' => get_class($e),
                'stack_trace' => $e->getTraceAsString(),
                'user_id' => auth()->id() ?? 'system',
                'timestamp' => now()->toISOString()
            ]);
            
            return [
                'success' => false,
                'error' => 'API Error: ' . $e->getMessage(),
                'request_id' => $requestId
            ];
        }
    }

    /**
     * Cancel a rental
     */
    public function cancelRental($rentalId)
    {
        // Check if API key is configured
        if (!$this->isApiKeyConfigured()) {
            return [
                'success' => false,
                'error' => 'DaisySMS API key is not configured. Please contact administrator.'
            ];
        }

        try {
            $response = $this->makeRequest([
                'action' => 'setStatus',
                'id' => $rentalId,
                'status' => 8 // Cancel status
            ]);

            if (strpos($response, 'ACCESS_CANCEL') === 0) {
                return [
                    'success' => true,
                    'message' => 'Rental cancelled successfully'
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to cancel rental: ' . $response
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'API Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Mark rental as finished
     */
    public function finishRental($rentalId)
    {
        // Check if API key is configured
        if (!$this->isApiKeyConfigured()) {
            return [
                'success' => false,
                'error' => 'DaisySMS API key is not configured. Please contact administrator.'
            ];
        }

        try {
            $response = $this->makeRequest([
                'action' => 'setStatus',
                'id' => $rentalId,
                'status' => 6 // Finish status
            ]);

            if (strpos($response, 'ACCESS_ACTIVATION') === 0) {
                return [
                    'success' => true,
                    'message' => 'Rental finished successfully'
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to finish rental: ' . $response
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'API Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get available services from database
     */
    public function getServices($fromApi = false)
    {
        if ($fromApi) {
            return $this->getServicesFromApi();
        }
        
        // Get services from database with caching
        return Cache::remember('daisy_sms_services', 3600, function () {
            $services = DaisyService::active()->ordered()->get();
            
            $servicesList = [];
            foreach ($services as $service) {
                $servicesList[$service->code] = $service->name;
            }
            
            // Fallback to hardcoded services if database is empty
            if (empty($servicesList)) {
                Log::warning('No services found in database, using fallback services');
                return $this->getFallbackServices();
            }
            
            return $servicesList;
        });
    }
    
    /**
     * Get services from DaisySMS API
     */
    public function getServicesFromApi()
    {
        try {
            $response = $this->makeRequest([
                'action' => 'getServices'
            ]);
            
            // Parse API response
            $services = $this->parseServicesResponse($response);
            
            if (empty($services)) {
                Log::warning('No services returned from API, using fallback');
                return $this->getFallbackServices();
            }
            
            return $services;
            
        } catch (Exception $e) {
            Log::error('Failed to fetch services from API: ' . $e->getMessage());
            return $this->getFallbackServices();
        }
    }
    
    /**
     * Parse services response from API
     */
    private function parseServicesResponse($response)
    {
        $services = [];
        
        // Try JSON format first
        $jsonData = json_decode($response, true);
        if ($jsonData && is_array($jsonData)) {
            foreach ($jsonData as $code => $name) {
                $services[$code] = is_array($name) ? ($name['name'] ?? $code) : $name;
            }
            return $services;
        }
        
        // Try line-by-line format
        $lines = explode("\n", $response);
        foreach ($lines as $line) {
            $parts = explode(':', trim($line));
            if (count($parts) >= 2) {
                $services[trim($parts[0])] = trim($parts[1]);
            }
        }
        
        return $services;
    }
    
    /**
     * Get fallback services when API/database is unavailable
     */
    private function getFallbackServices()
    {
        return [
            'ds' => 'Discord',
            'tg' => 'Telegram',
            'wa' => 'WhatsApp',
            'ig' => 'Instagram',
            'fb' => 'Facebook',
            'tw' => 'Twitter',
            'go' => 'Google',
            'ya' => 'Yahoo',
            'vk' => 'VKontakte',
            'ok' => 'Odnoklassniki',
            'li' => 'LinkedIn',
            'vi' => 'Viber',
            'am' => 'Amazon',
            'ub' => 'Uber',
            'nf' => 'Netflix'
        ];
    }

    /**
     * Get available countries from API
     */
    public function getCountries($fromApi = false)
    {
        if ($fromApi) {
            return $this->fetchCountriesFromApi();
        }
        
        // Get countries from cache
        return Cache::remember('daisy_sms_countries', 3600, function () {
            try {
                return $this->fetchCountriesFromApi();
            } catch (Exception $e) {
                Log::warning('Failed to fetch countries from API, using fallback: ' . $e->getMessage());
                return $this->getFallbackCountries();
            }
        });
    }
    
    /**
     * Fetch countries from DaisySMS API
     */
    private function fetchCountriesFromApi()
    {
        try {
            $response = $this->makeRequest([
                'action' => 'getCountries'
            ]);
            
            $countries = $this->parseCountriesResponse($response);
            
            if (empty($countries)) {
                Log::warning('No countries returned from API, using fallback');
                return $this->getFallbackCountries();
            }
            
            return $countries;
            
        } catch (Exception $e) {
            Log::error('Failed to fetch countries from API: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Parse countries response from API
     */
    private function parseCountriesResponse($response)
    {
        $countries = [];
        
        // Try JSON format first
        $jsonData = json_decode($response, true);
        if ($jsonData && is_array($jsonData)) {
            foreach ($jsonData as $code => $name) {
                $countries[$code] = is_array($name) ? ($name['name'] ?? $code) : $name;
            }
            return $countries;
        }
        
        // Try line-by-line format
        $lines = explode("\n", $response);
        foreach ($lines as $line) {
            $parts = explode(':', trim($line));
            if (count($parts) >= 2) {
                $countries[trim($parts[0])] = trim($parts[1]);
            }
        }
        
        return $countries;
    }
    
    /**
     * Get fallback countries when API is unavailable
     */
    private function getFallbackCountries()
    {
        return [
            'ng' => 'Nigeria',
            'us' => 'United States',
            'uk' => 'United Kingdom',
            'ca' => 'Canada',
            'au' => 'Australia',
            'de' => 'Germany',
            'fr' => 'France',
            'it' => 'Italy',
            'es' => 'Spain',
            'nl' => 'Netherlands',
            'be' => 'Belgium',
            'ch' => 'Switzerland',
            'at' => 'Austria',
            'se' => 'Sweden',
            'no' => 'Norway'
        ];
    }

    /**
     * Make HTTP request to DaisySMS API with enhanced logging
     */
    private function makeRequest($params)
    {
        // Ensure API key is available
        if (empty($this->apiKey)) {
            throw new Exception('API key is not configured');
        }

        $requestId = uniqid('req_');
        $params['api_key'] = $this->apiKey;
        
        $url = $this->baseUrl . '?' . http_build_query($params);
        
        // Enhanced request logging
        if ($this->logRequests) {
            Log::info('DaisySMS API Request Started', [
                'request_id' => $requestId,
                'action' => $params['action'] ?? 'unknown',
                'url' => $this->baseUrl,
                'params' => array_merge($params, ['api_key' => '[HIDDEN]']),
                'full_url_length' => strlen($url),
                'timestamp' => now()->toISOString(),
                'memory_usage' => memory_get_usage(true)
            ]);
        }
        
        $startTime = microtime(true);
        
        try {
            $curl = new CurlRequest();
            $response = $curl->curlContent($url);
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            if (!$response) {
                throw new Exception('No response from API - empty response body');
            }
            
            $trimmedResponse = trim($response);
            $responseLength = strlen($trimmedResponse);
            
            // Enhanced response logging
            if ($this->logRequests) {
                $logData = [
                    'request_id' => $requestId,
                    'action' => $params['action'] ?? 'unknown',
                    'response_length' => $responseLength,
                    'response_time_ms' => $responseTime,
                    'timestamp' => now()->toISOString(),
                    'memory_usage' => memory_get_usage(true)
                ];
                
                // Log full response for debugging (truncate if too long)
                if ($responseLength <= 1000) {
                    $logData['response'] = $trimmedResponse;
                } else {
                    $logData['response_preview'] = substr($trimmedResponse, 0, 500) . '... [TRUNCATED]';
                    $logData['response_end'] = '... [TRUNCATED] ' . substr($trimmedResponse, -200);
                }
                
                // Detect response type for better debugging
                if (strpos($trimmedResponse, 'ACCESS_') === 0) {
                    $logData['response_type'] = 'success';
                } elseif (strpos($trimmedResponse, 'ERROR_') === 0 || strpos($trimmedResponse, 'BAD_') === 0) {
                    $logData['response_type'] = 'error';
                } elseif (json_decode($trimmedResponse) !== null) {
                    $logData['response_type'] = 'json';
                } else {
                    $logData['response_type'] = 'text';
                }
                
                Log::info('DaisySMS API Response Received', $logData);
            }
            
            return $trimmedResponse;
            
        } catch (Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            // Enhanced error logging
            Log::error('DaisySMS API Request Failed', [
                'request_id' => $requestId,
                'action' => $params['action'] ?? 'unknown',
                'error_message' => $e->getMessage(),
                'error_type' => get_class($e),
                'params' => array_merge($params, ['api_key' => '[HIDDEN]']),
                'response_time_ms' => $responseTime,
                'timestamp' => now()->toISOString(),
                'memory_usage' => memory_get_usage(true),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Get service prices from DaisySMS API
     */
    public function getServicePrices($serviceCode, $countryCode = 'US')
    {
        // Check if API key is configured
        if (!$this->isApiKeyConfigured()) {
            return [
                'success' => false,
                'error' => 'DaisySMS API key is not configured. Please contact administrator.'
            ];
        }

        try {
            $response = $this->makeRequest([
                'action' => 'getPrices',
                'service' => $serviceCode
            ]);

            // Parse the response for all countries, passing the specific service code
            $prices = $this->parsePricesResponse($response, $serviceCode);
            
            if (empty($prices)) {
                return [
                    'success' => false,
                    'error' => 'No prices available for this service'
                ];
            }

            return [
                'success' => true,
                'prices' => $prices
            ];
            
        } catch (Exception $e) {
            Log::error('Failed to fetch prices from DaisySMS API: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'API Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Parse prices response from API
     */
    private function parsePricesResponse($response, $requestedServiceCode = null)
    {
        $prices = [];
        
        // Try JSON format first
        $jsonData = json_decode($response, true);
        if ($jsonData && is_array($jsonData)) {
            // Handle nested structure: country -> services -> service details
            foreach ($jsonData as $countryCode => $countryServices) {
                if (is_array($countryServices)) {
                    // Look for the specific service code requested
                    foreach ($countryServices as $serviceCode => $serviceData) {
                        // If we have a specific service code to match, only process that one
                        if ($requestedServiceCode && $serviceCode !== $requestedServiceCode) {
                            continue;
                        }
                        
                        if (isset($serviceData['cost'])) {
                            $cost = floatval($serviceData['cost']);
                            $available = isset($serviceData['count']) && intval($serviceData['count']) > 0;
                            
                            // Use the cost directly as price_usd (this is the actual service price)
                            $prices[$countryCode] = [
                                'price_usd' => $cost,
                                'available' => $available
                            ];
                            
                            // If we found the specific service, we can break
                            if ($requestedServiceCode) {
                                break;
                            }
                        }
                    }
                }
            }
            return $prices;
        }
        
        // Try line-by-line format: country:price:available
        $lines = explode("\n", $response);
        foreach ($lines as $line) {
            $parts = explode(':', trim($line));
            if (count($parts) >= 2) {
                $country = trim($parts[0]);
                $price = floatval(trim($parts[1]));
                $available = isset($parts[2]) ? (bool)trim($parts[2]) : true;
                
                $prices[$country] = [
                    'price_usd' => $price,
                    'available' => $available
                ];
            }
        }
        
        return $prices;
    }

    /**
     * Get all service prices from DaisySMS API in a single call
     * This is more efficient than calling getServicePrices for each service individually
     */
    public function getAllServicePrices()
    {
        // Check if API key is configured
        if (!$this->isApiKeyConfigured()) {
            return [
                'success' => false,
                'error' => 'DaisySMS API key is not configured. Please contact administrator.'
            ];
        }

        try {
            // Make a single API call to get prices for all services
            // Based on the logs, we can see that getPrices without a specific service returns all services
            $response = $this->makeRequest([
                'action' => 'getPrices'
            ]);

            // Parse the response to get all services and their prices
            $allPrices = $this->parseAllPricesResponse($response);
            
            if (empty($allPrices)) {
                return [
                    'success' => false,
                    'error' => 'No prices available from API'
                ];
            }

            return [
                'success' => true,
                'prices' => $allPrices
            ];
            
        } catch (Exception $e) {
            Log::error('Failed to fetch all prices from DaisySMS API: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'API Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Parse all prices response from API
     * Returns array with service_code => country_prices structure
     */
    private function parseAllPricesResponse($response)
    {
        $allPrices = [];
        
        // Try JSON format first
        $jsonData = json_decode($response, true);
        if ($jsonData && is_array($jsonData)) {
            // Handle nested structure: country -> services -> service details
            foreach ($jsonData as $countryCode => $countryServices) {
                if (is_array($countryServices)) {
                    foreach ($countryServices as $serviceCode => $serviceData) {
                        if (isset($serviceData['cost'])) {
                            $cost = floatval($serviceData['cost']);
                            $available = isset($serviceData['count']) && intval($serviceData['count']) > 0;
                            
                            // Initialize service array if not exists
                            if (!isset($allPrices[$serviceCode])) {
                                $allPrices[$serviceCode] = [];
                            }
                            
                            // Store price for this service and country
                            $allPrices[$serviceCode][$countryCode] = [
                                'price_usd' => $cost,
                                'available' => $available
                            ];
                        }
                    }
                }
            }
        }
        
        return $allPrices;
    }

    /**
     * Get price from last request headers (if available)
     */
    private function getLastRequestPrice()
    {
        // This would need to be implemented to capture X-Price header
        // For now, return null
        return null;
    }

    /**
     * Get SMS text from last request headers (if available)
     */
    private function getLastRequestText()
    {
        // This would need to be implemented to capture X-Text header
        // For now, return null
        return null;
    }
}