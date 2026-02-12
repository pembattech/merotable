<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\RestaurantController;
use App\Http\Controllers\API\V1\RestaurantDocumentsController;
use App\Http\Controllers\API\V1\AdminController;
use App\Http\Controllers\API\V1\OrdersController;
use App\Http\Controllers\API\V1\MenuController;
use App\Http\Controllers\API\V1\CategoryController;
use App\Http\Controllers\API\V1\StaffController;
use App\Http\Controllers\API\V1\TableController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
    ]);
});


Route::prefix('v1/auth')->group(function () {

    // AUTH
    Route::post('restaurant/register', [AuthController::class, 'registerRestaurant']);
    Route::post('restaurant/login', [AuthController::class, 'loginRestaurant']);
    // Route::post('user/register', [AuthController::class, 'registerUser']);
    // Route::post('user/login', [AuthController::class, 'loginUser']);
    Route::post('staff/login', [RestaurantController::class, 'loginStaff']);

    Route::post('logout', [AuthController::class, 'logout']);


    // Common Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/restaurant/documents', [RestaurantDocumentsController::class, 'storeMultiple']);

        Route::get('restaurant/{restaurant:slug}/menu', [RestaurantController::class, 'getMenuItems']);

    });

});


// Owner Routes
Route::middleware(['auth:sanctum', 'isRestaurantAuthenticated', 'isRestaurantVerified'])->prefix('v1/owner/restaurant')->group(function () {
    Route::post('/staff', [RestaurantController::class, 'createStaff']);
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

    // Not Implement!
    Route::get('/tables', [TableController::class, 'fetchTables']);
    Route::get('/tables/{tableId}', [TableController::class, 'fetchTableDetails']);



});

// Staff Routes
Route::middleware(['auth:sanctum'])->prefix('v1/staff')->group(function () {
    Route::middleware(['isRestaurantAuthenticated', 'isRestaurantVerified'])->post('/orders', [OrdersController::class, 'store']);
    Route::middleware(['isRestaurantAuthenticated', 'isRestaurantVerified'])->get('/orders', [OrdersController::class, 'fetchOrders']);

    Route::get('/orders/{order}/activities', [OrdersController::class, 'activityTimeline']);

    Route::get('/{restaurant:slug}/menu',[StaffController::class, 'getStaffMenu']);
    Route::get('/{restaurant:slug}/categories', [StaffController::class, 'fetchPublicCategories']);

    Route::get('/{restaurant:slug}/tables', [StaffController::class, 'fetchTables']);

    Route::post('/{restaurant:slug}/orders', [OrdersController::class, 'store']);
    Route::get('/{restaurant:slug}/order/table/{tableId}', [OrdersController::class, 'getOrderByTable']);


});


// Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('v1/admin')->group(function () {

    Route::get('/restaurants/pending', [AdminController::class, 'pending']);
    Route::post('/restaurants/{slug}/approve', [AdminController::class, 'approve']);
    Route::post('/restaurants/{slug}/reject', [AdminController::class, 'reject']);

    Route::post('/restaurant/documents/{slug}/approve', [AdminController::class, 'approveDocuments']);
});








