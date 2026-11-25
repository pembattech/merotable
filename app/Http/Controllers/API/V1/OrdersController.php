<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PrintBillResource;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\MenuItemSize;
use App\Models\OrderActivity;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;


class OrdersController extends Controller
{


    public function store(Request $request)
    {
        $staff = auth('staff')->user();

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Validate request
        $validated = $request->validate([
            'id' => [
                'required',
                'integer',
                Rule::exists('tables', 'id')
                    ->where('restaurant_id', $staff->restaurant_id),
            ],

            'note' => 'nullable|string|max:2000',

            'items' => 'required|array|min:1',

            'items.*.menu_item_id' => [
                'required',
                'integer',
                Rule::exists('menu_items', 'id')
                    ->where('restaurant_id', $staff->restaurant_id),
            ],

            'items.*.menu_item_size_id' => 'nullable|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.note' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($validated, $staff) {

            // Find and lock restaurant table
            $table = Table::where('restaurant_id', $staff->restaurant_id)
                ->where('id', $validated['id'])
                ->lockForUpdate()
                ->first();

            if (!$table) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found.',
                ], 404);
            }

            // Check table status
            if ($table->status !== 'available') {
                return response()->json([
                    'success' => false,
                    'message' => $table->status === 'occupied'
                        ? 'This table already has an active order.'
                        : 'This table is currently reserved.',
                    'table_number' => $table->table_number,
                    'table_status' => $table->status,
                ], 409);
            }

            // Double-check active orders
            $activeOrder = Order::where('restaurant_id', $staff->restaurant_id)
                ->where('table_id', $table->id)
                ->where('status', 'open')
                ->exists();

            if ($activeOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'This table already has an active order.',
                    'table_number' => $table->table_number,
                ], 409);
            }

            // Fetch restaurant menu items
            $menuItems = MenuItem::where('restaurant_id', $staff->restaurant_id)
                ->whereIn(
                    'id',
                    collect($validated['items'])->pluck('menu_item_id')
                )
                ->get()
                ->keyBy('id');

            $orderItems = [];
            $total = 0;

            // Prepare order items
            foreach ($validated['items'] as $item) {

                $menuItem = $menuItems->get($item['menu_item_id']);

                if (!$menuItem || !$menuItem->is_available) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Menu item is currently unavailable.',
                        'item_name' => $menuItem?->name,
                    ], 422);
                }

                $size = null;

                // Items with sizes
                if ($menuItem->has_sizes) {

                    if (empty($item['menu_item_size_id'])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Please select a size for this item.',
                            'item_name' => $menuItem->name,
                        ], 422);
                    }

                    $size = MenuItemSize::where('id', $item['menu_item_size_id'])
                        ->where('menu_item_id', $menuItem->id)
                        ->where('is_available', true)
                        ->first();

                    if (!$size) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Selected size is unavailable or invalid.',
                            'item_name' => $menuItem->name,
                        ], 422);
                    }

                    $price = (float) $size->price;

                } else {

                    // Items without sizes
                    if (!empty($item['menu_item_size_id'])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'This menu item does not have sizes.',
                            'item_name' => $menuItem->name,
                        ], 422);
                    }

                    $price = (float) $menuItem->price;
                }

                $quantity = $item['quantity'];
                $subtotal = round($price * $quantity, 2);

                $total += $subtotal;

                // Prepare item data
                $orderItems[] = [
                    'menu_item_id' => $menuItem->id,

                    'menu_item_size_id' => $size?->id,
                    'size_name' => $size?->name,
                    'size_code' => $size?->code,

                    'kot_number' => 1,

                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,

                    'note' => $item['note'] ?? null,
                    'status' => 'pending',
                ];
            }

            // Create order
            $order = Order::create([
                'restaurant_id' => $staff->restaurant_id,
                'table_id' => $table->id,
                'total_amount' => round($total, 2),
                'status' => 'open',
                'note' => $validated['note'] ?? null,
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                $order->orderItems()->create($item);
            }

            // Mark table occupied
            $table->update([
                'status' => 'occupied',
            ]);

            // Order activity
            OrderActivity::create([
                'order_id' => $order->id,
                'staff_id' => $staff->id,
                'action' => 'created',
                'meta' => json_encode([
                    'table_number' => $table->table_number,
                    'kot_number' => 1,
                    'items_count' => count($orderItems),
                ]),
            ]);

            // Activity log
            activityLog(
                'order_created_by_staff',
                'Staff created a new order',
                [
                    'restaurant_id' => $staff->restaurant_id,
                    'staff_id' => $staff->id,
                    'order_id' => $order->id,
                    'table_id' => $table->id,
                    'table_number' => $table->table_number,
                    'kot_number' => 1,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully.',
                'order_id' => $order->id,
                'table_number' => $table->table_number,
                'kot_number' => 1,
                'total' => round($total, 2),
                'status' => $order->status,
            ], 201);
        });
    }


    public function getOrderByTable(Request $request)
    {
        $staff = auth('staff')->user();

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $tableId = $request->route('tableId');

        $validated = validator([
            'tableId' => $tableId,
        ], [
            'tableId' => 'required|integer',
        ])->validate();

        $order = Order::with([
            'orderItems.menuItem',
            'orderItems.menuItemSize',
            'table',
        ])
            ->where('table_id', $validated['tableId'])
            ->where('restaurant_id', $staff->restaurant_id)
            ->where('status', 'open')
            ->latest('created_at')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => true,
                'message' => 'No active order for this table',
                'order_id' => null,
                'table_name' => null,
                'table_status' => null,
                'kots' => [],
                'total' => 0,
                'status' => null,
                'order_note' => '',
            ]);
        }

        // Group order items by KOT number
        $kots = $order->orderItems
            ->groupBy('kot_number')
            ->sortKeys()
            ->map(function ($items, $kotNumber) use ($order) {

                // Determine overall KOT status
                $statuses = $items->pluck('status')
                    ->filter()
                    ->map(fn($status) => strtolower($status));

                if ($statuses->isNotEmpty() && $statuses->every(fn($status) => $status === 'served')) {
                    $kotStatus = 'Served';
                } elseif ($statuses->contains('preparing')) {
                    $kotStatus = 'Preparing';
                } else {
                    $kotStatus = 'New';
                }

                // Get earliest item creation time
                $createdAt = $items->min('created_at');

                return [
                    'n' => (int) $kotNumber,

                    'time' => $createdAt
                        ? $createdAt->format('H:i')
                        : null,

                    'status' => $kotStatus,

                    // This is currently the order-level note.
                    'note' => $order->note ?? '',

                    'lines' => $items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->menuItem->name ?? 'Unknown',
                            'size' => $item->menuItemSize?->name,
                            'qty' => (int) $item->quantity,
                            'price' => (float) $item->price,
                            'note' => $item->note ?? '',
                        ];
                    })->values(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,

            'order_id' => $order->id,
            'table_name' => $order->table->table_number ?? null,
            'table_status' => $order->table->status ?? null,

            'kots' => $kots,

            'total' => (float) $order->total_amount,
            'status' => $order->status,
            'order_note' => $order->note ?? '',
        ]);
    }

    public function fetchOrders(Request $request)
    {
        $staff = auth('staff')->user();

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $orders = Order::with([
            'orderItems.menuItem',
            'orderItems.menuItemSize',
            'table',
        ])
            ->where('restaurant_id', $staff->restaurant_id)
            ->where('status', 'open')
            ->latest('created_at')
            ->get()
            ->groupBy('table_id')
            ->map(fn($tableOrders) => $tableOrders->first());

        $ordersByTable = [];
        $orderDetails = [];

        foreach ($orders as $order) {
            $ordersByTable[$order->table_id] = $this->formatOrderKots($order);

            $orderDetails[$order->table_id] = [
                'order_id' => $order->id,
                'table_id' => $order->table_id,
                'table_name' => $order->table->table_number ?? null,
                'table_status' => $order->table->status ?? null,
                'total' => (float) $order->total_amount,
                'status' => $order->status,
                'order_note' => $order->note ?? '',
            ];
        }

        return response()->json([
            'success' => true,
            'orders' => $ordersByTable,
            'order_details' => $orderDetails,
        ]);
    }

    private function formatOrderKots(Order $order)
    {
        return $order->orderItems
            ->groupBy('kot_number')
            ->sortKeys()
            ->map(function ($items, $kotNumber) use ($order) {

                $statuses = $items->pluck('status')
                    ->filter()
                    ->map(fn($status) => strtolower($status));

                if (
                    $statuses->isNotEmpty() &&
                    $statuses->every(fn($status) => $status === 'served')
                ) {
                    $kotStatus = 'Served';
                } elseif ($statuses->contains('preparing')) {
                    $kotStatus = 'Preparing';
                } else {
                    $kotStatus = 'New';
                }

                $createdAt = $items->sortBy('created_at')->first()?->created_at;

                return [
                    'n' => (int) $kotNumber,
                    'time' => $createdAt?->format('H:i'),
                    'status' => $kotStatus,
                    'note' => $order->note ?? '',

                    'lines' => $items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->menuItem->name ?? 'Unknown',
                            'size' => $item->menuItemSize?->name,
                            'qty' => (int) $item->quantity,
                            'price' => (float) $item->price,
                            'note' => $item->note ?? '',
                        ];
                    })->values(),
                ];
            })
            ->values();
    }




    public function addItem(Request $request)
    {
        $staff = auth('staff')->user();

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Validate request
        $validated = $request->validate([
            'id' => [
                'required',
                'integer',
                Rule::exists('tables', 'id')
                    ->where('restaurant_id', $staff->restaurant_id),
            ],

            'note' => 'nullable|string|max:2000',

            'items' => 'required|array|min:1',

            'items.*.menu_item_id' => [
                'required',
                'integer',
                Rule::exists('menu_items', 'id')
                    ->where('restaurant_id', $staff->restaurant_id),
            ],

            'items.*.menu_item_size_id' => 'nullable|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.note' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($validated, $staff) {

            // Find and lock table
            $table = Table::where('id', $validated['id'])
                ->where('restaurant_id', $staff->restaurant_id)
                ->lockForUpdate()
                ->first();

            if (!$table) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found.',
                ], 404);
            }

            // Find and lock open order
            $order = Order::where('table_id', $table->id)
                ->where('restaurant_id', $staff->restaurant_id)
                ->where('status', 'open')
                ->lockForUpdate()
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'No open order found for this table.',
                ], 404);
            }

            $hadPrintedBill = $order->bill_printed_at !== null;

            // Generate one new KOT number for this submission
            $nextKotNumber = ((int) $order->orderItems()
                ->max('kot_number')) + 1;


            // Fetch menu items
            $menuItems = MenuItem::where('restaurant_id', $staff->restaurant_id)
                ->whereIn(
                    'id',
                    collect($validated['items'])->pluck('menu_item_id')
                )
                ->get()
                ->keyBy('id');

            $itemsAdded = [];

            foreach ($validated['items'] as $itemData) {

                $menuItem = $menuItems->get($itemData['menu_item_id']);

                if (!$menuItem || !$menuItem->is_available) {
                    throw ValidationException::withMessages([
                        'items' => [
                            'Menu item ' . ($menuItem?->name ?? 'selected') .
                            ' is currently unavailable.',
                        ],
                    ]);
                }

                $size = null;

                // Handle menu items with sizes
                if ($menuItem->has_sizes) {

                    if (empty($itemData['menu_item_size_id'])) {
                        throw ValidationException::withMessages([
                            'items' => [
                                'Please select a size for ' . $menuItem->name,
                            ],
                        ]);
                    }

                    $size = MenuItemSize::where(
                        'id',
                        $itemData['menu_item_size_id']
                    )
                        ->where('menu_item_id', $menuItem->id)
                        ->where('is_available', true)
                        ->first();

                    if (!$size) {
                        throw ValidationException::withMessages([
                            'items' => [
                                'Selected size is unavailable or invalid for ' .
                                $menuItem->name,
                            ],
                        ]);
                    }

                    $price = (float) $size->price;

                } else {

                    if (!empty($itemData['menu_item_size_id'])) {
                        throw ValidationException::withMessages([
                            'items' => [
                                $menuItem->name . ' does not have sizes.',
                            ],
                        ]);
                    }

                    $price = (float) $menuItem->price;
                }

                $quantity = (int) $itemData['quantity'];

                $addedSubtotal = round($price * $quantity, 2);

                $note = $itemData['note'] ?? null;

                // Find matching item ONLY inside current KOT
                $orderItemQuery = $order->orderItems()
                    ->where('kot_number', $nextKotNumber)
                    ->where('menu_item_id', $menuItem->id);

                if ($size) {
                    $orderItemQuery->where(
                        'menu_item_size_id',
                        $size->id
                    );
                } else {
                    $orderItemQuery->whereNull('menu_item_size_id');
                }

                // Match note as well
                if ($note === null) {
                    $orderItemQuery->whereNull('note');
                } else {
                    $orderItemQuery->where('note', $note);
                }

                $orderItem = $orderItemQuery->first();

                if ($orderItem) {

                    // Increase quantity within the same KOT
                    $orderItem->quantity += $quantity;

                    // Explicitly preserve KOT number
                    $orderItem->kot_number = $nextKotNumber;

                    $orderItem->subtotal = round(
                        $orderItem->quantity * (float) $orderItem->price,
                        2
                    );

                    $orderItem->save();

                } else {

                    // Create new order item
                    $orderItem = $order->orderItems()->create([
                        'menu_item_id' => $menuItem->id,

                        'menu_item_size_id' => $size?->id,
                        'size_name' => $size?->name,
                        'size_code' => $size?->code,

                        // KOT number
                        'kot_number' => $nextKotNumber,

                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $addedSubtotal,

                        'note' => $note,

                        'status' => 'pending',
                    ]);
                }

                // Record activity
                OrderActivity::create([
                    'order_id' => $order->id,
                    'staff_id' => $staff->id,
                    'action' => 'item_added',

                    'meta' => [
                        'kot_number' => $nextKotNumber,
                        'order_item_id' => $orderItem->id,
                        'menu_item_id' => $menuItem->id,
                        'menu_item_size_id' => $size?->id,

                        'name' => $menuItem->name,
                        'size_name' => $size?->name,

                        // Newly added quantity
                        'quantity' => $quantity,

                        // Total quantity after addition
                        'total_quantity' => $orderItem->quantity,

                        'price' => $price,

                        // Newly added subtotal
                        'subtotal' => $addedSubtotal,

                        // Updated order item subtotal
                        'line_total' => $orderItem->subtotal,

                        'note' => $note,
                    ],
                ]);

                $itemsAdded[] = [
                    'order_item_id' => $orderItem->id,
                    'menu_item_id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'size_name' => $size?->name,

                    'quantity' => $quantity,
                    'total_quantity' => $orderItem->quantity,

                    'price' => $price,
                    'subtotal' => $addedSubtotal,
                    'line_total' => $orderItem->subtotal,

                    'kot_number' => $orderItem->kot_number,
                ];
            }

            // Update general order note only when supplied
            if (array_key_exists('note', $validated)) {
                $order->note = $validated['note'];
            }

            // Recalculate total using stored subtotals
            $total = $order->orderItems()
                ->where('status', '!=', 'cancelled')
                ->sum('subtotal');

            $order->total_amount = round($total, 2);

            // Invalidate previously printed bill
            if ($hadPrintedBill) {
                $order->bill_printed_at = null;
                $order->bill_printed_total = null;
            }

            $order->save();

            // Record bill invalidation
            if ($hadPrintedBill) {
                OrderActivity::create([
                    'order_id' => $order->id,
                    'staff_id' => $staff->id,
                    'action' => 'bill_invalidated',

                    'meta' => [
                        'reason' => 'items_added_after_print',
                        'kot_number' => $nextKotNumber,
                    ],
                ]);
            }

            // Activity log
            activityLog(
                'order_items_added_by_staff',
                'Staff added items to an existing order',
                [
                    'restaurant_id' => $staff->restaurant_id,
                    'staff_id' => $staff->id,
                    'order_id' => $order->id,
                    'kot_number' => $nextKotNumber,
                    'items_added' => $itemsAdded,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Items added successfully.',
                'order_id' => $order->id,
                'table_number' => $table->table_number,
                'kot_number' => $nextKotNumber,
                'total' => round($total, 2),
                'items_added' => $itemsAdded,
            ]);
        });
    }



    // public function updateItem(Request $request, Order $order)
// {
//     $request->validate([
//         'menu_item_id' => 'required|exists:menu_items,id',
//         'qty' => 'required|integer|min:1',
//     ]);

    //     $staff = auth()->user();

    //     $item = $order->items()
//         ->where('menu_item_id', $request->menu_item_id)
//         ->firstOrFail();

    //     $oldQty = $item->qty;

    //     // 1️⃣ Update state
//     $item->update(['qty' => $request->qty]);

    //     $order->update([
//         'total_amount' => $order->items()->sum(DB::raw('qty * price')),
//     ]);

    //     // 2️⃣ Log history
//     OrderActivity::create([
//         'order_id' => $order->id,
//         'staff_id' => $staff->id,
//         'action' => 'item_updated',
//         'meta' => [
//             'menu_item_id' => $item->menu_item_id,
//             'old_qty' => $oldQty,
//             'new_qty' => $request->qty,
//         ],
//     ]);

    //     return response()->json(['message' => 'Item updated']);
// }

    // public function cancel(Request $request, Order $order)
// {
//     $request->validate([
//         'reason' => 'required|string',
//     ]);

    //     $staff = auth()->user();

    //     $order->update(['status' => 'cancelled']);

    //     OrderActivity::create([
//         'order_id' => $order->id,
//         'staff_id' => $staff->id,
//         'action' => 'cancelled',
//         'meta' => [
//             'reason' => $request->reason,
//             'cancelled_by' => $staff->role ?? 'staff',
//         ],
//     ]);

    //     return response()->json(['message' => 'Order cancelled']);
// }


    public function updateOrderStatus(Request $request, string $slug, int $tableId, int $orderId)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,completed,cancelled'],
        ]);

        $staff = auth('staff')->user();

        $order = Order::query()
            ->whereKey($orderId)
            ->whereHas(
                'table',
                fn($q) =>
                    $q->whereKey($tableId)
                        ->whereHas(
                            'restaurant',
                            fn($r) =>
                                $r->where('slug', $slug)
                        )
            )
            ->firstOrFail();

        DB::transaction(function () use ($order, $validated, $staff) {

            $oldStatus = $order->status;

            $order->update([
                'status' => $validated['status']
            ]);

            if ($validated['status'] === 'completed') {
                $order_items_status = 'paid';
            } else if ($validated['status'] === 'cancelled') {
                $order_items_status = 'cancelled';
            }

            // update all related items
            $order->orderItems()->update([
                'status' => $order_items_status
            ]);

            OrderActivity::create([
                'order_id' => $order->id,
                'staff_id' => $staff->id,
                'action' => 'updated',
                'meta' => [
                    'from' => $oldStatus,
                    'to' => $validated['status'],
                ],
            ]);

            activityLog(
                'order_updated',
                'Order updated',
                [
                    'order_id' => $order->id,
                    'status' => $order->status
                ]
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Order and items status updated'
        ]);
    }



    //   public function updateOrderItemStatus(Request $request, Order $order)
    // {
    //     $request->validate([
    //         'status' => 'required|in:open,completed,cancelled',
    //     ]);

    //     $staff = auth('staff')->user();
    //     $oldStatus = $order->status;

    //     $order->update(['status' => $request->status]);

    //     OrderActivity::create([
    //         'order_id' => $order->id,
    //         'staff_id' => $staff->id,
    //         'action' => 'status_changed',
    //         'meta' => [
    //             'from' => $oldStatus,
    //             'to' => $request->status,
    //         ],
    //     ]);

    //     return response()->json(['message' => 'Status updated']);
    // }

    public function activityTimeline(Order $order)
    {
        $timeline = $order->activities()
            ->with('staff:id,name,role')
            ->orderBy('created_at')
            ->get()
            ->map(function ($activity) {

                $time = $activity->created_at->format('H:i');

                $name = $activity->staff?->name ?? 'System';
                $role = $activity->staff?->role
                    ? ' (' . ucfirst($activity->staff->role) . ')'
                    : '';

                $text = match ($activity->action) {

                    'created' =>
                        "created order",

                    'item_added' =>
                        "added {$activity->meta['name']} x{$activity->meta['qty']}",

                    'item_updated' =>
                        "updated {$activity->meta['name']} qty ({$activity->meta['old_qty']} → {$activity->meta['new_qty']})",

                    'cancelled' =>
                        "cancelled order ({$activity->meta['reason']})",

                    default =>
                        $activity->action,
                };

                return "{$time} — {$name}{$role} {$text}";
            });

        return response()->json($timeline);
    }


    public function printBill(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required|integer|exists:tables,id',
        ]);

        $staff = auth('staff')->user();
        $restaurantId = $staff->restaurant->id;

        $order = Order::where('table_id', $validated['table_id'])
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'open')
            ->with('orderItems.menuItem')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'No open order found for this table.',
            ], 404);
        }

        if ($order->orderItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot print a bill with no items.',
            ], 422);
        }

        DB::transaction(function () use ($order, $staff) {
            $order->update([
                'bill_printed_at' => now(),
                'bill_printed_total' => $order->total_amount,
            ]);

            OrderActivity::create([
                'order_id' => $order->id,
                'staff_id' => $staff->id,
                'action' => 'bill_printed',
                'meta' => [
                    'total_amount' => $order->total_amount,
                ],
            ]);

            activityLog(
                'order_bill_printed_by_staff',
                'Staff printed the bill for an order',
                [
                    'restaurant_id' => $staff->restaurant_id,
                    'staff_id' => $staff->id,
                    'order_id' => $order->id,
                    'total_amount' => $order->total_amount,
                ]
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Bill printed successfully',
            'data' => new PrintBillResource($order)
        ]);
    }

    public function showBill(Order $order)
    {
        $staff = auth('staff')->user();

        abort_unless($order->restaurant_id === $staff->restaurant_id, 403);

        $order->load('orderItems.menuItem', 'table');

        return view('orders.print-bill', [
            'order' => $order,
            'restaurant' => $staff->restaurant,
        ]);
    }


    public function completeOrder(Request $request, string $slug, int $tableId, int $orderId)
    {
        $staff = auth('staff')->user();

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        return DB::transaction(function () use ($staff, $slug, $tableId, $orderId) {
            // 1. Find and lock the table
            $table = Table::whereKey($tableId)
                ->where('restaurant_id', $staff->restaurant_id)
                ->whereHas('restaurant', function ($query) use ($slug) {
                    $query->where('slug', $slug);
                })
                ->lockForUpdate()
                ->first();

            if (!$table) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found.',
                ], 404);
            }

            // 2. Find and lock the order belonging to this table
            $order = Order::whereKey($orderId)
                ->where('table_id', $table->id)
                ->whereHas('table', function ($query) use ($staff, $slug) {
                    $query->where('restaurant_id', $staff->restaurant_id)
                        ->whereHas('restaurant', function ($restaurant) use ($slug) {
                            $restaurant->where('slug', $slug);
                        });
                })
                ->lockForUpdate()
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found.',
                ], 404);
            }

            // 3. Prevent completing an already closed order
            if ($order->status !== 'open') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only open orders can be completed.',
                ], 422);
            }

            // 4. Update order status
            $oldStatus = $order->status;

            $order->update([
                'status' => 'completed',
            ]);

            // 5. Update related order items
            $order->orderItems()->update([
                'status' => 'paid',
            ]);

            // 6. Record order activity
            OrderActivity::create([
                'order_id' => $order->id,
                'staff_id' => $staff->id,
                'action' => 'updated',
                'meta' => [
                    'from' => $oldStatus,
                    'to' => 'completed',
                ],
            ]);

            // 7. Release table only if no other open order exists
            $hasOpenOrders = Order::where('table_id', $table->id)
                ->where('status', 'open')
                ->exists();

            if (!$hasOpenOrders) {
                $table->update([
                    'status' => 'available',
                ]);
            }

            // 8. Record application activity
            activityLog(
                'order_updated',
                'Order completed',
                [
                    'order_id' => $order->id,
                    'table_id' => $table->id,
                    'status' => 'completed',
                    'table_status' => $table->fresh()->status,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Order completed and table status updated successfully.',
                'data' => [
                    'order_id' => $order->id,
                    'order_status' => $order->status,
                    'table_id' => $table->id,
                    'table_status' => $table->fresh()->status,
                ],
            ]);
        });
    }

}


