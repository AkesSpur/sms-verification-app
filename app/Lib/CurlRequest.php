<?php

namespace App\Lib;

use Exception;

class CurlRequest
{
    private $timeout;
    private $userAgent;
    private $followRedirects;
    private $maxRedirects;
    
    public function __construct($timeout = 30, $userAgent = null, $followRedirects = true, $maxRedirects = 5)
    {
        $this->timeout = $timeout;
        $this->userAgent = $userAgent ?: 'Mozilla/5.0 (compatible; PHP CurlRequest)';
        $this->followRedirects = $followRedirects;
        $this->maxRedirects = $maxRedirects;
    }
    
    /**
     * Make a GET request and return the response content
     *
     * @param string $url The URL to request
     * @param array $headers Optional headers to send
     * @return string|false The response content or false on failure
     * @throws Exception
     */
    public function curlContent($url, $headers = [])
    {
        if (!function_exists('curl_init')) {
            throw new Exception('cURL extension is not installed');
        }
        
        $curl = curl_init();
        
        if ($curl === false) {
            throw new Exception('Failed to initialize cURL');
        }
        
        try {
            // Set basic cURL options
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $this->timeout,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_USERAGENT => $this->userAgent,
                CURLOPT_FOLLOWLOCATION => $this->followRedirects,
                CURLOPT_MAXREDIRS => $this->maxRedirects,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_ENCODING => '', // Accept all supported encodings
                CURLOPT_HTTPHEADER => $headers,
            ]);
            
            $response = curl_exec($curl);
            
            if ($response === false) {
                $error = curl_error($curl);
                $errno = curl_errno($curl);
                throw new Exception("cURL error ({$errno}): {$error}");
            }
            
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            if ($httpCode >= 400) {
                throw new Exception("HTTP error: {$httpCode}");
            }
            
            return $response;
            
        } finally {
            curl_close($curl);
        }
    }
    
    /**
     * Make a POST request
     *
     * @param string $url The URL to request
     * @param mixed $data The data to send (array, string, or JSON)
     * @param array $headers Optional headers to send
     * @return string|false The response content or false on failure
     * @throws Exception
     */
    public function curlPost($url, $data = null, $headers = [])
    {
        if (!function_exists('curl_init')) {
            throw new Exception('cURL extension is not installed');
        }
        
        $curl = curl_init();
        
        if ($curl === false) {
            throw new Exception('Failed to initialize cURL');
        }
        
        try {
            // Prepare data
            if (is_array($data)) {
                $postData = http_build_query($data);
            } else {
                $postData = $data;
            }
            
            // Set cURL options for POST
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $this->timeout,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_USERAGENT => $this->userAgent,
                CURLOPT_FOLLOWLOCATION => $this->followRedirects,
                CURLOPT_MAXREDIRS => $this->maxRedirects,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HTTPHEADER => $headers,
            ]);
            
            $response = curl_exec($curl);
            
            if ($response === false) {
                $error = curl_error($curl);
                $errno = curl_errno($curl);
                throw new Exception("cURL error ({$errno}): {$error}");
            }
            
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            if ($httpCode >= 400) {
                throw new Exception("HTTP error: {$httpCode}");
            }
            
            return $response;
            
        } finally {
            curl_close($curl);
        }
    }
    
    /**
     * Set timeout for requests
     *
     * @param int $timeout Timeout in seconds
     * @return self
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }
    
    /**
     * Set user agent string
     *
     * @param string $userAgent User agent string
     * @return self
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }
}