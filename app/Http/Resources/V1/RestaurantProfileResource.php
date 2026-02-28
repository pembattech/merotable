<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantProfileResource extends JsonResource
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
            'email' => $this->email,
            'slug' => $this->slug,
            'status' => $this->status,
            'approvedAt' => $this->approved_at,
            'description' => $this->description,
            'contactNumber' => $this->contact_number,
            'address' => $this->address,
            'logo' => $this->logo,

            'settings' => new RestaurantSettingResource($this->whenLoaded('setting')),

            'documents' => RestaurantDocumentResource::collection(
                $this->whenLoaded('documents')
            ),

            'created_at' => $this->created_at,
        ];
    }
}
