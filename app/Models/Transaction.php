<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'event_id', 'order_id', 'customer_name', 'customer_email',
        'customer_phone', 'coupon_code', 'discount_amount', 'total_price',
        'status', 'snap_token', 'checked_in_at', 'checked_in_by',
    ];

    protected $casts = [
        'discount_amount' => 'integer',
        'checked_in_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function checkInOfficer()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function isPaid(): bool
    {
        return in_array(strtolower($this->status), ['settlement', 'success']);
    }
}
