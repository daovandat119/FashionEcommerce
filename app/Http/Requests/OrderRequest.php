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
            'PaymentMethodID' => 'required|exists:payment_methods,PaymentMethodID',
            'TotalAmount' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'PaymentMethodID.required' => 'The payment method ID is required.',
            'PaymentMethodID.exists' => 'The selected payment method ID is invalid.',
            'TotalAmount.required' => 'The total amount is required.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422));
    }

}
