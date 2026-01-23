<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\RestaurantController;
use App\Http\Controllers\API\V1\RestaurantDocumentsController;
use App\Http\Controllers\API\V1\AdminController;


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

// Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('v1/admin')->group(function () {

    Route::get('/restaurants/pending', [AdminController::class, 'pending']);
    Route::post('/restaurants/{slug}/approve', [AdminController::class, 'approve']);
    Route::post('/restaurants/{slug}/reject', [AdminController::class, 'reject']);

    Route::post('/restaurant/documents/{slug}/approve', [AdminController::class, 'approveDocuments']);
});





