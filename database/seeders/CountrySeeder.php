<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            // সোর্সিং দেশ (বিদ্যমান — Workers কোথা থেকে আসে)
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

            // গালফ / মধ্যপ্রাচ্য (Candidate-দের ডেস্টিনেশন)
            'Saudi Arabia',
            'UAE',
            'Qatar',
            'Kuwait',
            'Oman',
            'Bahrain',
            'Jordan',
            'Lebanon',

            // ইউরোপ (সম্পূর্ণ তালিকা)
            'Albania',
            'Andorra',
            'Austria',
            'Belarus',
            'Belgium',
            'Bosnia and Herzegovina',
            'Bulgaria',
            'Croatia',
            'Cyprus',
            'Czech Republic',
            'Denmark',
            'Estonia',
            'Finland',
            'France',
            'Germany',
            'Greece',
            'Hungary',
            'Iceland',
            'Ireland',
            'Italy',
            'Kosovo',
            'Latvia',
            'Liechtenstein',
            'Lithuania',
            'Luxembourg',
            'Malta',
            'Moldova',
            'Monaco',
            'Montenegro',
            'Netherlands',
            'North Macedonia',
            'Norway',
            'Poland',
            'Portugal',
            'Romania',
            'San Marino',
            'Serbia',
            'Slovakia',
            'Slovenia',
            'Spain',
            'Sweden',
            'Switzerland',
            'Ukraine',
            'United Kingdom',
            'Vatican City',

            // অন্যান্য জনপ্রিয় ডেস্টিনেশন
            'Malaysia',
            'Singapore',
            'South Korea',
            'Japan',
        ];

        foreach ($countries as $country) {
            Country::firstOrCreate(['name' => $country]);
        }
    }
}