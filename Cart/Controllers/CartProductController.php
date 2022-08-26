<?php

namespace Modules\Cart\Controllers;

use App\Http\Controllers\Controller;
use Modules\Cart\Requests\CartProductCreateRequest;
use Modules\Cart\Requests\CartProductUpdateRequest;
use Modules\Cart\Resources\CartResource;
use Modules\Cart\Services\CartProductRepositoryService;
use Psr\SimpleCache\InvalidArgumentException;

class CartProductController extends Controller
{
    /**
     * @var CartProductRepositoryService $cartProductRepositoryService
     */
    private CartProductRepositoryService $cartProductRepositoryService;

    /**
     * CartController constructor.
     * @param CartProductRepositoryService $cartProductRepositoryService
     */
    public function __construct(CartProductRepositoryService $cartProductRepositoryService)
    {
        $this->cartProductRepositoryService = $cartProductRepositoryService;
    }

    /**
     * @OA\Post (
     *      path="/v1/carts/{cartId}/items",
     *      summary="Creates cart product",
     *      description="Creates cart product",
     *      operationId="createCartProduct",
     *      tags={"Cart"},
     *      @OA\Parameter(
     *          name="cartId",
     *          required=true,
     *          @OA\Schema(type="string"),
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="product_id",
     *          required=true,
     *          @OA\Schema(type="string"),
     *          in="query"
     *      ),
     *      @OA\Parameter(
     *          name="quantity",
     *          required=true,
     *          @OA\Schema(type="string"),
     *          in="query"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful",
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/CartSchema",
     *          )
     *        ),
     *      @OA\Response(
     *          response=404,
     *          description="Cart not found",
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/NotFoundSchema",
     *          )
     *        )
     *      )
     * @param string $id
     * @param CartProductCreateRequest $request
     * @return CartResource
     * @throws InvalidArgumentException
     */
    public function create(string $id, CartProductCreateRequest $request): CartResource
    {
        /**
         * need to check user's rights to the cart
         */
        $data = $request->validated();

        $cartProduct = $this->cartProductRepositoryService->create($id, $data);

        return new CartResource($cartProduct);

    }

    /**
     * @OA\Delete   (
     *      path="/v1/carts/{cartId}/items",
     *      description="Delete cart",
     *      operationId="cart_delete",
     *      tags={"Cart"},
     *      @OA\Parameter(
     *          name="cartId",
     *          required=true,
     *          @OA\Schema(type="string"),
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful",
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/CartProductSchema",
     *          )
     *        ),
     *      @OA\Response(
     *          response=404,
     *          description="Cart not found",
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/NotFoundSchema",
     *          )
     *        )
     *      )
     * @param string $id
     * @return CartResource
     * @throws InvalidArgumentException
     */
    public function delete(string $id): CartResource
    {
        /**
         * need to check user's rights to the cart
         */
        $cartProduct = $this->cartProductRepositoryService->delete($id);

        return new CartResource($cartProduct);

    }

    /**
     * @OA\Delete   (
     *      path="/{cartId}/items/{itemId}",
     *      description="Removes item from cart",
     *      operationId="cart_delete_item",
     *      tags={"Cart"},
     *      @OA\Parameter(
     *          name="cartId",
     *          required=true,
     *          @OA\Schema(type="string"),
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="itemId",
     *          required=true,
     *          @OA\Schema(type="string"),
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful",
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/CartProductSchema",
     *          )
     *        ),
     *      @OA\Response(
     *          response=404,
     *          description="Cart or item not found",
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/NotFoundSchema",
     *          )
     *        )
     *      )
     * @param string $id
     * @param int $itemId
     * @return CartResource
     * @throws InvalidArgumentException
     */
    public function deleteItem(string $id, int $itemId): CartResource
    {
        /**
         * need to check user's rights to the cart
         */
        $cartProduct = $this->cartProductRepositoryService->deleteItem($id, $itemId);

        return new CartResource($cartProduct);

    }

    /**
     * @OA\Patch  (
     *      path="/v1/carts/{cartId}/items/{itemId}",
     *      summary="Change cart item amount",
     *      description="Edit cart item",
     *      operationId="cart_edit_item",
     *      tags={"Cart"},
     *      @OA\Parameter(
     *          name="cartId",
     *          required=true,
     *          @OA\Schema(type="string"),
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="itemId",
     *          required=true,
     *          @OA\Schema(type="string"),
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="quantity",
     *          @OA\Schema(type="integer"),
     *          in="query"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful",
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/CartProductSchema",
     *          )
     *        ),
     *      @OA\Response(
     *          response=404,
     *          description="Cart not found",
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/NotFoundSchema",
     *          )
     *        )
     *      )
     * @param string $id
     * @param int $itemId
     * @param CartProductUpdateRequest $request
     * @return CartResource
     * @throws InvalidArgumentException
     */
    public function updateItem(string $id, int $itemId, CartProductUpdateRequest $request): CartResource
    {
        /**
         * need to check user's rights to the cart
         */
        $data = $request->validated();

        $cartProduct = $this->cartProductRepositoryService->updateItem($id, $itemId, $data);

        return new CartResource($cartProduct);

    }
}
