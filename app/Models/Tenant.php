<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'logo_path', 'owner_id', 'is_approved', 'approved_at'];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function staff()
    {
        return $this->hasMany(User::class, 'tenant_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function totalRevenue(): int
    {
        return (int) \App\Models\Transaction::whereIn('status', ['settlement', 'success'])
            ->whereIn('event_id', $this->events()->pluck('id'))
            ->sum('total_price');
    }
}