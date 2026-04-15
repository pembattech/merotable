<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
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

            'restaurant' => $this->whenLoaded('restaurant', function () {
                return [
                    'name' => $this->restaurant->name,
                    'slug' => $this->restaurant->slug,
                ];
            }),

            'areaName' => $this->area_name,
            'tableNumber' => $this->table_number,
            'status' => $this->status,

            'orders' => OrderResource::collection(
                $this->whenLoaded('orders')
            ),
        ];
    }
}
