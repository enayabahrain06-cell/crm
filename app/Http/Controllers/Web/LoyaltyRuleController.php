<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoyaltyRuleController extends Controller
{
    /**
     * Display a listing of loyalty rules.
     */
    public function index(Request $request)
    {
        // Permission check handled by base controller (loyalty_rules.view)

        $rules = LoyaltyRule::when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->active, function ($query, $active) {
                $query->where('active', $active === 'true');
            })
            ->orderBy('priority')
            ->paginate(10);

        return view('loyalty.rules.index', compact('rules'));
    }

    /**
     * Show the form for creating a new rule.
     */
    public function create()
    {
        // Permission check handled by base controller (loyalty_rules.create)

        return view('loyalty.rules.create');
    }

    /**
     * Store a newly created rule.
     */
    public function store(Request $request)
    {
        // Permission check handled by base controller (loyalty_rules.create)

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:earn,burn,upgrade',
            'description' => 'nullable|string',
            'points_required' => 'nullable|integer|min:0',
            'points_earned' => 'nullable|integer|min:0',
            'multiplier' => 'nullable|numeric|min:1',
            'min_visit_count' => 'nullable|integer|min:0',
            'min_spent_amount' => 'nullable|numeric|min:0',
            'tier_name' => 'nullable|string|max:50',
            'tier_color' => 'nullable|string|max:7',
            'benefits' => 'nullable|array',
            'active' => 'boolean',
            'priority' => 'nullable|integer|min:0',
        ]);

        $validated['created_by'] = Auth::id();
        LoyaltyRule::create($validated);

        return redirect()->route('loyalty.rules.index')->with('success', 'Rule created successfully.');
    }

    /**
     * Show the form for editing the rule.
     */
    public function edit(LoyaltyRule $rule)
    {
        // Permission check handled by base controller (loyalty_rules.edit)

        return view('loyalty.rules.edit', compact('rule'));
    }

    /**
     * Update the rule.
     */
    public function update(Request $request, LoyaltyRule $rule)
    {
        // Permission check handled by base controller (loyalty_rules.edit)

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:earn,burn,upgrade',
            'description' => 'nullable|string',
            'points_required' => 'nullable|integer|min:0',
            'points_earned' => 'nullable|integer|min:0',
            'multiplier' => 'nullable|numeric|min:1',
            'min_visit_count' => 'nullable|integer|min:0',
            'min_spent_amount' => 'nullable|numeric|min:0',
            'tier_name' => 'nullable|string|max:50',
            'tier_color' => 'nullable|string|max:7',
            'benefits' => 'nullable|array',
            'active' => 'boolean',
            'priority' => 'nullable|integer|min:0',
        ]);

        $rule->update($validated);

        return redirect()->route('loyalty.rules.index')->with('success', 'Rule updated successfully.');
    }

    /**
     * Remove the rule.
     */
    public function destroy(LoyaltyRule $rule)
    {
        // Permission check handled by base controller (loyalty_rules.delete)

        $rule->delete();
        return redirect()->route('loyalty.rules.index')->with('success', 'Rule deleted successfully.');
    }
}

