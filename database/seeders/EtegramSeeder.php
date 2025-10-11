<?php

namespace Database\Seeders;

use App\Models\Etegram;
use Illuminate\Database\Seeder;

class EtegramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Etegram::create([
            'status' => true,
            'country_name' => 'Nigeria',
            'currency_name' => 'NGN',
            'public_key' => 'pk_test-3e9c3d07e3d84c1a91fefc82395b2321',
            'secret_key' => '68e7d25b4d8701df92ded29f',
            'merchant_id' => '68e7d25b4d8701df92ded29f',
        ]);
    }
}