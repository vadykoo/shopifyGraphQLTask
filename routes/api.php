<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/import-orders', [OrderController::class, 'import'])->name('orders.import');
Route::get('/orders', [OrderController::class, 'getOrders'])->name('orders.get');
Route::get('/import-orders', [OrderController::class, 'import']);
