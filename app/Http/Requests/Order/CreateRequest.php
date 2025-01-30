<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\BaseRequest;

class CreateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'products' => [
                'required',
                'array',
            ],
            'products.*.id' => [
                'required',
                'integer',
                'exists:products,id',
            ],
            'products.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'products.required' => 'products обязательное поле',
            'products.array' => 'products должен быть массивом',
            'products.*.id.required' => 'ID товара обязательное поле',
            'products.*.id.integer' => 'ID товара должно быть целым числом',
            'products.*.id.exists' => 'Товар не найден',
            'products.*.quantity.required' => 'Количество товара обязательное поле',
            'products.*.quantity.integer' => 'Количество товара должно быть целым числом',
            'products.*.quantity.min' => 'Количество товара должно быть больше 0',
        ];
    }
}
