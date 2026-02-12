<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicMenuItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => (float) $this->price,

            // Availability (cast to boolean for frontend clarity)
            'isAvailable' => (bool) $this->is_available,

            // // Timestamps (camelCase for JS apps)
            'createdAt' => $this->created_at->toISOString(),
            // 'updatedAt' => $this->updated_at->toISOString(),

            // expose category as object
            'category' => [
                'name' => $this->category?->name
            ],

            'restaurant' => [
                'name' => $this->restaurant?->name,
            ],
        ];
    }
}
