<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesTableSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Seeding countries from SMSPool JSON file...');
        
        // Read countries from JSON file
        $countriesJson = file_get_contents(base_path('countries-smspool.json'));
        $countriesData = json_decode($countriesJson, true);
        
        // Country code to flag emoji mapping
        $flagMapping = $this->getFlagMapping();
        
        $countries = [];
        foreach ($countriesData as $isoCode => $name) {
            $countries[] = [
                'name' => $name,
                'code' => $isoCode, // Now using ISO code instead of numeric ID
                'flag' => $flagMapping[$isoCode] ?? '🏳️',
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
    
    private function getFlagMapping()
    {
        return [
            'AF' => '🇦🇫', // Afghanistan
            'AO' => '🇦🇴', // Angola
            'AR' => '🇦🇷', // Argentina
            'AU_V' => '🇦🇺', // Australia (Virtual)
            'AT' => '🇦🇹', // Austria
            'AZ' => '🇦🇿', // Azerbaijan
            'BY' => '🇧🇾', // Belarus
            'BE' => '🇧🇪', // Belgium
            'BJ' => '🇧🇯', // Benin
            'BO' => '🇧🇴', // Bolivia
            'BA' => '🇧🇦', // Bosnia
            'BR' => '🇧🇷', // Brazil
            'BN' => '🇧🇳', // Brunei
            'BG' => '🇧🇬', // Bulgaria
            'BI' => '🇧🇮', // Burundi
            'KH' => '🇰🇭', // Cambodia
            'CM' => '🇨🇲', // Cameroon
            'TD' => '🇹🇩', // Chad
            'CL' => '🇨🇱', // Chile
            'CN' => '🇨🇳', // China
            'CO' => '🇨🇴', // Colombia
            'HR' => '🇭🇷', // Croatia
            'CY' => '🇨🇾', // Cyprus
            'CZ' => '🇨🇿', // Czech Republic
            'DK' => '🇩🇰', // Denmark
            'EC' => '🇪🇨', // Ecuador
            'EG' => '🇪🇬', // Egypt
            'EE' => '🇪🇪', // Estonia
            'ET' => '🇪🇹', // Ethiopia
            'FI' => '🇫🇮', // Finland
            'FR' => '🇫🇷', // France
            'GA' => '🇬🇦', // Gabon
            'GM' => '🇬🇲', // Gambia
            'GE' => '🇬🇪', // Georgia
            'DE' => '🇩🇪', // Germany
            'GH' => '🇬🇭', // Ghana
            'GI' => '🇬🇮', // Gibraltar
            'GR' => '🇬🇷', // Greece
            'GT' => '🇬🇹', // Guatemala
            'GN' => '🇬🇳', // Guinea
            'HT' => '🇭🇹', // Haiti
            'HN' => '🇭🇳', // Honduras
            'HK' => '🇭🇰', // Hong Kong
            'HU' => '🇭🇺', // Hungary
            'IN' => '🇮🇳', // India
            'ID' => '🇮🇩', // Indonesia
            'IQ' => '🇮🇶', // Iraq
            'IE' => '🇮🇪', // Ireland
            'IL' => '🇮🇱', // Israel
            'IT' => '🇮🇹', // Italy
            'JP' => '🇯🇵', // Japan
            'KZ' => '🇰🇿', // Kazakhstan
            'KE' => '🇰🇪', // Kenya
            'KG' => '🇰🇬', // Kyrgyzstan
            'LA' => '🇱🇦', // Laos
            'LV' => '🇱🇻', // Latvia
            'LB' => '🇱🇧', // Lebanon
            'LR' => '🇱🇷', // Liberia
            'LT' => '🇱🇹', // Lithuania
            'LU' => '🇱🇺', // Luxembourg
            'MO' => '🇲🇴', // Macao
            'MW' => '🇲🇼', // Malawi
            'MY' => '🇲🇾', // Malaysia
            'ML' => '🇲🇱', // Mali
            'MR' => '🇲🇷', // Mauritania
            'MX' => '🇲🇽', // Mexico
            'MD' => '🇲🇩', // Moldova
            'MN' => '🇲🇳', // Mongolia
            'MA' => '🇲🇦', // Morocco
            'MZ' => '🇲🇿', // Mozambique
            'NP' => '🇳🇵', // Nepal
            'NL' => '🇳🇱', // Netherlands
            'NI' => '🇳🇮', // Nicaragua
            'NG' => '🇳🇬', // Nigeria
            'NO' => '🇳🇴', // Norway
            'PK' => '🇵🇰', // Pakistan
            'PA' => '🇵🇦', // Panama
            'PY' => '🇵🇾', // Paraguay
            'PE' => '🇵🇪', // Peru
            'PH' => '🇵🇭', // Philippines
            'PL' => '🇵🇱', // Poland
            'PT' => '🇵🇹', // Portugal
            'RO' => '🇷🇴', // Romania
            'RU' => '🇷🇺', // Russia
            'SN' => '🇸🇳', // Senegal
            'RS' => '🇷🇸', // Serbia
            'SG' => '🇸🇬', // Singapore
            'SK' => '🇸🇰', // Slovakia
            'SI' => '🇸🇮', // Slovenia
            'ZA' => '🇿🇦', // South Africa
            'ES' => '🇪🇸', // Spain
            'SE' => '🇸🇪', // Sweden
            'CH' => '🇨🇭', // Switzerland
            'TW' => '🇹🇼', // Taiwan
            'TJ' => '🇹🇯', // Tajikistan
            'TZ' => '🇹🇿', // Tanzania
            'TH' => '🇹🇭', // Thailand
            'TN' => '🇹🇳', // Tunisia
            'TR' => '🇹🇷', // Turkey
            'UG' => '🇺🇬', // Uganda
            'UA' => '🇺🇦', // Ukraine
            'GB' => '🇬🇧', // United Kingdom
            'US' => '🇺🇸', // United States
            'US_V' => '🇺🇸', // United States (Virtual)
            'UZ' => '🇺🇿', // Uzbekistan
            'VN' => '🇻🇳', // Vietnam
            'YE' => '🇾🇪', // Yemen
            'ZM' => '🇿🇲', // Zambia
            'ZW' => '🇿🇼', // Zimbabwe
        ];
    }
}