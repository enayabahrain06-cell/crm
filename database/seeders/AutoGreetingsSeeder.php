<?php

namespace Database\Seeders;

use App\Models\AutoGreetingRule;
use Illuminate\Database\Seeder;

class AutoGreetingsSeeder extends Seeder
{
    public function run(): void
    {
        $greetings = [
            [
                'name' => 'Birthday Greeting',
                'trigger_type' => 'birthday',
                'nationality_filter' => null,
                'channel' => 'email',
                'template_subject' => '🎂 Happy Birthday from {{outlet_name}}!',
                'template_body' => <<<'HTML'
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
        <h1 style="color: white; margin: 0;">🎂 Happy Birthday! 🎂</h1>
    </div>
    <div style="padding: 30px; background: #f8f9fa;">
        <p>Dear {{customer_name}},</p>
        <p>Happy Birthday! We hope your special day is filled with joy and wonderful moments.</p>
        <p>As our gift to you, enjoy <strong>{{birthday_bonus_points}} bonus loyalty points</strong> on your next visit!</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{outlet_registration_url}}" style="background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">Visit Us Today</a>
        </div>
        <p>With warm wishes,<br>The {{outlet_name}} Team</p>
    </div>
</div>
HTML,
                'active' => true,
            ],
            [
                'name' => 'Bahrain National Day Greeting',
                'trigger_type' => 'fixed_date',
                'trigger_date' => '12-16', // Bahrain National Day is December 16
                'nationality_filter' => 'BH',
                'channel' => 'email',
                'template_subject' => '🇧🇭 Happy National Day from {{outlet_name}}!',
                'template_body' => <<<'HTML'
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <div style="background: linear-gradient(135deg, #ce1126 0%, #007a3d 100%); padding: 30px; text-align: center;">
        <h1 style="color: white; margin: 0;">🇧🇭 Happy National Day! 🇧🇭</h1>
    </div>
    <div style="padding: 30px; background: #f8f9fa;">
        <p>Dear {{customer_name}},</p>
        <p>Warm wishes on Bahrain's National Day! We're proud to celebrate this special occasion with you.</p>
        <p>Present this email on National Day and get <strong>20% discount</strong> on your bill!</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{outlet_registration_url}}" style="background: #ce1126; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">Celebrate With Us</a>
        </div>
        <p>With pride,<br>The {{outlet_name}} Team</p>
    </div>
</div>
HTML,
                'active' => true,
            ],
            [
                'name' => 'New Year Greeting',
                'trigger_type' => 'fixed_date',
                'trigger_date' => '01-01',
                'nationality_filter' => null,
                'channel' => 'email',
                'template_subject' => '🎉 Happy New Year from {{outlet_name}}!',
                'template_body' => <<<'HTML'
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <div style="background: linear-gradient(135deg, #1a2a6c 0%, #b21f1f 50%, #fdbb2d 100%); padding: 30px; text-align: center;">
        <h1 style="color: white; margin: 0;">🎉 Happy New Year! 🎉</h1>
    </div>
    <div style="padding: 30px; background: #f8f9fa;">
        <p>Dear {{customer_name}},</p>
        <p>Wishing you a wonderful New Year filled with joy, success, and memorable experiences!</p>
        <p>Start the year right with <strong>{{new_year_bonus_points}} bonus loyalty points</strong> on your first visit!</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{outlet_registration_url}}" style="background: #1a2a6c; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">Visit Us in the New Year</a>
        </div>
        <p>Best wishes for 2025,<br>The {{outlet_name}} Team</p>
    </div>
</div>
HTML,
                'active' => true,
            ],
        ];

        foreach ($greetings as $greeting) {
            AutoGreetingRule::firstOrCreate(
                ['name' => $greeting['name']],
                $greeting
            );
        }
    }
}
