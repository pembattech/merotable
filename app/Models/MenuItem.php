<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    /**
     * Attributes that can be mass assigned.
     */
    protected $fillable = [
        'restaurant_id',
        'category_id',
        'name',
        'image',
        'description',
        'price',
        'is_vegetarian',
        'is_popular',
        'is_available',
        'has_sizes',
        'size_type',
    ];

    /**
     * Cast database values to appropriate PHP types.
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_vegetarian' => 'boolean',
            'is_popular' => 'boolean',
            'is_available' => 'boolean',
            'has_sizes' => 'boolean',
        ];
    }

    /**
     * The restaurant that owns this menu item.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * The category this menu item belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * All serving sizes and their prices.
     */
    public function sizes(): HasMany
    {
        return $this->hasMany(MenuItemSize::class)
            ->orderBy('sort_order');
    }

    /**
     * Scope: retrieve only available menu items.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope: retrieve popular menu items.
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Scope: retrieve vegetarian menu items.
     */
    public function scopeVegetarian($query)
    {
        return $query->where('is_vegetarian', true);
    }
}
