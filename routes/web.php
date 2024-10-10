<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    redirect('/orders');
});

Route::get('/orders', [OrderController::class, 'index']);
Route::get('/import-orders', [OrderController::class, 'import']);
Route::post('/import-orders', [OrderController::class, 'import']);
