<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class ShopifyService
{
    protected $apiPassword;
    protected $baseUrl;

    const GRAPHQL_VERSION = '2024-10';
    //@todo check if it possible to filter > 100 in graphQL
    const ORDER_QUERY = <<<GRAPHQL
        query GetOrders(\$cursor: String) {
                orders(first: 250, after: \$cursor) {
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
        $this->apiPassword = env('SHOPIFY_API_PASSWORD');
        $this->baseUrl = env('SHOPIFY_BASE_URL');
    }

    public function importOrders($minTotalPrice = 100)
    {
        $cursor = null;

        do {
            $ordersData = json_decode($this->getOrders($cursor), true);
            if (!empty($ordersData['data']['orders']['edges'])) {
                // Filter the orders by price greater than $minTotalPrice
                $filteredOrders = array_filter($ordersData['data']['orders']['edges'], function($order) use ($minTotalPrice) {
                    return $order['node']['currentTotalPriceSet']['shopMoney']['amount'] > $minTotalPrice;
                });

                // Maintain the structure of $ordersData while updating the edges
                $ordersData['data']['orders']['edges'] = array_values($filteredOrders);

                $this->saveOrders($ordersData['data']['orders']['edges']);
                $cursor = end($ordersData['data']['orders']['edges'])['cursor'];
                break;
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
        DB::transaction(function () use ($orderEdges) {
            $customersToSave = [];
            $ordersToSave = [];
            $customerEmails = [];

            // Collect customer and order data
            foreach ($orderEdges as $orderEdge) {
                $order = $orderEdge['node'];

                if (isset($order['customer']['email'])) {
                    $customerEmail = $order['customer']['email'];
                    $customerEmails[] = $customerEmail;

                    $customersToSave[$customerEmail] = [
                        'first_name' => $order['customer']['firstName'] ?? null,
                        'last_name'  => $order['customer']['lastName'] ?? null,
                        'email'      => $customerEmail,
                        'updated_at' => now(),
                    ];
                }

                $ordersToSave[] = [
                    'shopify_order_id'   => $order['id'],
                    'total_price'        => $order['currentTotalPriceSet']['shopMoney']['amount'],
                    'financial_status'   => $order['displayFinancialStatus'],
                    'fulfillment_status' => $order['displayFulfillmentStatus'],
                    'created_at'         => now(),
                    'updated_at'         => now(),
                    'customer_email'     => $order['customer']['email'] ?? ''
                ];
            }

            // Batch upsert customers
            Customer::upsert(array_values($customersToSave), ['email'], ['first_name', 'last_name', 'updated_at']);

            // Get saved customer IDs to link them to orders
            $savedCustomers = Customer::whereIn('email', $customerEmails)->get(['id', 'email'])->keyBy('email');
            // Update orders with correct customer IDs
            foreach ($ordersToSave as &$orderToSave) { // Use & to modify by reference
                if (isset($orderToSave['customer_email'])) { // Check for customer email
                    $customerEmail = $orderToSave['customer_email'];
                    unset($orderToSave['customer_email']); // Unset the email from the current order
                    $orderToSave['customer_id'] = $savedCustomers[$customerEmail]->id ?? null; // Assign customer ID
                }
            }

            // Batch upsert orders
            Order::upsert($ordersToSave, ['shopify_order_id'], ['total_price', 'financial_status', 'fulfillment_status', 'updated_at']);
        });
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
