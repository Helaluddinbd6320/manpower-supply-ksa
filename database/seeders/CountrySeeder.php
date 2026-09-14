<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            'Bangladesh',
            'Philippines',
            'India',
            'Pakistan',
            'Nepal',
            'Sri Lanka',
            'Indonesia',
            'Kenya',
            'Uganda',
            'Ethiopia',
            'Vietnam',
            'Myanmar',
            'Egypt',
        ];

        foreach ($countries as $country) {
            Country::firstOrCreate(['name' => $country]);
        }
    }
}