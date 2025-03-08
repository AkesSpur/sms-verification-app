<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['name' => 'Telegram', 'code' => 'tg', 'price' => 0.50],
            ['name' => 'WhatsApp', 'code' => 'wa', 'price' => 0.75],
            ['name' => 'Google', 'code' => 'gg', 'price' => 0.30],
            // Add more services
        ];
    
        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
