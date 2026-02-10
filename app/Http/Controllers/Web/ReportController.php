<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display the reports index.
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Customer report.
     */
    public function customerReport(Request $request)
    {
        $this->authorize('reports.view');
        return view('reports.customers');
    }

    /**
     * Visit report.
     */
    public function visitReport(Request $request)
    {
        $this->authorize('reports.view');
        return view('reports.visits');
    }

    /**
     * Loyalty report.
     */
    public function loyaltyReport(Request $request)
    {
        $this->authorize('reports.view');
        return view('reports.loyalty');
    }
}

