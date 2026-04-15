<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'taxPercentage' => (float) $this->tax_percentage,
            'serviceChargePercentage' => (float) $this->service_charge_percentage,
            'taxEnabled' => (bool) $this->tax_enabled,
            'serviceChargeEnabled' => (bool) $this->service_charge_enabled,
            'deliveryCharge' => (float) $this->delivery_charge,

            'currency' => $this->currency,

            'updatedAt' => $this->updated_at,
        ];
    }
}
