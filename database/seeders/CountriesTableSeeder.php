<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesTableSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Seeding countries from JSON file...');
        
        // Read countries from JSON file
        $countriesJson = file_get_contents(base_path('countries (1).json'));
        $countriesData = json_decode($countriesJson, true);
        
        $countries = [];
        foreach ($countriesData as $code => $name) {
            $countries[] = [
                'name' => $name,
                'code' => (int)$code,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        // Clear existing countries and insert new ones
        // Disable foreign key checks temporarily to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('countries')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Insert in chunks to avoid memory issues
        $chunks = array_chunk($countries, 100);
        foreach ($chunks as $chunk) {
            DB::table('countries')->insert($chunk);
        }
        
        $this->command->info('Countries seeded successfully! Total: ' . count($countries));
    }
}