<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\Customer;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CampaignService
{
    /**
     * Get customers matching the segment definition
     */
    public function getSegmentedCustomers(array $segmentDefinition): \Illuminate\Database\Eloquent\Collection
    {
        $query = Customer::active()->with(['loyaltyWallet', 'visits']);

        // Apply filters based on segment definition
        if (isset($segmentDefinition['nationalities']) && !empty($segmentDefinition['nationalities'])) {
            $query->whereIn('nationality', $segmentDefinition['nationalities']);
        }

        if (isset($segmentDefinition['genders']) && !empty($segmentDefinition['genders'])) {
            $query->whereIn('gender', $segmentDefinition['genders']);
        }

        if (isset($segmentDefinition['age_groups']) && !empty($segmentDefinition['age_groups'])) {
            foreach ($segmentDefinition['age_groups'] as $ageGroup) {
                $query->orWhereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN ? AND ?', 
                    Customer::getAgeGroupRange($ageGroup));
            }
        }

        if (isset($segmentDefinition['outlets_visited']) && !empty($segmentDefinition['outlets_visited'])) {
            $query->whereHas('visitedOutlets', function ($q) use ($segmentDefinition) {
                $q->whereIn('outlets.id', $segmentDefinition['outlets_visited']);
            });
        }

        if (isset($segmentDefinition['min_visits'])) {
            $query->whereHas('visits', function ($q) use ($segmentDefinition) {
                $q->select('customer_id')
                    ->groupBy('customer_id')
                    ->havingRaw('COUNT(*) >= ?', [$segmentDefinition['min_visits']]);
            });
        }

        if (isset($segmentDefinition['min_points'])) {
            $query->whereHas('loyaltyWallet', function ($q) use ($segmentDefinition) {
                $q->where('total_points', '>=', $segmentDefinition['min_points']);
            });
        }

        if (isset($segmentDefinition['max_points'])) {
            $query->whereHas('loyaltyWallet', function ($q) use ($segmentDefinition) {
                $q->where('total_points', '<=', $segmentDefinition['max_points']);
            });
        }

        if (isset($segmentDefinition['tags']) && !empty($segmentDefinition['tags'])) {
            $query->whereHas('tags', function ($q) use ($segmentDefinition) {
                $q->whereIn('customer_tags.slug', $segmentDefinition['tags']);
            });
        }

        if (isset($segmentDefinition['customer_types']) && !empty($segmentDefinition['customer_types'])) {
            $query->whereIn('type', $segmentDefinition['customer_types']);
        }

        if (isset($segmentDefinition['exclude_blacklisted']) && $segmentDefinition['exclude_blacklisted']) {
            $query->where('status', '!=', 'blacklisted');
        }

        return $query->get();
    }

    /**
     * Prepare campaign for sending
     */
    public function prepareCampaign(Campaign $campaign): array
    {
        $segment = $campaign->segment_definition_json ?? [];
        $customers = $this->getSegmentedCustomers($segment);
        
        return [
            'campaign' => $campaign,
            'customers' => $customers,
            'count' => $customers->count(),
        ];
    }

    /**
     * Send campaign
     */
    public function sendCampaign(Campaign $campaign, ?int $userId = null): void
    {
        DB::transaction(function () use ($campaign, $userId) {
            $campaign->markAsSending();
            
            $preparation = $this->prepareCampaign($campaign);
            $customers = $preparation['customers'];
            
            $campaign->total_recipients = $customers->count();
            $campaign->save();

            foreach ($customers as $customer) {
                if (!$customer->email) {
                    // Skip customers without email
                    CampaignMessage::create([
                        'campaign_id' => $campaign->id,
                        'customer_id' => $customer->id,
                        'status' => 'failed',
                        'error_message' => 'No email address',
                    ]);
                    continue;
                }

                try {
                    $this->sendCampaignEmail($campaign, $customer);
                    
                    CampaignMessage::create([
                        'campaign_id' => $campaign->id,
                        'customer_id' => $customer->id,
                        'email' => $customer->email,
                        'status' => 'sent',
                        'sent_at' => now(),
                        'tracking_token' => CampaignMessage::generateTrackingToken(),
                    ]);
                } catch (\Exception $e) {
                    Log::error("Failed to send campaign email to customer {$customer->id}: " . $e->getMessage());
                    
                    CampaignMessage::create([
                        'campaign_id' => $campaign->id,
                        'customer_id' => $customer->id,
                        'email' => $customer->email,
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                }
            }

            $campaign->updateStats();
            $campaign->markAsCompleted();

            AuditLog::log(
                $userId,
                'campaign_sent',
                'Campaign',
                $campaign->id,
                null,
                ['recipients' => $campaign->total_recipients]
            );
        });
    }

    /**
     * Send a single campaign email
     */
    protected function sendCampaignEmail(Campaign $campaign, Customer $customer): void
    {
        $trackingToken = CampaignMessage::generateTrackingToken();
        $body = $this->personalizeContent($campaign->body, $customer, $campaign, $trackingToken);
        $subject = $this->personalizeContent($campaign->subject, $customer, $campaign, $trackingToken);

        Mail::send([], [], function ($message) use ($customer, $subject, $body) {
            $message->to($customer->email)
                ->subject($subject)
                ->html($body);
        });
    }

    /**
     * Personalize email content with customer data
     */
    protected function personalizeContent(string $content, Customer $customer, Campaign $campaign, string $trackingToken): string
    {
        $replacements = [
            '{{name}}' => $customer->name,
            '{{first_name}}' => explode(' ', $customer->name)[0],
            '{{email}}' => $customer->email,
            '{{company_name}}' => $customer->company_name ?? '',
            '{{mobile}}' => $customer->formatted_mobile ?? '',
            '{{points}}' => $customer->loyaltyWallet?->total_points ?? '0',
            '{{tier}}' => $customer->loyaltyWallet?->tier ?? 'basic',
            '{{total_visits}}' => $customer->visits->count(),
            '{{campaign_name}}' => $campaign->name,
            '{{open_tracking}}' => '<img src="' . route('campaign.track.open', $trackingToken) . '" width="1" height="1" />',
            '{{click_tracking_base}}' => route('campaign.track.click', $trackingToken) . '?url=',
        ];

        $content = strtr($content, $replacements);

        // Add click tracking to links
        $content = preg_replace_callback(
            '/<a\s+href=["\']([^"\']+)["\']/i',
            function ($matches) use ($trackingToken) {
                return '<a href="' . route('campaign.track.click', $trackingToken) . '?url=' . urlencode($matches[1]) . '"';
            },
            $content
        );

        return $content;
    }

    /**
     * Get campaign statistics
     */
    public function getCampaignStats(Campaign $campaign): array
    {
        $messages = $campaign->messages();
        
        return [
            'total_recipients' => $campaign->total_recipients,
            'sent_count' => $campaign->sent_count,
            'failed_count' => $campaign->failed_count,
            'opened_count' => $campaign->opened_count,
            'clicked_count' => $campaign->clicked_count,
            'open_rate' => $campaign->sent_count > 0 
                ? round(($campaign->opened_count / $campaign->sent_count) * 100, 2) 
                : 0,
            'click_rate' => $campaign->opened_count > 0 
                ? round(($campaign->clicked_count / $campaign->opened_count) * 100, 2) 
                : 0,
            'delivery_rate' => $campaign->total_recipients > 0 
                ? round((($campaign->sent_count + $campaign->failed_count) / $campaign->total_recipients) * 100, 2) 
                : 0,
        ];
    }

    /**
     * Estimate segment size
     */
    public function estimateSegmentSize(array $segmentDefinition): int
    {
        return $this->getSegmentedCustomers($segmentDefinition)->count();
    }
}

