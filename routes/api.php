<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VanController;
use App\Http\Controllers\VanImageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Public)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Public Browsing Routes (Customers and Visitors)
|--------------------------------------------------------------------------
*/
Route::get('/vans', [VanController::class, 'index']);
Route::get('/vans/{van}', [VanController::class, 'show']);
Route::get('/vans/{van}/images', [VanImageController::class, 'index']);
Route::get('/vans/{van}/reviews', [ReviewController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Requires Sanctum Bearer Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // for User Profile & Logout
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Van Management 
    Route::middleware('role:owner')->group(function () {
        Route::get('/my-vans', [VanController::class, 'myVans']);
        Route::post('/vans', [VanController::class, 'store']);
    });
    Route::match(['put', 'patch'], '/vans/{van}', [VanController::class, 'update']);
    Route::delete('/vans/{van}', [VanController::class, 'destroy']);

    // Van Images Management 
    Route::post('/vans/{van}/images', [VanImageController::class, 'store']);
    Route::match(['post', 'put', 'patch'], '/van-images/{image}', [VanImageController::class, 'update']);
    Route::delete('/van-images/{image}', [VanImageController::class, 'destroy']);

    // Review Management (Guarded by ReviewPolicy for customer & ownership verification)
    Route::post('/vans/{van}/reviews', [ReviewController::class, 'store']);
    Route::match(['put', 'patch'], '/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
});
