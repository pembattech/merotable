<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinancialReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,

            'filters' => [
                'dateFrom' => $this['filters']['date_from'],
                'dateTo' => $this['filters']['date_to'],
            ],

            'orders' => $this['orders']->map(function ($order) {
                return [
                    'id' => '#mt-' . $order->id,
                    'status' => $order->status,
                    'createdAt' => $order->created_at,
                    'totalAmount' => $order->total_amount,

                    'orderItems' => $order->orderItems->map(function ($item) {
                        return [
                            'menuItem' => $item->menuItem?->name,
                            'quantity' => $item->quantity,
                            'total' => $item->quantity * $item->price,
                        ];
                    }),
                ];
            }),

            'totalOrders' => $this['total_orders'],

            'topItems' => collect($this['top_items'])
                ->map(fn($item) => [
                    'name' => $item,
                    'totalQuantity' => (int) $item->total_quantity,
                    'revenue' => (float) $item->revenue,
                ])
                ->values(),

            'peakHours' => collect($this['peak_hours'])
                ->map(fn($item) => [
                    'timeline' => $item['timeline'],
                    'totalOrders' => (int) $item['total_orders'],
                ])
                ->values(),

            'tablePerformance' => collect($this['table_performance'])
                ->map(fn($table) => [
                    'tableNumber' => $table->table_number,
                    'totalOrders' => (int) $table->total_orders,
                    'totalRevenue' => (float) $table->total_revenue,
                ])
                ->values(),


        ];
    }
}
