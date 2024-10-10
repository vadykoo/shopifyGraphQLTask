<?php

namespace App\Services;

class ShopifyRESTService
{
    protected $apiKey;
    protected $password;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('SHOPIFY_API_KEY');
        $this->password = env('SHOPIFY_API_PASSWORD');
        $this->baseUrl = env('SHOPIFY_BASE_URL');
    }

    public function getOrders($status = 'any')
    {
        $endpoint = "{$this->baseUrl}/admin/orders.json?status={$status}";
        return $this->makeRequest($endpoint);
    }

    public function getCustomers($customerIds = null)
    {
        $endpoint = "{$this->baseUrl}/admin/customers.json";
        if ($customerIds) {
            $endpoint .= "?ids=" . implode(',', $customerIds);
        }
        return $this->makeRequest($endpoint);
    }

    private function makeRequest($url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => "{$this->apiKey}:{$this->password}",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($httpCode !== 200) {
            curl_close($curl);
            throw new \Exception("Shopify API Request failed with status code: $httpCode");
        }

        curl_close($curl);
        return json_decode($response, true);
    }
}
