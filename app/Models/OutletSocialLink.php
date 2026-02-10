<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutletSocialLink extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'outlet_id',
        'platform',
        'label',
        'url',
        'icon',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForOutlet($query, int $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public function getPlatformIconAttribute(): string
    {
        return match ($this->platform) {
            'instagram' => 'bi-instagram',
            'facebook' => 'bi-facebook',
            'tiktok' => 'bi-tiktok',
            'snapchat' => 'bi-snapchat',
            'whatsapp' => 'bi-whatsapp',
            'website' => 'bi-globe',
            'email' => 'bi-envelope',
            default => 'bi-link-45deg',
        };
    }

    public function getBrandColorAttribute(): string
    {
        return match ($this->platform) {
            'instagram' => '#E4405F',
            'facebook' => '#1877F2',
            'tiktok' => '#000000',
            'snapchat' => '#FFFC00',
            'whatsapp' => '#25D366',
            default => $this->color ?? '#6c757d',
        };
    }
}

