<?php

namespace Database\Seeders;

use App\Models\Localbank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocalbankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Localbank::create([
            'status' => true,
            'account_name' => 'Your Company Name',
            'account_number' => '1234567890',
            'bank_name' => 'First Bank of Nigeria',
            'extra_info' => '<p>Please include your username in the transfer description for faster processing.</p><p>Transfer processing time: 1-2 business hours during working days.</p>'
        ]);
    }
}