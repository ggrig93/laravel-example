<?php

namespace Modules\Cart\Services;

use App\DTO\GetNearDateTimeDto;
use App\Http\Controllers\API\DaDataController;
use App\Services\DelayTime\DelayTimeService;
use App\Services\GetPolygons\GetPolygonsService;
use App\DTO\DaDataFilterDto;
use App\Interfaces\DaData\SuggestServiceInterface;
use App\Services\SailPlay\SailPlayService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;
use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartProduct;
use Modules\Order\Services\OrderRepositoryService;
use Psr\SimpleCache\InvalidArgumentException;

class CartRepositoryService
{
    /**
     * @param string $id
     * @return Builder|Model
     * @throws InvalidArgumentException
     */
    public function show(string $id)
    {
        $cart = Cart::query()->where('cart_id', $id)->firstOrFail();
        $sum['cart_id'] = $id;
        $productPrice = (new OrderRepositoryService(new GetPolygonsService()))->sum($sum);
        $deliveryData = $productPrice['cart']->toArray();

        $cart->deliveryCost = $deliveryData['deliveryCost'];
        $cart->diffCost = $deliveryData['diffCost'];
        $cart->excludeDelivery = $deliveryData['excludeDelivery'];

        return $cart;
    }

    /**
     * @param $data
     * @param $cityService
     * @return null
     */
    private function getDaDataId($requestData, $cityService)
    {
        $daDataService = App::make(SuggestServiceInterface::class);

        $data = DaDataFilterDto::transform($requestData);
        $data->setFromBound('country');
        $data->setToBound('street');
        $citySlug = App::getLocale();
        $cityRadius = $cityService->getRadiusBySlug($citySlug);
        $data->setRadius($cityRadius);

        $daData = $daDataService->suggest($data, $cityService);

        $daDataId = null;

        if ($daData) {
            $daDataId = $daData[0]['data']['kladr_id'] ?? $daData[0]['data']['city_kladr_id'];
        }

        return $daDataId;
    }

    /**
     * @param $data
     * @param $cityService
     * @return Builder|Model
     * @throws InvalidArgumentException
     */
    public function create($data, $cityService)
    {
        $daDataId = $this->getDaDataId($data, $cityService);

        $newCart = Cart::query()->create([
            'promo_code' => array_key_exists('promo_code', $data) ? $data['promo_code'] : null,
            'company_id' => $data['company_id'],
            'delivery_type' => $data['delivery_type'],
            'delivery_address_classified' => $daDataId ?? null,
            'address_id' => $data['address_id'] ?? null,
            'address' => $data['delivery_address']['address'] ?? null,
            'homeNumber' => $data['delivery_address']['homeNumber'] ?? null,
            'building' => $data['delivery_address']['building'] ?? null,
            'entrance' => $data['delivery_address']['entrance'] ?? null,
            'intercom' => $data['delivery_address']['intercom'] ?? null,
            'floor' => $data['delivery_address']['floor'] ?? null,
            'apartment' => $data['delivery_address']['apartment'] ?? null,
            'comment' => $data['delivery_address']['comment'] ?? null,
        ]);

        $cartId = str_replace('/', '', bcrypt($newCart->id));
        $newCart->cart_id = $cartId;

        if (Auth::id()) {
            $newCart->user_id = Auth::id();
        }

        $newCart->save();

        return $newCart;
    }

    /**
     * @return mixed
     */
    public function getAuthCart(): mixed
    {
        return Cart::query()->where('user_id', Auth::id())->pluck('cart_id')->first();
    }

    /**
     * @param $data
     * @return ?string
     */
    public function checkValidPromo(&$data): ?string
    {
        if (empty($data['promo_code'])) {
            return null;
        }

        $companyId = $data['company_id'];
        $sailPayService = new SailPlayService();


        return $sailPayService->sailPaySearch($companyId, $data['promo_code']);
    }

    /**
     * @param string $id
     * @param $data
     * @param $cityService
     * @return Builder|Model
     * @throws InvalidArgumentException
     */
    public function update(string $id, $data, $cityService): Model|Builder
    {
        $cart = Cart::query()->where('cart_id', $id)->firstOrFail();

        if (!$cart->user_id && Auth::id()) {

            Cart::query()->where('cart_id', $id)->update(['user_id' => Auth::id()]);
        }

        $daDataId = $this->getDaDataId($data, $cityService);

        if (array_key_exists('promo_code', $data)) {
            $SailPlayData['s_cart_id'] = $cart->id;
            $SailPlayData['company_id'] = $data['company_id'];
            $SailPlayData['promo_code'] = $data['promo_code'];
            $SailPlayData['cartProducts'] = $cart->cartProduct->toArray();

            if ($data['promo_code']) {
                (new SailPlayService)->sailPlayCart($SailPlayData);
            } else {
                $filter = [['cart_id', $cart->id], ['is_promo', 1]];
                CartProduct::query()->where($filter)->delete();
            }
        }

        $cart->update([
            'promo_code' => array_key_exists('promo_code', $data) ? $data['promo_code'] : null,
            'company_id' => $data['company_id'],
            'delivery_type' => $data['delivery_type'],
            'delivery_address_classified' => $daDataId ?? null,
            'address_id' => $data['address_id'] ?? null,
            'address' => $data['delivery_address']['address'] ?? null,
            'homeNumber' => $data['delivery_address']['homeNumber'] ?? null,
            'building' => $data['delivery_address']['building'] ?? null,
            'entrance' => $data['delivery_address']['entrance'] ?? null,
            'intercom' => $data['delivery_address']['intercom'] ?? null,
            'floor' => $data['delivery_address']['floor'] ?? null,
            'apartment' => $data['delivery_address']['apartment'] ?? null,
            'comment' => $data['delivery_address']['comment'] ?? null,
        ]);

        return $cart->load('cartProduct');
    }

    /**
     * @param $cartId
     * @return mixed
     * @throws GuzzleException
     */
    public function getNearDateTime($cartId): mixed
    {
        $cart = Cart::query()->where('cart_id', $cartId)->firstOrFail();
        $delayTime = (new DelayTimeService())->get($cartId);

        $cart->update(['delay_time' => $delayTime['delay_time']]);
        $cart->zoneTime = $delayTime['zone_time'];

        return $cart;
    }

}
