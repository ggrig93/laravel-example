<?php

namespace Modules\Cart\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Cart\Models\CartProduct;
use Modules\Product\Resources\ProductIndexResource;

/**
 * Class CartProductResource
 * @package Modules\Cart\Resources
 * @OA\Schema(schema="CartProductSchema", type="object")
 */
class CartProductResource extends JsonResource {
    /** @var CartProduct */
    public $resource;

    /** @OA\Property(type="array",
     *     @OA\Items(
     *          @OA\Property(property="id", type="integer"),
     *          @OA\Property(property="cart_id", type="integer"),
     *          @OA\Property(property="product_id", type="integer"),
     *          @OA\Property(property="quantity", type="integer"),
     *          @OA\Property(property="weight", type="number"),
     *          @OA\Property(property="is_promo", type="boolean"),
     *   )
     * )
     */

    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array {
        return [
            'id'       => $this->resource->id,
            'product'  => new ProductIndexResource($this->resource->product, $this->resource->cart->company_id),
            'weight'   => $this->resource->product->weight ?? null,
            'cart_id'  => $this->resource->cart_id,
            'quantity' => $this->resource->quantity,
            'is_promo' => $this->resource->is_promo,
        ];
    }
}
