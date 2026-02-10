<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyRule extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'active',
        'condition_json',
        'formula_json',
        'priority',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'active' => 'boolean',
        'condition_json' => 'array',
        'formula_json' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'asc');
    }

    public function matchesConditions(Visit $visit, Customer $customer): bool
    {
        $conditions = $this->condition_json ?? [];
        
        if (isset($conditions['outlet_ids']) && !empty($conditions['outlet_ids'])) {
            $outletIds = $conditions['outlet_ids'];
            // Ensure outlet_ids is an array (handle string/numeric cases)
            if (is_string($outletIds)) {
                $outletIds = json_decode($outletIds, true) ?? [];
            }
            if (!is_array($outletIds) || !in_array($visit->outlet_id, $outletIds)) {
                return false;
            }
        }
        
        if (isset($conditions['visit_type']) && !empty($conditions['visit_type'])) {
            if ($visit->visit_type !== $conditions['visit_type']) {
                return false;
            }
        }
        
        if (isset($conditions['min_spend']) && $visit->bill_amount < $conditions['min_spend']) {
            return false;
        }
        
        if (isset($conditions['first_visit']) && $conditions['first_visit'] === true) {
            if (!$visit->isFirstVisitAtOutlet()) {
                return false;
            }
        }
        
        if (isset($conditions['birthday_visit']) && $conditions['birthday_visit'] === true) {
            if (!$visit->isBirthdayVisit()) {
                return false;
            }
        }
        
        return true;
    }

    public function calculatePoints(Visit $visit): int
    {
        $formula = $this->formula_json ?? [];
        
        if (isset($formula['fixed_points'])) {
            return (int) $formula['fixed_points'];
        }
        
        if (isset($formula['points_per_amount'])) {
            $rate = (float) $formula['points_per_amount'];
            $points = floor($visit->bill_amount * $rate);
            if (isset($formula['max_points'])) {
                $points = min($points, (int) $formula['max_points']);
            }
            return $points;
        }
        
        return 0;
    }
}

