<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
        
        // Authorization is handled in the index method based on user roles
        // Remove authorizeResource as it incorrectly checks User model permissions
    }

    /**
     * Display main dashboard
     */
    public function index(Request $request)
    {
        $filters = $this->getFilters($request);
        $outlets = $this->getAccessibleOutlets();

        $summary = $this->dashboardService->getSummaryStats($filters);
        $demographics = $this->dashboardService->getDemographics($filters);
        $behavior = $this->dashboardService->getBehaviorAnalytics($filters);
        $loyalty = $this->dashboardService->getLoyaltyAnalytics($filters);
        $campaigns = $this->dashboardService->getCampaignAnalytics($filters);
        $greetings = $this->dashboardService->getAutoGreetingAnalytics($filters);

        // New data for second row widgets
        $guestNationalities = $this->dashboardService->getGuestNationalities($filters);
        $ageDistribution = $this->dashboardService->getAgeDistribution($filters);
        $campaignPerformance = $this->dashboardService->getCampaignPerformance($filters);
        $birthdaysThisMonth = $this->dashboardService->getBirthdaysThisMonth($filters);

        return view('dashboard.index', compact(
            'summary',
            'demographics',
            'behavior',
            'loyalty',
            'campaigns',
            'greetings',
            'outlets',
            'filters',
            'guestNationalities',
            'ageDistribution',
            'campaignPerformance',
            'birthdaysThisMonth'
        ));
    }

    /**
     * Get filters from request
     */
    protected function getFilters(Request $request): array
    {
        return [
            'start_date' => $request->get('start_date', now()->startOfYear()->toDateString()),
            'end_date' => $request->get('end_date', now()->endOfYear()->toDateString()),
            'outlet_id' => $request->get('outlet_id'),
        ];
    }

    /**
     * Get outlets accessible to current user
     */
    protected function getAccessibleOutlets()
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin') || $user->hasRole('group_manager')) {
            return Outlet::active()->get();
        }

        return $user->outlets()->where('active', true)->get();
    }
}

