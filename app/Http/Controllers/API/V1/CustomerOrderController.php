<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\OrderActivity;
use App\Models\Table;
use App\Models\Category;
use App\Http\Resources\V1\PublicCategoryResource;
use App\Http\Resources\V1\PublicMenuItemResource;
use App\Http\Resources\V1\PublicRestaurantResource;

class CustomerOrderController extends Controller
{
    public function getRestaurant($token)
    {
        $table = Table::with('restaurant')
            ->where('qr_token', $token)
            ->firstOrFail();

        $restaurant = $table->restaurant;

        return response()->json([
            'success' => true,
            'data' => new PublicRestaurantResource($restaurant),
        ]);
    }


    public function getMenu(Request $request, $token)
    {
        $table = Table::where('qr_token', $token)->first();

        $restaurant = $table->getRestaurantDetails();

        $menu = $restaurant->menuItems()
            ->where('is_available', true)
            ->when($request->category_id, function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            })
            ->with('category:id,name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => PublicMenuItemResource::collection($menu)
        ]);
    }


    public function fetchPublicCategories($token)
    {

        $table = Table::where('qr_token', $token)->first();

        $restaurant = $table->getRestaurantDetails();


        $categories = Category::whereHas('menuItems', function ($q) use ($restaurant) {
            $q->where('restaurant_id', $restaurant->id)
                ->where('is_available', true);
        })
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $categories->count(),

            'data' => PublicCategoryResource::collection($categories)
        ]);
    }

    // Works on both create and update by customer
    public function placeOrder(Request $request, $token)
    {
        $table = Table::where('qr_token', $token)->firstOrFail();
        $restaurant = $table->restaurant;

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|integer|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request, $table, $restaurant) {

            $lockedTable = Table::where('id', $table->id)
                ->where('restaurant_id', $restaurant->id)
                ->lockForUpdate()
                ->first();

            // Create or get active order
            $order = Order::updateOrCreate(
                [
                    'table_id' => $table->id,
                    'status' => 'open'
                ],
                [
                    'restaurant_id' => $restaurant->id,
                    'total_amount' => 0
                ]
            );

            // // Log order creation (only if new)
            // if ($order->wasRecentlyCreated) {
            //     OrderActivity::create([
            //         'order_id' => $order->id,
            //         'staff_id' => null,
            //         'action' => 'created',
            //         'meta' => json_encode([
            //             'source' => 'qr_order',
            //             'table_id' => $table->id
            //         ])
            //     ]);
            // }

            foreach ($request->items as $item) {

                $menuItem = MenuItem::whereKey($item['menu_item_id'])
                    ->where('restaurant_id', $restaurant->id)
                    ->first();

                if (!$menuItem) {
                    return response()->json([
                        'message' => 'Menu item not found',
                    ], 404);
                }

                if (!$menuItem->is_available) {
                    return response()->json([
                        'message' => "{$menuItem->name} is currently unavailable",
                    ], 422);
                }

                $orderItem = OrderItem::firstOrNew([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id
                ]);

                $orderItem->quantity += $item['quantity'];
                $orderItem->price = $menuItem->price;
                $orderItem->save();

                // // Log item added activity
                // OrderActivity::create([
                //     'order_id' => $order->id,
                //     'staff_id' => null,
                //     'action' => 'item_added',
                //     'meta' => json_encode([
                //         'menu_item_id' => $menuItem->id,
                //         'name' => $menuItem->name,
                //         'quantity_added' => $item['quantity'],
                //         'price' => $menuItem->price
                //     ])
                // ]);
            }

            $order->update([
                'total_amount' => $order->orderItems()->sum(DB::raw('price * quantity'))
            ]);

            // // Log order update
            // OrderActivity::create([
            //     'order_id' => $order->id,
            //     'staff_id' => null,
            //     'action' => 'updated',
            //     'meta' => json_encode([
            //         'total_amount' => $order->total_amount
            //     ])
            // ]);

            $table->update([
                'status' => 'occupied'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
                'total' => $order->total_amount
            ], 201);
        });
    }
}
