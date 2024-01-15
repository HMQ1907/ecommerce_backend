<?php

namespace Modules\Setting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryPriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'prices.*.from' => [
                'required',
            ],
            'prices.*.to' => [
                'required',
            ],
            'prices.*.price' => [
                'required',
            ],
        ];
    }
}
