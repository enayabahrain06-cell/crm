<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LoyaltyWallet;
use App\Models\LoyaltyPointLedger;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoyaltyController extends Controller
{
    protected $loyaltyService;

    /**
     * Custom permission map for loyalty-specific permissions
     * Maps the method name (ability) called by authorize() to the actual permission
     */
    protected $customPermissionMap = [
        // For $this->authorize('loyalty_wallets.view') in wallet method
        'loyalty_wallets.view' => 'loyalty_wallets.view',
        
        // For $this->authorize('loyalty_rules.view') in index/rules methods
        'loyalty_rules.view' => 'loyalty_rules.view',
        
        // For $this->authorize('loyalty_rules.edit') in adjustPoints method
        'loyalty_rules.edit' => 'loyalty_rules.edit',

        // For $this->authorize('rewards.view') in rewards/showReward methods
        'rewards.view' => 'rewards.view',
        
        // For $this->authorize('rewards.create') in createReward/storeReward methods
        'rewards.create' => 'rewards.create',
        
        // For $this->authorize('rewards.redeem') in redeemReward method
        'rewards.redeem' => 'rewards.redeem',
        
        // For $this->authorize('rewards.delete') in destroyReward method
        'rewards.delete' => 'rewards.delete',

        // For $this->authorize('loyalty_rules.settings') in rules/CRUD methods
        'loyalty_rules.settings' => 'loyalty_rules.settings',
    ];

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Display loyalty dashboard
     */
    public function index(Request $request)
    {
        $this->authorize('loyalty_rules.view');

        $query = LoyaltyWallet::with('customer')->whereHas('customer');

        // Filter by tier
        if ($request->has('tier')) {
            $query->where('tier', $request->get('tier'));
        }

        $wallets = $query->orderBy('total_points', 'desc')->paginate(30);

        $stats = [
            'total_points_issued' => LoyaltyPointLedger::where('points', '>', 0)->sum('points'),
            'total_points_redeemed' => abs(LoyaltyPointLedger::where('points', '<', 0)->sum('points')),
            'total_wallets' => LoyaltyWallet::count(),
            'wallets_by_tier' => LoyaltyWallet::select('tier', \DB::raw('COUNT(*) as count'))
                ->groupBy('tier')
                ->pluck('count', 'tier')
                ->toArray(),
        ];

        return view('loyalty.index', compact('wallets', 'stats'));
    }

    /**
     * Display rewards list
     */
    public function rewards()
    {
        $this->authorize('rewards.view');

        $rewards = Reward::available()->orderBy('required_points')->get();

        return view('loyalty.rewards', compact('rewards'));
    }

    /**
     * Show create reward form
     */
    public function createReward()
    {
        $this->authorize('rewards.create');

        return view('loyalty.rewards-create');
    }

    /**
     * Display single reward details
     */
    public function showReward(Reward $reward)
    {
        $this->authorize('rewards.view');

        return view('loyalty.rewards-show', compact('reward'));
    }

    /**
     * Store new reward
     */
    public function storeReward(Request $request)
    {
        $this->authorize('rewards.create');

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'required_points' => 'required|integer|min:1',
            'outlet_scope_json' => 'nullable|array',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'active' => 'nullable|boolean',
        ]);

        Reward::create($data);

        return redirect()
            ->route('loyalty.rewards')
            ->with('success', 'Reward created successfully.');
    }

    /**
     * Display wallet details for a customer
     */
    public function wallet(Customer $customer)
    {
        $this->authorize('loyalty_wallets.view');

        $wallet = $customer->loyaltyWallet;
        
        $ledger = $customer->loyaltyLedger()
            ->with('outlet')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $availableRewards = $this->loyaltyService->getAvailableRewardsForCustomer($customer);

        $stats = $this->loyaltyService->getPointsSummary($customer);

        return view('loyalty.wallet', compact('customer', 'wallet', 'ledger', 'availableRewards', 'stats'));
    }

    /**
     * Adjust points manually
     */
    public function adjustPoints(Request $request, Customer $customer)
    {
        $this->authorize('loyalty_rules.edit');

        $data = $request->validate([
            'points' => 'required|integer',
            'description' => 'required|string|max:255',
        ]);

        $this->loyaltyService->adjustPoints(
            $customer,
            $data['points'],
            $data['description'],
            Auth::id()
        );

        return back()->with('success', 'Points adjusted successfully.');
    }

    /**
     * Redeem reward for customer
     */
    public function redeemReward(Request $request, Reward $reward)
    {
        $this->authorize('rewards.redeem');

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);

        $customer = Customer::findOrFail($request->get('customer_id'));

        try {
            $redemption = $this->loyaltyService->redeemReward(
                $customer,
                $reward,
                null,
                Auth::id()
            );

            return back()->with('success', 'Reward redeemed successfully. Code: ' . $redemption->redemption_code);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete reward
     */
    public function destroyReward(Reward $reward)
    {
        $this->authorize('rewards.delete');

        $reward->delete();

        return redirect()
            ->route('loyalty.rewards')
            ->with('success', 'Reward deleted successfully.');
    }

    /**
     * Display loyalty rules
     */
    public function rules()
    {
        $this->authorize('loyalty_rules.settings');

        $rules = \App\Models\LoyaltyRule::with('creator')
            ->orderBy('priority')
            ->get();

        return view('loyalty.rules', compact('rules'));
    }

    /**
     * Show create rule form
     */
    public function createRule()
    {
        $this->authorize('loyalty_rules.settings');

        return view('loyalty.rules-create');
    }

    /**
     * Store new loyalty rule
     */
    public function storeRule(Request $request)
    {
        $this->authorize('loyalty_rules.settings');

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:earn,burn',
            'active' => 'nullable|boolean',
            'condition_json' => 'nullable|array',
            'formula_json' => 'nullable|array',
            'priority' => 'nullable|integer',
        ]);

        $data['created_by'] = Auth::id();

        \App\Models\LoyaltyRule::create($data);

        return redirect()
            ->route('loyalty.rules')
            ->with('success', 'Loyalty rule created successfully.');
    }

    /**
     * Edit loyalty rule
     */
    public function editRule(\App\Models\LoyaltyRule $rule)
    {
        $this->authorize('loyalty_rules.settings');

        return view('loyalty.rules-edit', compact('rule'));
    }

    /**
     * Update loyalty rule
     */
    public function updateRule(Request $request, \App\Models\LoyaltyRule $rule)
    {
        $this->authorize('loyalty_rules.settings');

        $rule->update($request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:earn,burn',
            'active' => 'nullable|boolean',
            'condition_json' => 'nullable|array',
            'formula_json' => 'nullable|array',
            'priority' => 'nullable|integer',
        ]));

        return redirect()
            ->route('loyalty.rules')
            ->with('success', 'Loyalty rule updated successfully.');
    }

    /**
     * Delete loyalty rule
     */
    public function destroyRule(\App\Models\LoyaltyRule $rule)
    {
        $this->authorize('loyalty_rules.settings');

        $rule->delete();

        return redirect()
            ->route('loyalty.rules')
            ->with('success', 'Loyalty rule deleted successfully.');
    }
}

