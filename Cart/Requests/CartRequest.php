<?php

namespace Modules\Cart\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

/**
 * Class CartRequest
 * @package Modules\Cart\Requests
 */
class CartRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {

        return [
            'promo_code' => ['nullable', 'string'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'address_id' => ['nullable', 'integer', 'exists:user_addresses,id'],
            'delivery_type' => ['required', 'integer'],
            'delivery_address.address' => ['required', 'string'],
            'delivery_address.homeNumber' => ['required', 'string'],
            'delivery_address.building' => ['nullable', 'string'],
            'delivery_address.entrance' => ['nullable', 'integer', 'min:0'],
            'delivery_address.intercom' => ['nullable', 'integer', 'min:0'],
            'delivery_address.floor' => ['nullable', 'integer', 'min:0'],
            'delivery_address.apartment' => ['nullable', 'integer', 'min:0'],
            'delivery_address.comment' => ['nullable', 'string'],
        ];
    }

}
