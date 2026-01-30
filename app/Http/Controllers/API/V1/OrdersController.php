<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\OrderActivity;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function store(Request $request)
    {
        $staff = auth('staff')->user();


        $request->validate([
            'table_id' => 'required|integer|exists:tables,id',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|integer|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request, $staff) {

            // Create order
            $order = Order::create([
                'restaurant_id' => $staff->restaurant_id,
                'table_id' => $request->table_id,
                'total_amount' => 0,
            ]);

            $total = 0;

            foreach ($request->items as $item) {

                $menuItem = MenuItem::where('id', $item['menu_item_id'])
                    ->where('restaurant_id', $staff->restaurant_id) // 🔒 safety
                    ->where('is_available', 1)
                    ->firstOrFail();

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

            return response()->json([
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
                'total' => $total
            ], 201);
        });
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

//     public function addItem(Request $request, Order $order)
// {
//     $request->validate([
//         'menu_item_id' => 'required|exists:menu_items,id',
//         'qty' => 'required|integer|min:1',
//     ]);

//     $staff = auth()->user();
//     $menuItem = MenuItem::findOrFail($request->menu_item_id);

//     // 1️⃣ Update current state
//     $item = $order->items()->updateOrCreate(
//         ['menu_item_id' => $menuItem->id],
//         [
//             'qty' => DB::raw("qty + {$request->qty}"),
//             'price' => $menuItem->price,
//         ]
//     );

//     // 2️⃣ Recalculate order total
//     $order->update([
//         'total_amount' => $order->items()->sum(DB::raw('qty * price')),
//     ]);

//     // 3️⃣ Log activity
//     OrderActivity::create([
//         'order_id' => $order->id,
//         'staff_id' => $staff->id,
//         'action' => 'item_added',
//         'meta' => [
//             'menu_item_id' => $menuItem->id,
//             'name' => $menuItem->name,
//             'qty' => $request->qty,
//             'price' => $menuItem->price,
//         ],
//     ]);

//     return response()->json(['message' => 'Item added']);
// }


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


