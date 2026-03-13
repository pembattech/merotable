<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Table extends Model
{
    protected $fillable = [
        'restaurant_id',
        'area_name',
        'table_number',
        'status',
        'qr_token',
    ];


    protected static function booted()
    {
        static::creating(function ($table) {
            $table->qr_token = Str::random(20);
        });
    }

    /**
     * A table belongs to a restaurant
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getRestaurantDetails()
    {
        return $this->restaurant;
    }
}
