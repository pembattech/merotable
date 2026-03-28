<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'invoiceNumber' => $this->invoice_number,

            'restaurant' => $this->when(
                $this->relationLoaded('restaurant') && $this->restaurant,
                fn() => [
                    'restaurantName' => $this->restaurant->name,
                    'restaurantContact' => $this->restaurant->contact_number,
                    'restaurantAddress' => $this->restaurant->address,
                    ]
            ),

            'orderId' => $this->order_id,

            'tableNumber' => $this->when(
                $this->relationLoaded('table') && $this->table,
                fn() => $this->table->table_number
            ),

            'orderItems' => $this->when(
                $this->relationLoaded('order') && $this->order,
                fn() => OrderItemsResource::collection($this->order->orderItems)
            ),

            'subtotal' => $this->subtotal,
            'taxAmount' => $this->tax_amount,
            'discountAmount' => $this->discount_amount,
            'serviceCharge' => $this->service_charge,
            'totalAmount' => $this->total_amount,

            'paymentMethod' => $this->payment_method,
            'paymentStatus' => $this->payment_status,

            'paidAt' => $this->paid_at?->toDateTimeString(),
            'createdAt' => $this->created_at->toDateTimeString(),
        ];
    }
}
