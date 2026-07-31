<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'category_id', 'tenant_id', 'title', 'description', 'date',
        'location', 'price', 'stock', 'poster_path'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating(): float
    {
        return round((float) $this->reviews()->avg('rating'), 1);
    }

    public function hasEnded(): bool
    {
        return $this->date->lt(now());
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopePubliclyVisible($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('tenant_id')
                ->orWhereHas('tenant', fn ($tenant) => $tenant->where('is_approved', true));
        });
    }
}
