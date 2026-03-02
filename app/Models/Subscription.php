<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'restaurant_id',
        'plan_id',
        'starts_at',
        'expires_at',
        'status',
        'billing_cycle',
    ];

    protected $dates = [
        'starts_at',
        'expires_at',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // public function isActive(): bool
    // {
    //     return $this->status === 'active'
    //         && $this->expires_at
    //         && $this->expires_at->isFuture();
    // }

    public function daysLeft(): int
    {
        if (!$this->expires_at) {
            return 0;
        }

        return now()->diffInDays($this->expires_at, false);
    }
}