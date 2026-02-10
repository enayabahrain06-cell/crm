<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Visit;
use App\Services\CustomerService;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    protected $customerService;
    protected $loyaltyService;

    public function __construct(CustomerService $customerService, LoyaltyService $loyaltyService)
    {
        $this->customerService = $customerService;
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Public registration page (accessed via QR code)
     */
    public function register(Request $request)
    {
        $outletCode = $request->get('outlet');
        
        if (!$outletCode) {
            return view('public.register-error', [
                'message' => 'No outlet specified. Please scan a valid QR code.',
            ]);
        }

        $outlet = Outlet::where('code', $outletCode)->active()->first();

        if (!$outlet) {
            return view('public.register-error', [
                'message' => 'Invalid outlet. This outlet may have been deactivated.',
            ]);
        }

        return view('public.register', compact('outlet'));
    }

    /**
     * Process public registration
     */
    public function processRegistration(Request $request)
    {
        $outletCode = $request->get('outlet_code');
        
        $outlet = Outlet::where('code', $outletCode)->active()->first();

        if (!$outlet) {
            return back()->with('error', 'Invalid outlet.');
        }

        $data = $request->validate([
            'type' => 'required|in:individual,corporate',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email',
            'country_code' => 'required|string|size:2',
            'mobile_number' => 'required|string',
            'nationality' => 'nullable|string|size:2',
            'gender' => 'nullable|in:male,female,other,unknown',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'company_name' => 'nullable|required_if:type,corporate|string|max:100',
            'position' => 'nullable|required_if:type,corporate|string|max:100',
            'terms_accepted' => 'required|accepted',
            'record_visit' => 'nullable|boolean',
            'bill_amount' => 'nullable|required_if:record_visit,1|numeric|min:0',
            'visit_type' => 'nullable|required_if:record_visit,1|string',
        ]);

        // Combine first_name and last_name into name field
        $data['name'] = trim($data['first_name'] . ' ' . $data['last_name']);
        unset($data['first_name'], $data['last_name']);

        // Check if customer already exists
        $result = $this->customerService->findOrCreate(
            $data,
            null, // No user ID for public registration
            $outlet->id,
            false // Don't upsert, just check
        );

        if (!$result['is_new'] && $result['action'] === 'found') {
            // Customer already exists - check if we should record a visit
            if ($request->boolean('record_visit')) {
                $this->recordVisitForExistingCustomer($result['customer'], $outlet, $request);
                return view('public.register-success', [
                    'customer' => $result['customer'],
                    'outlet' => $outlet,
                    'visit_recorded' => true,
                ]);
            }
            
            return view('public.register-existing', [
                'customer' => $result['customer'],
                'outlet' => $outlet,
            ]);
        }

        // Create new customer
        $customer = $this->customerService->createCustomer(
            $data,
            null, // No user ID
            $outlet->id
        );

        // Check if we should record a visit (default to yes for new customers)
        $shouldRecordVisit = $request->boolean('record_visit') || !isset($data['record_visit']);
        
        if ($shouldRecordVisit) {
            $this->recordVisitForNewCustomer($customer, $outlet, $request);
        }

        return view('public.register-success', [
            'customer' => $customer,
            'outlet' => $outlet,
            'visit_recorded' => $shouldRecordVisit,
        ]);
    }

    /**
     * Record a visit for a newly registered customer
     */
    protected function recordVisitForNewCustomer($customer, $outlet, Request $request)
    {
        $billAmount = $request->input('bill_amount', 0);
        $visitType = $request->input('visit_type', 'dine_in');

        $visit = Visit::create([
            'customer_id' => $customer->id,
            'outlet_id' => $outlet->id,
            'visited_at' => now(),
            'bill_amount' => $billAmount,
            'visit_type' => $visitType,
            'staff_user_id' => null, // No staff - from public registration
            'notes' => 'Auto-recorded from public registration',
        ]);

        // Mark as first visit
        $customer->update(['first_visit_at' => now()]);

        // Process loyalty points
        if ($billAmount > 0) {
            $this->loyaltyService->processVisitPoints($visit);
        }

        return $visit;
    }

    /**
     * Record a visit for an existing customer
     */
    protected function recordVisitForExistingCustomer($customer, $outlet, Request $request)
    {
        $billAmount = $request->input('bill_amount', 0);
        $visitType = $request->input('visit_type', 'dine_in');

        $visit = Visit::create([
            'customer_id' => $customer->id,
            'outlet_id' => $outlet->id,
            'visited_at' => now(),
            'bill_amount' => $billAmount,
            'visit_type' => $visitType,
            'staff_user_id' => null, // No staff - from public registration
            'notes' => 'Auto-recorded from public registration',
        ]);

        // Process loyalty points
        if ($billAmount > 0) {
            $this->loyaltyService->processVisitPoints($visit);
        }

        return $visit;
    }

    /**
     * Outlet linktree-style public page
     */
    public function outletLinks(string $code)
    {
        $outlet = Outlet::where('code', $code)->active()->firstOrFail();

        $socialLinks = $outlet->socialLinks()
            ->active()
            ->ordered()
            ->get();

        return view('public.outlet-links', compact('outlet', 'socialLinks'));
    }

    /**
     * Track campaign email open
     */
    public function trackCampaignOpen(string $token)
    {
        $message = \App\Models\CampaignMessage::where('tracking_token', $token)->first();

        if ($message) {
            $message->markAsOpened();
        }

        // Return a 1x1 transparent image
        return response()->make(
            base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'),
            200,
            ['Content-Type' => 'image/gif']
        );
    }

    /**
     * Track campaign email click
     */
    public function trackCampaignClick(string $token, Request $request)
    {
        $url = $request->get('url');

        $message = \App\Models\CampaignMessage::where('tracking_token', $token)->first();

        if ($message && $url) {
            $message->markAsClicked($url);
            
            // Redirect to original URL
            return redirect()->to(urldecode($url));
        }

        abort(404);
    }
}

