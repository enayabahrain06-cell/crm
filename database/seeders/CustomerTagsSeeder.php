<?php

namespace Database\Seeders;

use App\Models\CustomerTag;
use Illuminate\Database\Seeder;

class CustomerTagsSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'VIP', 'slug' => 'vip', 'color' => '#FFD700', 'description' => 'Very Important Person - premium customer status'],
            ['name' => 'High Spender', 'slug' => 'high-spender', 'color' => '#DC3545', 'description' => 'Customer with high average spend'],
            ['name' => 'Regular', 'slug' => 'regular', 'color' => '#28A745', 'description' => 'Frequent visitor'],
            ['name' => 'New Customer', 'slug' => 'new-customer', 'color' => '#17A2B8', 'description' => 'Recently registered customer'],
            ['name' => 'Corporate', 'slug' => 'corporate', 'color' => '#6C757D', 'description' => 'Corporate/business customer'],
            ['name' => 'Birthday Month', 'slug' => 'birthday-month', 'color' => '#E83E8C', 'description' => 'Customer with birthday this month'],
            ['name' => 'At Risk', 'slug' => 'at-risk', 'color' => '#FF6B6B', 'description' => 'Customer showing reduced engagement'],
            ['name' => 'Champion', 'slug' => 'champion', 'color' => '#6610F2', 'description' => 'Top tier loyal customer'],
            ['name' => 'Event Host', 'slug' => 'event-host', 'color' => '#FD7E14', 'description' => 'Customer who hosts events'],
            ['name' => 'Influencer', 'slug' => 'influencer', 'color' => '#20C997', 'description' => 'Social media influencer'],
        ];

        foreach ($tags as $tag) {
            CustomerTag::firstOrCreate(
                ['slug' => $tag['slug']],
                $tag
            );
        }
    }
}
