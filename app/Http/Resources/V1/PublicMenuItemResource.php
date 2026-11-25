<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicMenuItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Determine default size code
        $defaultSizeCode = match ($this->size_type) {
            'hf' => 'full',
            'sml' => 'medium',
            default => null,
        };

        // Find default size price (Full / Medium)
        $defaultSize = $this->has_sizes
            ? $this->sizes->firstWhere('code', $defaultSizeCode)
            : null;

        return [
            'id' => $this->id,
            'name' => $this->name,

            // Category
            'cat' => $this->category?->name,

            // Default price: Full / Medium / Single
            'price' => (float) (
                $defaultSize?->price ?? $this->price
            ),

            // Dietary preference
            'veg' => (bool) $this->is_vegetarian,

            // Popular item
            'pop' => (bool) $this->is_popular,

            // Out of stock
            'out' => !(bool) $this->is_available,

            // Size configuration: hf / sml / null
            'sizes' => $this->has_sizes
                ? $this->size_type
                : null,

            // Available size options with prices
            'sizeOptions' => $this->has_sizes
                ? $this->sizes
                    ->where('is_available', true)
                    ->map(fn ($size) => [
                        'id' => $size->id,
                        'name' => $size->name,
                        'code' => $size->code,
                        'price' => (float) $size->price,
                    ])
                    ->values()
                : [],

            'description' => $this->description,

            'image' => $this->image
                ? asset('storage/' . $this->image)
                : null,

            'isAvailable' => (bool) $this->is_available,

            'createdAt' => $this->created_at?->toISOString(),
        ];
    }
}
