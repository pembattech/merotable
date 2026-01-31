<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            // Ownership & relations
            'restaurantId' => $this->restaurant_id,
            'categoryId' => $this->category_id,

            // Menu item info
            'name' => $this->name,
            'price' => (float) $this->price,

            // Availability (cast to boolean for frontend clarity)
            'isAvailable' => (bool) $this->is_available,

            // Timestamps (camelCase for JS apps)
            'createdAt' => $this->created_at->toISOString(),
            'updatedAt' => $this->updated_at->toISOString(),

            // Optional: related category (only if eager-loaded)
            'category' => new CategoryResource(
                $this->whenLoaded('category')
            ),
        ];

    }
}
