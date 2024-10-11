<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderFilterRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Customer;
use App\Services\ShopifyServiceInterface;

class OrderController extends Controller
{
    protected $shopifyService;

    public function __construct(ShopifyServiceInterface $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    public function getOrders(OrderFilterRequest $request)
    {
        $query = Order::with('customer');

        if ($request->financial_status) {
            $query->where('financial_status', $request->financial_status);
        }

        $orders = $query->paginate(5);

        return OrderResource::collection($orders);
    }

    public function import()
    {
        Customer::truncate();
        Order::truncate();
        
        if(!Order::first() and !Customer::first()) {
            $this->shopifyService->importOrders();
            return response()->json(['message' => 'Data imported successfully']);
        }

        return response()->json(['message' => 'Data not imported, database is not fully truncated'], 500);
    }
}
