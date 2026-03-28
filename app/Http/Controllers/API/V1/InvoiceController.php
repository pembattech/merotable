<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Http\Resources\V1\InvoiceResource;

use App\Models\Invoice;
use App\Models\Order;

class InvoiceController extends Controller
{
    public function store(Request $request)
    {
        $order = Order::with(['invoice', 'orderItems.menuItem'])
            ->findOrFail($request->order_id);

        if ($order->invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice already exists for this order',
                'data' => new InvoiceResource($order->invoice),
            ], 409);
        }

        $invoice = Invoice::create([
            'restaurant_id' => $order->restaurant_id,
            'order_id' => $order->id,
            'table_id' => $request->table_id,
            'subtotal' => $request->subtotal,
            'tax_amount' => $request->tax_amount ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'service_charge' => $request->service_charge ?? 0,
            'total_amount' => $request->total_amount,
            'payment_method' => $request->payment_method,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        // $invoice->load('order.orderItems.menuItem');
        $invoice->load(['restaurant', 'order.orderItems.menuItem', 'table']);

        activityLog(
            'invoice_created',
            'Invoice created successfully',
            [
                'restaurant_id' => $order->restaurant_id,
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'order_id' => $order->id,
                'table_id' => $invoice->table_id,
                'items' => $order->orderItems->map(function ($item) {
                    return [
                        'menu_item_id' => $item->menu_item_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                }),
                'total_amount' => $invoice->total_amount,
                'payment_method' => $invoice->payment_method,
                'paid_at' => $invoice->paid_at,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => new InvoiceResource($invoice),
            'message' => 'Invoice created successfully.'
        ], 201);
    }
}
