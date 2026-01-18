<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\V1\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1/auth')->group(function () {
    // Restaurant
    Route::post('restaurant/register', [AuthController::class, 'registerRestaurant']);
    Route::post('restaurant/login', [AuthController::class, 'loginRestaurant']);

    // Users / Staff
    Route::post('user/login', [AuthController::class, 'loginUser']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('user/register', [AuthController::class, 'registerUser']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});
