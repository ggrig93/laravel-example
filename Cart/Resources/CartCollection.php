<?php

namespace Modules\Cart\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CartCollection extends ResourceCollection
{

    public $collects = CartResource::class;

    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request) : array
    {
        return [
            'data' => $this->collection,
        ];
    }

}
