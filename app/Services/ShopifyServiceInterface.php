<?php
namespace App\Services;

interface ShopifyServiceInterface
{
    public function importOrders($minTotalPrice = 100);
    public function getOrders($cursor = null);
}
