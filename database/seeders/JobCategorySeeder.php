<?php

namespace Database\Seeders;

use App\Models\JobCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Household / Domestic
            ['name' => 'Housemaid', 'group' => 'Household / Domestic'],
            ['name' => 'Babysitter', 'group' => 'Household / Domestic'],
            ['name' => 'Cook (Home)', 'group' => 'Household / Domestic'],
            ['name' => 'Gardener (Home)', 'group' => 'Household / Domestic'],
            ['name' => 'House Driver', 'group' => 'Household / Domestic'],

            // Hospitality / Food
            ['name' => 'Waiter/Waitress', 'group' => 'Hospitality / Food'],
            ['name' => 'Chef', 'group' => 'Hospitality / Food'],
            ['name' => 'Cook (Commercial)', 'group' => 'Hospitality / Food'],
            ['name' => 'Kitchen Helper', 'group' => 'Hospitality / Food'],
            ['name' => 'Bar Staff', 'group' => 'Hospitality / Food'],
            ['name' => 'Baker', 'group' => 'Hospitality / Food'],
            ['name' => 'Butler', 'group' => 'Hospitality / Food'],

            // Cleaning / Maintenance
            ['name' => 'Cleaner (General)', 'group' => 'Cleaning / Maintenance'],
            ['name' => 'Hospital Cleaner', 'group' => 'Cleaning / Maintenance'],
            ['name' => 'Office Cleaner', 'group' => 'Cleaning / Maintenance'],
            ['name' => 'Janitor', 'group' => 'Cleaning / Maintenance'],

            // Construction / Technical
            ['name' => 'Mason', 'group' => 'Construction / Technical'],
            ['name' => 'Carpenter', 'group' => 'Construction / Technical'],
            ['name' => 'Electrician', 'group' => 'Construction / Technical'],
            ['name' => 'Plumber', 'group' => 'Construction / Technical'],
            ['name' => 'Welder', 'group' => 'Construction / Technical'],
            ['name' => 'Painter', 'group' => 'Construction / Technical'],
            ['name' => 'Steel Fixer', 'group' => 'Construction / Technical'],
            ['name' => 'Scaffolder', 'group' => 'Construction / Technical'],
            ['name' => 'AC Technician', 'group' => 'Construction / Technical'],

            // Driving / Transport
            ['name' => 'Heavy Driver', 'group' => 'Driving / Transport'],
            ['name' => 'Light Driver', 'group' => 'Driving / Transport'],
            ['name' => 'Forklift Operator', 'group' => 'Driving / Transport'],
            ['name' => 'Heavy Equipment Operator', 'group' => 'Driving / Transport'],

            // Security
            ['name' => 'Security Guard', 'group' => 'Security'],
            ['name' => 'Watchman', 'group' => 'Security'],

            // Agriculture
            ['name' => 'Farm Worker', 'group' => 'Agriculture'],
            ['name' => 'Livestock Handler', 'group' => 'Agriculture'],

            // Medical / Caregiving
            ['name' => 'Nursing Aid', 'group' => 'Medical / Caregiving'],
            ['name' => 'Caregiver (Elderly/Patient)', 'group' => 'Medical / Caregiving'],
            ['name' => 'Hospital Attendant', 'group' => 'Medical / Caregiving'],

            // Retail / Sales
            ['name' => 'Salesman', 'group' => 'Retail / Sales'],
            ['name' => 'Shop Assistant', 'group' => 'Retail / Sales'],
            ['name' => 'Cashier', 'group' => 'Retail / Sales'],

            // Industrial / Factory
            ['name' => 'Production Worker', 'group' => 'Industrial / Factory'],
            ['name' => 'Packaging Worker', 'group' => 'Industrial / Factory'],
            ['name' => 'Mechanic', 'group' => 'Industrial / Factory'],
            ['name' => 'Helper (General Labor)', 'group' => 'Industrial / Factory'],

            // Others
            ['name' => 'Tailor', 'group' => 'Others'],
            ['name' => 'Landscaper', 'group' => 'Others'],
            ['name' => 'Laundry Worker', 'group' => 'Others'],
            ['name' => 'Storekeeper/Warehouse Staff', 'group' => 'Others'],
        ];

        foreach ($categories as $category) {
            JobCategory::updateOrCreate(
                ['name' => $category['name']],
                [
                    'slug' => Str::slug($category['name']),
                    'group' => $category['group'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info(count($categories) . ' job categories seeded successfully.');
    }
}