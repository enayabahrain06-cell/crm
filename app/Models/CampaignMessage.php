<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignMessage extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'customer_id',
        'email',
        'status',
        'sent_at',
        'opened_at',
        'clicked_at',
        'error_message',
        'tracking_token',
        'meta',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'meta' => 'array',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeQueued($query)
    {
        return $query->where('status', 'queued');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function markAsSent(): self
    {
        $this->status = 'sent';
        $this->sent_at = now();
        $this->save();
        return $this;
    }

    public function markAsFailed(string $error): self
    {
        $this->status = 'failed';
        $this->error_message = $error;
        $this->save();
        return $this;
    }

    public function markAsOpened(): self
    {
        if ($this->status === 'sent') {
            $this->status = 'opened';
            $this->opened_at = now();
            $this->save();
        }
        return $this;
    }

    public function markAsClicked(): self
    {
        if (in_array($this->status, ['sent', 'opened'])) {
            $this->status = 'clicked';
            $this->clicked_at = now();
            $this->save();
        }
        return $this;
    }

    public static function generateTrackingToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isSent(): bool
    {
        return in_array($this->status, ['sent', 'opened', 'clicked']);
    }

    public function isOpened(): bool
    {
        return in_array($this->status, ['opened', 'clicked']);
    }
}

