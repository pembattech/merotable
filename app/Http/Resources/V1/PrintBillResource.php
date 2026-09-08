<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrintBillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        // Restaurant settings
        $taxPercentage = $this->restaurant?->setting?->tax_percentage ?? 0;
        $serviceChargePercentage = $this->restaurant?->setting?->service_charge_percentage ?? 0;

        // Calculate subtotal from order items
        $subtotal = $this->orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        // Discount
        $discountAmount = $this->discount_amount ?? 0;

        // Amount after discount
        $taxableAmount = max(0, $subtotal - $discountAmount);

        // Calculate tax
        $taxAmount = ($taxableAmount * $taxPercentage) / 100;

        // Calculate service charge
        $serviceCharge = ($taxableAmount * $serviceChargePercentage) / 100;

        // Final total
        $totalAmount = $taxableAmount + $taxAmount + $serviceCharge;

        return [
            'restaurant' => [
                'restaurantName' => $this->restaurant?->name,
                'restaurantContact' => $this->restaurant?->contact_number,
                'restaurantAddress' => $this->restaurant?->address,

                'restaurantTaxPercentage' => $taxPercentage,
                'restaurantServiceChargePercentage' => $serviceChargePercentage,
            ],

            'order' => [
                'id' => $this->id,

                'tableNumber' => $this->table?->table_number,

                'printedAt' => $this->bill_printed_at,

                'items' => $this->orderItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'menuItem' => $item->menuItem?->name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'total' => $item->quantity * $item->price,
                    ];
                }),

                'subtotal' => round($subtotal, 2),

                'discountAmount' => round($discountAmount, 2),

                'taxPercentage' => $taxPercentage,
                'taxAmount' => round($taxAmount, 2),

                'serviceChargePercentage' => $serviceChargePercentage,
                'serviceCharge' => round($serviceCharge, 2),

                'totalAmount' => round($totalAmount, 2),

                'paymentStatus' => $this->payment_status,
            ],
        ];
    }
}
