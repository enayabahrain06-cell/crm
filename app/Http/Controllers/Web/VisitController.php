<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVisitRequest;
use App\Models\Visit;
use App\Models\Customer;
use App\Models\Outlet;
use App\Models\LoyaltyWallet;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VisitController extends Controller
{
    protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Display visits list
     */
    public function index(Request $request)
    {
        $this->authorize('visits.view');

        $query = Visit::with(['customer', 'outlet', 'staff']);

        // Filter by outlet
        $outletId = $request->get('outlet_id');
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        } elseif (!empty($accessibleOutletIds)) {
            $query->whereIn('outlet_id', $accessibleOutletIds);
        } else {
            // No accessible outlets - return empty result set
            $query->whereNull('id'); // This ensures no results are returned
        }

        // Filter by customer
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->get('customer_id'));
        }

        // Date range
        if ($request->has('start_date')) {
            $query->whereDate('visited_at', '>=', $request->get('start_date'));
        }
        if ($request->has('end_date')) {
            $query->whereDate('visited_at', '<=', $request->get('end_date'));
        }

        // Get paginated results
        $visits = $query->orderBy('visited_at', 'desc')->paginate(20);

        // Get stats for the filtered query
        $statsQuery = clone $query;
        $stats = [
            'total_visits' => $statsQuery->count(),
            'total_revenue' => (clone $query)->sum('bill_amount'),
            'total_points' => (clone $query)->sum('points_awarded'),
        ];

        $outlets = $this->getAccessibleOutlets();

        return view('visits.index', compact('visits', 'outlets', 'stats'));
    }

    /**
     * Show create form
     */
    public function create(Request $request)
    {
        $this->authorize('visits.create');

        $outletId = $request->get('outlet_id');
        $customerId = $request->get('customer_id');

        $outlets = $this->getAccessibleOutlets();
        
        // For preselected customer, get their data
        $selectedCustomer = null;
        if ($customerId) {
            $selectedCustomer = Customer::active()
                ->with('loyaltyWallet')
                ->find($customerId);
        }

        return view('visits.create', compact('outlets', 'outletId', 'customerId', 'selectedCustomer'));
    }

    /**
     * Store new visit
     */
    public function store(StoreVisitRequest $request)
    {
        $this->authorize('visits.create');

        $data = $request->validated();
        $data['staff_user_id'] = Auth::id();
        $data['visited_at'] = $data['visited_at'] ?? now();

        $visit = DB::transaction(function () use ($data) {
            $visit = Visit::create($data);

            // Process loyalty points
            $pointsAwarded = $this->loyaltyService->processVisitPoints($visit);

            return $visit;
        });

        return redirect()
            ->route('visits.index')
            ->with('success', 'Visit recorded successfully. Points awarded: ' . $visit->points_awarded ?? 0);
    }

    /**
     * Show visit details
     */
    public function show(Visit $visit)
    {
        $this->authorize('visits.view');
        
        $visit->load(['customer', 'outlet', 'staff']);

        return view('visits.show', compact('visit'));
    }

    /**
     * Delete visit
     */
    public function destroy(Visit $visit)
    {
        $this->authorize('visits.delete');

        $visit->delete();

        return redirect()
            ->route('visits.index')
            ->with('success', 'Visit deleted successfully.');
    }

    /**
     * Bulk delete visits
     */
    public function bulkDelete(Request $request)
    {
        $this->authorize('visits.delete');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:visits,id'
        ]);

        Visit::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Visits deleted successfully.'
        ]);
    }

    /**
     * Quick visit entry for staff
     */
    public function quickEntry(Request $request)
    {
        $this->authorize('visits.create');

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'outlet_id' => 'required|exists:outlets,id',
            'bill_amount' => 'nullable|numeric|min:0',
            'visit_type' => 'nullable|string',
        ]);

        $outletId = $request->get('outlet_id');
        if (!in_array($outletId, $this->getAccessibleOutletIds())) {
            abort(403);
        }

        $visit = Visit::create([
            'customer_id' => $request->get('customer_id'),
            'outlet_id' => $outletId,
            'visited_at' => now(),
            'bill_amount' => $request->get('bill_amount', 0),
            'visit_type' => $request->get('visit_type', 'other'),
            'staff_user_id' => Auth::id(),
            'items_json' => $request->get('items'),
        ]);

        // Process loyalty points
        $this->loyaltyService->processVisitPoints($visit);

        return response()->json([
            'success' => true,
            'visit_id' => $visit->id,
            'message' => 'Visit recorded successfully',
        ]);
    }

    /**
     * Get customer's recent visits
     */
    public function customerVisits(Customer $customer)
    {
        $this->authorize('visits.view');

        $visits = $customer->visits()
            ->with('outlet')
            ->orderBy('visited_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json($visits);
    }

    /**
     * Live search for visits (search as you type)
     * Returns filtered visits as JSON for AJAX requests
     */
    public function liveSearch(Request $request)
    {
        $this->authorize('visits.view');

        $filters = $request->all();
        $perPage = $request->get('per_page', 20);
        $page = $request->get('page', 1);

        $query = Visit::with(['customer', 'outlet', 'staff']);

        // Filter by outlet
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        
        if (isset($filters['outlet_id']) && !empty($filters['outlet_id'])) {
            $query->where('outlet_id', $filters['outlet_id']);
        } elseif (!empty($accessibleOutletIds)) {
            $query->whereIn('outlet_id', $accessibleOutletIds);
        } else {
            $query->whereNull('id'); // No results if no accessible outlets
        }

        // Filter by customer
        if (isset($filters['customer_id']) && !empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        // Text search on customer name, email, mobile
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('mobile_json', 'like', '%' . $search . '%');
            });
        }

        // Date range
        if (isset($filters['start_date']) && !empty($filters['start_date'])) {
            $query->whereDate('visited_at', '>=', $filters['start_date']);
        }
        if (isset($filters['end_date']) && !empty($filters['end_date'])) {
            $query->whereDate('visited_at', '<=', $filters['end_date']);
        }

        // Sort by visited_at descending
        $query->orderBy('visited_at', 'desc');

        $visits = $query->paginate($perPage, ['*'], 'page', $page);

        // Calculate stats for filtered query
        $statsQuery = clone $query;
        $stats = [
            'total_visits' => $statsQuery->count(),
            'total_revenue' => (clone $query)->sum('bill_amount'),
            'total_points' => (clone $query)->sum('points_awarded'),
        ];

        // Transform data for JSON response
        $data = [
            'visits' => $visits->map(function ($visit) {
                return [
                    'id' => $visit->id,
                    'customer_id' => $visit->customer_id,
                    'customer_name' => $visit->customer?->name ?? null,
                    'customer_mobile' => $visit->customer ? formatMobileNumber($visit->customer->mobile_json) : null,
                    'customer_initial' => $visit->customer ? substr($visit->customer->name, 0, 1) : null,
                    'outlet_id' => $visit->outlet_id,
                    'outlet_name' => $visit->outlet?->name ?? null,
                    'staff_id' => $visit->staff_user_id,
                    'staff_name' => $visit->staff?->name ?? null,
                    'bill_amount' => $visit->bill_amount,
                    'points_awarded' => $visit->points_awarded,
                    'visited_at' => $visit->visited_at,
                    'visited_date' => $visit->visited_at->format('M d, Y'),
                    'visited_time' => $visit->visited_at->format('h:i A'),
                    'show_url' => route('visits.show', $visit),
                ];
            })->toArray(),
            'pagination' => [
                'current_page' => $visits->currentPage(),
                'last_page' => $visits->lastPage(),
                'total' => $visits->total(),
                'per_page' => $visits->perPage(),
                'has_more' => $visits->hasMorePages(),
            ],
            'stats' => [
                'total_count' => $visits->total(),
                'showing_count' => $visits->count(),
                'total_visits' => $stats['total_visits'],
                'total_revenue' => $stats['total_revenue'],
                'total_points' => $stats['total_points'],
            ],
        ];

        return response()->json($data);
    }

    /**
     * Get accessible outlets
     */
    protected function getAccessibleOutlets()
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin') || $user->hasRole('group_manager')) {
            return Outlet::active()->get();
        }

        return $user->outlets()->where('active', true)->get();
    }

    /**
     * Get accessible outlet IDs as a properly typed integer array
     */
    protected function getAccessibleOutletIds(): array
    {
        $outlets = $this->getAccessibleOutlets();
        
        // Ensure we return an array of integers
        // Return empty array if no outlets are accessible to avoid SQL errors with whereIn
        return $outlets->pluck('id')->map(fn($id) => (int) $id)->toArray();
    }

    /**
     * Export visits to CSV or Excel
     */
    public function export(Request $request)
    {
        $this->authorize('visits.view');

        $format = $request->get('format', 'csv');
        
        // Validate format
        if (!in_array($format, ['csv', 'xlsx'])) {
            $format = 'csv';
        }

        $filters = [
            'outlet_id' => $request->get('outlet_id'),
            'customer_id' => $request->get('customer_id'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'search' => $request->get('search'),
        ];

        // Apply outlet access control
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        if (empty($filters['outlet_id']) && !empty($accessibleOutletIds)) {
            // If no specific outlet selected, user can only export their accessible outlets
            // We'll let the service handle this by not setting outlet_id filter
        }

        try {
            return app(\App\Services\ImportExportService::class)->exportVisits($filters, $format);
        } catch (\Exception $e) {
            return redirect()
                ->route('visits.index')
                ->with('error', 'Failed to export visits: ' . $e->getMessage());
        }
    }

    /**
     * Export visits to PDF
     */
    public function exportPdf(Request $request)
    {
        $this->authorize('visits.view');

        $filters = [
            'outlet_id' => $request->get('outlet_id'),
            'customer_id' => $request->get('customer_id'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'search' => $request->get('search'),
        ];

        // Build query with same filters as index
        $query = Visit::with(['customer', 'outlet', 'staff']);

        // Apply outlet filter
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        if (!empty($filters['outlet_id'])) {
            $query->where('outlet_id', $filters['outlet_id']);
        } elseif (!empty($accessibleOutletIds)) {
            // User can only see their accessible outlets
        }

        // Apply customer filter
        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        // Apply date range
        if (!empty($filters['start_date'])) {
            $query->whereDate('visited_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('visited_at', '<=', $filters['end_date']);
        }

        // Apply search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('mobile_json', 'like', '%' . $search . '%');
            });
        }

        // Get results
        $visits = $query->orderBy('visited_at', 'desc')->limit(500)->get();

        // Calculate stats
        $statsQuery = clone $query;
        $stats = [
            'total_visits' => $statsQuery->count(),
            'total_revenue' => (clone $query)->sum('bill_amount'),
            'total_points' => (clone $query)->sum('points_awarded'),
        ];

        // Get outlet name for filter display
        $outletName = null;
        if (!empty($filters['outlet_id'])) {
            $outletName = \App\Models\Outlet::find($filters['outlet_id'])?->name;
        }

        // Load outlet relationship for PDF
        $visits->load(['customer', 'outlet', 'staff']);

        try {
            $pdf = \PDF::loadView('visits.pdf', [
                'visits' => $visits,
                'stats' => $stats,
                'filters' => array_merge($filters, ['outlet_name' => $outletName]),
            ]);
            
            $pdf->setPaper('a4', 'landscape');
            
            $filename = 'visits_report_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()
                ->route('visits.index')
                ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}

