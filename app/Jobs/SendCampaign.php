<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Services\CampaignService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Campaign $campaign
    ) {
        $this->onQueue('campaigns');
    }

    public function handle(CampaignService $campaignService): void
    {
        $campaignService->sendCampaign($this->campaign);
    }

    public function failed(\Throwable $exception): void
    {
        $this->campaign->markAsFailed($exception->getMessage());
    }
}

