<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use \App\Http\Resources\V1\CategoryResource;
use \App\Http\Resources\V1\MenuItemResource;

class RestaurantResource extends JsonResource
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

            'slug' => $this->slug,

            // Core restaurant info
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,

            // Computed property to quickly check if restaurant is active
            'isActive' => $this->status === 'active',

            // Timestamps in camelCase for frontend consistency
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),

            // Optional: human-readable created time
            'createdAgo' => $this->created_at->diffForHumans(),

            // Counts of related entities for dashboard stats
            'staffCount' => $this->staff()->count(),
            'tableCount' => $this->tables()->count(),
            'menuCount' => $this->menuItems()->count(),

            // Relationships (optional, eager load in controller to avoid N+1)
            'categories' => CategoryResource::collection(
                $this->whenLoaded('categories')
            ),
            'menuItems' => MenuItemResource::collection(
                $this->whenLoaded('menuItems')
            ),

            // Optional: add logo or image URL if available
            'logo' => $this->logo_url ?? null,
            'address' => $this->address ?? null,
        ];
    }
}
