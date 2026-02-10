<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerTag extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'description',
    ];

    protected $casts = [
        'color' => 'string',
    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_tag_pivot', 'tag_id', 'customer_id')
            ->withPivot(['tagged_by'])
            ->withTimestamps();
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}

