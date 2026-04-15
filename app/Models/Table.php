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

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getRestaurantDetails()
    {
        return $this->restaurant;
    }

    public static function getIdByTableNumber($tableNumber)
    {
        return self::where('table_number', $tableNumber)->value('id');
    }
}
