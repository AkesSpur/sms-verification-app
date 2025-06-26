<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Country;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ImprovedDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding improved database structure...');
        
        // Update existing services with new structure
        $this->updateServices();
        
        // Update existing country_service pivot table
        $this->updateCountryServicePivot();
        
        // Update existing orders
        $this->updateOrders();
        
        $this->command->info('Database seeding completed!');
    }
    
    /**
     * Update existing services with new fields
     */
    private function updateServices()
    {
        $this->command->info('Updating services...');
        
        $services = Service::all();
        
        foreach ($services as $service) {
            $service->update([
                'status' => Service::STATUS_ACTIVE,
                'is_available' => true,
                'available_numbers' => rand(10, 100),
                'last_availability_check' => Carbon::now(),
                'max_retry_attempts' => 3,
                'sms_timeout_minutes' => 20,
                'auto_refund_on_timeout' => true,
                'api_service_code' => $service->code,
                'base_price' => 1.00,
                'markup_percentage' => 20.00,
                'use_dynamic_pricing' => true,
                'total_orders' => 0,
                'successful_orders' => 0,
                'success_rate' => 0.00,
                'description' => "SMS verification for {$service->name}",
                'sort_order' => $service->id
            ]);
        }
        
        $this->command->info("Updated {$services->count()} services.");
    }
    
    /**
     * Update existing country_service pivot table
     */
    private function updateCountryServicePivot()
    {
        $this->command->info('Updating country-service relationships...');
        
        $pivotRecords = DB::table('country_service')->get();
        
        foreach ($pivotRecords as $record) {
            DB::table('country_service')
                ->where('id', $record->id)
                ->update([
                    'is_available' => true,
                    'available_numbers' => rand(5, 50),
                    'last_availability_check' => Carbon::now(),
                    'api_price' => $record->price ? $record->price / 1.2 : 0.83, // Reverse calculate API price
                    'markup_percentage' => 20.00,
                    'final_price' => $record->price ?? 1.00,
                    'last_price_update' => Carbon::now(),
                    'total_orders' => 0,
                    'successful_orders' => 0,
                    'failed_orders' => 0,
                    'success_rate' => 0.00,
                    'max_daily_orders' => 100,
                    'max_hourly_orders' => 10,
                    'min_balance_required' => 1.00,
                    'status' => 'active',
                    'last_api_check' => Carbon::now()
                ]);
        }
        
        $this->command->info("Updated {$pivotRecords->count()} country-service relationships.");
    }
    
    /**
     * Update existing orders with new structure
     */
    private function updateOrders()
    {
        $this->command->info('Updating orders...');
        
        $orders = Order::all();
        
        foreach ($orders as $order) {
            // Map old country string to country_id
            $countryId = null;
            if ($order->country) {
                $country = Country::where('code', $order->country)->first();
                $countryId = $country?->id;
            }
            
            // Map old status to new status enum
            $newStatus = $this->mapOldStatusToNew($order->status);
            
            // Calculate SMS window expiration
            $smsWindowExpiration = $order->created_at->addMinutes(Order::SMS_WINDOW_MINUTES);
            
            $order->update([
                'country_id' => $countryId,
                'status' => $newStatus,
                'api_price' => $order->price ? $order->price / 1.2 : null,
                'markup_percentage' => 20.00,
                'final_price' => $order->price,
                'sms_window_expires_at' => $smsWindowExpiration,
                'sms_received_at' => $order->sms_code ? $order->created_at->addMinutes(rand(1, 19)) : null,
                'max_retries' => 3,
                'can_cancel' => in_array($newStatus, [Order::STATUS_PENDING, Order::STATUS_ACTIVE]) && !$order->sms_code,
                'order_source' => 'web'
            ]);
            
            // Create initial status history
            $order->statusHistory()->create([
                'status' => $newStatus,
                'previous_status' => null,
                'reason' => 'Initial order creation',
                'changed_by_type' => 'system',
                'changed_at' => $order->created_at
            ]);
            
            // If SMS was received, create completion status
            if ($order->sms_code) {
                $order->statusHistory()->create([
                    'status' => Order::STATUS_COMPLETED,
                    'previous_status' => Order::STATUS_ACTIVE,
                    'reason' => 'SMS received',
                    'changed_by_type' => 'system',
                    'changed_at' => $order->sms_received_at
                ]);
            }
        }
        
        $this->command->info("Updated {$orders->count()} orders.");
    }
    
    /**
     * Map old status values to new enum values
     */
    private function mapOldStatusToNew($oldStatus)
    {
        return match($oldStatus) {
            'pending' => Order::STATUS_PENDING,
            'active' => Order::STATUS_ACTIVE,
            'completed' => Order::STATUS_COMPLETED,
            'cancelled' => Order::STATUS_CANCELLED,
            'refunded' => Order::STATUS_REFUNDED,
            'expired' => Order::STATUS_EXPIRED,
            'failed' => Order::STATUS_FAILED,
            'blacklisted' => Order::STATUS_FAILED,
            'suspicious' => Order::STATUS_FAILED,
            default => Order::STATUS_PENDING
        };
    }
}