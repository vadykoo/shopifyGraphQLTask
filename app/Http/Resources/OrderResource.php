<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'customer' => [
                'first_name' => $this->customer->first_name ?? null,
                'last_name'  => $this->customer->last_name  ?? null,
                'email'      => $this->customer->email  ?? null,
            ],
            'total_price'       => $this->total_price,
            'financial_status'  => $this->financial_status,
            'fulfillment_status'=> $this->fulfillment_status,
        ];
    }
}
