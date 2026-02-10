<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutoGreetingLog extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'rule_id',
        'customer_id',
        'channel',
        'status',
        'sent_at',
        'error_message',
        'tracking_token',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function rule()
    {
        return $this->belongsTo(AutoGreetingRule::class, 'rule_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
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

    public static function generateTrackingToken(): string
    {
        return bin2hex(random_bytes(16));
    }
}

