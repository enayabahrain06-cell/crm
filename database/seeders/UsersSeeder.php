<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Outlet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@hospitality.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password123'),
                'active' => true,
            ]
        );
        $superAdmin->assignRole('super_admin');

        // Group Manager
        $groupManager = User::firstOrCreate(
            ['email' => 'manager@hospitality.com'],
            [
                'name' => 'Group Manager',
                'password' => Hash::make('password123'),
                'active' => true,
            ]
        );
        $groupManager->assignRole('group_manager');

        // Marketing Officer
        $marketing = User::firstOrCreate(
            ['email' => 'marketing@hospitality.com'],
            [
                'name' => 'Marketing Officer',
                'password' => Hash::make('password123'),
                'active' => true,
            ]
        );
        $marketing->assignRole('marketing_officer');

        // Analytics Read-only
        $analytics = User::firstOrCreate(
            ['email' => 'analytics@hospitality.com'],
            [
                'name' => 'Analytics Team',
                'password' => Hash::make('password123'),
                'active' => true,
            ]
        );
        $analytics->assignRole('analytics_readonly');

        // Outlet Managers and Staff (will be assigned after outlets are created)
    }
}
