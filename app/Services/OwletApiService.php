<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OwletApiService
{
    /** API URL */
    private $api_url = 'https://the-owlet.com/api/v2';

    /** Your API key */
    private $api_key;

    public function __construct()
    {
        $this->api_key = config('services.owlet.api_key', env('OWLET_API_KEY'));
    }

    /**
     * Add order
     */
    public function order($data)
    {
        $post = array_merge(['key' => $this->api_key, 'action' => 'add'], $data);
        return $this->makeRequest($post);
    }

    /**
     * Get order status
     */
    public function status($order_id)
    {
        return $this->makeRequest([
            'key' => $this->api_key,
            'action' => 'status',
            'order' => $order_id
        ]);
    }

    /**
     * Get order status (alias for status method)
     */
    public function getOrderStatus($order_id)
    {
        return $this->status($order_id);
    }

    /**
     * Get orders status
     */
    public function multiStatus($order_ids)
    {
        return $this->makeRequest([
            'key' => $this->api_key,
            'action' => 'status',
            'orders' => implode(",", (array)$order_ids)
        ]);
    }

    /**
     * Get services
     */
    public function services()
    {
        return $this->makeRequest([
            'key' => $this->api_key,
            'action' => 'services',
        ]);
    }

    /**
     * Refill order
     */
    public function refill(int $orderId)
    {
        return $this->makeRequest([
            'key' => $this->api_key,
            'action' => 'refill',
            'order' => $orderId,
        ]);
    }

    /**
     * Refill orders
     */
    public function multiRefill(array $orderIds)
    {
        return $this->makeRequest([
            'key' => $this->api_key,
            'action' => 'refill',
            'orders' => implode(',', $orderIds),
        ]);
    }

    /**
     * Get refill status
     */
    public function refillStatus(int $refillId)
    {
        return $this->makeRequest([
            'key' => $this->api_key,
            'action' => 'refill_status',
            'refill' => $refillId,
        ]);
    }

    /**
     * Get refill statuses
     */
    public function multiRefillStatus(array $refillIds)
    {
        return $this->makeRequest([
            'key' => $this->api_key,
            'action' => 'refill_status',
            'refills' => implode(',', $refillIds),
        ]);
    }

    /**
     * Cancel orders
     */
    public function cancel(array $orderIds)
    {
        return $this->makeRequest([
            'key' => $this->api_key,
            'action' => 'cancel',
            'orders' => implode(',', $orderIds),
        ]);
    }

    /**
     * Get balance
     */
    public function balance()
    {
        return $this->makeRequest([
            'key' => $this->api_key,
            'action' => 'balance',
        ]);
    }

    /**
     * Make HTTP request to Owlet API
     */
    private function makeRequest($data)
    {
        try {
            $response = Http::asForm()
                ->timeout(30)
                ->post($this->api_url, $data);

            if ($response->successful()) {
                $result = $response->json();
                
                // Log successful requests for debugging
                Log::info('Owlet API Request Success', [
                    'action' => $data['action'] ?? 'unknown',
                    'response' => $result
                ]);
                
                return $result;
            } else {
                Log::error('Owlet API Request Failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'data' => $data
                ]);
                
                throw new Exception('API request failed with status: ' . $response->status());
            }
        } catch (Exception $e) {
            // Log::error('Owlet API Exception', [
            //     'message' => $e->getMessage(),
            //     'data' => $data
            // ]);
            
            throw $e;
        }
    }

    /**
     * Map local service to Owlet service ID
     * This method should be customized based on your service mapping
     */
    public function mapServiceToOwletId($productId)
    {
        // You'll need to create a mapping between your local products and Owlet service IDs
        // For now, this is a placeholder - you should implement proper mapping logic
        $serviceMapping = [
            // Example mapping - replace with actual mappings
            // 'local_product_id' => 'owlet_service_id'
        ];

        return $serviceMapping[$productId] ?? null;
    }

    /**
     * Place an order for social media boosting
     */
    public function placeOrder($serviceId, $link, $quantity)
    {
        $data = [
            'service' => $serviceId,
            'link' => $link,
            'quantity' => $quantity
        ];
        
        return $this->order($data);
    }

    /**
     * Validate API key
     */
    public function validateApiKey()
    {
        try {
            $result = $this->balance();
            return isset($result['balance']) || isset($result['currency']);
        } catch (Exception $e) {
            return false;
        }
    }
}