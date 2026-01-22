<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function (): void {
    Route::get('/', fn() => 'API is active');

    // Categories
    Route::get('/categories', [\App\Http\Controllers\Api\V1\CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [\App\Http\Controllers\Api\V1\CategoryController::class, 'show']);

    // Auth Routes
    Route::prefix('auth')->group(function (): void {

        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });
});
