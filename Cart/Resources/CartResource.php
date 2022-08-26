<?php

namespace Modules\Cart\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Cart\Models\Cart;

/**
 * Class CartResource
 * @package Modules\Cart\Resources
 * @OA\Schema(schema="CartSchema", type="object")
 */
class CartResource extends JsonResource
{
    /** @var Cart */
    public $resource;

    /** @OA\Property(type="array",
     *     @OA\Items(
     *          @OA\Property(property="cart_product", type="array",
     *              @OA\Items(ref="#/components/schemas/CartProductSchema")),
     *          @OA\Property(property="company_id", type="integer"),
     *          @OA\Property(property="delivery_address", type="string"),
     *          @OA\Property(property="delivery_address_classified", type="string"),
     *          @OA\Property(property="delivery_type", type="integer"),
     *          @OA\Property(property="address", type="string"),
     *          @OA\Property(property="promo_code", type="string"),
     *          @OA\Property(property="user_id", type="integer"),
     *          @OA\Property(property="homeNumber", type="string"),
     *          @OA\Property(property="intercom", type="integer"),
     *          @OA\Property(property="floor", type="integer"),
     *          @OA\Property(property="apartment", type="integer"),
     *          @OA\Property(property="comment", type="string"),
     *   )
     * )
     */

    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'promo_code' => $this->resource->promo_code,
            'cart_id' => $this->resource->cart_id,
            'user_id'    => $this->resource->user_id,
            'delivery_type'  => $this->resource->delivery_type,
            'address'  => $this->resource->address,
            'homeNumber'  => $this->resource->homeNumber,
            'building'  => $this->resource->building,
            'entrance'  => $this->resource->entrance,
            'intercom'  => $this->resource->intercom,
            'floor'  => $this->resource->floor,
            'apartment'  => $this->resource->apartment,
            'comment'  => $this->resource->comment,
            'delivery_address_classified'  => $this->resource->delivery_address_classified,
            'company_id' => $this->resource->company_id,
            'address_id' => $this->resource->address_id,
            'deliveryCost' => $this->resource->deliveryCost,
            'diffCost' => $this->resource->diffCost,
            'excludeDelivery' => $this->resource->excludeDelivery,
            'delayTime' => $this->resource->delay_time,
            'zoneTime' => $this->resource->zoneTime,
            'cart_product' => new CartProductCollection($this->resource->cartProduct),
            'promo_error_message' => $this->promoErrorMessage
        ];
    }
}
