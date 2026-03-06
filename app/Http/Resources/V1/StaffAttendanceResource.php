<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffAttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date' => $this->date->format('Y-m-d'),
            'checkIn' => $this->check_in ? $this->check_in->format('H:i') : null,
            'checkOut' => $this->check_out ? $this->check_out->format('H:i') : null,
            'status' => $this->status,
            'totalHours' => $this->total_hours ?? 0,
            'isLate' => $this->status === 'late',
        ];

    }
}
