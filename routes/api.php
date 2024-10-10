<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/import-orders', [OrderController::class, 'import']);
Route::post('/import-orders', [OrderController::class, 'import']);
Route::get('/orders', [OrderController::class, 'getOrders'])->name('orders.get');
