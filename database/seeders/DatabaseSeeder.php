<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Run seeders in order
        $this->call([
            RolesAndPermissionsSeeder::class,
            UsersSeeder::class,
            OutletsSeeder::class,
            LoyaltyRulesSeeder::class,
            RewardsSeeder::class,
            CustomerTagsSeeder::class,
            AutoGreetingsSeeder::class,
            CampaignsSeeder::class,
            SampleDataSeeder::class,
        ]);
    }
}

