<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\Order;

class ShopifyService
{
    // protected $apiKey;
    protected $apiPassword;
    protected $baseUrl;

    const GRAPHQL_VERSION = '2024-10';
    //@todo > 100 do not working need to fix
    const ORDER_QUERY = <<<GRAPHQL
        query GetOrders(\$cursor: String) {
                orders(first: 250, after: \$cursor, query: "current_total_price:>=100") {
                    edges {
                    cursor
                    node {
                        id
                        customer {
                            id
                            firstName
                            lastName
                            email
                        }
                        currentTotalPriceSet {
                        shopMoney {
                            amount
                            currencyCode
                        }
                        }
                        displayFinancialStatus
                        displayFulfillmentStatus
                    }
                    }
                    pageInfo {
                    hasNextPage
                    }
                }
                }
    GRAPHQL;

    public function __construct()
    {
        // $this->apiKey = env('SHOPIFY_API_KEY');
        $this->apiPassword = env('SHOPIFY_API_PASSWORD');
        $this->baseUrl = env('SHOPIFY_BASE_URL');
    }

    public function importOrders()
    {
        $cursor = null;

        do {
            $ordersData = json_decode($this->getOrders($cursor), true);

            if (!empty($ordersData['data']['orders']['edges'])) {
                $this->saveOrders($ordersData['data']['orders']['edges']);
                $cursor = end($ordersData['data']['orders']['edges'])['cursor'];
            } else {
                $cursor = null;
            }
        } while (!empty($ordersData['data']['orders']['pageInfo']['hasNextPage']));
    }

    public function getOrders($cursor = null)
    {
        $variables = ['cursor' => $cursor];
        return $this->makeGraphqlRequest(self::ORDER_QUERY, $variables);
    }

    private function saveOrders(array $orderEdges)
    {
        $customersToSave = [];
        $ordersToSave = [];

        foreach ($orderEdges as $orderEdge) {
            $order = $orderEdge['node'];

            if(isset($order['customer']['email'])) {
                $customersToSave[] = [
                    'first_name' => $order['customer']['firstName'] ?? null,
                    'last_name'  => $order['customer']['lastName'] ?? null,
                    'email'      => $order['customer']['email'],
                ];
            }

            $ordersToSave[] = [
                'shopify_order_id'   => $order['id'],
                'total_price'        => $order['currentTotalPriceSet']['shopMoney']['amount'],
                'financial_status'   => $order['displayFinancialStatus'],
                'fulfillment_status' => $order['displayFulfillmentStatus'],
                'customer_id'       => 1, //@todo fix
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        foreach ($customersToSave as $customer) {
            $ret = Customer::upsert(
                [$customer],
                ['email'], // unique column to avoid duplicates
                ['first_name', 'last_name', 'updated_at'] // columns to update
            );
        }

        Order::upsert($ordersToSave, ['shopify_order_id'], ['total_price', 'financial_status', 'fulfillment_status', 'updated_at']);
    }



    private function makeGraphqlRequest($query, $variables)
    {
        $url = $this->baseUrl . '/admin/api/' . self::GRAPHQL_VERSION . '/graphql.json';

        $headers = [
            "Content-Type: application/json",
            "X-Shopify-Access-Token: {$this->apiPassword}"
        ];

        $postData = json_encode([
            'query' => $query,
            'variables' => $variables
        ]);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
