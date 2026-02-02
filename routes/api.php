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


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
    ]);
});


Route::prefix('v1/auth')->group(function () {
    // Restaurant
    Route::post('restaurant/register', [AuthController::class, 'registerRestaurant']);
    Route::post('restaurant/login', [AuthController::class, 'loginRestaurant']);

    // Users / Staff
    Route::post('user/login', [AuthController::class, 'loginUser']);


    Route::middleware('auth:sanctum')->group(function () {

        // Restaurant
        Route::post('/restaurant/documents', [RestaurantDocumentsController::class, 'storeMultiple']);

        Route::apiResource('restaurants', RestaurantController::class)
            ->parameters(['restaurants' => 'restaurant:slug']);

        Route::get('restaurant/{restaurant:slug}/menu', [RestaurantController::class, 'getMenuItems']);

        // Users / Staff
        Route::post('user/register', [AuthController::class, 'registerUser']);


        Route::post('logout', [AuthController::class, 'logout']);
    });




});


// Owner Routes
Route::middleware(['auth:sanctum', 'isRestaurantAuthenticated', 'isRestaurantVerified'])->prefix('v1/owner/restaurant')->group(function () {
    Route::post('/staff', [RestaurantController::class, 'createStaff']);
    Route::get('/activities', [RestaurantController::class, 'restaurantActivities']);

    Route::post('/add-menu', [MenuController::class, 'store']);
    Route::patch('/update-menu/{menuItem}', [MenuController::class, 'update']);
    Route::patch('/menu/{menuItem}/availability', [MenuController::class, 'updateAvailability']);
    Route::get('/menu/{menuItem}', [MenuController::class, 'show']);

    Route::post('/add-category', [CategoryController::class, 'store']);
    Route::patch('/update-category/{category}', [CategoryController::class, 'update']);
    Route::get('/category', [CategoryController::class, 'index']);

});

// Staff Routes
Route::middleware(['auth:sanctum'])->prefix('v1/staff')->group(function () {
    Route::post('/login', [RestaurantController::class, 'loginStaff']);
    Route::middleware(['isRestaurantAuthenticated', 'isRestaurantVerified'])->post('/orders', [OrdersController::class, 'store']);
    Route::middleware(['isRestaurantAuthenticated', 'isRestaurantVerified'])->get('/orders', [OrdersController::class, 'fetchOrders']);

    Route::get('/orders/{order}/activities', [OrdersController::class, 'activityTimeline']);


});


// Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('v1/admin')->group(function () {

    Route::get('/restaurants/pending', [AdminController::class, 'pending']);
    Route::post('/restaurants/{slug}/approve', [AdminController::class, 'approve']);
    Route::post('/restaurants/{slug}/reject', [AdminController::class, 'reject']);

    Route::post('/restaurant/documents/{slug}/approve', [AdminController::class, 'approveDocuments']);
});








