<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
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

    public function getOrders(Request $request)
    {
        $query = Order::with('customer');

        // Apply financial status filter if provided
        //add validation
        if ($request->financial_status) {
            $query->where('financial_status', $request->financial_status);
        }

        // Paginate the results (5 orders per page)
        $orders = $query->paginate(5);
        // Return JSON for AJAX
        return OrderResource::collection($orders);
    }

    public function import()
    {
        Schema::disableForeignKeyConstraints();
        Customer::truncate();
        Order::truncate();
        Schema::enableForeignKeyConstraints();

        if(!Order::first() and !Customer::first()) {
            $this->shopifyService->importOrders();
            return response()->json(['message' => 'Data imported successfully']);
        }

        return response()->json(['message' => 'Data not imported, database is not fully truncated'], 500);
    }
}
