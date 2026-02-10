<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Display the customer's wallet.
     */
    public function wallet(Request $request, $customerId)
    {
        $customer = Customer::with('wallet')->findOrFail($customerId);
        
        return response()->json([
            'wallet' => $customer->wallet,
            'tier' => $this->loyaltyService->getCustomerTier($customer),
        ]);
    }

    /**
     * Display the customer's point ledger.
     */
    public function ledger(Request $request, $customerId)
    {
        $ledger = \App\Models\LoyaltyPointLedger::where('customer_id', $customerId)
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json($ledger);
    }

    /**
     * Handle point redemption.
     */
    public function redeem(Request $request, $customerId)
    {
        $request->validate([
            'reward_id' => 'required|exists:rewards,id',
            'quantity' => 'nullable|integer|min:1|max:10',
        ]);

        $customer = Customer::findOrFail($customerId);
        $reward = \App\Models\Reward::findOrFail($request->reward_id);
        $quantity = $request->quantity ?? 1;

        $result = $this->loyaltyService->redeemReward($customer, $reward, $quantity);

        if ($result['success']) {
            return response()->json([
                'message' => 'Reward redeemed successfully.',
                'redemption' => $result['redemption'],
            ]);
        }

        return response()->json([
            'error' => $result['message'],
        ], 400);
    }
}

