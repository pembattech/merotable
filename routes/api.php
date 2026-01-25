<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\RestaurantController;
use App\Http\Controllers\API\V1\RestaurantDocumentsController;
use App\Http\Controllers\API\V1\AdminController;
use App\Http\Controllers\API\V1\OrdersController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'user' => $request->user()
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
Route::middleware(['auth:sanctum', 'isRestaurantAuthenticated', 'isRestaurantVerified'])->prefix('v1/owner')->group(function () {
    Route::post('/restaurant/staff', [RestaurantController::class, 'createStaff']);
});

// Staff Routes
Route::middleware(['auth:sanctum'])->prefix('v1/staff')->group(function () {
    Route::post('/login', [RestaurantController::class, 'loginStaff']);
    Route::post('/orders', [OrdersController::class, 'store']);
    Route::get('/orders', [OrdersController::class, 'fetchOrders']);

});


// Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('v1/admin')->group(function () {

    Route::get('/restaurants/pending', [AdminController::class, 'pending']);
    Route::post('/restaurants/{slug}/approve', [AdminController::class, 'approve']);
    Route::post('/restaurants/{slug}/reject', [AdminController::class, 'reject']);

    Route::post('/restaurant/documents/{slug}/approve', [AdminController::class, 'approveDocuments']);
});








