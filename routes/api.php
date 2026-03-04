<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\RestaurantController;
use App\Http\Controllers\API\V1\RestaurantSettingController;
use App\Http\Controllers\API\V1\RestaurantDocumentsController;
use App\Http\Controllers\API\V1\AdminController;
use App\Http\Controllers\API\V1\OrdersController;
use App\Http\Controllers\API\V1\MenuController;
use App\Http\Controllers\API\V1\CategoryController;
use App\Http\Controllers\API\V1\StaffController;
use App\Http\Controllers\API\V1\TableController;
use App\Http\Controllers\API\V1\TransactionController;
use App\Http\Controllers\API\V1\SubscriptionController;

Route::middleware(['auth:sanctum', 'checkSubscription'])->get('/user', function (Request $request) {
    $restaurant = auth('restaurant')->user()
        ?? auth('staff')->user()->restaurant;

    return response()->json([
        'success' => true,
        'days_left' => $request->subscription_days_left ?? 0, // from middleware
    ]);
});


Route::prefix('v1/auth')->group(function () {

    // AUTH
    Route::post('restaurant/register', [AuthController::class, 'registerRestaurant']);
    Route::post('restaurant/login', [AuthController::class, 'loginRestaurant']);

    // Route::post('user/register', [AuthController::class, 'registerUser']);
    // ROOT LOGIN
    Route::post('user/login', [AuthController::class, 'loginUser']);

    Route::post('staff/login', [RestaurantController::class, 'loginStaff']);

    Route::post('logout', [AuthController::class, 'logout']);


    // Common Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/restaurant/documents', [RestaurantDocumentsController::class, 'storeMultiple']);

        Route::get('restaurant/{restaurant:slug}/menu', [RestaurantController::class, 'getMenuItems']);

    });

});


// Owner Routes
Route::middleware(['auth:sanctum', 'isRestaurantAuthenticated'])->prefix('v1/owner/restaurant')->group(function () {

    Route::post('/subscription/transaction', [TransactionController::class, 'store']);
    Route::get('/subscription/expired', [SubscriptionController::class, 'lastExpiredSubscription']);

    Route::middleware(['checkSubscription'])->group(function () {

        Route::get('/', [RestaurantController::class, 'index']);

        Route::get('/profile', [RestaurantController::class, 'show']);
        Route::patch('/basic-settings', [RestaurantController::class, 'update']);


        Route::get('/settings', [RestaurantSettingController::class, 'show']);
        Route::patch('/settings', [RestaurantSettingController::class, 'update']);

        Route::post('/staff', [RestaurantController::class, 'createStaff']);
        Route::patch('/staff/{id}', [RestaurantController::class, 'updateStaff']);
        Route::get('/staff', [RestaurantController::class, 'fetchStaff']);

        Route::get('/activities', [RestaurantController::class, 'restaurantActivities']);

        Route::post('/add-menu', [MenuController::class, 'store']);
        Route::patch('/update-menu/{menuItem}', [MenuController::class, 'update']);
        Route::patch('/menu/{menuItem}/availability', [MenuController::class, 'updateAvailability']);
        Route::get('/menu/{menuItem}', [MenuController::class, 'show']);

        // Not Implement!
        Route::post('/add-category', [CategoryController::class, 'store']);
        Route::patch('/update-category/{category}', [CategoryController::class, 'update']);
        Route::get('/category', [CategoryController::class, 'index']);

        Route::post('/table/add', [TableController::class, 'store']);
        Route::get('/tables', [TableController::class, 'fetchTables']);
        Route::get('/{restaurant:slug}/tables/{tableId}', [TableController::class, 'fetchTableDetails']);


    });


});

// Staff Routes
Route::middleware(['auth:sanctum'])->prefix('v1/staff')->group(function () {
    Route::middleware(['isRestaurantAuthenticated', 'isRestaurantVerified'])->post('/orders', [OrdersController::class, 'store']);
    Route::middleware(['isRestaurantAuthenticated', 'isRestaurantVerified'])->get('/orders', [OrdersController::class, 'fetchOrders']);

    Route::get('/orders/{order}/activities', [OrdersController::class, 'activityTimeline']);

    Route::get('/{restaurant:slug}/menu', [StaffController::class, 'getStaffMenu']);
    Route::get('/{restaurant:slug}/categories', [StaffController::class, 'fetchPublicCategories']);

    // Fetch basic table info
    Route::get('/{restaurant:slug}/tables', [StaffController::class, 'fetchTables']);
    // Fetch table info with orders and total amounts
    Route::get('/{restaurant:slug}/tables/overview', [TableController::class, 'fetchTables']);
    Route::get('/{restaurant:slug}/tables/{tableId}', [TableController::class, 'fetchTableDetails']);
    Route::get('/{restaurant:slug}/table/{tableId}', [TableController::class, 'tableStatus']);
    Route::put(
        '/{restaurant:slug}/table/{tableId}/status',
        [TableController::class, 'tableUpdateStatus']
    );


    Route::post('/{restaurant:slug}/orders', [OrdersController::class, 'store']);
    Route::post('/{restaurant:slug}/add-items', [OrdersController::class, 'addItem']);
    Route::get('/{restaurant:slug}/order/table/{tableId}', [OrdersController::class, 'getOrderByTable']);
    Route::put('/{restaurant:slug}/table/{tableId}/{orderId}/status', [OrdersController::class, 'updateOrderStatus']);


});


// Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('v1/admin')->group(function () {

    Route::get('/restaurants/pending', [AdminController::class, 'pending']);
    Route::post('/restaurants/{slug}/approve', [AdminController::class, 'approve']);
    Route::post('/restaurants/{slug}/reject', [AdminController::class, 'reject']);

    Route::post('/restaurant/documents/{slug}/approve', [AdminController::class, 'approveDocuments']);

    Route::get('/subscription/transaction/pending', [AdminController::class, 'getPendingTranscation']);

    Route::post('/subscription/transaction/{slug}/approve', [AdminController::class, 'approveTransaction']);
});

use App\Models\OrderItem; // assuming each item is stored in OrderItem

Route::get('/items', function (Request $request) {
    $query = $request->query('search', '');

    if (!$query) {
        return response()->json(['success' => true, 'items' => []]);
    }

    // Search distinct item names containing the query (case-insensitive)
    $items = OrderItem::join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
        ->where('menu_items.name', 'LIKE', "%{$query}%")
        ->distinct()
        ->pluck('menu_items.name');

    return response()->json([
        'success' => true,
        'items' => $items,
        'query' => $query,
    ]);
});


// use App\Models\Table;

// Route::get('/search-tables', function(Request $request) {
//     $itemName = $request->query('item', '');

//     if (!$itemName) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Item name is required'
//         ], 400);
//     }

//     // Get tables that have order items with the selected menu item name
//     $tables = Table::whereHas('orders.orderItems.menuItem', function($q) use ($itemName) {
//         $q->where('name', $itemName);
//     })
//     ->with(['orders.orderItems' => function($q) use ($itemName) {
//         $q->whereHas('menuItem', function($q2) use ($itemName) {
//             $q2->where('name', $itemName);
//         })->with('menuItem');
//     }])
//     ->get();

//     return response()->json([
//         'success' => true,
//         'tables' => $tables
//     ]);
// });


// use App\Models\Table;


// Route::get('/search-tables', function(Request $request) {

//     $items = $request->query('item');

//     if (!$items) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Item name is required'
//         ], 400);
//     }

//     // Convert to array if needed
//     if (!is_array($items)) {
//         $items = explode(',', $items);
//     }

//     $tables = Table::whereHas('orders', function ($orderQuery) use ($items) {

//         $orderQuery->where('status', 'open')
//             ->whereHas('orderItems.menuItem', function($q) use ($items) {
//                 $q->whereIn('name', $items);
//             });

//     })
//     ->with(['orders' => function ($orderQuery) use ($items) {

//         $orderQuery->where('status', 'open')
//             ->with(['orderItems' => function($q) use ($items) {
//                 $q->whereHas('menuItem', function($q2) use ($items) {
//                     $q2->whereIn('name', $items);
//                 })->with('menuItem');
//             }]);

//     }])
//     ->get();

//     return response()->json([
//         'success' => true,
//         'items' => $items,
//         'tables' => $tables,
//     ]);
// });


use App\Models\Table;

Route::get('/search-tables', function (Request $request) {

    $items = $request->query('item');

    if (!$items) {
        return response()->json([
            'success' => false,
            'message' => 'Item name is required'
        ], 400);
    }

    // Convert to array if needed
    if (!is_array($items)) {
        $items = explode(',', $items);
    }

    $tables = Table::whereHas('orders', function ($orderQuery) use ($items) {
        $orderQuery->where('status', 'open');

        // Loop each item for AND logic
        foreach ($items as $item) {
            $orderQuery->whereHas('orderItems.menuItem', function ($q) use ($item) {
                $q->where('name', $item);
            });
        }

    })
        ->with([
            'orders' => function ($orderQuery) use ($items) {
                $orderQuery->where('status', 'open')
                    ->with([
                        'orderItems' => function ($q) use ($items) {
                            // Only keep items that match selected items
                            $q->whereHas('menuItem', function ($q2) use ($items) {
                                $q2->whereIn('name', $items);
                            })->with('menuItem');
                        }
                    ]);
            }
        ])
        ->get();

    return response()->json([
        'success' => true,
        'items' => $items,
        'tables' => $tables,
    ]);
});







