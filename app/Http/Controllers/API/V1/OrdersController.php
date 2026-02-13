<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\OrderActivity;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Support\Facades\DB;


class OrdersController extends Controller
{
    public function store(Request $request)
    {
        $staff = auth('staff')->user();


        $validated = $request->validate([
            'table_id' => 'required|integer|exists:tables,id',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|integer|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);


        return DB::transaction(function () use ($request, $staff) {

            $occupiedTable = Table::where('id', $request->table_id)
                ->where('restaurant_id', $staff->restaurant_id)
                ->where('status', 'occupied')
                ->lockForUpdate()
                ->first();

            if ($occupiedTable) {
                return response()->json([
                    'message' => 'This table already has an active order',
                    'table_name' => $occupiedTable->table_name ?? $occupiedTable->id,

                ], 409);
            }



            // Create order
            $order = Order::create([
                'restaurant_id' => $staff->restaurant_id,
                'table_id' => $request->table_id,
                'total_amount' => 0,
            ]);

            $total = 0;


            foreach ($request->items as $item) {

                $menuItem = MenuItem::where('id', $item['menu_item_id'])
                    ->where('restaurant_id', $staff->restaurant_id)
                    ->first();

                if (!$menuItem) {
                    return response()->json([
                        'message' => 'Menu item not found',
                        'item_name' => 'Unknown item'
                    ], 404);
                }

                if (!$menuItem->is_available) {
                    return response()->json([
                        'message' => 'Menu item is currently unavailable',
                        'item_name' => $menuItem->name
                    ], 422);
                }


                $lineTotal = $menuItem->price * $item['quantity'];
                $total += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $item['quantity'],
                    'price' => $menuItem->price,
                ]);
            }

            OrderActivity::create([
                'order_id' => $order->id,
                'staff_id' => $staff->id,
                'action' => 'created',
                'meta' => '{
                "table_id": ' . $request->table_id . ',
                "items_count": ' . count($request->items) . '
            }',
            ]);

            $order->update(['total_amount' => $total]);

            $order->table()->update([
                'status' => 'occupied'
            ]);



            return response()->json([
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
                'total' => $total
            ], 201);
        });
    }

    public function getOrderByTable(Request $request)
    {
        $staff = auth('staff')->user();
        $tableId = $request->tableId;

        $order = Order::with('orderItems.menuItem', 'table')
            ->where('table_id', $tableId)
            ->where('restaurant_id', $staff->restaurant_id)
            ->where('status', 'open') // only active orders
            ->first();


        if (!$order) {
            return response()->json([
                'message' => 'No active order for this table',
                'items' => 0,
            ], 200);
        }

        return response()->json([
            'order_id' => $order->id,
            'table_name' => $order->table->table_number ?? null,
            'table_status' => $order->table->status ?? null,
            'items' => $order->orderItems->map(function ($item) {
                return [
                    'menu_item_id' => $item->menu_item_id,
                    'name' => $item->menuItem->name ?? 'Unknown',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'item_status' => $item->status,
                    'line_total' => $item->quantity * $item->price,
                    'time' => $item->created_at->diffForHumans(),
                ];
            }),
            'total' => $order->total_amount,
            'status' => $order->status,
        ]);
    }


    public function fetchOrders(Request $request)
    {
        $staff = auth('staff')->user();

        $orders = Order::with('orderItems.menuItem')
            ->where('restaurant_id', $staff->restaurant_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'orders' => $orders
        ], 200);
    }

    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required|integer|exists:tables,id',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|integer|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $staff = auth('staff')->user();
        $restaurantId =$staff->restaurant->id;

        $order = Order::where('table_id', $validated['table_id'])
        ->where('restaurant_id', $restaurantId)
        ->first();

        $orderId = $order->id;


        DB::transaction(function () use ($validated, $order, $staff) {

            foreach ($validated['items'] as $itemData) {

                $menuItem = MenuItem::findOrFail($itemData['menu_item_id']);

                // 1️⃣ Add or update item
                $orderItem = $order->orderItems()
                    ->where('menu_item_id', $menuItem->id)
                    ->first();


                if ($orderItem) {
                    $orderItem->increment('quantity', $itemData['quantity']);
                } else {
                    $orderItem = $order->orderItems()->create([
                        'order_id' => $order->id,
                        'menu_item_id' => $menuItem->id,
                        'quantity' => $itemData['quantity'],
                        'price' => $menuItem->price,
                        'status' => 'pending',

                    ]);
                }


                // 2️⃣ Log activity
                OrderActivity::create([
                    'order_id' => $order->id,
                    'staff_id' => $staff->id,
                    'action' => 'item_added',
                    'meta' => [
                        'menu_item_id' => $menuItem->id,
                        'name' => $menuItem->name,
                        'quantity' => $itemData['quantity'],
                        'price' => $menuItem->price,
                    ],
                ]);
            }

            // 3️⃣ Recalculate total
            $order->update([
                'total_amount' => $order->orderItems()
                    ->selectRaw('SUM(quantity * price) as total')
                    ->value('total'),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Items added successfully',
        ]);


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


    // public function updateStatus(Request $request, Order $order)
// {
//     $request->validate([
//         'status' => 'required|in:preparing,served,closed,paid',
//     ]);

    //     $staff = auth()->user();
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


}


