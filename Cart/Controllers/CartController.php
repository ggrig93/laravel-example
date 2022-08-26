<?php

namespace Modules\Cart\Controllers;

use App\Http\Controllers\Controller;
use App\Interfaces\City\CityServiceInterface;
use GuzzleHttp\Exception\GuzzleException;
use App\Services\SailPlay\SailPlayService;
use Modules\Cart\Models\Cart;
use Modules\Cart\Requests\CartRequest;
use Modules\Cart\Resources\CartResource;
use Modules\Cart\Services\CartRepositoryService;
use Psr\SimpleCache\InvalidArgumentException;

class CartController extends Controller
{
    /**
     * @var CartRepositoryService $cartRepositoryService
     */
    private CartRepositoryService $cartRepositoryService;

    /**
     * CartController constructor.
     * @param CartRepositoryService $cartRepositoryService
     */
    public function __construct(CartRepositoryService $cartRepositoryService)
    {
        $this->cartRepositoryService = $cartRepositoryService;
    }

    /**
     * @OA\Get (
     *      path="/v1/carts/{cartId}",
     *      summary="Get cart by ID",
     *      description="Get cart by ID",
     *      operationId="getCart",
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
     * @return CartResource
     * @throws InvalidArgumentException
     */
    public function show(string $id): CartResource
    {
        $cart = $this->cartRepositoryService->show($id);

        return new CartResource(
            $cart
        );
    }

    /**
     * @OA\Post (
     *      path="/v1/cart",
     *      summary="Create cart",
     *      description="Create cart. If your delivery type is not 0, than you must send delivery_address.address and
     *          delivery_address.homeNumber params",
     *      operationId="cart_get",
     *      tags={"Cart"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="delivery_address", type="object",
     *                  ref="#/components/schemas/DeliveryAddressSchema",
     *               ),
     *            )
     *      ),
     *      @OA\Parameter(
     *          name="promo_code",
     *          @OA\Schema(type="string"),
     *          in="query"
     *      ),
     *      @OA\Parameter(
     *          name="company_id",
     *          required=true,
     *          @OA\Schema(type="integer"),
     *          in="query"
     *      ),
     *      @OA\Parameter(
     *          name="delivery_type",
     *          required=true,
     *          description="0 or 1",
     *          @OA\Schema(type="integer"),
     *          in="query"
     *      ),
     *      @OA\Parameter(
     *          name="delivery_address_classified",
     *          @OA\Schema(type="string"),
     *          in="query"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful",
     *         @OA\JsonContent(
     *             type="string",
     *             @OA\Examples(example="string", value="asdfsadfdsfsf.cvasfsaf", summary="Id of new cart"),
     *         )
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
     * @param CartRequest $request
     * @param CityServiceInterface $cityService
     * @return CartResource
     */
    public function create(CartRequest $request, CityServiceInterface $cityService): CartResource
    {
        $requestData = $request->toArray();
        $promoErrorMessage = $this->cartRepositoryService->checkValidPromo($requestData);

        if (!empty($promoErrorMessage)) {
            unset($requestData['promo_code']);
        }

        $cartAndStatus = $this->cartRepositoryService->create($requestData, $cityService);

        $cartResource = new CartResource($cartAndStatus);
        $cartResource->promoErrorMessage = $promoErrorMessage;

        return $cartResource;
    }

    /**
     * @OA\Get  (
     *      path="/v1/cart/{userId}",
     *      summary="Get user's cart",
     *      operationId="auth_cart_get",
     *      tags={"Cart"},
     *      security={
     *         {"sanctum": {}},
     *      },
     *      @OA\Response(
     *          response=200,
     *          description="Successful",
     *        ),
     *      @OA\Response(
     *          response=404,
     *          description="Cart not found",
     *          @OA\JsonContent(
     *              type="string",
     *              @OA\Examples(example="string", value="$2y$10$mitOIIoAiEcxAS0yRbQvn.XXXXXXXXXXXXX", summary="New cart_id"),
     *          )
     *        ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/ErrorMessageSchema",
     *          ),
     *      ),
     *      )
     * @return mixed
     */
    public function getAuthCart(): mixed
    {
        return $this->cartRepositoryService->getAuthCart();
    }

    /**
     * @OA\Patch  (
     *      path="/v1/carts/{cart_id}",
     *      summary="Edit cart",
     *      description="Edit cart. If your delivery type is not 0, than you must send delivery_address.address and
     *          delivery_address.homeNumber params",
     *      operationId="cart_edit",
     *      tags={"Cart"},
     *      @OA\Parameter(
     *          name="cart_id",
     *          required=true,
     *          @OA\Schema(type="string"),
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="promo_code",
     *          @OA\Schema(type="string"),
     *          in="query"
     *      ),
     *      @OA\Parameter(
     *          name="company_id",
     *          required=true,
     *          @OA\Schema(type="integer"),
     *          in="query"
     *      ),
     *      @OA\Parameter(
     *          name="delivery_type",
     *          required=true,
     *          description="0 or 1",
     *          @OA\Schema(type="integer"),
     *          in="query"
     *      ),
     *      @OA\Parameter(
     *          name="delivery_address",
     *          @OA\Schema(type="array",
     *          @OA\Items(
     *              ref="#/components/schemas/DeliveryAddressSchema",
     *           )),
     *          in="query"
     *      ),
     *      @OA\Parameter(
     *          name="delivery_address_classified",
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
     * @param CartRequest $request
     * @param CityServiceInterface $cityService
     * @return CartResource
     */
    public function update(string $id, CartRequest $request, CityServiceInterface $cityService): CartResource
    {
        /**
         * need to check user's rights to the cart
         */
        $promoErrorMessage = null;
        $requestData = $request->toArray();
        $promoValidErrorMessage = $this->cartRepositoryService->checkValidPromo($requestData);

        if (!empty($promoValidErrorMessage)) {
            unset($requestData['promo_code']);
        }

        if (!empty($requestData['promo_code'])) {
            $cart = Cart::query()->where('cart_id', $id)->firstOrFail();

            $cartData['s_cart_id'] = $cart->id;
            $cartData['company_id'] = $cart->company_id;
            $cartData['cartProducts'] = $cart->cartProduct;
            $cartData['promo_code'] = $requestData['promo_code'];
            $promoErrorMessage = (new SailPlayService)->sailPlayCart($cartData);
        }

        if (!empty($promoErrorMessage)) {
            unset($requestData['promo_code']);
        }

        $cartAndStatus = $this->cartRepositoryService->update($id, $requestData, $cityService);

        $cartResource = new CartResource($cartAndStatus);
        $cartResource->promoErrorMessage = $promoValidErrorMessage ?? $promoErrorMessage;

        return $cartResource;
    }

    /**
     * @param $cartId
     * @return mixed
     * @throws GuzzleException
     */
    public function getNearDateTime($cartId): mixed
    {
        $data = $this->cartRepositoryService->getNearDateTime($cartId);

        return new CartResource($data);
    }

}
