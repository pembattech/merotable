<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicStaffResource extends JsonResource
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
            'role' => $this->role,
            'isActive' => $this->is_active,
            'phone' => $this->phone,
            'createdAt' => $this->created_at,
            'restaurant' => [
                'name' => $this->restaurant?->name,
                'slug' => $this->restaurant?->slug,

            ],
            'todayAttendance' => $this->todayAttendance ? new StaffAttendanceResource($this->todayAttendance) : null,
            
        ];
    }
}
