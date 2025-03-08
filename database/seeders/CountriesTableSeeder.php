<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesTableSeeder extends Seeder
{
    public function run()
    {
        $countries = [
            ['name' => 'United States', 'code' => 187],
            ['name' => 'Canada', 'code' => 36],
            ['name' => 'United Kingdom', 'code' => 16],
            ['name' => 'Germany', 'code' => 43],
            ['name' => 'France', 'code' => 78],
            ['name' => 'Italy', 'code' => 86],
            ['name' => 'Russia', 'code' => 0],
        ];

        DB::table('countries')->insert($countries);
    }
}