<?php

namespace Modules\Cart\Services;

use App\Services\GetPolygons\GetPolygonsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartProduct;
use Modules\Order\Services\OrderRepositoryService;
use Psr\SimpleCache\InvalidArgumentException;

class CartProductRepositoryService
{
    /**
     * @param string $id
     * @param $data
     * @return Model|Builder
     * @throws InvalidArgumentException
     */
    public function create(string $id, $data): Model|Builder
    {
        $cart = Cart::query()->where('cart_id', $id)->first();
        $cartId =$cart->id;
        $data['cart_id'] = $cartId;
        $cartProduct = CartProduct::query()->where('cart_id', $cartId)->where('product_id', $data['product_id'])->first();
        if ($cartProduct) {
            $count = $cartProduct['quantity'] + $data['quantity'];
            $data['quantity'] = $count;
            CartProduct::query()->where('id', $cartProduct['id'])->update($data);

        } else {
            CartProduct::query()->create($data);
        }

        return $cart;
    }

    /**
     * @param string $id
     * @return Builder|Model
     * @throws InvalidArgumentException
     */
    public function delete(string $id): Builder|Model
    {
        $cart = Cart::query()->where('cart_id', $id)->first();
        $cartId = $cart->id;
        CartProduct::query()->where('cart_id', $cartId)->delete();

        return $cart;
    }

    /**
     * @param string $id
     * @param int $itemId
     * @return Builder|Model
     * @throws InvalidArgumentException
     */
    public function deleteItem(string $id, int $itemId): Builder|Model
    {
        $cart = Cart::query()->where('cart_id', $id)->first();
        $cartId = $cart->id;

        CartProduct::query()->where(['id' => $itemId, 'cart_id' => $cartId])->delete();

        return $cart;
    }

    /**
     * @param string $id
     * @param int $itemId
     * @param $data
     * @return Builder|Model
     * @throws InvalidArgumentException
     */
    public function updateItem(string $id, int $itemId, $data)
    {
        $cart = Cart::query()->where('cart_id', $id)->first();
        $cartId = $cart->id;
        $cartProduct = CartProduct::query()->where(['id' => $itemId, 'cart_id' => $cartId])->firstOrFail();
        $cartProduct->update($data);

        return $cart;
    }
}
