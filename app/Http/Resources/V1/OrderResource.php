<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'createdAt' => $this->created_at,
            'totalAmount' => $this->total_amount,
            'itemsCount' => $this->orderItems->count(),

            // ✅ Use Resource for order items
            'orderItems' => OrderItemsResource::collection(
                $this->whenLoaded('orderItems')
            ),

            'activities' => OrderActivityResource::collection(
                $this->whenLoaded('activities')
            ),
        ];
    }
}
