<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\V1\InvoiceResource;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Table;
use App\Models\OrderActivity;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{

    public function getInvoices(Request $request)
    {
        // GET /invoices
        // GET /invoices?date_from=2026-09-01&date_to=2026-09-19
        // GET /invoices?payment_status=paid
        // GET /invoices?search=INV-001
        // GET /invoices?per_page=15&page=2

        $restaurantId = auth('restaurant')->id();

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


        /*
        |--------------------------------------------------------------------------
        | Default Date = Today
        |--------------------------------------------------------------------------
        */

        if (!isset($validated['date_from']) && !isset($validated['date_to'])) {

            $validated['date_from'] = today()->toDateString();

            $validated['date_to'] = today()->toDateString();

        }


        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $baseQuery = Invoice::query()

            ->where('restaurant_id', $restaurantId)

            ->when(
                isset($validated['payment_status']),
                fn($q) =>
                    $q->where(
                        'payment_status',
                        $validated['payment_status']
                    )
            )

            ->when(
                isset($validated['payment_method']),
                fn($q) =>
                    $q->where(
                        'payment_method',
                        $validated['payment_method']
                    )
            )

            ->when(
                isset($validated['date_from']),
                fn($q) =>
                    $q->whereDate(
                        'created_at',
                        '>=',
                        $validated['date_from']
                    )
            )

            ->when(
                isset($validated['date_to']),
                fn($q) =>
                    $q->whereDate(
                        'created_at',
                        '<=',
                        $validated['date_to']
                    )
            )

            ->when(
                isset($validated['search']),
                fn($q) =>
                    $q->where(
                        'invoice_number',
                        'like',
                        '%' . $validated['search'] . '%'
                    )
            );


        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        |
        | Calculate summary BEFORE pagination so it represents
        | the complete selected date range.
        |
        */

        $summaryQuery = clone $baseQuery;


        $totalInvoices = $summaryQuery->count();


        $paidAmount = (clone $baseQuery)
            ->where('payment_status', 'paid')
            ->sum('total_amount');


        $unpaidAmount = (clone $baseQuery)
            ->where('payment_status', 'pending')
            ->sum('total_amount');


        $averageInvoice = $totalInvoices > 0
            ? round(
                (float) (clone $baseQuery)->avg('total_amount'),
                2
            )
            : 0;


        /*
        |--------------------------------------------------------------------------
        | Paginated Invoices
        |--------------------------------------------------------------------------
        */

        $invoices = (clone $baseQuery)

            ->with([
                'restaurant',
                'order.orderItems.menuItem',
                'table'
            ])

            ->orderBy(
                $validated['sort_by'] ?? 'created_at',
                $validated['sort_order'] ?? 'desc'
            )

            ->paginate(
                $validated['per_page'] ?? 15
            );


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'data' => InvoiceResource::collection(
                $invoices->items()
            ),

            'summary' => [

                'total_invoices' => $totalInvoices,

                'paid_amount' => (float) $paidAmount,

                'unpaid_amount' => (float) $unpaidAmount,

                'average_invoice' => $averageInvoice,

            ],

            'meta' => [

                'current_page' => $invoices->currentPage(),

                'per_page' => $invoices->perPage(),

                'total' => $invoices->total(),

                'last_page' => $invoices->lastPage(),

                'from' => $invoices->firstItem(),

                'to' => $invoices->lastItem(),

                'has_next' => $invoices->hasMorePages(),

                'has_prev' => $invoices->currentPage() > 1,

                'date_from' => $validated['date_from'] ?? null,

                'date_to' => $validated['date_to'] ?? null,

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
        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'table_number' => ['required'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string'],
        ]);

        try {
            DB::beginTransaction();

            // Load order with everything required for invoice calculation
            $order = Order::with([
                'invoice',
                'orderItems.menuItem',
                'restaurant.setting',
            ])->findOrFail($validated['order_id']);

            // Prevent duplicate invoice
            if ($order->invoice) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Invoice already exists for this order.',
                    'data' => new InvoiceResource($order->invoice),
                ], 409);
            }

            // Get table
            $tableId = Table::getIdByTableNumber($validated['table_number']);

            if (!$tableId) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Table not found.',
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | Calculate Invoice
            |--------------------------------------------------------------------------
            */

            // Subtotal from order items
            $subtotal = $order->orderItems->sum(function ($item) {
                return $item->quantity * $item->price;
            });

            // Discount
            $discountAmount = $validated['discount_amount'] ?? 0;

            // Make sure discount cannot exceed subtotal
            $discountAmount = min($discountAmount, $subtotal);

            // Amount after discount
            $taxableAmount = max(0, $subtotal - $discountAmount);

            // Restaurant settings
            $taxPercentage = $order->restaurant?->setting?->tax_percentage ?? 0;

            $serviceChargePercentage =
                $order->restaurant?->setting?->service_charge_percentage ?? 0;

            // Calculate tax
            $taxAmount = ($taxableAmount * $taxPercentage) / 100;

            // Calculate service charge
            $serviceCharge = ($taxableAmount * $serviceChargePercentage) / 100;

            // Final total
            $totalAmount =
                $taxableAmount +
                $taxAmount +
                $serviceCharge;

            /*
            |--------------------------------------------------------------------------
            | Create Invoice
            |--------------------------------------------------------------------------
            */

            $invoice = Invoice::create([
                'restaurant_id' => $order->restaurant_id,
                'order_id' => $order->id,
                'table_id' => $tableId,

                'subtotal' => round($subtotal, 2),

                'discount_amount' => round($discountAmount, 2),

                'tax_percentage' => round($taxPercentage, 2),
                'tax_amount' => round($taxAmount, 2),

                'service_charge_percentage' =>
                    round($serviceChargePercentage, 2),

                'service_charge' => round($serviceCharge, 2),

                'total_amount' => round($totalAmount, 2),

                'payment_method' => $validated['payment_method'],
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Load Invoice Relationships
            |--------------------------------------------------------------------------
            */

            $invoice->load([
                'restaurant',
                'order.orderItems.menuItem',
                'table',
            ]);


            OrderActivity::create([
                'order_id' => $order->id,
                'staff_id' => auth('staff')->user()?->id,
                'action' => 'checkout',
                'meta' => [
                    'invoice_number' => $invoice->invoice_number,
                ],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */

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
                            'total' => $item->quantity * $item->price,
                        ];
                    })->values(),

                    'subtotal' => $invoice->subtotal,
                    'discount_amount' => $invoice->discount_amount,

                    'tax_percentage' => $invoice->tax_percentage,
                    'tax_amount' => $invoice->tax_amount,

                    'service_charge_percentage' =>
                        $invoice->service_charge_percentage,

                    'service_charge' => $invoice->service_charge,

                    'total_amount' => $invoice->total_amount,

                    'payment_method' => $invoice->payment_method,
                    'paid_at' => $invoice->paid_at,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => new InvoiceResource($invoice),
                'message' => 'Invoice created successfully.',
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function export(Request $request): StreamedResponse
    // NOT IMPLEMENTED!
    {
        $validated = $request->validate([
            'restaurant_id' => 'nullable|integer|exists:restaurants,id',

            'payment_status' => 'nullable|in:all,paid,unpaid,pending,failed',
            'payment_method' => 'nullable|in:all,cash,card,esewa,khalti',

            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',

            'search' => 'nullable|string|max:255',

            'sort_by' => 'nullable|in:created_at,invoice_number,total_amount',
            'sort_order' => 'nullable|in:asc,desc',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Restaurant
        |--------------------------------------------------------------------------
        */

        $restaurantId = $validated['restaurant_id']
            ?? auth('staff')->user()?->restaurant_id;

        if (!$restaurantId) {
            abort(422, 'Restaurant could not be identified.');
        }

        /*
        |--------------------------------------------------------------------------
        | Query
        |--------------------------------------------------------------------------
        */

        $query = Invoice::with([
            'restaurant',
            'order.orderItems.menuItem',
            'table',
        ])
            ->where('restaurant_id', $restaurantId);

        /*
        |--------------------------------------------------------------------------
        | Date filter
        |--------------------------------------------------------------------------
        */

        $dateFrom = $validated['date_from'] ?? now()->toDateString();
        $dateTo = $validated['date_to'] ?? now()->toDateString();

        $query->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        /*
        |--------------------------------------------------------------------------
        | Payment status
        |--------------------------------------------------------------------------
        */

        $paymentStatus = $validated['payment_status'] ?? 'all';

        if ($paymentStatus !== 'all') {

            // Your frontend uses "unpaid"
            // while the database uses "pending".
            if ($paymentStatus === 'unpaid') {
                $query->where('payment_status', 'pending');
            } else {
                $query->where('payment_status', $paymentStatus);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Payment method
        |--------------------------------------------------------------------------
        */

        $paymentMethod = $validated['payment_method'] ?? 'all';

        if ($paymentMethod !== 'all') {
            $query->where('payment_method', $paymentMethod);
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['search'])) {

            $search = trim($validated['search']);

            $query->where(function ($q) use ($search) {

                $q->where('invoice_number', 'like', "%{$search}%")

                    ->orWhereHas('order', function ($orderQuery) use ($search) {

                        $orderQuery->where('id', 'like', "%{$search}%")
                            ->orWhereHas('table', function ($tableQuery) use ($search) {
                                $tableQuery->where('table_number', 'like', "%{$search}%");
                            });

                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortOrder = $validated['sort_order'] ?? 'desc';

        $query->orderBy($sortBy, $sortOrder);

        /*
        |--------------------------------------------------------------------------
        | File name
        |--------------------------------------------------------------------------
        */

        $fileName = 'invoices-' .
            $dateFrom .
            '-to-' .
            $dateTo .
            '.csv';

        /*
        |--------------------------------------------------------------------------
        | CSV response
        |--------------------------------------------------------------------------
        */

        return response()->streamDownload(function () use ($query) {

            $handle = fopen('php://output', 'w');

            /*
            | CSV heading
            */

            fputcsv($handle, [
                'Invoice Number',
                'Date',
                'Time',
                'Table',
                'Subtotal',
                'Discount',
                'Tax',
                'Service Charge',
                'Total',
                'Payment Method',
                'Payment Status',
            ]);

            /*
            | Write invoices
            */

            $query->chunkById(500, function ($invoices) use ($handle) {

                foreach ($invoices as $invoice) {

                    $order = $invoice->order;

                    $tableNumber = $invoice->table?->table_number
                        ?? $order?->table?->table_number
                        ?? '';

                    fputcsv($handle, [
                        $invoice->invoice_number,

                        optional($invoice->created_at)->format('Y-m-d'),

                        optional($invoice->created_at)->format('H:i:s'),

                        $tableNumber,

                        number_format(
                            (float) ($invoice->subtotal ?? 0),
                            2,
                            '.',
                            ''
                        ),

                        number_format(
                            (float) ($invoice->discount_amount ?? 0),
                            2,
                            '.',
                            ''
                        ),

                        number_format(
                            (float) ($invoice->tax_amount ?? 0),
                            2,
                            '.',
                            ''
                        ),

                        number_format(
                            (float) ($invoice->service_charge ?? 0),
                            2,
                            '.',
                            ''
                        ),

                        number_format(
                            (float) ($order?->total_amount ?? $invoice->total_amount ?? 0),
                            2,
                            '.',
                            ''
                        ),

                        ucfirst($invoice->payment_method ?? ''),

                        ucfirst(
                            $invoice->payment_status === 'pending'
                            ? 'unpaid'
                            : ($invoice->payment_status ?? '')
                        ),
                    ]);
                }
            });

            fclose($handle);

        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

}
