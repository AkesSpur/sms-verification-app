<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Order;
use App\Models\Service;
use App\Models\Country;
use App\Models\BlacklistedNumber;
use App\Models\Transaction;
use App\Models\User;
use App\Exceptions\RequestError;
use Exception;
use Carbon\Carbon;

class SmsActivateService
{
    private $apiKey;
    private $baseUrl;
    private $timeout;
    private $maxRetries;
    private $retryDelay;
    
    // USA country code for SMSActivate
    private const USA_COUNTRY_CODE = 187;
    private const DEFAULT_TIMEOUT = 10; // Reduced from 30 to prevent timeouts
    private const DEFAULT_RETRY_DELAY = 500; // Reduced from 1000 milliseconds
    
    private const ERROR_CODES = [
        'NO_NUMBERS', 'NO_BALANCE', 'BAD_ACTION', 'BAD_SERVICE', 
        'BAD_KEY', 'ERROR_SQL', 'NO_ACTIVATION', 'BAD_STATUS'
    ];
    
    // Status mappings
    private const STATUS_MAPPING = [
        'STATUS_WAIT_CODE' => 'pending',
        'STATUS_WAIT_RETRY' => 'pending',
        'STATUS_WAIT_RESEND' => 'pending',
        'STATUS_OK' => 'completed',
        'STATUS_CANCEL' => 'cancelled',
        'ACCESS_CANCEL' => 'cancelled',
        'STATUS_WAIT_PHONE_RETRY' => 'pending',
    ];
    
    public function __construct()
    {
        $this->apiKey = config('services.smsactivate.api_key');
        $this->baseUrl = config('services.smsactivate.base_url');
        $this->timeout = config('services.smsactivate.timeout', self::DEFAULT_TIMEOUT);
        $this->maxRetries = config('services.smsactivate.max_retries', 2);
        $this->retryDelay = config('services.smsactivate.retry_delay', self::DEFAULT_RETRY_DELAY);
        
        if (!$this->apiKey) {
            throw new RequestError('BAD_KEY');
        }
    }

    public function getNumber($service, $country, $forward = 0, $operator = null, $ref = null)
    {
        $requestParam = [
            'service' => $service,
            'forward' => $forward,
        ];

        if ($country) {
            $requestParam['country'] = $country;
        }
        if ($operator && ($country == 0 || $country == 1 || $country == 2)) {
            $requestParam['operator'] = $operator;
        }
        if ($ref) {
            $requestParam['ref'] = $ref;
        }

        return $this->makeRequest('getNumber', $requestParam, null, 1);
    }

    public function getStatus($id)
{
    $response = $this->makeRequest('getStatus', [
        'id' => $id,
    ], null, 2);

    // Log the raw response for debugging
    Log::info('SMS Activate API Response: ', ['response' => $response]);

    return $response;
}

    public function setStatus($id, $status, $forward = 0)
    {
        $requestParam = [
            'id' => $id,
            'status' => $status,
        ];

        if ($forward) {
            $requestParam['forward'] = $forward;
        }

        return $this->makeRequest('setStatus', $requestParam, null, 3);
    }



    /**
     * Get prices for services in a specific country.
     */
    public function getPrices($country = null, $service = null)
    {
        $requestParam = [];

        if ($country !== null) {
            $requestParam['country'] = $country;
        }
        if ($service !== null) {
            $requestParam['service'] = $service;
        }

        return $this->makeRequest('getPrices', $requestParam, true);
    }

    /**
     * Get available countries.
     */
    public function getCountries()
    {
        return $this->makeRequest('getCountries', [], true);
    }
    
    /**
     * Get the balance from SMS Activate API
     */
    public function getBalance()
    {
        try {
            $response = $this->makeRequest('getBalance');
            
            if (strpos($response, 'ACCESS_BALANCE:') === 0) {
                return (float) str_replace('ACCESS_BALANCE:', '', $response);
            }
            
            throw new RequestError('ERROR_SQL');
        } catch (Exception $e) {
            Log::error('SMS Activate balance check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Check service availability for numbers
     */
    public function checkAvailability($serviceCode, $countryCode = null)
    {
        try {
            $countryCode = $countryCode ?? self::USA_COUNTRY_CODE;
            $cacheKey = "sms_availability_{$serviceCode}_{$countryCode}";
            
            return Cache::remember($cacheKey, 60, function() use ($serviceCode, $countryCode) {
                $response = $this->makeRequest('getNumbersStatus', [
                    'country' => $countryCode
                ]);
                
                // Handle direct JSON response (new format)
                if (is_string($response) && $this->isJson($response)) {
                    $data = json_decode($response, true);
                    
                    if (json_last_error() === JSON_ERROR_NONE && isset($data[$serviceCode])) {
                        $count = (int) $data[$serviceCode];
                        
                        Log::info('SMS Activate availability check', [
                            'service_code' => $serviceCode,
                            'country_code' => $countryCode,
                            'numbers_available' => $count,
                            'is_available' => $count > 0
                        ]);
                        
                        try {
                            $price = $this->getServicePrice($serviceCode, $countryCode);
                        } catch (Exception $e) {
                            Log::warning('Price unavailable for availability check', [
                                'service_code' => $serviceCode,
                                'country_code' => $countryCode,
                                'error' => $e->getMessage()
                            ]);
                            $price = null;
                        }
                        
                        return [
                            'available' => $count > 0,
                            'count' => $count,
                            'price' => $price
                        ];
                    }
                }
                // Handle legacy ACCESS_NUMBER_STATUS format
                elseif (is_string($response) && strpos($response, 'ACCESS_NUMBER_STATUS:') === 0) {
                    $jsonData = str_replace('ACCESS_NUMBER_STATUS:', '', $response);
                    $data = json_decode($jsonData, true);
                    
                    if (json_last_error() === JSON_ERROR_NONE) {
                        // Try direct service code access first (new format)
                        if (isset($data[$serviceCode])) {
                            $count = (int) $data[$serviceCode];
                            
                            Log::info('SMS Activate availability check (legacy direct)', [
                                'service_code' => $serviceCode,
                                'country_code' => $countryCode,
                                'numbers_available' => $count,
                                'is_available' => $count > 0
                            ]);
                            
                            try {
                                $price = $this->getServicePrice($serviceCode, $countryCode);
                            } catch (Exception $e) {
                                Log::warning('Price unavailable for availability check (legacy direct)', [
                                    'service_code' => $serviceCode,
                                    'country_code' => $countryCode,
                                    'error' => $e->getMessage()
                                ]);
                                $price = null;
                            }
                            
                            return [
                                'available' => $count > 0,
                                'count' => $count,
                                'price' => $price
                            ];
                        }
                        // Fallback to nested format (legacy)
                        elseif (isset($data[$countryCode][$serviceCode])) {
                            $count = (int) $data[$countryCode][$serviceCode];
                            
                            Log::info('SMS Activate availability check (legacy nested)', [
                                'service_code' => $serviceCode,
                                'country_code' => $countryCode,
                                'numbers_available' => $count,
                                'is_available' => $count > 0
                            ]);
                            
                            try {
                                $price = $this->getServicePrice($serviceCode, $countryCode);
                            } catch (Exception $e) {
                                Log::warning('Price unavailable for availability check (legacy nested)', [
                                    'service_code' => $serviceCode,
                                    'country_code' => $countryCode,
                                    'error' => $e->getMessage()
                                ]);
                                $price = null;
                            }
                            
                            return [
                                'available' => $count > 0,
                                'count' => $count,
                                'price' => $price
                            ];
                        }
                    }
                }
                
                return ['available' => false, 'count' => 0, 'price' => 0];
            });
        } catch (Exception $e) {
            Log::error('SMS Activate availability check failed', [
                'service' => $serviceCode,
                'country_code' => $countryCode,
                'error' => $e->getMessage()
            ]);
            return ['available' => false, 'count' => 0, 'price' => 0];
        }
    }
    
    /**
     * Get service price for a specific country
     */
    public function getServicePrice($serviceCode, $countryCode = null)
    {
        try {
            $countryCode = $countryCode ?? self::USA_COUNTRY_CODE;
            $cacheKey = "sms_price_{$serviceCode}_{$countryCode}";
            
            return Cache::remember($cacheKey, 3600, function() use ($serviceCode, $countryCode) {
                $response = $this->makeRequest('getPrices', [
                    'country' => $countryCode,
                    'service' => $serviceCode
                ]);
                
                if (strpos($response, 'ACCESS_PRICES:') == 0) {
                    $data = json_decode(str_replace('ACCESS_PRICES:', '', $response), true);
                    
                    if (isset($data[$countryCode][$serviceCode]['cost'])) {
                        return (float) $data[$countryCode][$serviceCode]['cost'];
                    }
                }
                
                // If we reach here, the API didn't return a valid price
                throw new \Exception("Price not available from API for service {$serviceCode} in country {$countryCode}");
            });
        } catch (Exception $e) {
            Log::error('SMS Activate price check failed', [
                'service' => $serviceCode,
                'country_code' => $countryCode,
                'error' => $e->getMessage()
            ]);
            throw $e; // Re-throw the exception instead of returning default price
        }
    }
     
     /**
     * Purchase a number for a specific service and country
     */
    public function purchaseNumber($serviceCode, $userId, $countryCode = null, $priceInNaira = null, $orderSource = 'web')
    {
        try {
            $countryCode = $countryCode ?? self::USA_COUNTRY_CODE;
            
            // Check user balance first
            $user = User::find($userId);
            if (!$user) {
                throw new RequestError('User not found');
            }
            
            // Verify country is supported
            $country = $this->getCountryByCode($countryCode);
            if (!$country) {
                throw new RequestError('Country not supported');
            }
            
            // Find service by code
            $service = Service::where('code', $serviceCode)->first();
            if (!$service) {
                throw new RequestError('Service not found');
            }
            
            // Get pricing details from PricingService
            $pricingService = app(PricingService::class);
            $pricingDetails = $this->getPricingDetails($service, $country, $pricingService);
            
            // Use provided price in Naira or calculated price
            $finalPriceInNaira = $priceInNaira ?? $pricingDetails['final_price'];
            
            if (!$finalPriceInNaira) {
                Log::error('Failed to get service price for purchase', [
                    'service_code' => $serviceCode,
                    'country_code' => $countryCode
                ]);
                throw new RequestError('Service pricing not available');
            }
            
            if ($user->balance < $finalPriceInNaira) {
                throw new RequestError('Insufficient balance');
            }
             
             // Check availability
             $availability = $this->checkAvailability($serviceCode, $countryCode);
             if (!$availability['available']) {
                 throw new RequestError('Service not available');
             }
             
             Log::info('SMS Activate purchase request', [
                 'service_code' => $serviceCode,
                 'country_code' => $countryCode,
                 'user_id' => $userId,
                 'pricing_details' => $pricingDetails
             ]);
             
             // Purchase number from SMSActivate (without max price restriction)
             $response = $this->makeRequest('getNumber', [
                 'service' => $serviceCode,
                 'country' => $countryCode
             ], null, 1);
             
             // Response is already parsed as array with 'id' and 'number' keys
             if (is_array($response) && isset($response['id'], $response['number'])) {
                 $activationId = $response['id'];
                 $phoneNumber = $response['number'];
                 
                 // Check if number is blacklisted
                 if (BlacklistedNumber::where('number', $phoneNumber)->exists()) {
                     // Cancel the activation and try again
                     $this->cancelActivation($activationId);
                     throw new RequestError('Number is blacklisted');
                 }
                 
                 // Create order record with enhanced pricing details
                 $order = Order::create([
                     'user_id' => $userId,
                     'service_id' => $service->id,
                     'country_id' => $country->id,
                     'phone_number' => $phoneNumber,
                     'activation_id' => $activationId,
                     'price' => $finalPriceInNaira, // Final price charged to user
                     'api_price' => $pricingDetails['api_price_usd'], // Original API price in USD
                     'markup_percentage' => $pricingDetails['markup_percentage'], // Markup applied
                     'final_price' => $finalPriceInNaira, // Same as price for consistency
                     'api_response' => json_encode($response), // Store API response
                     'order_source' => $orderSource, // Source of the order
                     'status' => 'pending',
                     'expires_at' => now()->addMinutes(50),
                     'sms_window_expires_at' => now()->addMinutes(20)
                 ]);
                 
                 // Log transaction and deduct balance
                 Transaction::createTransaction(
                     $user,
                     'debit',
                     'sms_purchase',
                     $finalPriceInNaira,
                     "SMS verification for {$serviceCode} ({$country->name})",
                     ['service_code' => $serviceCode, 'country_code' => $countryCode],
                     $order
                 );
                 
                 // Update user balance
                 $user->decrement('balance', $finalPriceInNaira);
                 
                 return [
                     'success' => true,
                     'order' => $order,
                     'phone_number' => $phoneNumber,
                     'activation_id' => $activationId,
                     'country' => $country
                 ];
             }
             
             // Handle API errors
             $this->handleApiError($response);
             
         } catch (RequestError $e) {
             throw $e;
         } catch (Exception $e) {
             Log::error('SMS Activate purchase failed', [
                 'service' => $serviceCode,
                 'country_code' => $countryCode,
                 'user_id' => $userId,
                 'error' => $e->getMessage()
             ]);
             throw new RequestError('Failed to purchase number');
         }
     }
     
     /**
      * Check SMS status for an activation
      */
     public function checkSmsStatus($activationId)
     {
         try {
             $response = $this->makeRequest('getStatus', [
                 'id' => $activationId
             ], null, 2);
             
             // Response is already parsed as array with 'status' and 'code' keys
             if (is_array($response)) {
                 $status = $response['status'];
                 $code = $response['code'] ?? null;
                 
                 if ($status === 'STATUS_OK' && $code) {
                     return [
                         'status' => 'completed',
                         'sms_code' => $code,
                         'message' => 'SMS code received'
                     ];
                 }
                 
                 return [
                     'status' => $this->parseStatusResponse($status),
                     'sms_code' => $code,
                     'message' => $this->getStatusMessage($status)
                 ];
             }
             
         } catch (Exception $e) {
             Log::error('SMS Activate status check failed', [
                 'activation_id' => $activationId,
                 'error' => $e->getMessage()
             ]);
             throw $e;
         }
     }
     
     /**
      * Cancel a number activation
      */
     public function cancelNumber($activationId)
     {
         try {
             $response = $this->makeRequest('setStatus', [
                 'id' => $activationId,
                 'status' => 8 // Cancel status
             ], null, 3);
             
             // Response is already parsed as array with 'status' key
             $status = is_array($response) ? $response['status'] : $response;
             
             if ($status === 'ACCESS_CANCEL') {
                 return [
                     'success' => true,
                     'message' => 'Number cancelled successfully'
                 ];
             }
             
             throw new RequestError($response);
             
         } catch (Exception $e) {
             Log::error('SMS Activate number cancellation failed', [
                 'activation_id' => $activationId,
                 'error' => $e->getMessage()
             ]);
             throw $e;
         }
     }
     
     /**
      * Request SMS retry for an activation
      */
     public function requestSmsRetry($activationId)
     {
         try {
             $response = $this->makeRequest('setStatus', [
                 'id' => $activationId,
                 'status' => 3 // Request new SMS
             ], null, 3);
             
             // Response is already parsed as array with 'status' key
             $status = is_array($response) ? $response['status'] : $response;
             
             if ($status === 'ACCESS_RETRY_GET') {
                 return [
                     'success' => true,
                     'message' => 'SMS retry requested successfully'
                 ];
             }
             
             throw new RequestError($response);
             
         } catch (Exception $e) {
             Log::error('SMS Activate retry request failed', [
                 'activation_id' => $activationId,
                 'error' => $e->getMessage()
             ]);
             throw $e;
         }
     }
     
     /**
      * Make API request with retry logic
      */
     private function makeRequestWithRetry($action, $params = [], $retries = null)
     {
         $retries = $retries ?? $this->maxRetries;
         
         for ($i = 0; $i <= $retries; $i++) {
             try {
                 return $this->makeRequest($action, $params);
             } catch (Exception $e) {
                 if ($i === $retries) {
                     throw $e;
                 }
                 
                 // Wait before retry
                 usleep($this->retryDelay * 1000 * ($i + 1));
                 
                 Log::warning('SMS Activate request retry', [
                     'action' => $action,
                     'attempt' => $i + 1,
                     'error' => $e->getMessage()
                 ]);
             }
         }
     }
     
     /**
      * Enhanced makeRequest method
      */
     private function makeRequest($action, $params = [], $parseAsJSON = null, $getNumber = null)
     {
         $requestParams = array_merge([
             'api_key' => $this->apiKey,
             'action' => $action
         ], $params);
         
         try {
             $response = Http::timeout($this->timeout)
                 ->get($this->baseUrl, $requestParams);
             
             if (!$response->successful()) {
                 throw new RequestError('NO_CONNECTION');
             }
             
             $result = trim($response->body());
             
             // Log the request for debugging (without API key)
             Log::info('SMS Activate API request', [
                 'action' => $action,
                 'params' => array_diff_key($params, ['api_key' => '']),
                 'response' => substr($result, 0, 400)
             ]);
             
             if (!$result) {
                 throw new RequestError('Empty response from SMS Activate API');
             }
             
             // Handle specific cases for getNumber
             if ($getNumber == 1) {
                 // Check for error responses first
                 if (in_array($result, self::ERROR_CODES)) {
                     throw new RequestError($result);
                 }
                 
                 if (strpos($result, 'ACCESS_NUMBER:') !== false) {
                     $parsedResponse = explode(':', $result);
                     if (count($parsedResponse) >= 3) {
                         return ['id' => $parsedResponse[1], 'number' => $parsedResponse[2]];
                     }
                 }
                 throw new RequestError("Invalid response format: $result");
             }
             
             if ($getNumber == 2) {
                 // Check for error responses first
                 if (in_array($result, self::ERROR_CODES)) {
                     throw new RequestError($result);
                 }
                 
                 $parsedResponse = explode(':', $result);
                 if (count($parsedResponse) >= 2) {
                     return ['status' => $parsedResponse[0], 'code' => $parsedResponse[1]];
                 }
                 return ['status' => $parsedResponse[0], 'code' => null];
             }
             
             if ($getNumber == 3) {
                 // Check for error responses first
                 if (in_array($result, self::ERROR_CODES)) {
                     throw new RequestError($result);
                 }
                 
                 $parsedResponse = explode(':', $result);
                 return ['status' => $parsedResponse[0]];
             }
             
             // Parse as JSON if requested
             if ($parseAsJSON) {
                 $decoded = json_decode($result, true);
                 if (json_last_error() === JSON_ERROR_NONE) {
                     return $decoded;
                 }
                 // If JSON parsing fails, return raw result
             }
             
             return $result;
             
         } catch (Exception $e) {
             Log::error('SMS Activate API request failed', [
                 'action' => $action,
                 'params' => array_diff_key($params, ['api_key' => '']),
                 'error' => $e->getMessage()
             ]);
             throw $e;
         }
     }
     
     /**
      * Parse status response from API
      */
     private function parseStatusResponse($response)
     {
         return self::STATUS_MAPPING[$response] ?? 'unknown';
     }
     
     /**
      * Cancel an activation
      */
     private function cancelActivation($activationId)
     {
         try {
             $response = $this->makeRequest('setStatus', [
                 'id' => $activationId,
                 'status' => 8 // Cancel status
             ], null, 3);
             
             Log::info('Activation cancelled', [
                 'activation_id' => $activationId,
                 'response' => $response
             ]);
             
             $status = is_array($response) ? $response['status'] : $response;
             return $status === 'ACCESS_CANCEL';
             
         } catch (Exception $e) {
             Log::error('Failed to cancel activation', [
                 'activation_id' => $activationId,
                 'error' => $e->getMessage()
             ]);
             return false;
         }
     }
     
     /**
      * Handle API errors based on response
      */
     private function handleApiError($response)
     {
         Log::error('SMS Activate API Error', [
             'response' => $response
         ]);
         
         // Use the response code directly as RequestError expects only one parameter
         throw new RequestError($response);
     }
     
     /**
      * Get human-readable status message
      */
     private function getStatusMessage($response)
     {
         $messages = [
             'STATUS_WAIT_CODE' => 'Waiting for SMS code',
             'STATUS_WAIT_RETRY' => 'Waiting for SMS retry',
             'STATUS_WAIT_RESEND' => 'Waiting for SMS resend',
             'STATUS_OK' => 'SMS code received',
             'STATUS_CANCEL' => 'Activation cancelled',
             'ACCESS_CANCEL' => 'Activation cancelled',
             'STATUS_WAIT_PHONE_RETRY' => 'Waiting for phone retry',
         ];
         
         return $messages[$response] ?? 'Unknown status: ' . $response;
     }
     
     /**
      * Check if a string is valid JSON
      */
     private function isJson($string)
     {
         if (!is_string($string)) {
             return false;
         }
         
         json_decode($string);
         return json_last_error() === JSON_ERROR_NONE;
     }
     
     /**
      * Handle error responses from API
      */
     private function handleErrorResponse($response)
     {
         $errorMessages = [
             'NO_NUMBERS' => 'No numbers available for this service',
             'NO_BALANCE' => 'Insufficient balance in SMS Activate account',
             'BAD_ACTION' => 'Invalid API action',
             'BAD_SERVICE' => 'Invalid service code',
             'BAD_KEY' => 'Invalid API key',
             'ERROR_SQL' => 'Database error on SMS Activate side',
             'NO_ACTIVATION' => 'Activation not found',
             'BAD_STATUS' => 'Invalid status for this activation',
         ];
         
         throw new RequestError($response);
     }
     
     /**
      * Validate service code
      */
     public function validateServiceCode($serviceCode)
     {
         $validServices = [
             'wa', 'tg', 'dc', 'ig', 'fb', 'tw', 'go', 'vi', 'wb', 'ot'
         ];
         
         return in_array($serviceCode, $validServices);
     }
     
     /**
      * Get all available services for a specific country
      */
     public function getAvailableServices($countryCode = null)
     {
         try {
             $countryCode = $countryCode ?? self::USA_COUNTRY_CODE;
             $cacheKey = "sms_services_{$countryCode}";
             
             return Cache::remember($cacheKey, 1800, function() use ($countryCode) {
                 $response = $this->makeRequest('getNumbersStatus', [
                     'country' => $countryCode
                 ]);
                 
                 if (strpos($response, 'ACCESS_NUMBER_STATUS:') === 0) {
                     $jsonData = str_replace('ACCESS_NUMBER_STATUS:', '', $response);
                     $data = json_decode($jsonData, true);
                     
                     if (json_last_error() === JSON_ERROR_NONE && isset($data[$countryCode])) {
                         return array_keys($data[$countryCode]);
                     }
                 }
                 
                 return [];
             });
         } catch (Exception $e) {
             Log::error('Failed to get available services', [
                 'country_code' => $countryCode,
                 'error' => $e->getMessage()
             ]);
             return [];
          }
      }
      
      /**
       * Get country by name or code
       */
      public function getCountryByCode($code)
      {
          return Country::where('code', $code)->first();
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
       * Check if a country is supported
       */
      public function isCountrySupported($countryCode)
      {
          return Country::where('code', $countryCode)->exists();
      }
      
      /**
       * Get detailed pricing information for order creation
       */
      private function getPricingDetails($service, $country, $pricingService)
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
              Log::error('Failed to get pricing details', [
                  'service_code' => $service->code,
                  'country_code' => $country->code,
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