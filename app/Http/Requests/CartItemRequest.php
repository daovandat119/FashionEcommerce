<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CartItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'productID' => 'required|exists:product_variants,ProductID',
            'sizeID' => 'required|exists:product_variants,SizeID',
            'colorID' => 'required|exists:product_variants,ColorID',
            'quantity' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'productID.required' => 'The product ID is required.',
            'productID.exists' => 'The selected product does not exist.',
            'sizeID.required' => 'The size ID is required.',
            'sizeID.exists' => 'The selected size does not exist.',
            'colorID.required' => 'The color ID is required.',
            'colorID.exists' => 'The selected color does not exist.',
            'quantity.required' => 'The quantity is required.',
            'quantity.integer' => 'The quantity must be an integer.',
            'quantity.min' => 'The quantity must be at least 1.',
        ];
    }

    public function attributes()
    {
        return [
            'productID' => 'product ID',
            'sizeID' => 'size ID',
            'colorID' => 'color ID',
            'quantity' => 'quantity',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
