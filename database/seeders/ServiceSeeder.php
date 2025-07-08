<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Dropping existing services table data...');
        
        // Truncate the services table to remove all existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Service::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('Seeding top 200 services from curated JSON file...');
        
        // Read services from the curated top 200 services JSON file
        $servicesJson = file_get_contents(base_path('top-200-services-updated.json'));
        $servicesData = json_decode($servicesJson, true);
        
        if (!$servicesData) {
            $this->command->error('Failed to read or parse top-200-services-updated.json file');
            return;
        }
        
        $sortOrder = 1;
        
        foreach ($servicesData as $serviceData) {
            $code = $serviceData['code'];
            $name = $serviceData['name'];
            
            // Create service record
            Service::create([
                'code' => $code,
                'name' => $name,
                'price' => 1.00, // Required field from migration
                'status' => Service::STATUS_ACTIVE,
                'is_available' => true,
                'available_numbers' => rand(50, 200),
                'last_availability_check' => Carbon::now(),
                'max_retry_attempts' => 3,
                'sms_timeout_minutes' => 20,
                'auto_refund_on_timeout' => true,
                'api_service_code' => $code,
                'base_price' => 1.00,
                'markup_percentage' => 20.00,
                'use_dynamic_pricing' => true,
                'total_orders' => 0,
                'successful_orders' => 0,
                'success_rate' => 0.00,
                'description' => "SMS verification for {$name}",
                'sort_order' => $sortOrder++
            ]);
        }
        
        $this->command->info('Top 200 services seeded successfully! Total: ' . count($servicesData));
    }
}
