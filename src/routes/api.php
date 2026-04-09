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

    // Items
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/{item}', [ItemController::class, 'show']);

    // Categories & Locations
    Route::get('/categories', [ItemController::class, 'categories']);
    Route::get('/locations', [ItemController::class, 'locations']);

    // Loans
    Route::get('/loans', [ItemController::class, 'loans']);
    Route::get('/loans/{loan}', [ItemController::class, 'loanShow']);

    // Stock Movements
    Route::get('/stock-movements', [ItemController::class, 'stockMovements']);
});
