<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'AddressID' => 'required|exists:addresses,AddressID',
            'PaymentMethodID' => 'required|exists:payment_methods,PaymentMethodID',
            // 'products' => 'required|array',
            // 'products.*.ProductID' => 'required|exists:products,ProductID',
            // 'products.*.VariantID' => 'required|exists:variants,VariantID',
            // 'products.*.Quantity' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'AddressID.required' => 'The address ID is required.',
            'AddressID.exists' => 'The selected address ID is invalid.',
            'PaymentMethodID.required' => 'The payment method ID is required.',
            'PaymentMethodID.exists' => 'The selected payment method ID is invalid.',
            // 'products.*.ProductID.required' => 'The product ID is required.',
            // 'products.*.ProductID.exists' => 'The selected product ID is invalid.',
            // 'products.*.VariantID.required' => 'The variant ID is required.',
            // 'products.*.VariantID.exists' => 'The selected variant ID is invalid.',
            // 'products.*.Quantity.required' => 'The quantity is required.',
            // 'products.*.Quantity.integer' => 'The quantity must be an integer.',
            // 'products.*.Quantity.min' => 'The quantity must be at least 1.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422));
    }

}
