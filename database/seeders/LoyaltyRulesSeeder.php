<?php

namespace Database\Seeders;

use App\Models\LoyaltyRule;
use Illuminate\Database\Seeder;

class LoyaltyRulesSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'name' => 'Base Points per Visit',
                'type' => 'earn',
                'active' => true,
                'condition_json' => [
                    'outlet_ids' => 'all',
                    'visit_types' => ['dine', 'bar', 'stay', 'event', 'other'],
                ],
                'formula_json' => [
                    'type' => 'fixed',
                    'points' => 10,
                ],
                'priority' => 1,
            ],
            [
                'name' => 'Points per BHD Spent',
                'type' => 'earn',
                'active' => true,
                'condition_json' => [
                    'outlet_ids' => 'all',
                    'visit_types' => ['dine', 'bar', 'stay', 'event', 'other'],
                    'min_spend' => 0,
                ],
                'formula_json' => [
                    'type' => 'per_amount',
                    'points_per_amount' => 1,
                    'amount_per_point' => 1, // 1 point per 1 BHD
                    'max_points' => null,
                ],
                'priority' => 2,
            ],
            [
                'name' => 'Birthday Visit Bonus',
                'type' => 'earn',
                'active' => true,
                'condition_json' => [
                    'outlet_ids' => 'all',
                    'is_birthday_visit' => true,
                ],
                'formula_json' => [
                    'type' => 'fixed',
                    'points' => 100,
                ],
                'priority' => 3,
            ],
            [
                'name' => 'First Visit Bonus',
                'type' => 'earn',
                'active' => true,
                'condition_json' => [
                    'outlet_ids' => 'all',
                    'is_first_visit' => true,
                ],
                'formula_json' => [
                    'type' => 'fixed',
                    'points' => 50,
                ],
                'priority' => 4,
            ],
            [
                'name' => '5th Visit Milestone',
                'type' => 'earn',
                'active' => true,
                'condition_json' => [
                    'outlet_ids' => 'all',
                    'nth_visit' => 5,
                ],
                'formula_json' => [
                    'type' => 'fixed',
                    'points' => 75,
                ],
                'priority' => 5,
            ],
            [
                'name' => '10th Visit Milestone',
                'type' => 'earn',
                'active' => true,
                'condition_json' => [
                    'outlet_ids' => 'all',
                    'nth_visit' => 10,
                ],
                'formula_json' => [
                    'type' => 'fixed',
                    'points' => 150,
                ],
                'priority' => 6,
            ],
            [
                'name' => 'High Spend Bonus (50+ BHD)',
                'type' => 'earn',
                'active' => true,
                'condition_json' => [
                    'outlet_ids' => 'all',
                    'min_spend' => 50,
                ],
                'formula_json' => [
                    'type' => 'multiplier',
                    'multiplier' => 2,
                ],
                'priority' => 7,
            ],
        ];

        foreach ($rules as $rule) {
            LoyaltyRule::firstOrCreate(
                ['name' => $rule['name']],
                $rule
            );
        }
    }
}
