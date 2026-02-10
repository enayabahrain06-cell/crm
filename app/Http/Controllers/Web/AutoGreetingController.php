<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAutoGreetingRequest;
use App\Http\Requests\UpdateAutoGreetingRequest;
use App\Models\AutoGreetingRule;
use App\Services\AutoGreetingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoGreetingController extends Controller
{
    protected $autoGreetingService;

    public function __construct(AutoGreetingService $autoGreetingService)
    {
        $this->autoGreetingService = $autoGreetingService;
    }

    /**
     * Display auto-greeting rules list
     */
    public function index()
    {
        $this->authorize('auto_greetings.view');

        $rules = AutoGreetingRule::withCount('logs')
            ->orderByDesc('active')
            ->orderBy('name')
            ->get();

        return view('auto_greetings.index', compact('rules'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('auto_greetings.create');

        return view('auto_greetings.create');
    }

    /**
     * Store new auto-greeting rule
     */
    public function store(StoreAutoGreetingRequest $request)
    {
        $this->authorize('auto_greetings.create');

        $data = $request->validated();
        $data['created_by'] = Auth::id();

        AutoGreetingRule::create($data);

        return redirect()
            ->route('auto-greetings.index')
            ->with('success', 'Auto-greeting rule created successfully.');
    }

    /**
     * Show edit form
     */
    public function edit(AutoGreetingRule $rule)
    {
        $this->authorize('auto_greetings.edit');

        return view('auto_greetings.edit', compact('rule'));
    }

    /**
     * Update auto-greeting rule
     */
    public function update(UpdateAutoGreetingRequest $request, AutoGreetingRule $rule)
    {
        $this->authorize('auto_greetings.edit');

        $rule->update($request->validated());

        return redirect()
            ->route('auto-greetings.index')
            ->with('success', 'Auto-greeting rule updated successfully.');
    }

    /**
     * Delete auto-greeting rule
     */
    public function destroy(AutoGreetingRule $rule)
    {
        $this->authorize('auto_greetings.delete');

        $rule->delete();

        return redirect()
            ->route('auto_greetings.index')
            ->with('success', 'Auto-greeting rule deleted successfully.');
    }

    /**
     * Toggle rule active status
     */
    public function toggle(AutoGreetingRule $rule)
    {
        $this->authorize('auto_greetings.edit');

        $rule->update(['active' => !$rule->active]);

        $status = $rule->active ? 'activated' : 'deactivated';

        return back()->with('success', "Rule {$status} successfully.");
    }

    /**
     * Preview greeting for a customer
     */
    public function preview(Request $request, AutoGreetingRule $rule)
    {
        $this->authorize('auto_greetings.view');

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);

        $customer = \App\Models\Customer::findOrFail($request->get('customer_id'));
        $preview = $this->autoGreetingService->previewGreeting($rule, $customer);

        return response()->json([
            'subject' => $preview['subject'],
            'body' => $preview['body'],
        ]);
    }

    /**
     * View greeting logs
     */
    public function logs(AutoGreetingRule $rule, Request $request)
    {
        $this->authorize('auto_greetings.view');

        $query = $rule->logs()->with('customer');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Date range
        if ($request->has('start_date')) {
            $query->whereDate('sent_at', '>=', $request->get('start_date'));
        }
        if ($request->has('end_date')) {
            $query->whereDate('sent_at', '<=', $request->get('end_date'));
        }

        $logs = $query->orderByDesc('sent_at')->paginate(50);

        return view('auto_greetings.logs', compact('rule', 'logs'));
    }

    /**
     * Process greetings manually (for testing)
     */
    public function processManual()
    {
        $this->authorize('auto_greetings.manage');

        $results = $this->autoGreetingService->processActiveRules(Auth::id());

        return back()->with('success', "Processed {$results['processed']} rules. Sent: {$results['sent']}, Failed: {$results['failed']}");
    }
}

