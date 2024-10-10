<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        return 'data';
        //DB is empty
    }

    public function import()
    {
        dd('import');
        Schema::disableForeignKeyConstraints();
        Customer::truncate();
        Order::truncate();
        Schema::enableForeignKeyConstraints();

        //@todo DB is empty
        $this->shopifyService->importOrders();

        return response()->json(['message' => 'Data imported successfully']);
    }
}
