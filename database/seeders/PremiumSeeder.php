<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PremiumSeeder extends Seeder
{
    public function run()
    {
        // Create Premium Categories
        $categories = [
            [
                'id' => 1,
                'name' => 'Additional Premium Pay for 1st 8 hrs',
                'description' => 'Premium pay for first 8 hours of work',
                'sort_order' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Overtime Pay in excess of 8 hrs',
                'description' => 'Premium pay for overtime work exceeding 8 hours',
                'sort_order' => 2,
            ],
            [
                'id' => 3,
                'name' => 'Night Differential',
                'description' => 'Premium pay for night shift work (10PM-6AM)',
                'sort_order' => 3,
            ]
        ];

        foreach ($categories as $category) {
            DB::table('premium_categories')->insert($category);
        }

        // Create Premium Types
        $premiumTypes = [
            // Additional Premium Pay for 1st 8 hrs (Category 1)
            [
                'category_id' => 1,
                'name' => 'Restday',
                'regular_rate' => 30.00,
                'special_rate' => null,
                'sort_order' => 1,
            ],
            [
                'category_id' => 1,
                'name' => 'Holiday Regular',
                'regular_rate' => 100.00,
                'special_rate' => null,
                'sort_order' => 2,
            ],
            [
                'category_id' => 1,
                'name' => 'Holiday Special',
                'regular_rate' => 30.00,
                'special_rate' => null,
                'sort_order' => 3,
            ],
            [
                'category_id' => 1,
                'name' => 'Holiday on RD (Regular)',
                'regular_rate' => 160.00,
                'special_rate' => null,
                'sort_order' => 4,
            ],
            [
                'category_id' => 1,
                'name' => 'Holiday on RD (Special)',
                'regular_rate' => 50.00,
                'special_rate' => null,
                'sort_order' => 5,
            ],

            // Overtime Pay in excess of 8 hrs (Category 2)
            [
                'category_id' => 2,
                'name' => 'Holiday',
                'regular_rate' => 260.00,
                'special_rate' => 169.00,
                'sort_order' => 1,
            ],
            [
                'category_id' => 2,
                'name' => 'Holiday on a Restday',
                'regular_rate' => 338.00,
                'special_rate' => 195.00,
                'sort_order' => 2,
            ],
            [
                'category_id' => 2,
                'name' => 'Regular',
                'regular_rate' => 125.00,
                'special_rate' => null,
                'sort_order' => 3,
            ],
            [
                'category_id' => 2,
                'name' => 'Restday',
                'regular_rate' => 169.00,
                'special_rate' => null,
                'sort_order' => 4,
            ],
            [
                'category_id' => 2,
                'name' => 'Ordinary Working days',
                'regular_rate' => 10.00,
                'special_rate' => null,
                'sort_order' => 5,
            ],

            // Night Differential - Ordinary Working days (Category 3)
            [
                'category_id' => 3,
                'name' => 'Ordinary Working days',
                'regular_rate' => 10.00,
                'special_rate' => null,
                'sort_order' => 1,
            ],
            
            // Night Differential - Premium Pay for 1st 8 hrs (Category 3)
            [
                'category_id' => 3,
                'name' => 'Holiday',
                'regular_rate' => 13.00,
                'special_rate' => 26.00,
                'sort_order' => 2,
            ],
            [
                'category_id' => 3,
                'name' => 'Holiday on a Restday',
                'regular_rate' => 20.00,
                'special_rate' => 15.00,
                'sort_order' => 3,
            ],
            [
                'category_id' => 3,
                'name' => 'Restday',
                'regular_rate' => 13.00,
                'special_rate' => null,
                'sort_order' => 4,
            ],
            
            // Night Differential - Overtime Pay in excess of 8 hrs (Category 3)
            [
                'category_id' => 3,
                'name' => 'Holiday (OT)',
                'regular_rate' => 12.50,
                'special_rate' => 16.90,
                'sort_order' => 5,
            ],
            [
                'category_id' => 3,
                'name' => 'Holiday on a Restday (OT)',
                'regular_rate' => 33.80,
                'special_rate' => 19.50,
                'sort_order' => 6,
            ],
        ];

        foreach ($premiumTypes as $type) {
            DB::table('premium_types')->insert($type);
        }
    }
}