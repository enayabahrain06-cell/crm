<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Services\CampaignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    protected $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    /**
     * Display campaigns list
     */
    public function index(Request $request)
    {
        $this->authorize('campaigns.view');

        $query = Campaign::with('creator');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        $campaigns = $query->orderByDesc('created_at')->paginate(20);

        return view('campaigns.index', compact('campaigns'));
    }

    /**
     * Show campaign details
     */
    public function show(Campaign $campaign)
    {
        $this->authorize('campaigns.view');

        $campaign->load(['creator', 'messages' => fn($q) => $q->limit(100)]);

        $stats = $this->campaignService->getCampaignStats($campaign);

        return view('campaigns.show', compact('campaign', 'stats'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('campaigns.create');

        return view('campaigns.create');
    }

    /**
     * Store new campaign
     */
    public function store(StoreCampaignRequest $request)
    {
        $this->authorize('campaigns.create');

        $data = $request->validated();
        $data['created_by'] = Auth::id();

        $campaign = Campaign::create($data);

        return redirect()
            ->route('campaigns.show', $campaign)
            ->with('success', 'Campaign created successfully.');
    }

    /**
     * Show edit form
     */
    public function edit(Campaign $campaign)
    {
        $this->authorize('campaigns.edit');

        if ($campaign->isSent()) {
            return redirect()
                ->route('campaigns.show', $campaign)
                ->with('error', 'Cannot edit a campaign that has already been sent.');
        }

        return view('campaigns.edit', compact('campaign'));
    }

    /**
     * Update campaign
     */
    public function update(UpdateCampaignRequest $request, Campaign $campaign)
    {
        $this->authorize('campaigns.edit');

        if ($campaign->isSent()) {
            return redirect()
                ->route('campaigns.show', $campaign)
                ->with('error', 'Cannot update a campaign that has already been sent.');
        }

        $campaign->update($request->validated());

        return redirect()
            ->route('campaigns.show', $campaign)
            ->with('success', 'Campaign updated successfully.');
    }

    /**
     * Delete campaign
     */
    public function destroy(Campaign $campaign)
    {
        $this->authorize('campaigns.delete');

        if ($campaign->isSending() || $campaign->isCompleted()) {
            return redirect()
                ->route('campaigns.show', $campaign)
                ->with('error', 'Cannot delete a campaign that is sending or completed.');
        }

        $campaign->delete();

        return redirect()
            ->route('campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    /**
     * Preview campaign for a test customer
     */
    public function preview(Request $request, Campaign $campaign)
    {
        $this->authorize('campaigns.view');

        $request->validate([
            'email' => 'required|email',
        ]);

        // In a real implementation, you would preview the email here
        return view('campaigns.preview', compact('campaign'));
    }

    /**
     * Estimate segment size
     */
    public function estimateSegment(Request $request)
    {
        $this->authorize('campaigns.create');

        $segmentDefinition = $request->get('segment_definition', []);

        $count = $this->campaignService->estimateSegmentSize($segmentDefinition);

        return response()->json([
            'estimated_count' => $count,
            'message' => "This campaign will reach approximately {$count} customers.",
        ]);
    }

    /**
     * Send campaign
     */
    public function send(Campaign $campaign)
    {
        $this->authorize('campaigns.send');

        if (!$campaign->isReadyToSend()) {
            return redirect()
                ->route('campaigns.show', $campaign)
                ->with('error', 'Campaign is not ready to send. Please ensure it has a subject and body.');
        }

        // Dispatch job to send campaign
        \App\Jobs\SendCampaign::dispatch($campaign)->onQueue('campaigns');

        return redirect()
            ->route('campaigns.show', $campaign)
            ->with('success', 'Campaign is being sent in the background.');
    }

    /**
     * Cancel campaign
     */
    public function cancel(Campaign $campaign)
    {
        $this->authorize('campaigns.edit');

        if (!$campaign->canBeCancelled()) {
            return redirect()
                ->route('campaigns.show', $campaign)
                ->with('error', 'This campaign cannot be cancelled.');
        }

        $campaign->markAsCancelled();

        return redirect()
            ->route('campaigns.show', $campaign)
            ->with('success', 'Campaign cancelled successfully.');
    }

    /**
     * View campaign messages
     */
    public function messages(Campaign $campaign, Request $request)
    {
        $this->authorize('campaigns.view');

        $query = $campaign->messages();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        $messages = $query->orderByDesc('created_at')->paginate(50);

        return view('campaigns.messages', compact('campaign', 'messages'));
    }

    /**
     * Retry failed messages
     */
    public function retryFailed(Campaign $campaign)
    {
        $this->authorize('campaigns.edit');

        $failedMessages = $campaign->messages()->failed()->get();

        foreach ($failedMessages as $message) {
            \App\Jobs\SendCampaignEmail::dispatch($message);
        }

        return redirect()
            ->route('campaigns.messages', $campaign)
            ->with('success', 'Retrying ' . $failedMessages->count() . ' failed messages.');
    }
}

