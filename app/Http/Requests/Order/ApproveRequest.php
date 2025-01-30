<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\BaseRequest;

class ApproveRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => [
                'required',
                'exists:orders,id',
                'integer',
            ],

            // user_id только в рамках тестового задания
            'user_id' =>[
                'required',
                'exists:users,id',
                'integer',
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'order_id.required' => 'ID заказа обязательное поле',
            'order_id.exists' => 'Заказ не найден',
            'order_id.integer' => 'ID заказа должно быть целым числом',
        ];
    }
}
