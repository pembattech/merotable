<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
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
                'staff_id' => $staff->id,
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
}
