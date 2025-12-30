<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Items - All roles can view
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    
    // Items - Admin & Operator only (MUST be before {item} route)
    Route::middleware('role:admin,operator')->group(function () {
        Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('/items', [ItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    });
    
    // Items - Show (after /create to avoid conflict)
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
    
    // Items - Admin only delete
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])
        ->name('items.destroy')
        ->middleware('role:admin');

    // Categories - Admin & Operator can manage
    Route::middleware('role:admin,operator')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show']);
    });
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

    // Locations - Admin & Operator can manage
    Route::middleware('role:admin,operator')->group(function () {
        Route::resource('locations', LocationController::class)->except(['show', 'index']);
    });
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');

    // Reports - All roles can view
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');

    // Loans - Admin & Operator can manage
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::middleware('role:admin,operator')->group(function () {
        Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
        Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
        Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
        Route::get('/loans/{loan}/return', [LoanController::class, 'returnForm'])->name('loans.return');
        Route::post('/loans/{loan}/return', [LoanController::class, 'returnItem'])->name('loans.return.store');
        Route::delete('/loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');
    });

    // Stock Movements - Admin & Operator can manage
    Route::get('/stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
    Route::get('/stock-movements/item/{item}', [StockMovementController::class, 'itemHistory'])->name('stock-movements.item');
    Route::middleware('role:admin,operator')->group(function () {
        Route::get('/stock-movements/create', [StockMovementController::class, 'create'])->name('stock-movements.create');
        Route::post('/stock-movements', [StockMovementController::class, 'store'])->name('stock-movements.store');
    });

    // Export - All roles can export
    Route::get('/export', [ExportController::class, 'index'])->name('export.index');
    Route::get('/export/excel', [ExportController::class, 'excel'])->name('export.excel');
    Route::get('/export/pdf', [ExportController::class, 'pdf'])->name('export.pdf');

    // Feedback - All roles can submit
    Route::get('/feedbacks/create', [FeedbackController::class, 'create'])->name('feedbacks.create');
    Route::post('/feedbacks', [FeedbackController::class, 'store'])->name('feedbacks.store');
    
    // Feedback management - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/feedbacks', [FeedbackController::class, 'index'])->name('feedbacks.index');
        Route::put('/feedbacks/{feedback}', [FeedbackController::class, 'update'])->name('feedbacks.update');
        Route::delete('/feedbacks/{feedback}', [FeedbackController::class, 'destroy'])->name('feedbacks.destroy');
    });

    // Users - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Backups - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups', [BackupController::class, 'create'])->name('backups.create');
        Route::get('/backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
        Route::delete('/backups/{backup}', [BackupController::class, 'destroy'])->name('backups.destroy');
    });
});
