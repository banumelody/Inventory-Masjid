<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItemController;
use Illuminate\Support\Facades\Route;

// Public API routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Authenticated API routes
Route::middleware(['auth:sanctum', \App\Http\Middleware\SetApiMasjidContext::class])->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Dashboard stats
    Route::get('/stats', [ItemController::class, 'stats']);

    // Items (CRUD)
    Route::get('/items', [ItemController::class, 'index']);
    Route::post('/items', [ItemController::class, 'store']);
    Route::get('/items/{item}', [ItemController::class, 'show']);
    Route::put('/items/{item}', [ItemController::class, 'update']);
    Route::delete('/items/{item}', [ItemController::class, 'destroy']);

    // Categories (CRUD)
    Route::get('/categories', [ItemController::class, 'categories']);
    Route::post('/categories', [ItemController::class, 'storeCategory']);
    Route::put('/categories/{category}', [ItemController::class, 'updateCategory']);
    Route::delete('/categories/{category}', [ItemController::class, 'destroyCategory']);

    // Locations (CRUD)
    Route::get('/locations', [ItemController::class, 'locations']);
    Route::post('/locations', [ItemController::class, 'storeLocation']);
    Route::put('/locations/{location}', [ItemController::class, 'updateLocation']);
    Route::delete('/locations/{location}', [ItemController::class, 'destroyLocation']);

    // Loans (CRUD)
    Route::get('/loans', [ItemController::class, 'loans']);
    Route::post('/loans', [ItemController::class, 'storeLoan']);
    Route::get('/loans/{loan}', [ItemController::class, 'loanShow']);
    Route::put('/loans/{loan}/return', [ItemController::class, 'returnLoan']);
    Route::delete('/loans/{loan}', [ItemController::class, 'destroyLoan']);

    // Stock Movements
    Route::get('/stock-movements', [ItemController::class, 'stockMovements']);
});
