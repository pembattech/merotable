<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantSetting extends Model
{
    protected $fillable = [
        'restaurant_id',
        'tax_percentage',
        'service_charge_percentage',
        'tax_enabled',
        'service_charge_enabled',
        'delivery_charge',
        'currency'
    ];


    protected function casts(): array
    {
        return [
            'tax_enabled' => 'boolean',
            'tax_percentage' => 'decimal:2',
            'service_charge_enabled' => 'boolean',
            'service_charge_percentage' => 'decimal:2',
            'delivery_charge' => 'decimal:2',
        ];
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }


}