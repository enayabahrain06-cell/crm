<?php

namespace App\Services;

use App\Models\AutoGreetingRule;
use App\Models\AutoGreetingLog;
use App\Models\Customer;
use App\Models\CustomerEvent;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AutoGreetingService
{
    /**
     * Process all active auto-greeting rules
     */
    public function processActiveRules(?int $userId = null): array
    {
        $results = [
            'processed' => 0,
            'sent' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $rules = AutoGreetingRule::active()->get();

        foreach ($rules as $rule) {
            try {
                $result = $this->processRule($rule, $userId);
                
                $results['processed']++;
                $results['sent'] += $result['sent'];
                $results['failed'] += $result['failed'];
                
                if (!empty($result['errors'])) {
                    $results['errors'] = array_merge($results['errors'], $result['errors']);
                }
            } catch (\Exception $e) {
                Log::error("Error processing auto-greeting rule {$rule->id}: " . $e->getMessage());
                $results['errors'][] = [
                    'rule_id' => $rule->id,
                    'rule_name' => $rule->name,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Process a single rule
     */
    protected function processRule(AutoGreetingRule $rule, ?int $userId): array
    {
        $customers = $this->getMatchingCustomers($rule);
        $results = ['sent' => 0, 'failed' => 0, 'errors' => []];

        foreach ($customers as $customer) {
            try {
                $this->sendGreeting($rule, $customer, $userId);
                $results['sent']++;
            } catch (\Exception $e) {
                Log::error("Failed to send greeting to customer {$customer->id}: " . $e->getMessage());
                $results['failed']++;
                $results['errors'][] = [
                    'customer_id' => $customer->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Get customers matching the rule criteria
     */
    protected function getMatchingCustomers(AutoGreetingRule $rule): \Illuminate\Database\Eloquent\Collection
    {
        $query = Customer::active();

        if ($rule->trigger_type === 'birthday') {
            // Find customers whose birthday is today
            $query->whereMonth('date_of_birth', now()->month)
                ->whereDay('date_of_birth', now()->day);
        } elseif ($rule->trigger_type === 'fixed_date') {
            // Find customers matching the fixed date criteria
            $query->whereMonth('date_of_birth', now()->month)
                ->whereDay('date_of_birth', now()->day);
        }

        // Apply nationality filter if set
        if ($rule->nationality_filter) {
            $query->where('nationality', $rule->nationality_filter);
        }

        // Exclude customers who already received this greeting today
        $query->whereDoesntHave('autoGreetingLogs', function ($q) use ($rule) {
            $q->where('rule_id', $rule->id)
                ->whereDate('sent_at', today());
        });

        return $query->get();
    }

    /**
     * Send a greeting to a customer
     */
    protected function sendGreeting(AutoGreetingRule $rule, Customer $customer, ?int $userId): void
    {
        if (!$customer->email) {
            throw new \Exception('Customer has no email address');
        }

        // Personalize content
        $subject = $this->personalizeContent($rule->template_subject, $customer);
        $body = $this->personalizeContent($rule->template_body, $customer);

        // Send email
        try {
            Mail::send([], [], function ($message) use ($customer, $subject, $body) {
                $message->to($customer->email)
                    ->subject($subject)
                    ->html($body);
            });

            // Create log entry
            $log = AutoGreetingLog::create([
                'rule_id' => $rule->id,
                'customer_id' => $customer->id,
                'channel' => $rule->channel,
                'status' => 'sent',
                'sent_at' => now(),
                'tracking_token' => AutoGreetingLog::generateTrackingToken(),
            ]);

            // Log customer event
            CustomerEvent::create([
                'customer_id' => $customer->id,
                'event_type' => 'greeting_sent',
                'meta' => [
                    'rule_id' => $rule->id,
                    'rule_name' => $rule->name,
                    'greeting_type' => $rule->trigger_type,
                    'log_id' => $log->id,
                ],
            ]);

        } catch (\Exception $e) {
            // Log failed attempt
            AutoGreetingLog::create([
                'rule_id' => $rule->id,
                'customer_id' => $customer->id,
                'channel' => $rule->channel,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Personalize content with customer data
     */
    protected function personalizeContent(string $content, Customer $customer): string
    {
        $replacements = [
            '{{name}}' => $customer->name,
            '{{first_name}}' => explode(' ', $customer->name)[0],
            '{{email}}' => $customer->email,
            '{{company_name}}' => $customer->company_name ?? '',
            '{{date_of_birth}}' => $customer->date_of_birth?->format('d F Y') ?? '',
            '{{points}}' => $customer->loyaltyWallet?->total_points ?? '0',
            '{{tier}}' => $customer->loyaltyWallet?->tier ?? 'basic',
        ];

        return strtr($content, $replacements);
    }

    /**
     * Get greeting statistics
     */
    public function getStats(?int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $logs = AutoGreetingLog::where('created_at', '>=', $startDate)
            ->selectRaw('rule_id, status, COUNT(*) as count')
            ->groupBy('rule_id', 'status')
            ->get();

        $stats = [
            'total_sent' => 0,
            'total_failed' => 0,
            'by_rule' => [],
        ];

        foreach ($logs as $log) {
            if ($log->status === 'sent') {
                $stats['total_sent'] += $log->count;
            } else {
                $stats['total_failed'] += $log->count;
            }

            if (!isset($stats['by_rule'][$log->rule_id])) {
                $stats['by_rule'][$log->rule_id] = [
                    'rule_id' => $log->rule_id,
                    'sent' => 0,
                    'failed' => 0,
                ];
            }

            if ($log->status === 'sent') {
                $stats['by_rule'][$log->rule_id]['sent'] += $log->count;
            } else {
                $stats['by_rule'][$log->rule_id]['failed'] += $log->count;
            }
        }

        return $stats;
    }

    /**
     * Preview greeting content for a customer
     */
    public function previewGreeting(AutoGreetingRule $rule, Customer $customer): array
    {
        return [
            'subject' => $this->personalizeContent($rule->template_subject, $customer),
            'body' => $this->personalizeContent($rule->template_body, $customer),
        ];
    }
}

