<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CampaignsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'admin@hospitality.com')->first();

        if (!$user) {
            $user = User::first();
        }

        if (!$user) {
            $this->command->warn('No user found, skipping campaign seeding');
            return;
        }

        $campaigns = [
            [
                'name' => 'Summer Special Promotion',
                'description' => 'Exclusive summer discounts for loyal customers',
                'channel' => 'email',
                'status' => 'draft',
                'subject' => '☀️ Summer Special: 20% Off Your Next Visit!',
                'body' => '<h1>Summer Special Offer</h1><p>Dear Valued Customer,</p><p>Enjoy <strong>20% off</strong> your next dining experience this summer!</p><p>Use code: <strong>SUMMER20</strong></p><p>Valid until August 31, 2025.</p>',
                'segment_definition_json' => ['type' => 'active'],
                'total_recipients' => 0,
                'sent_count' => 0,
                'failed_count' => 0,
                'opened_count' => 0,
                'clicked_count' => 0,
                'bookings_count' => 0,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Birthday Wishes Campaign',
                'description' => 'Automated birthday greetings with special offers',
                'channel' => 'email',
                'status' => 'ready',
                'subject' => '🎂 Happy Birthday! Enjoy Your Free Dessert',
                'body' => '<h1>Happy Birthday!</h1><p>Dear {{customer_name}},</p><p>Warmest wishes on your special day!</p><p>Enjoy a <strong>free dessert</strong> on us when you visit any of our outlets.</p><p>Show this email to claim your treat!</p>',
                'segment_definition_json' => ['type' => 'all'],
                'total_recipients' => 150,
                'sent_count' => 150,
                'failed_count' => 2,
                'opened_count' => 89,
                'clicked_count' => 45,
                'bookings_count' => 12,
                'created_by' => $user->id,
                'sent_at' => now()->subDays(5),
            ],
            [
                'name' => 'VIP Customer Exclusive',
                'description' => 'Exclusive invitation for VIP members',
                'channel' => 'email',
                'status' => 'completed',
                'subject' => '⭐ VIP Exclusive: Private Dining Experience',
                'body' => '<h1>You\'re Invited!</h1><p>As one of our valued VIP customers, we cordially invite you to an exclusive private dining event.</p><p><strong>Date:</strong> Next Friday<br><strong>Time:</strong> 7:00 PM<br><strong>Location:</strong> Main Dining Hall</p>',
                'segment_definition_json' => ['type' => 'vip'],
                'total_recipients' => 25,
                'sent_count' => 25,
                'failed_count' => 0,
                'opened_count' => 23,
                'clicked_count' => 18,
                'bookings_count' => 15,
                'created_by' => $user->id,
                'sent_at' => now()->subDays(10),
            ],
            [
                'name' => 'New Customer Welcome',
                'description' => 'Welcome email for new customers',
                'channel' => 'sms',
                'status' => 'ready',
                'subject' => 'Welcome SMS',
                'body' => '🎉 Welcome to Hospitality! Thank you for joining us. Enjoy 10% off your first visit. Code: WELCOME10',
                'segment_definition_json' => ['type' => 'new'],
                'total_recipients' => 0,
                'sent_count' => 0,
                'failed_count' => 0,
                'opened_count' => 0,
                'clicked_count' => 0,
                'bookings_count' => 0,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Re-engagement Campaign',
                'description' => 'Win back inactive customers',
                'channel' => 'email',
                'status' => 'draft',
                'subject' => 'We Miss You! Here\'s 25% Off',
                'body' => '<h1>We Miss You!</h1><p>It\'s been a while since your last visit. We\'d love to see you again!</p><p>Enjoy <strong>25% off</strong> your next visit. Use code: COMEBACK25</p>',
                'segment_definition_json' => ['type' => 'inactive'],
                'total_recipients' => 0,
                'sent_count' => 0,
                'failed_count' => 0,
                'opened_count' => 0,
                'clicked_count' => 0,
                'bookings_count' => 0,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Weekend Brunch Promotion',
                'description' => 'Promote weekend brunch special',
                'channel' => 'push',
                'status' => 'completed',
                'subject' => '🌴 Weekend Brunch Alert',
                'body' => 'Don\'t miss our famous Weekend Brunch! Unlimited food and drinks. Book now!',
                'segment_definition_json' => ['type' => 'all'],
                'total_recipients' => 200,
                'sent_count' => 198,
                'failed_count' => 2,
                'opened_count' => 145,
                'clicked_count' => 87,
                'bookings_count' => 34,
                'created_by' => $user->id,
                'sent_at' => now()->subDays(3),
            ],
            [
                'name' => 'Loyalty Points Bonus',
                'description' => 'Double points weekend promotion',
                'channel' => 'email',
                'status' => 'ready',
                'subject' => '🚀 Double Points Weekend!',
                'body' => '<h1>Double Your Rewards!</h1><p>This weekend, earn <strong>2x points</strong> on every purchase!</p><p>Friday to Sunday only. Don\'t miss out!</p>',
                'segment_definition_json' => ['type' => 'loyal'],
                'total_recipients' => 0,
                'sent_count' => 0,
                'failed_count' => 0,
                'opened_count' => 0,
                'clicked_count' => 0,
                'bookings_count' => 0,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Holiday Season Greetings',
                'description' => 'Holiday season promotional campaign',
                'channel' => 'email',
                'status' => 'draft',
                'subject' => '🎄 Season\'s Greetings & Special Offers',
                'body' => '<h1>Happy Holidays!</h1><p>Celebrate the season with exclusive holiday deals at our outlets.</p><p>Special menus, decorations, and memories await!</p>',
                'segment_definition_json' => ['type' => 'all'],
                'total_recipients' => 0,
                'sent_count' => 0,
                'failed_count' => 0,
                'opened_count' => 0,
                'clicked_count' => 0,
                'bookings_count' => 0,
                'created_by' => $user->id,
            ],
        ];

        foreach ($campaigns as $campaign) {
            Campaign::create($campaign);
            $this->command->info("Created campaign: {$campaign['name']}");
        }

        $this->command->info('Campaigns seeding completed!');
    }
}

