<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'plan' => [
                'id' => $this->plan?->id,
                'name' => $this->plan?->name,
                'price' => $this->plan?->price ?? null,
            ],

            'startsAt' => $this->starts_at,
            'expiresAt' => $this->expires_at,

            'daysLeft' => $this->expires_at ? now()->diffInDays($this->expires_at, false) : 0,
            'status' => $this->status,

            'created_at' => $this->created_at,
        ];
    }
}
