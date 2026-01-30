<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderActivity extends Model
{
    protected $fillable = [
        'order_id',
        'staff_id',
        'action',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class);
    }
}
