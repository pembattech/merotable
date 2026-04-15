<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Resources\V1\InvoiceResource;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Table;

class InvoiceController extends Controller
{

    public function getInvoices(Request $request)
    {
        // GET /invoices
        // GET /invoices?payment_status=pending&restaurant_id=1
        // GET /invoices?date_from=2025-01-01&date_to=2025-03-31
        // GET /invoices?search=INV-001&sort_by=total_amount&sort_order=asc
        // GET /invoices?per_page=50&payment_method=esewa

        $validated = $request->validate([
            'page' => 'sometimes|integer|min:1',
            'restaurant_id' => 'sometimes|exists:restaurants,id',
            'payment_status' => 'sometimes|in:pending,paid,failed',
            'payment_method' => 'sometimes|in:cash,card,esewa,khalti',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'search' => 'sometimes|string|max:100',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'sort_by' => 'sometimes|in:invoice_number,total_amount,paid_at,created_at',
            'sort_order' => 'sometimes|in:asc,desc',
        ]);

        $invoices = Invoice::query()
            ->with(['restaurant', 'order.orderItems.menuItem', 'table'])
            ->when(
                isset($validated['restaurant_id']),
                fn($q) =>
                $q->where('restaurant_id', $validated['restaurant_id'])
            )
            ->when(
                isset($validated['payment_status']),
                fn($q) =>
                $q->where('payment_status', $validated['payment_status'])
            )
            ->when(
                isset($validated['payment_method']),
                fn($q) =>
                $q->where('payment_method', $validated['payment_method'])
            )
            ->when(
                isset($validated['date_from']),
                fn($q) =>
                $q->whereDate('created_at', '>=', $validated['date_from'])
            )
            ->when(
                isset($validated['date_to']),
                fn($q) =>
                $q->whereDate('created_at', '<=', $validated['date_to'])
            )
            ->when(
                isset($validated['search']),
                fn($q) =>
                $q->where('invoice_number', 'like', "%{$validated['search']}%")
            )
            ->orderBy(
                $validated['sort_by'] ?? 'created_at',
                $validated['sort_order'] ?? 'desc'
            )
            ->paginate($validated['per_page'] ?? 15);

        return response()->json([
            'success' => true,
            'data' => InvoiceResource::collection($invoices->items()),
            'meta' => [
                'current_page' => $invoices->currentPage(),
                'per_page' => $invoices->perPage(),
                'total' => $invoices->total(),
                'last_page' => $invoices->lastPage(),
                'from' => $invoices->firstItem(),  // first record number on this page
                'to' => $invoices->lastItem(),   // last record number on this page
                'has_next' => $invoices->hasMorePages(),
                'has_prev' => $invoices->currentPage() > 1,
            ],
        ]);
    }


    public function getInvoice(string $invoiceNumber)
    {
        try {
            $invoice = Invoice::with([
                'restaurant',
                'order.orderItems.menuItem',
                'table',
            ])
                ->where('invoice_number', $invoiceNumber)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => new InvoiceResource($invoice),
                // 'data' => [
                //     'id' => $invoice->id,
                //     'invoice_number' => $invoice->invoice_number,

                //     'restaurant' => [
                //         'id' => $invoice->restaurant->id,
                //         'name' => $invoice->restaurant->name,
                //     ],
                //     'table' => [
                //         'id' => $invoice->table->id,
                //         'number' => $invoice->table->number,
                //     ],
                //     'order' => [
                //         'id' => $invoice->order->id,
                //         'items' => $invoice->order->orderItems->map(fn($item) => [
                //             'id' => $item->id,
                //             'name' => $item->menuItem->name,
                //             'quantity' => $item->quantity,
                //             'unit_price' => $item->unit_price,
                //             'subtotal' => $item->quantity * $item->unit_price,
                //         ]),
                //     ],

                //     'subtotal' => $invoice->subtotal,
                //     'tax_amount' => $invoice->tax_amount,
                //     'discount_amount' => $invoice->discount_amount,
                //     'service_charge' => $invoice->service_charge,
                //     'total_amount' => $invoice->total_amount,

                //     'payment_method' => $invoice->payment_method,
                //     'payment_status' => $invoice->payment_status,
                //     'paid_at' => $invoice->paid_at?->toDateTimeString(),

                //     'created_at' => $invoice->created_at->toDateTimeString(),
                //     'updated_at' => $invoice->updated_at->toDateTimeString(),
                // ],
            ]);

        } catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => "Invoice '{$invoiceNumber}' not found.",
            ], 404);
        }
    }

    public function store(Request $request)
    {
        // $staff = auth('staff')->user();

        $order = Order::with(['invoice', 'orderItems.menuItem'])
            ->findOrFail($request->order_id);

        if ($order->invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice already exists for this order',
                'data' => new InvoiceResource($order->invoice),
            ], 409);
        }

        $tableId = Table::getIdByTableNumber($request->table_number);

        $invoice = Invoice::create([
            'restaurant_id' => $order->restaurant_id,
            'order_id' => $order->id,
            'table_id' => $tableId,
            'subtotal' => $request->subtotal,
            'tax_amount' => $request->tax_amount ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'service_charge' => $request->service_charge ?? 0,
            'total_amount' => $request->total_amount,
            'payment_method' => $request->payment_method,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

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
