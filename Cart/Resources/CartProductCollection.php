<?php

namespace Modules\Cart\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class CartProductCollection extends ResourceCollection
{

    public $collects = CartProductResource::class;

    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request) : Collection
    {
        return $this->collection;
    }

}
