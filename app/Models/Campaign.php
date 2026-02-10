<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'channel',
        'status',
        'segment_definition_json',
        'subject',
        'body',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'sent_count',
        'failed_count',
        'opened_count',
        'clicked_count',
        'bookings_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'segment_definition_json' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function messages()
    {
        return $this->hasMany(CampaignMessage::class);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function isReadyToSend(): bool
    {
        return $this->status === 'draft' 
            && $this->subject 
            && $this->body;
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSending(): bool
    {
        return $this->status === 'sending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isSent(): bool
    {
        return in_array($this->status, ['sending', 'sent', 'completed']);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['draft', 'ready']);
    }

    public function markAsSending(): self
    {
        $this->status = 'sending';
        $this->save();
        return $this;
    }

    public function markAsCompleted(): self
    {
        $this->status = 'completed';
        $this->sent_at = now();
        $this->save();
        return $this;
    }

    public function markAsCancelled(): self
    {
        $this->status = 'cancelled';
        $this->save();
        return $this;
    }

    public function updateStats(): self
    {
        $this->sent_count = $this->messages()->where('status', 'sent')->count();
        $this->failed_count = $this->messages()->where('status', 'failed')->count();
        $this->opened_count = $this->messages()->where('status', 'opened')->count();
        $this->clicked_count = $this->messages()->where('status', 'clicked')->count();
        $this->save();
        return $this;
    }
}

