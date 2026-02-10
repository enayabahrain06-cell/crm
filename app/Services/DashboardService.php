<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Visit;
use App\Models\LoyaltyPointLedger;
use App\Models\Campaign;
use App\Models\AutoGreetingLog;
use App\Models\Outlet;
use Illuminate\Database\Eloquent\Collection;

class DashboardService
{
    /**
     * Zodiac sign ranges (month-day format)
     * SQLite-compatible format: ['start_month-start_day', 'end_month-end_day']
     */
    protected const ZODIAC_RANGES = [
        'Capricorn' => ['12-22', '01-19'],
        'Aquarius' => ['01-20', '02-18'],
        'Pisces' => ['02-19', '03-20'],
        'Aries' => ['03-21', '04-19'],
        'Taurus' => ['04-20', '05-20'],
        'Gemini' => ['05-21', '06-20'],
        'Cancer' => ['06-21', '07-22'],
        'Leo' => ['07-23', '08-22'],
        'Virgo' => ['08-23', '09-22'],
        'Libra' => ['09-23', '10-22'],
        'Scorpio' => ['10-23', '11-21'],
        'Sagittarius' => ['11-22', '12-21'],
    ];
    /**
     * Get dashboard summary stats
     */
    public function getSummaryStats(array $filters = []): array
    {
        $customerQuery = Customer::query();
        $visitQuery = Visit::query();
        $ledgerQuery = LoyaltyPointLedger::query();

        $this->applyOutletFilter($customerQuery, $filters);
        $this->applyOutletFilter($visitQuery, $filters);
        $this->applyOutletFilter($ledgerQuery, $filters);

        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();

        $customerQuery->whereBetween('created_at', [$startDate, $endDate]);
        $visitQuery->whereBetween('visited_at', [$startDate, $endDate]);
        $ledgerQuery->whereBetween('created_at', [$startDate, $endDate]);

        return [
            'customers' => [
                'total' => Customer::count(),
                'active' => (clone $customerQuery)->where('status', 'active')->count(),
                'new_this_period' => $customerQuery->count(),
                'by_type' => [
                    'individual' => Customer::active()->where('type', 'individual')->count(),
                    'corporate' => Customer::active()->where('type', 'corporate')->count(),
                ],
            ],
            'visits' => [
                'total_this_period' => $visitQuery->count(),
                'total_spend' => $visitQuery->sum('bill_amount'),
            ],
            'loyalty' => [
                'points_issued' => (clone $ledgerQuery)->where('points', '>', 0)->sum('points'),
                'points_redeemed' => (clone $ledgerQuery)->where('points', '<', 0)->sum('points'),
            ],
            'outlets' => [
                'active' => Outlet::active()->count(),
            ],
        ];
    }

    /**
     * Get demographic distribution
     */
    public function getDemographics(array $filters = []): array
    {
        $query = Customer::active();
        $this->applyOutletFilter($query, $filters);

        return [
            'nationalities' => $query->clone()
                ->select('nationality', \DB::raw('COUNT(*) as count'))
                ->whereNotNull('nationality')
                ->groupBy('nationality')
                ->orderByDesc('count')
                ->limit(10)
                ->get()
                ->pluck('count', 'nationality')
                ->toArray(),
            'genders' => $query->clone()
                ->select('gender', \DB::raw('COUNT(*) as count'))
                ->groupBy('gender')
                ->pluck('count', 'gender')
                ->toArray(),
            'age_groups' => $this->getAgeGroupDistribution($query),
            'zodiac_signs' => $this->getZodiacDistribution($query),
        ];
    }

    /**
     * Get age group distribution
     * SQLite-compatible age calculation using strftime
     */
    protected function getAgeGroupDistribution($query): array
    {
        // SQLite-compatible age calculation:
        // strftime('%Y', 'now') - strftime('%Y', date_of_birth) - (strftime('%m-%d', 'now') < strftime('%m-%d', date_of_birth) ? 1 : 0)
        $ageCalculation = "CAST(strftime('%Y', 'now') AS INTEGER) - CAST(strftime('%Y', date_of_birth) AS INTEGER) - (CASE WHEN strftime('%m-%d', 'now') < strftime('%m-%d', date_of_birth) THEN 1 ELSE 0 END)";

        return [
            'toddler' => (clone $query)->whereBetween(\DB::raw($ageCalculation), [0, 3])->count(),
            'child' => (clone $query)->whereBetween(\DB::raw($ageCalculation), [4, 12])->count(),
            'youth' => (clone $query)->whereBetween(\DB::raw($ageCalculation), [13, 25])->count(),
            'adult' => (clone $query)->whereBetween(\DB::raw($ageCalculation), [26, 59])->count(),
            'senior' => (clone $query)->where(\DB::raw($ageCalculation), '>=', 60)->count(),
        ];
    }

    /**
     * Get zodiac sign distribution
     * SQLite-compatible zodiac calculation
     */
    protected function getZodiacDistribution($query): array
    {
        $zodiacs = [];

        foreach (self::ZODIAC_RANGES as $sign => $range) {
            $start = $range[0]; // 'MM-DD' format
            $end = $range[1];   // 'MM-DD' format

            // For zodiac signs that cross year boundary (e.g., Dec 22 - Jan 19)
            if ($start > $end) {
                $zodiacs[$sign] = (clone $query)
                    ->whereRaw("(strftime('%m-%d', date_of_birth) >= ? OR strftime('%m-%d', date_of_birth) <= ?)", [$start, $end])
                    ->count();
            } else {
                // Normal zodiac range within same month
                $zodiacs[$sign] = (clone $query)
                    ->whereRaw("strftime('%m-%d', date_of_birth) BETWEEN ? AND ?", [$start, $end])
                    ->count();
            }
        }

        return $zodiacs;
    }

    /**
     * Get behavior analytics
     */
    public function getBehaviorAnalytics(array $filters = []): array
    {
        $visitQuery = Visit::query();
        $this->applyOutletFilter($visitQuery, $filters);

        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();
        $visitQuery->whereBetween('visited_at', [$startDate, $endDate]);

        // Visits per outlet
        $visitsPerOutlet = $visitQuery->clone()
            ->select('outlet_id', \DB::raw('COUNT(*) as count'))
            ->groupBy('outlet_id')
            ->with('outlet')
            ->get()
            ->mapWithKeys(fn($v) => [$v->outlet?->name ?? 'Unknown' => $v->count]);

        // Top outlets by spend
        $topOutletsBySpend = $visitQuery->clone()
            ->select('outlet_id', \DB::raw('SUM(bill_amount) as total'))
            ->groupBy('outlet_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->mapWithKeys(fn($v) => [$v->outlet?->name ?? 'Unknown' => round($v->total, 2)]);

        // Customers by outlets visited
        $customersByOutletsVisited = Customer::active()
            ->withCount('visits')
            ->get()
            ->groupBy(fn($c) => match (true) {
                $c->visits_count == 0 => '0',
                $c->visits_count <= 1 => '1',
                $c->visits_count <= 3 => '2-3',
                default => '4+',
            })
            ->map(fn($g) => $g->count());

        return [
            'visits_per_outlet' => $visitsPerOutlet,
            'top_outlets_by_spend' => $topOutletsBySpend,
            'customers_by_outlets_visited' => $customersByOutletsVisited,
            'visits_over_time' => $this->getVisitsOverTime($visitQuery),
            'spend_over_time' => $this->getSpendOverTime($visitQuery),
        ];
    }

    /**
     * Get visits over time
     */
    protected function getVisitsOverTime($query): array
    {
        return $query->clone()
            ->select(
                \DB::raw('DATE(visited_at) as date'),
                \DB::raw('COUNT(*) as count')
            )
            ->groupBy(\DB::raw('DATE(visited_at)'))
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();
    }

    /**
     * Get spend over time
     */
    protected function getSpendOverTime($query): array
    {
        return $query->clone()
            ->select(
                \DB::raw('DATE(visited_at) as date'),
                \DB::raw('SUM(bill_amount) as total')
            )
            ->groupBy(\DB::raw('DATE(visited_at)'))
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn($v) => [$v->date => round($v->total, 2)])
            ->toArray();
    }

    /**
     * Get loyalty analytics
     */
    public function getLoyaltyAnalytics(array $filters = []): array
    {
        $ledgerQuery = LoyaltyPointLedger::query();
        $this->applyOutletFilter($ledgerQuery, $filters);

        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();
        $ledgerQuery->whereBetween('created_at', [$startDate, $endDate]);

        return [
            'points_over_time' => $this->getPointsOverTime($ledgerQuery),
            'points_by_source' => $this->getPointsBySource($ledgerQuery),
            'tier_distribution' => $this->getTierDistribution(),
            'top_customers_by_points' => $this->getTopCustomersByPoints(),
        ];
    }

    /**
     * Get points over time
     */
    protected function getPointsOverTime($query): array
    {
        $earned = (clone $query)->where('points', '>', 0)
            ->select(\DB::raw('DATE(created_at) as date'), \DB::raw('SUM(points) as total'))
            ->groupBy(\DB::raw('DATE(created_at)'))
            ->get()
            ->mapWithKeys(fn($v) => [$v->date => $v->total]);

        $redeemed = (clone $query)->where('points', '<', 0)
            ->select(\DB::raw('DATE(created_at) as date'), \DB::raw('SUM(points) as total'))
            ->groupBy(\DB::raw('DATE(created_at)'))
            ->get()
            ->mapWithKeys(fn($v) => [$v->date => abs($v->total)]);

        return [
            'earned' => $earned,
            'redeemed' => $redeemed,
        ];
    }

    /**
     * Get points by source type
     */
    protected function getPointsBySource($query): array
    {
        return $query->clone()
            ->where('points', '>', 0)
            ->select('source_type', \DB::raw('SUM(points) as total'))
            ->groupBy('source_type')
            ->pluck('total', 'source_type')
            ->toArray();
    }

    /**
     * Get tier distribution
     */
    protected function getTierDistribution(): array
    {
        return \App\Models\LoyaltyWallet::select('tier', \DB::raw('COUNT(*) as count'))
            ->groupBy('tier')
            ->pluck('count', 'tier')
            ->toArray();
    }

    /**
     * Get top customers by points
     */
    protected function getTopCustomersByPoints(int $limit = 10): array
    {
        return \App\Models\LoyaltyWallet::with('customer')
            ->orderBy('total_points', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($w) => [
                'name' => $w->customer?->name ?? 'Unknown',
                'points' => $w->total_points,
                'tier' => $w->tier,
            ])
            ->toArray();
    }

    /**
     * Get campaign analytics
     */
    public function getCampaignAnalytics(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();

        $campaigns = Campaign::whereBetween('created_at', [$startDate, $endDate])->get();
        $recentCampaigns = Campaign::whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return [
            'campaigns_sent' => $campaigns->where('status', 'completed')->count(),
            'campaigns_scheduled' => $campaigns->where('status', 'scheduled')->count(),
            'total_recipients' => $campaigns->sum('total_recipients'),
            'total_sent' => $campaigns->sum('sent_count'),
            'total_opened' => $campaigns->sum('opened_count'),
            'total_clicked' => $campaigns->sum('clicked_count'),
            'recent_campaigns' => $recentCampaigns,
        ];
    }

    /**
     * Get auto-greeting analytics
     */
    public function getAutoGreetingAnalytics(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();

        $logs = AutoGreetingLog::whereBetween('created_at', [$startDate, $endDate])->get();

        return [
            'total_sent' => $logs->where('status', 'sent')->count(),
            'total_failed' => $logs->where('status', 'failed')->count(),
            'by_type' => $logs->groupBy('rule_id')->map(fn($g) => $g->count()),
        ];
    }

    /**
     * Apply outlet filter to query
     */
    protected function applyOutletFilter($query, array $filters): void
    {
        if (isset($filters['outlet_id'])) {
            $query->whereHas('visits', fn($q) => $q->where('outlet_id', $filters['outlet_id']));
        }
    }

    /**
     * Get guest nationalities distribution for donut chart
     */
    public function getGuestNationalities(array $filters = []): array
    {
        $query = Customer::active();
        $this->applyOutletFilter($query, $filters);

        // Get all nationalities and group them
        $nationalities = $query->clone()
            ->select('nationality', \DB::raw('COUNT(*) as count'))
            ->whereNotNull('nationality')
            ->groupBy('nationality')
            ->orderByDesc('count')
            ->get()
            ->pluck('count', 'nationality')
            ->toArray();

        // Group into top countries + "Other"
        $topCountries = ['USA', 'UK', 'China', 'Germany', 'Bahrain', 'India', 'Saudi Arabia', 'UAE'];
        $result = [];
        $otherCount = 0;

        foreach ($nationalities as $country => $count) {
            if (in_array($country, $topCountries)) {
                $result[$country] = $count;
            } else {
                $otherCount += $count;
            }
        }

        if ($otherCount > 0) {
            $result['Other'] = $otherCount;
        }

        // Ensure all top countries are represented (even with 0 count)
        foreach ($topCountries as $country) {
            if (!isset($result[$country])) {
                $result[$country] = 0;
            }
        }

        return $result;
    }

    /**
     * Get age distribution for bar chart
     * Age groups: 18-24, 25-34, 35-44, 45-54, 55+
     */
    public function getAgeDistribution(array $filters = []): array
    {
        $query = Customer::active()->whereNotNull('date_of_birth');
        $this->applyOutletFilter($query, $filters);

        // SQLite-compatible age calculation
        $ageCalculation = "CAST(strftime('%Y', 'now') AS INTEGER) - CAST(strftime('%Y', date_of_birth) AS INTEGER) - (CASE WHEN strftime('%m-%d', 'now') < strftime('%m-%d', date_of_birth) THEN 1 ELSE 0 END)";

        return [
            '18-24' => (clone $query)->whereBetween(\DB::raw($ageCalculation), [18, 24])->count(),
            '25-34' => (clone $query)->whereBetween(\DB::raw($ageCalculation), [25, 34])->count(),
            '35-44' => (clone $query)->whereBetween(\DB::raw($ageCalculation), [35, 44])->count(),
            '45-54' => (clone $query)->whereBetween(\DB::raw($ageCalculation), [45, 54])->count(),
            '55+' => (clone $query)->where(\DB::raw($ageCalculation), '>=', 55)->count(),
        ];
    }

    /**
     * Get campaign performance with booking metrics
     */
    public function getCampaignPerformance(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();

        $campaigns = Campaign::whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return $campaigns->map(function ($campaign) {
            $totalRecipients = $campaign->total_recipients ?: 1;
            $openRate = $totalRecipients > 0 ? round(($campaign->opened_count / $totalRecipients) * 100, 1) : 0;
            $clickRate = $totalRecipients > 0 ? round(($campaign->clicked_count / $totalRecipients) * 100, 1) : 0;

            // Calculate conversion rate (bookings from campaign)
            $bookings = $campaign->bookings_count ?? 0;
            $conversionRate = $totalRecipients > 0 ? round(($bookings / $totalRecipients) * 100, 1) : 0;

            return [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'status' => $campaign->status,
                'sent' => $campaign->sent_count,
                'opened' => $campaign->opened_count,
                'clicked' => $campaign->clicked_count,
                'bookings' => $bookings,
                'total_recipients' => $totalRecipients,
                'open_rate' => $openRate,
                'conversion_rate' => $conversionRate,
                'click_rate' => $clickRate,
                'created_at' => $campaign->created_at,
            ];
        })->toArray();
    }

    /**
     * Get API stats for dashboard
     */
    public function getApiStats(Request $request): array
    {
        return [
            'customers' => [
                'total' => Customer::count(),
                'active' => Customer::where('status', 'active')->count(),
            ],
            'visits' => [
                'total' => Visit::count(),
                'this_month' => Visit::whereMonth('visited_at', now()->month)->count(),
            ],
            'loyalty' => [
                'total_points_issued' => \App\Models\LoyaltyPointLedger::where('points', '>', 0)->sum('points'),
                'total_points_redeemed' => \App\Models\LoyaltyPointLedger::where('points', '<', 0)->sum('points'),
            ],
            'campaigns' => [
                'total' => Campaign::count(),
                'sent' => Campaign::where('status', 'completed')->count(),
            ],
        ];
    }

    /**
     * Get customers with birthdays this month
     * SQLite-compatible: use strftime('%m') to get month from date_of_birth
     */
    public function getBirthdaysThisMonth(array $filters = []): array
    {
        $currentMonth = now()->format('m');
        $currentMonthName = now()->format('F');

        $query = Customer::active()
            ->whereNotNull('date_of_birth')
            ->whereRaw("strftime('%m', date_of_birth) = ?", [$currentMonth])
            ->orderByRaw("CAST(strftime('%d', date_of_birth) AS INTEGER)");

        if (isset($filters['outlet_id'])) {
            $query->whereHas('visits', fn($q) => $q->where('outlet_id', $filters['outlet_id']));
        }

        $birthdays = $query->get()->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'date_of_birth' => $customer->date_of_birth->format('Y-m-d'),
                'birth_day' => $customer->date_of_birth->format('d'),
                'birth_month' => $customer->date_of_birth->format('F'),
                'formatted_dob' => $customer->date_of_birth->format('F d'),
                'age' => $customer->age,
                'email' => $customer->email,
                'type' => $customer->type,
                'zodiac' => $customer->zodiac,
            ];
        });

        return [
            'count' => $birthdays->count(),
            'month' => $currentMonthName,
            'customers' => $birthdays->toArray(),
        ];
    }
}

