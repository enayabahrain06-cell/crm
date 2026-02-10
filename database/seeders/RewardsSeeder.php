<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardsSeeder extends Seeder
{
    public function run(): void
    {
        $rewards = [
            [
                'name' => 'Free Soft Drink',
                'description' => 'Enjoy a complimentary soft drink of your choice',
                'required_points' => 50,
                'outlet_scope_json' => ['all'],
                'active' => true,
            ],
            [
                'name' => 'Free Coffee',
                'description' => 'Complimentary premium coffee beverage',
                'required_points' => 75,
                'outlet_scope_json' => ['all'],
                'active' => true,
            ],
            [
                'name' => '10 BHD Voucher',
                'description' => '10 BHD voucher valid for food and beverages',
                'required_points' => 200,
                'outlet_scope_json' => ['all'],
                'active' => true,
            ],
            [
                'name' => 'Free Appetizer',
                'description' => 'Complimentary appetizer from our menu',
                'required_points' => 150,
                'outlet_scope_json' => ['all'],
                'active' => true,
            ],
            [
                'name' => '25 BHD Voucher',
                'description' => '25 BHD voucher valid for food and beverages',
                'required_points' => 450,
                'outlet_scope_json' => ['all'],
                'active' => true,
            ],
            [
                'name' => 'Free Night Stay',
                'description' => 'Complimentary one-night stay at Grand Hotel Bahrain (breakfast included)',
                'required_points' => 2000,
                'outlet_scope_json' => ['grand-hotel'],
                'active' => true,
            ],
            [
                'name' => 'Spa Treatment',
                'description' => '60-minute relaxing massage at Seaside Resort Spa',
                'required_points' => 1500,
                'outlet_scope_json' => ['seaside-resort'],
                'active' => true,
            ],
            [
                'name' => 'VIP Table Reservation',
                'description' => 'VIP table reservation at Skyline Nightclub with bottle service',
                'required_points' => 1000,
                'outlet_scope_json' => ['skyline-club'],
                'active' => true,
            ],
            [
                'name' => 'Chef\'s Table Experience',
                'description' => 'Exclusive dining experience with the head chef at La Piazza',
                'required_points' => 800,
                'outlet_scope_json' => ['la-piazza'],
                'active' => true,
            ],
            [
                'name' => '50 BHD Voucher',
                'description' => '50 BHD voucher valid for food and beverages',
                'required_points' => 850,
                'outlet_scope_json' => ['all'],
                'active' => true,
            ],
        ];

        foreach ($rewards as $reward) {
            Reward::firstOrCreate(
                ['name' => $reward['name']],
                $reward
            );
        }
    }
}
