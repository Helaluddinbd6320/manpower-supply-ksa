<?php

namespace Database\Seeders;

use App\Models\SaudiCity;
use Illuminate\Database\Seeder;

class SaudiCitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            'Riyadh',
            'Jeddah',
            'Makkah',
            'Madinah',
            'Dammam',
            'Khobar',
            'Dhahran',
            'Jubail',
            'Taif',
            'Tabuk',
            'Abha',
            'Khamis Mushait',
            'Buraidah',
            'Unaizah',
            'Hail',
            'Najran',
            'Jazan',
            'Al Ahsa (Hofuf)',
            'Yanbu',
            'Qatif',
            'Sakaka',
            'Arar',
            'Baha',
            'Al Kharj',
        ];

        foreach ($cities as $city) {
            SaudiCity::firstOrCreate(['name' => $city]);
        }
    }
}