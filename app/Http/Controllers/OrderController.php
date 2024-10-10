<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderController extends Controller
{
    public function index() {
        $financial_statuses = Order::all()->pluck('financial_status')->unique();

        return view('orders', ['financial_statuses' => $financial_statuses]);
    }
}
