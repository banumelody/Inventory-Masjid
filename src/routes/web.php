<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('items.index');
});

Route::resource('categories', CategoryController::class)->except(['show']);
Route::resource('locations', LocationController::class)->except(['show']);
Route::resource('items', ItemController::class)->except(['show']);

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
