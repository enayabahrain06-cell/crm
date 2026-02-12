<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Services\CustomerService;
use App\Models\Customer;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Display customer list/search
     */
    public function index(Request $request)
    {
        $this->authorize('customers.view');

        $filters = $request->all();
        $customers = $this->customerService->search($filters);
        $outlets = $this->getAccessibleOutlets();
        
        // Nationalities list for filter dropdown
        $nationalities = ['BH', 'SA', 'AE', 'KW', 'QA', 'OM', 'EG', 'JO', 'LB', 'SY', 'IN', 'PK', 'BD', 'PH', 'US', 'GB', 'DE', 'FR'];

        return view('customers.index', compact('customers', 'outlets', 'filters', 'nationalities'));
    }

    /**
     * Display Customer 360° view
     */
    public function show(Customer $customer)
    {
        $this->authorize('customers.view');

        $data = $this->customerService->getCustomer360($customer);
        
        return view('customers.show', $data);
    }

    /**
     * Display Customer 360° view (alternative route)
     */
    public function show360(Customer $customer)
    {
        $this->authorize('customers.view');

        $data = $this->customerService->getCustomer360($customer);
        
        // Build timeline from various data sources
        $timeline = [];
        
        // Add visits to timeline
        foreach ($data['visits'] as $visit) {
            $timeline[] = [
                'type' => 'visit',
                'title' => 'Visit',
                'outlet' => $visit->outlet?->name,
                'date' => $visit->visited_at,
                'description' => $visit->bill_amount > 0 ? 'Spent ' . number_format($visit->bill_amount, 3) . ' BHD' : null,
            ];
        }
        
        // Add loyalty ledger entries to timeline
        foreach ($data['loyalty']['ledger'] as $ledger) {
            $timeline[] = [
                'type' => $ledger->points >= 0 ? 'points' : 'redemption',
                'title' => $ledger->points >= 0 ? 'Points Earned' : 'Points Redeemed',
                'outlet' => $ledger->outlet?->name,
                'date' => $ledger->created_at,
                'description' => $ledger->description,
            ];
        }
        
        // Add reward redemptions to timeline
        foreach ($data['redemptions'] as $redemption) {
            $timeline[] = [
                'type' => 'redemption',
                'title' => 'Reward Redeemed',
                'outlet' => $redemption->outlet?->name ?? $redemption->reward?->name,
                'date' => $redemption->created_at,
                'description' => $redemption->reward?->name,
            ];
        }
        
        // Add customer events to timeline
        foreach ($data['events'] as $event) {
            $timeline[] = [
                'type' => $event->event_type === 'campaign_received' ? 'campaign' : 'other',
                'title' => ucfirst(str_replace('_', ' ', $event->event_type)),
                'outlet' => $event->outlet?->name,
                'date' => $event->created_at,
                'description' => null,
            ];
        }
        
        // Sort timeline by date (newest first)
        usort($timeline, function ($a, $b) {
            return $b['date']->timestamp - $a['date']->timestamp;
        });

        return view('customers.show-360', array_merge($data, ['timeline' => $timeline]));
    }

    /**
     * Show create form
     */
    public function create(Request $request)
    {
        $this->authorize('customers.create');

        $outletId = null;
        if ($request->has('outlet_id')) {
            $outletId = $request->get('outlet_id');
        }

        $outlets = $this->getAccessibleOutlets();

        return view('customers.create', compact('outlets', 'outletId'));
    }

    /**
     * Store new customer
     */
    public function store(StoreCustomerRequest $request)
    {
        $this->authorize('customers.create');

        $data = $request->validated();
        $userId = Auth::id();
        $outletId = $request->get('outlet_id');

        $customer = $this->customerService->createCustomer($data, $userId, $outletId);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Show edit form
     */
    public function edit(Customer $customer)
    {
        $this->authorize('customers.edit');

        $outlets = $this->getAccessibleOutlets();
        $customer->load(['tags']);

        return view('customers.edit', compact('customer', 'outlets'));
    }

    /**
     * Update customer
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $this->authorize('customers.edit');

        $data = $request->validated();
        $userId = Auth::id();

        $this->customerService->updateCustomer($customer, $data, $userId);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Delete customer
     */
    public function destroy(Customer $customer)
    {
        $this->authorize('customers.delete');

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    /**
     * Export customer profile as PDF
     */
    public function exportPdf(Customer $customer)
    {
        $this->authorize('customers.view');

        $data = $this->customerService->getCustomer360($customer);
        
        $pdf = \PDF::loadView('customers.pdf', array_merge($data, [
            'profile' => $customer,
        ]));
        
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('customer-' . $customer->customer_id . '.pdf');
    }

    /**
     * Quick search for staff
     */
    public function search(Request $request)
    {
        $this->authorize('customers.view');

        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $customers = Customer::active()
            ->with(['firstRegistrationOutlet', 'loyaltyWallet'])
            ->search($query)
            ->limit(10)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'email' => $c->email,
                'mobile' => $c->formatted_mobile,
                'points' => $c->loyaltyWallet?->total_points ?? 0,
            ]);

        return response()->json($customers);
    }

    /**
     * Autocomplete search for customers (used in forms like Record Visit)
     * Searches by name, phone, or email
     */
    public function autocomplete(Request $request)
    {
        try {
            $this->authorize('customers.view');

            $query = $request->get('q', '');
            $limit = $request->get('limit', 20);
            
            if (strlen($query) < 1) {
                return response()->json([]);
            }

            $customers = Customer::active()
                ->with(['loyaltyWallet'])
                ->search($query)
                ->limit($limit)
                ->get()
                ->map(function($c) {
                    return [
                        'id' => $c->id,
                        'name' => $c->name,
                        'email' => $c->email,
                        'mobile' => $c->formatted_mobile ?? '',
                        'mobile_raw' => $c->mobile_json,
                        'points' => $c->loyaltyWallet?->total_points ?? 0,
                    ];
                });

            return response()->json($customers);
        } catch (\Exception $e) {
            \Log::error('Customer autocomplete error: ' . $e->getMessage(), [
                'query' => $request->get('q'),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Live search for customers (search as you type)
     * Returns filtered customers as JSON for AJAX requests
     */
    public function liveSearch(Request $request)
    {
        $this->authorize('customers.view');

        $filters = $request->all();
        $perPage = $request->get('per_page', 20);
        $page = $request->get('page', 1);

        $query = Customer::with(['firstRegistrationOutlet', 'loyaltyWallet'])
            ->withCount('visits');

        // Text search
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Type filter
        if (isset($filters['type']) && !empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        // Status filter
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Nationality filter
        if (isset($filters['nationality']) && !empty($filters['nationality'])) {
            $query->byNationality($filters['nationality']);
        }

        // Gender filter
        if (isset($filters['gender']) && !empty($filters['gender'])) {
            $query->byGender($filters['gender']);
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        $customers = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform data for JSON response
        $data = [
            'customers' => $customers->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'mobile' => $customer->formatted_mobile ?? null,
                    'mobile_json' => $customer->mobile_json,
                    'type' => $customer->type,
                    'nationality' => $customer->nationality,
                    'nationality_name' => countryName($customer->nationality),
                    'country_flag' => getCountryFlag($customer->nationality),
                    'company_name' => $customer->company_name,
                    'status' => $customer->status,
                    'points' => $customer->loyaltyWallet?->total_points ?? 0,
                    'visits_count' => $customer->visits_count ?? 0,
                    'show_url' => route('customers.show', $customer),
                    '360_url' => route('customers.360', $customer),
                    'edit_url' => route('customers.edit', $customer),
                ];
            })->toArray(),
            'pagination' => [
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
                'total' => $customers->total(),
                'per_page' => $customers->perPage(),
                'has_more' => $customers->hasMorePages(),
            ],
            'filters' => [
                'total_count' => $customers->total(),
                'showing_count' => $customers->count(),
            ],
        ];

        return response()->json($data);
    }

    /**
     * Add tags to customer
     */
    public function addTag(Request $request, Customer $customer)
    {
        $this->authorize('customers.edit');

        $request->validate([
            'tag_id' => 'required|exists:customer_tags,id'
        ]);

        $tagId = $request->get('tag_id');
        
        if ($tagId && !$customer->tags()->where('customer_tag_id', $tagId)->exists()) {
            $customer->tags()->attach($tagId, ['tagged_by' => Auth::id()]);
        }

        return back()->with('success', 'Tag added successfully.');
    }

    /**
     * Remove tag from customer
     */
    public function removeTag(Customer $customer, $tagId)
    {
        $this->authorize('customers.edit');

        $customer->tags()->detach($tagId);

        return back()->with('success', 'Tag removed successfully.');
    }

    /**
     * Update tags (for form submission)
     */
    public function updateTags(Request $request, Customer $customer)
    {
        $this->authorize('customers.edit');

        if ($request->has('tag_id')) {
            $tagId = $request->get('tag_id');
            if (!$customer->tags()->where('customer_tag_id', $tagId)->exists()) {
                $customer->tags()->attach($tagId, ['tagged_by' => Auth::id()]);
            }
        }

        return back()->with('success', 'Tag added successfully.');
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

