<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductVariantRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ProductID' => 'required|exists:products,ProductID',
            'SizeID' => 'required',
            'SizeID.*' => 'exists:sizes,SizeID',
            'ColorID' => 'required',
            'ColorID.*' => 'exists:colors,ColorID',
            'Quantity' => 'required|integer|min:0',
            'Price' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'ProductID.required' => 'The product ID is required.',
            'ProductID.exists' => 'The selected product does not exist.',
            'SizeID.required' => 'The size ID is required.',
            'ColorID.required' => 'The color ID is required.',
            'Quantity.required' => 'The quantity is required.',
            'Quantity.integer' => 'The quantity must be an integer.',
            'Quantity.min' => 'The quantity must be at least 0.',
            'Price.required' => 'The price is required.',
            'Price.numeric' => 'The price must be a number.',
            'Price.min' => 'The price must be at least 0.',
        ];
    }

    public function attributes()
    {
        return [
            'ProductID' => 'product ID',
            'SizeID' => 'size ID',
            'ColorID' => 'color ID',
            'Quantity' => 'quantity',
            'Price' => 'price',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

}
