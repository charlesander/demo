<?php

namespace App\Http\Resources;

use App\Models\SalesChannel;
use GDebrauwer\Hateoas\Traits\HasLinks;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource
{
    use HasLinks;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (is_null($this->resource)) {
            return null;
        }

        $order = [
            'id' => $this->id,
            'ambassador_id' => $this->ambassador_id,
            'pamper_id' => $this->pamper_id,
            'status_id' => $this->status_id,
            'source' => $this->source,
            'sales_channel_id' => $this->sales_channel_id,
            'shopify_id' => $this->shopify_id,
            'email' => $this->email,
            'note' => $this->note,
            'payment_gateway' => $this->payment_gateway,
            'gross' => $this->gross,
            'net' => $this->net,
            'vat' => $this->vat,
            'total_weight' => $this->total_weight,
            'currency' => $this->currency,
            'financial_status' => $this->financial_status,

            'original_gross' => $this->original_gross,
            'original_net' => $this->original_net,
            'original_vat' => $this->original_vat,
            'original_total_weight' => $this->original_total_weight,

            'billing_address' => $this->billing_address_id ?
                new Address($this->billingAddress()->get()->first()) : [],

            'shipping_address' => $this->shipping_address_id ?
                new Address($this->shippingAddress()->get()->first()) : [],

            'processed_at' => $this->processed_at,

            //'order_lines' => LineItem::collection(($this->resource->OrderLines()->get() ?: []))
            'lines' => [
                'hostess_lines' => LineItem::collection(
                    ($this->resource->OrderLinesHostess()->get() ?: [])
                ),
                'order_lines' => LineItem::collection(
                    ($this->resource->OrderLines()->get() ?: [])
                ),
                'delivery_lines' => LineItem::collection(
                    ($this->resource->OrderLinesDelivery()->get() ?: [])
                ),
                'discount_lines' => LineItem::collection(
                    ($this->resource->OrderLinesDiscount()->get() ?: [])
                ),
                'hostess_cost_lines' => LineItem::collection(
                    ($this->resource->OrderLinesHostessCost()->get() ?: [])
                )
            ],
            'apportions' => [
                'ambassador' => new OrderApportion($this->resource->ambassadorApportionBreakdown),
                '' => new OrderApportion($this->resource->ApportionBreakdown)
            ]
        ];

        return array_merge($order, [
            '_links' => $this->links(),
        ]);
    }
}
