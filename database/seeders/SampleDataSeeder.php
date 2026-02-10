<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerEvent;
use App\Models\LoyaltyWallet;
use App\Models\LoyaltyPointLedger;
use App\Models\Outlet;
use App\Models\Visit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $outlets = Outlet::all();
        $users = User::whereIn('email', [
            'manager@hospitality.com',
            'marketing@hospitality.com',
        ])->get();

        $nationalities = ['BH', 'SA', 'AE', 'KW', 'QA', 'OM', 'EG', 'JO', 'LB', 'SY', 'IN', 'PK', 'BD', 'PH', 'US', 'GB', 'DE', 'FR'];
        $genders = ['male', 'female'];
        $visitTypes = ['dine', 'bar', 'stay', 'event', 'other'];

        $firstNames = ['Ahmed', 'Mohammed', 'Ali', 'Hassan', 'Khalid', 'Sarah', 'Fatima', 'Mariam', 'Aisha', 'Noor',
                      'John', 'Michael', 'David', 'James', 'Emma', 'Olivia', 'Sophia', 'Isabella', 'Wang', 'Wei',
                      'Priya', 'Anjali', 'Ravi', 'Sanjay', 'Deepa', 'Abdul', 'Omar', 'Hussein', 'Tariq', 'Yusuf'];
        $lastNames = ['Al-Mansour', 'Al-Khalifa', 'Al-Saeed', 'Al-Rashid', 'Al-Dosari', 'Al-Habib', 'Al-Muhammad',
                     'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez',
                     'Chen', 'Wang', 'Li', 'Zhang', 'Liu', 'Yang', 'Huang', 'Zhao', 'Wu', 'Zhou', 'Sun',
                     'Patel', 'Sharma', 'Gupta', 'Singh', 'Kumar', 'Shah', 'Mehta', 'Reddy', 'Nair', 'Iyer'];

        $companies = [
            'Bahrain National Holding',
            'Gulf Corporation Council',
            'Arabian Investments Ltd',
            'Al-Mansour Group',
            'Bahrain Development Bank',
            'Nasser Group',
            'Khalifa Industries',
            'Gulf Petro Industries',
            'Bahrain Media Group',
            'Arabian Construction Co',
        ];

        // Create 50 sample customers
        for ($i = 1; $i <= 50; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $fullName = $firstName . ' ' . $lastName;
            $isCorporate = rand(1, 10) <= 3; // 30% corporate customers

            $countryCode = $nationalities[array_rand($nationalities)];
            $dialCode = $this->getDialCode($countryCode);
            $mobileNumber = rand(30000000, 39999999);
            $mobileE164 = $dialCode . $mobileNumber;

            $customer = Customer::create([
                'type' => $isCorporate ? 'corporate' : 'individual',
                'name' => $fullName,
                'email' => strtolower(str_replace(' ', '.', $fullName)) . $i . '@example.com',
                'mobile_json' => [
                    'country_iso2' => $countryCode,
                    'country_dial_code' => $dialCode,
                    'national_number' => (string)$mobileNumber,
                    'e164' => $mobileE164,
                ],
                'nationality' => $countryCode,
                'gender' => $genders[array_rand($genders)],
'date_of_birth' => (bool)rand(0, 1) ? date('Y-m-d', strtotime('-' . rand(18, 65) . ' years -' . rand(0, 11) . ' months -' . rand(0, 28) . ' days')) : null,
                'address' => $isCorporate ? null : 'Block ' . rand(100, 900) . ', Road ' . rand(1, 50) . ', Manama, Bahrain',
                'company_name' => $isCorporate ? $companies[array_rand($companies)] : null,
                'position' => $isCorporate ? ['Manager', 'Director', 'CEO', 'Executive', 'Head of Department'][rand(0, 4)] : null,
                'first_registration_outlet_id' => $outlets->random()->id,
                'status' => 'active',
            ]);

            // Create loyalty wallet
            $wallet = LoyaltyWallet::create([
                'customer_id' => $customer->id,
                'total_points' => 0,
                'points_earned' => 0,
                'points_redeemed' => 0,
                'points_expired' => 0,
            ]);

            // Create registration event
            CustomerEvent::create([
                'customer_id' => $customer->id,
                'outlet_id' => $customer->first_registration_outlet_id,
                'event_type' => 'registration',
                'meta' => ['source' => 'seed_data'],
            ]);

            // Create random visits (5-20 per customer)
            $numVisits = rand(5, 20);
            $visitedOutlets = [];
            $totalSpend = 0;

            for ($v = 1; $v <= $numVisits; $v++) {
                $outlet = $outlets->random();
                $visitedOutlets[] = $outlet->id;

                $billAmount = rand(5, 500); // 5 to 500 BHD
                $totalSpend += $billAmount;

                $visit = Visit::create([
                    'customer_id' => $customer->id,
                    'outlet_id' => $outlet->id,
                    'visited_at' => now()->subDays(rand(1, 365)),
                    'visit_type' => $visitTypes[array_rand($visitTypes)],
                    'bill_amount' => $billAmount,
                    'currency' => 'BHD',
                    'items_json' => [
                        ['name' => 'Main Course', 'quantity' => rand(1, 3), 'unit_price' => rand(5, 30)],
                        ['name' => 'Drinks', 'quantity' => rand(1, 5), 'unit_price' => rand(2, 10)],
                    ],
                    'staff_user_id' => $users->random()->id,
                    'notes' => null,
                ]);

                // Calculate points for this visit
                $pointsEarned = $this->calculatePointsForVisit($visit, $customer);
                $wallet->total_points += $pointsEarned;
                $wallet->points_earned += $pointsEarned;

                // Create ledger entry
                LoyaltyPointLedger::create([
                    'customer_id' => $customer->id,
                    'outlet_id' => $outlet->id,
                    'visit_id' => $visit->id,
                    'source_type' => 'visit',
                    'source_id' => $visit->id,
                    'points' => $pointsEarned,
                    'description' => 'Points earned from visit at ' . $outlet->name,
                    'created_by_user_id' => $users->random()->id,
                ]);
            }

            $wallet->save();

            // Update customer with stats
            $customer->update([
                'meta' => [
                    'total_visits' => $numVisits,
                    'total_spend' => $totalSpend,
                    'unique_outlets_visited' => count(array_unique($visitedOutlets)),
                ],
            ]);

            if ($i % 10 === 0) {
                $this->command->info("Created $i customers...");
            }
        }

        $this->command->info('Sample data seeding completed!');
    }

    protected function getDialCode(string $countryCode): string
    {
        $dialCodes = [
            'BH' => '+973',
            'SA' => '+966',
            'AE' => '+971',
            'KW' => '+965',
            'QA' => '+974',
            'OM' => '+968',
            'EG' => '+20',
            'JO' => '+962',
            'LB' => '+961',
            'SY' => '+963',
            'IN' => '+91',
            'PK' => '+92',
            'BD' => '+880',
            'PH' => '+63',
            'US' => '+1',
            'GB' => '+44',
            'DE' => '+49',
            'FR' => '+33',
        ];

        return $dialCodes[$countryCode] ?? '+973';
    }

    protected function calculatePointsForVisit(Visit $visit, Customer $customer): int
    {
        // Base points
        $points = 10;

        // Points per BHD spent
        $points += (int)$visit->bill_amount;

        // Birthday bonus
        if ($customer->date_of_birth && $visit->visited_at->isBirthday()) {
            $points += 100;
        }

        // First visit bonus (simplified - would check actual first visit in production)
        if ($visit->visited_at->diffInDays($customer->created_at) < 7) {
            $points += 50;
        }

        // High spend bonus
        if ($visit->bill_amount >= 50) {
            $points = (int)($points * 1.5); // 50% bonus
        }

        return $points;
    }
}
