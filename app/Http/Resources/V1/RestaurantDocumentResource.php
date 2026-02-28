<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantDocumentResource extends JsonResource
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
            'documentType' => $this->document_type,
            'documentPath' => $this->document_path ? asset('storage/' . $this->document_path) : null,
            'status' => $this->status,
            'remarks' => $this->remarks,
            'verifiedAt' => $this->verified_at,
        ];
    }

}
