<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'restaurant_id',
        'category_id',
        'name',
        'price',
        'is_available',
    ];

    /**
     * A menu item belongs to a restaurant
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * A menu item belongs to a category (optional)
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
