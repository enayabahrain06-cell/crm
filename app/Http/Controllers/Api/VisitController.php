<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    /**
     * Store a newly created visit.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'outlet_id' => 'required|exists:outlets,id',
            'visit_date' => 'nullable|date',
            'amount_spent' => 'nullable|numeric|min:0',
            'points_earned' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $visit = Visit::create($validated);

        return response()->json([
            'message' => 'Visit recorded successfully.',
            'visit' => $visit,
        ], 201);
    }

    /**
     * Display visits for a specific customer.
     */
    public function customerVisits(Request $request, $customerId)
    {
        $visits = Visit::where('customer_id', $customerId)
            ->when($request->outlet_id, function ($query, $outletId) {
                $query->where('outlet_id', $outletId);
            })
            ->when($request->start_date, function ($query, $startDate) {
                $query->where('visit_date', '>=', $startDate);
            })
            ->when($request->end_date, function ($query, $endDate) {
                $query->where('visit_date', '<=', $endDate);
            })
            ->orderBy('visit_date', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json($visits);
    }
}

