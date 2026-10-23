<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItemSize extends Model
{
    /**
     * Attributes that can be mass assigned.
     */
    protected $fillable = [
        'menu_item_id',
        'name',
        'code',
        'price',
        'sort_order',
        'is_available',
    ];

    /**
     * Cast database values to appropriate PHP types.
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sort_order' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    /**
     * The menu item associated with this size.
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}
