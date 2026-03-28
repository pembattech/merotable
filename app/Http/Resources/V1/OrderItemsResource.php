<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            // Menu item info
            'menuItem' => [
                'name' => $this->whenLoaded('menuItem')?->name,
            ],

            'status' => $this->status,

            'quantity' => $this->quantity,
            'price' => $this->price,

            'total' => $this->quantity * $this->price,
        ];
    }
}


