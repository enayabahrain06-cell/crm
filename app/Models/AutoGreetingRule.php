<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutoGreetingRule extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'trigger_type',
        'trigger_date',
        'nationality_filter',
        'channel',
        'template_subject',
        'template_body',
        'active',
        'days_before',
        'time',
        'description',
    ];

    protected $casts = [
        'active' => 'boolean',
        'template_body' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByTriggerType($query, string $type)
    {
        return $query->where('trigger_type', $type);
    }

    public function logs()
    {
        return $this->hasMany(AutoGreetingLog::class, 'rule_id');
    }

    public function shouldRunToday(): bool
    {
        if (!$this->active) {
            return false;
        }

        $today = now()->format('m-d');
        
        if ($this->trigger_type === 'birthday') {
            return true;
        }
        
        if ($this->trigger_type === 'fixed_date') {
            return $this->trigger_date === $today;
        }
        
        return false;
    }

    public function getRecipientsForToday(): \Illuminate\Database\Eloquent\Collection
    {
        $query = Customer::active();
        
        if ($this->nationality_filter) {
            $query->where('nationality', $this->nationality_filter);
        }
        
        if ($this->trigger_type === 'birthday') {
            return $query->birthdayToday()->get();
        }
        
        if ($this->trigger_type === 'fixed_date') {
            $month = substr($this->trigger_date, 0, 2);
            $day = substr($this->trigger_date, 3, 2);
            return $query->birthdayOnDate($month, $day)->get();
        }
        
        return new \Illuminate\Database\Eloquent\Collection();
    }

    public function renderTemplate(Customer $customer): array
    {
        $subject = $this->template_subject ?? '';
        $body = $this->template_body['html'] ?? '';
        
        $replacements = [
            '{{name}}' => $customer->name,
            '{{first_name}}' => explode(' ', $customer->name)[0],
            '{{company_name}}' => $customer->company_name ?? '',
            '{{outlet_name}}' => $customer->firstRegistrationOutlet?->name ?? '',
            '{{email}}' => $customer->email ?? '',
        ];
        
        $subject = strtr($subject, $replacements);
        $body = strtr($body, $replacements);
        
        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }
}

