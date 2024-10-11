<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class WishlistRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');
        return [
            'ProductID' => 'required|exists:products,ProductID,' . $id . ',ProductID',
        ];
    }

    public function messages()
    {
        return [
            'ProductID.required' => 'The product ID is required.',
            'ProductID.exists' => 'The selected product does not exist.',
        ];
    }

    public function attributes()
    {
        return [
            'ProductID' => 'product ID',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
    
}
