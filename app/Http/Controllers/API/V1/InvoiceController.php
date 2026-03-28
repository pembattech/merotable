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
                'data'    => new InvoiceResource($order->invoice),
            ], 409);
        }

        $invoice = Invoice::create([
            'restaurant_id'   => $order->restaurant_id,
            'order_id'        => $order->id,
            'table_id'        => $request->table_id,
            'subtotal'        => $request->subtotal,
            'tax_amount'      => $request->tax_amount ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'service_charge'  => $request->service_charge ?? 0,
            'total_amount'    => $request->total_amount,
            'payment_method'  => $request->payment_method,
            'payment_status'  => 'paid',
            'paid_at'         => now(),
        ]);

        // $invoice->load('order.orderItems.menuItem');
         $invoice->load(['restaurant', 'order.orderItems.menuItem', 'table']);

        return response()->json([
            'success' => true,
            'data' => new InvoiceResource($invoice),
            'message' => 'Invoice created successfully.'
        ], 201);
    }
}
