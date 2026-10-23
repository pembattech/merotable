<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AdminMenuItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,

            // Pricing
            'price' => (float) $this->price,
            'pricingMode' => $this->pricing_mode ?? 'single',

            // Menu details
            'description' => $this->description,
            'image' => $this->image
                ? Storage::disk('public')->url($this->image)
                : null,

            // Menu attributes
            'isAvailable' => (bool) $this->is_available,
            'isVegetarian' => (bool) $this->is_vegetarian,
            'isPopular' => (bool) $this->is_popular,

            // Related sizes and their prices
            'sizes' => MenuItemSizeResource::collection(
                $this->whenLoaded('sizes')
            ),

            // Category
            'category' => new PublicCategoryResource(
                $this->whenLoaded('category')
            ),

            // Restaurant
            'restaurant' => new PublicRestaurantResource(
                $this->whenLoaded('restaurant')
            ),

            // Timestamps
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
