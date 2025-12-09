<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'symbol' => ['required', 'string', 'max:10'],
            'side' => ['required', 'in:buy,sell'],
            'price' => ['required', 'numeric', 'min:0.00000001'],
            'amount' => ['required', 'numeric', 'min:0.00000001'],
        ];
    }

    public function messages(): array
    {
        return [
            'symbol.required' => 'Trading symbol is required',
            'symbol.max' => 'Symbol cannot exceed 10 characters',
            'side.required' => 'Order side (buy/sell) is required',
            'side.in' => 'Order side must be either buy or sell',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a valid number',
            'price.min' => 'Price must be greater than 0',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a valid number',
            'amount.min' => 'Amount must be greater than 0',
        ];
    }
}
