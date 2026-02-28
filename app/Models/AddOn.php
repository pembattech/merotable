<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AddOn extends Model
{
    protected $fillable = [
        'restaurant_id',
        'add_on_id',
        'quantity',
        'starts_at',
        'expires_at',
    ];

    protected $dates = [
        'starts_at',
        'expires_at',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function addOn(): BelongsTo
    {
        return $this->belongsTo(AddOn::class);
    }

    public function isActive(): bool
    {
        return $this->expires_at
            && $this->expires_at->isFuture();
    }
}