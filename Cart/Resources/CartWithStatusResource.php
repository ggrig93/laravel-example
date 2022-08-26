<?php

namespace Modules\Cart\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartWithStatusResource extends JsonResource
{
    /** @var CartResource */
    public $resource;

    /**
     * @var ?string
     */
    public ?string $promoErrorMessage = null;

    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'cart' => $this->resource,
            'promo_error_message' => $this->promoErrorMessage
        ];
    }
}
