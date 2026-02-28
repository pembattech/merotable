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
    
    
public function restaurant()
{
    return $this->belongsTo(Restaurant::class);
}


}