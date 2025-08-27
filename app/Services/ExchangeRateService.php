<?php

namespace App\Services;

use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ExchangeRateService
{
    protected $apiKey;
    protected $baseUrl;
    protected $timeout;
    protected $markupPercentage;
    protected $logRequests;

    public function __construct()
    {
        $this->apiKey = config('services.exchange_rate.api_key');
        $this->baseUrl = config('services.exchange_rate.base_url');
        $this->timeout = config('services.exchange_rate.timeout');
        $this->markupPercentage = config('services.exchange_rate.markup_percentage');
        $this->logRequests = config('services.exchange_rate.log_requests');
    }

    /**
     * Fetch current USD to NGN exchange rate from API
     *
     * @return float|null
     */
    public function fetchCurrentRate(): ?float
    {
        try {
            $url = $this->baseUrl . '/USD';
            
            $response = Http::timeout($this->timeout)
                ->when($this->apiKey, function ($http) {
                    return $http->withHeaders(['Authorization' => 'Bearer ' . $this->apiKey]);
                })
                ->get($url);

            if ($this->logRequests) {
                Log::info('Exchange Rate API Request', [
                    'url' => $url,
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
            }

            if ($response->successful()) {
                $data = $response->json();
                
                // Check if NGN rate exists in response
                if (isset($data['rates']['NGN'])) {
                    return (float) $data['rates']['NGN'];
                }
                
                Log::warning('NGN rate not found in exchange rate response', ['data' => $data]);
                return null;
            }

            Log::error('Exchange Rate API request failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
            return null;
        } catch (Exception $e) {
            Log::error('Exchange Rate API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }

    /**
     * Apply markup percentage to the exchange rate
     *
     * @param float $rate
     * @return float
     */
    public function applyMarkup(float $rate): float
    {
        // Get markup percentage from database, fallback to config
        $generalSettings = GeneralSetting::first();
        $markupPercentage = $generalSettings->exchange_rate_markup_percentage ?? $this->markupPercentage;
        
        if ($markupPercentage <= 0) {
            return $rate;
        }

        $markup = ($rate * $markupPercentage) / 100;
        return $rate + $markup;
    }

    /**
     * Update the exchange rate in the database
     *
     * @param float $rate
     * @return bool
     */
    public function updateDatabaseRate(float $rate): bool
    {
        try {
            $generalSetting = GeneralSetting::first();
            
            if (!$generalSetting) {
                Log::error('GeneralSetting record not found');
                return false;
            }

            $generalSetting->usd_to_ngn_rate = $rate;
            $generalSetting->naira_to_dollar_rate = $rate;
            $generalSetting->exchange_rate_updated_at = now();
            $saved = $generalSetting->save();

            if ($saved) {
                Log::info('Exchange rate updated successfully', [
                    'rate' => $rate,
                    'markup_percentage' => $this->markupPercentage,
                    'updated_at' => now()
                ]);
            }

            return $saved;
        } catch (Exception $e) {
            Log::error('Failed to update exchange rate in database', [
                'rate' => $rate,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }

    /**
     * Fetch current rate, apply markup, and update database
     *
     * @return array
     */
    public function updateExchangeRate(): array
    {
        $currentRate = $this->fetchCurrentRate();
        
        if ($currentRate === null) {
            return [
                'success' => false,
                'message' => 'Failed to fetch current exchange rate from API',
                'rate' => null
            ];
        }

        $rateWithMarkup = $this->applyMarkup($currentRate);
        $updated = $this->updateDatabaseRate($rateWithMarkup);
        
        // Get the actual markup percentage used
        $generalSettings = GeneralSetting::first();
        $actualMarkupPercentage = $generalSettings->exchange_rate_markup_percentage ?? $this->markupPercentage;

        return [
            'success' => $updated,
            'message' => $updated ? 'Exchange rate updated successfully' : 'Failed to update exchange rate in database',
            'original_rate' => $currentRate,
            'markup_percentage' => $actualMarkupPercentage,
            'final_rate' => $rateWithMarkup
        ];
    }

    /**
     * Get current exchange rate from database
     *
     * @return float|null
     */
    public function getCurrentDatabaseRate(): ?float
    {
        $generalSetting = GeneralSetting::first();
        return $generalSetting ? $generalSetting->usd_to_ngn_rate : null;
    }
}