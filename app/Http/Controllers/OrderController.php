<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Services\ShopifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
    protected $shopifyService;

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    public function index(Request $request) {

    }

    public function import()
    {
        Schema::disableForeignKeyConstraints();
        Customer::truncate();
        Order::truncate();
        Schema::enableForeignKeyConstraints();

        $this->shopifyService->importOrders();

        return response()->json(['message' => 'Data imported successfully']);
    }
}
