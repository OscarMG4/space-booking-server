<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:api')->group(function () {
    
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    Route::get('spaces', [SpaceController::class, 'index']);
    Route::get('spaces/{id}', [SpaceController::class, 'show']);
    Route::middleware('permission:spaces.create')->post('spaces', [SpaceController::class, 'store']);
    Route::middleware('permission:spaces.edit')->put('spaces/{id}', [SpaceController::class, 'update']);
    Route::middleware('permission:spaces.delete')->delete('spaces/{id}', [SpaceController::class, 'destroy']);

    Route::apiResource('bookings', BookingController::class);
    Route::post('bookings/{id}/cancel', [BookingController::class, 'cancel']);

    Route::middleware('permission:users.view')->group(function () {
        Route::get('users', [UserController::class, 'index']);
        Route::get('users/{id}', [UserController::class, 'show']);
    });
    Route::middleware('permission:users.create')->post('users', [UserController::class, 'store']);
    Route::middleware('permission:users.edit')->put('users/{id}', [UserController::class, 'update']);
    Route::middleware('permission:users.delete')->delete('users/{id}', [UserController::class, 'destroy']);
    Route::get('roles', [UserController::class, 'getRoles']);

    Route::middleware('permission:reviews.view')->get('reviews', [ReviewController::class, 'index']);
    Route::middleware('permission:reviews.create')->post('reviews', [ReviewController::class, 'store']);
    Route::middleware('permission:reviews.moderate')->group(function () {
        Route::post('reviews/{id}/approve', [ReviewController::class, 'approve']);
        Route::post('reviews/{id}/reject', [ReviewController::class, 'reject']);
    });
    Route::delete('reviews/{id}', [ReviewController::class, 'destroy']);
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String()
    ]);
});
