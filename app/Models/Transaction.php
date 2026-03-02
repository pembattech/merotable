<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'restaurant_id',
        'plan_id',
        'amount',
        'type',
        'reference_id',
        'payment_method',
        'status',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function plan(){
        return $this->belongsTo(Plan::class);
    }

}