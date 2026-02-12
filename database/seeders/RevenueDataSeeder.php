<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\LoyaltyPointLedger;
use App\Models\Outlet;
use App\Models\Visit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RevenueDataSeeder extends Seeder
{
    /**
     * Run the database seeds to add realistic revenue data for all outlets
     */
    public function run(): void
    {
        $this->command->info('Adding revenue data for all outlets...');

        $outlets = Outlet::all();
        $users = User::whereIn('email', [
            'manager@hospitality.com',
            'marketing@hospitality.com',
        ])->get();

        if ($users->isEmpty()) {
            $users = User::all();
        }

        // Use the full past year (all 12 months)
        $currentYear = now()->year - 1; // Previous full year
        $totalMonths = 12; // Full year

        $this->command->info("Creating visits for year: $currentYear (full year)");

        foreach ($outlets as $outlet) {
            $this->command->info("Processing outlet: {$outlet->name}");

            // Get existing customers or create some
            $customers = Customer::inRandomOrder()->limit(rand(5, 15))->get();

            if ($customers->isEmpty()) {
                // Create some new customers for this outlet
                for ($c = 0; $c < 10; $c++) {
                    $customers->push(Customer::create([
                        'type' => ['individual', 'corporate'][rand(0, 1)],
                        'name' => 'Customer ' . Str::random(8),
                        'email' => strtolower(Str::random(10)) . '@example.com',
                        'mobile_json' => [
                            'country_iso2' => 'BH',
                            'country_dial_code' => '+973',
                            'national_number' => (string)rand(30000000, 39999999),
                            'e164' => '+973' . rand(30000000, 39999999),
                        ],
                        'nationality' => ['BH', 'SA', 'AE', 'KW', 'IN', 'PK'][rand(0, 5)],
                        'gender' => ['male', 'female'][rand(0, 1)],
                        'date_of_birth' => date('Y-m-d', strtotime('-' . rand(18, 65) . ' years -' . rand(0, 11) . ' months')),
                        'status' => 'active',
                    ]));
                }
                $customers = Customer::inRandomOrder()->limit(10)->get();
            }

            // Create visits for each month of the full year
            for ($month = 1; $month <= $totalMonths; $month++) {
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $currentYear);
                
                // Number of visits varies by outlet and month (higher in weekends/holidays)
                $numVisits = rand(8, 25);

                for ($v = 0; $v < $numVisits; $v++) {
                    $customer = $customers->random();
                    $day = rand(1, $daysInMonth);
                    
                    // Revenue varies by outlet type and month
                    $baseRevenue = match($outlet->type) {
                        'restaurant' => rand(20, 150),
                        'cafe' => rand(10, 50),
                        'bar' => rand(30, 200),
                        'lounge' => rand(40, 250),
                        'hotel' => rand(50, 500),
                        default => rand(20, 100),
                    };

                    // Higher revenue on weekends
                    $date = \Carbon\Carbon::createFromDate($currentYear, $month, $day);
                    if ($date->dayOfWeek == \Carbon\Carbon::FRIDAY || $date->dayOfWeek == \Carbon\Carbon::SATURDAY) {
                        $baseRevenue = (int)($baseRevenue * 1.5);
                    }

                    // Seasonal variation - more revenue in certain months
                    if (in_array($month, [6, 7, 8, 12])) {
                        $baseRevenue = (int)($baseRevenue * 1.3); // Summer and December holidays
                    }
                    // Ramadan typically affects some months (varies by year)
                    if (in_array($month, [1, 2])) {
                        $baseRevenue = (int)($baseRevenue * 0.8); // Slightly lower in Jan/Feb
                    }
                    // Bahrain National Day and festive season
                    if ($month == 12) {
                        $baseRevenue = (int)($baseRevenue * 1.4); // December is festive
                    }

                    $billAmount = $baseRevenue + rand(0, 20); // Add some randomness

                    $visit = Visit::create([
                        'customer_id' => $customer->id,
                        'outlet_id' => $outlet->id,
                        'visited_at' => \Carbon\Carbon::createFromDate($currentYear, $month, $day)->addHours(rand(11, 22))->addMinutes(rand(0, 59)),
                        'visit_type' => ['dine', 'bar', 'stay', 'event', 'other'][rand(0, 4)],
                        'bill_amount' => $billAmount,
                        'currency' => 'BHD',
                        'items_json' => $this->generateSampleItems($outlet->type, $billAmount),
                        'staff_user_id' => $users->random()->id ?? $users->first()->id,
                        'notes' => null,
                    ]);

                    // Create loyalty points
                    $pointsEarned = $this->calculatePointsForVisit($billAmount);
                    
                    LoyaltyPointLedger::create([
                        'customer_id' => $customer->id,
                        'outlet_id' => $outlet->id,
                        'visit_id' => $visit->id,
                        'source_type' => 'visit',
                        'source_id' => $visit->id,
                        'points' => $pointsEarned,
                        'description' => 'Points earned from visit at ' . $outlet->name,
                        'created_by_user_id' => $users->random()->id ?? $users->first()->id,
                    ]);
                }
            }

            $this->command->info("  - Created visits for {$outlet->name}");
        }

        $this->command->info('Revenue data seeding completed!');
    }

    /**
     * Generate sample order items based on outlet type and bill amount
     */
    protected function generateSampleItems(string $outletType, float $billAmount): array
    {
        $items = [];
        $remainingBudget = $billAmount;

        $menuItems = match($outletType) {
            'restaurant' => [
                ['name' => 'Appetizer', 'price' => [5, 15]],
                ['name' => 'Main Course', 'price' => [15, 40]],
                ['name' => 'Dessert', 'price' => [5, 12]],
                ['name' => 'Beverage', 'price' => [3, 8]],
            ],
            'cafe' => [
                ['name' => 'Coffee', 'price' => [2, 6]],
                ['name' => 'Pastry', 'price' => [3, 8]],
                ['name' => 'Sandwich', 'price' => [5, 12]],
                ['name' => 'Salad', 'price' => [6, 12]],
            ],
            'bar' => [
                ['name' => 'Beer', 'price' => [5, 10]],
                ['name' => 'Cocktail', 'price' => [10, 20]],
                ['name' => 'Shot', 'price' => [5, 8]],
                ['name' => 'Finger Food', 'price' => [5, 15]],
            ],
            'lounge' => [
                ['name' => 'Premium Cocktail', 'price' => [15, 30]],
                ['name' => 'Shisha', 'price' => [10, 25]],
                ['name' => 'Appetizer Platter', 'price' => [15, 30]],
                ['name' => 'Mocktail', 'price' => [8, 15]],
            ],
            'hotel' => [
                ['name' => 'Room Service', 'price' => [20, 50]],
                ['name' => 'Minibar', 'price' => [15, 40]],
                ['name' => 'Spa Service', 'price' => [30, 100]],
                ['name' => 'Restaurant Meal', 'price' => [25, 60]],
            ],
            default => [
                ['name' => 'Main Item', 'price' => [10, 30]],
                ['name' => 'Beverage', 'price' => [3, 10]],
                ['name' => 'Side Dish', 'price' => [5, 15]],
            ],
        };

        // Add 2-4 items per visit
        $numItems = rand(2, min(4, floor($billAmount / 5)));

        for ($i = 0; $i < $numItems; $i++) {
            if ($remainingBudget < 5) break;

            $itemTemplate = $menuItems[array_rand($menuItems)];
            $quantity = rand(1, max(1, floor($remainingBudget / max($itemTemplate['price'][0], 1))));
            $unitPrice = rand($itemTemplate['price'][0], min($itemTemplate['price'][1], $remainingBudget));
            $itemTotal = $unitPrice * $quantity;

            if ($itemTotal > $remainingBudget) {
                $quantity = max(1, floor($remainingBudget / $unitPrice));
                $itemTotal = $unitPrice * $quantity;
            }

            if ($itemTotal > 0 && $quantity > 0) {
                $items[] = [
                    'name' => $itemTemplate['name'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ];
                $remainingBudget -= $itemTotal;
            }
        }

        return $items;
    }

    /**
     * Calculate loyalty points for a visit
     */
    protected function calculatePointsForVisit(float $billAmount): int
    {
        // 1 point per BHD spent
        $points = (int)$billAmount;
        
        // Bonus points for higher bills
        if ($billAmount >= 100) {
            $points += 50;
        } elseif ($billAmount >= 50) {
            $points += 25;
        } elseif ($billAmount >= 25) {
            $points += 10;
        }

        return $points;
    }
}

