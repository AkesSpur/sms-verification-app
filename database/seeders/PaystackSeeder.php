<?php

namespace Database\Seeders;

use App\Models\Paystack;
use Illuminate\Database\Seeder;

class PaystackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Paystack::create([
            'status' => true ,
            'country_name' => 'Nigeria',
            'currency_name' => 'NGN',
            'public_key' => 'pk_test_your_public_key_here',
            'secret_key' => 'sk_test_your_secret_key_here',
        ]);
    }
}