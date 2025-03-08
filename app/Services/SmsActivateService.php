<?php 

namespace App\Services;

use App\Exceptions\RequestError;
use Exception;
use InvalidArgumentException;
use App\Exceptions\ErrorCodes;
use Illuminate\Support\Facades\Log;

class SmsActivateService
{
    private $url = 'https://api.sms-activate.ae/stubs/handler_api.php'; // Updated endpoint
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = env('SMS_ACTIVATE_API_KEY'); // Fetch API key from .env
    }

    public function getNumber($service, $country, $forward = 0, $operator = null, $ref = null)
    {
        $requestParam = [
            'api_key' => $this->apiKey,
            'action' => 'getNumber',
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

        return $this->request($requestParam, 'POST', null, 1);
    }

    public function getStatus($id)
{
    $response = $this->request([
        'api_key' => $this->apiKey,
        'action' => 'getStatus',
        'id' => $id,
    ], 'GET', false, 2);

    // Log the raw response for debugging
    Log::info('SMS Activate API Response: ', ['response' => $response]);

    // Check if the response is a JSON object
    if (is_string($response) && strpos($response, '{') === 0) {
        $response = json_decode($response, true); // Decode JSON response
        if (json_last_error() === JSON_ERROR_NONE) {
            return $response; // Return the decoded JSON response
        }
    }

    // Handle colon-separated response (e.g., STATUS_OK:123456)
    if (is_string($response)) {
        $parsedResponse = explode(':', $response);
        if (count($parsedResponse) >= 2) {
            return ['status' => $parsedResponse[0], 'code' => $parsedResponse[1]];
        } else {
            return ['status' => $parsedResponse[0]];
        }
    }

    // Handle unexpected response format
    throw new RequestError("Invalid response format: " . print_r($response, true));
}

    public function setStatus($id, $status, $forward = 0)
    {
        $requestParam = [
            'api_key' => $this->apiKey,
            'action' => 'setStatus',
            'id' => $id,
            'status' => $status,
        ];

        if ($forward) {
            $requestParam['forward'] = $forward;
        }

        return $this->request($requestParam, 'POST', null, 3);
    }

    private function request($data, $method, $parseAsJSON = null, $getNumber = null)
{
    $method = strtoupper($method);

    if (!in_array($method, ['GET', 'POST'])) {
        throw new InvalidArgumentException('Method can only be GET or POST');
    }

    $serializedData = http_build_query($data);

    if ($method === 'GET') {
        $result = file_get_contents("$this->url?$serializedData");
    } else {
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => $serializedData
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($this->url, false, $context);
    }

    // Log response for debugging
    Log::info('SMS Activate API Response: ', ['response' => $result]);

    if (!$result) {
        throw new RequestError('Empty response from SMS Activate API');
    }

    // Handle specific cases for getNumber
    if ($getNumber == 1) {
        if (strpos($result, 'ACCESS_NUMBER:') !== false) {
            $parsedResponse = explode(':', $result);
            if (count($parsedResponse) >= 3) {
                return ['id' => $parsedResponse[1], 'number' => $parsedResponse[2]];
            }
        }
        throw new RequestError("Invalid response format: $result");
    }

    // Return raw response for getStatus to handle in getStatus method
    return $result;
}
    
}