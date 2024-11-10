<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'UserName' => 'required|string|max:255',
            'Address' => 'required|string|max:255',
            'PhoneNumber' => 'required|string|max:15',
            'DistrictID' => 'required',
            'WardCode' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'UserName.required' => 'The user name is required.',
            'UserName.string' => 'The user name must be a string.',
            'UserName.max' => 'The user name may not be greater than 255 characters.',
            'Address.required' => 'The address is required.',
            'Address.string' => 'The address must be a string.',
            'Address.max' => 'The address may not be greater than 255 characters.',
            'PhoneNumber.required' => 'The phone number is required.',
            'PhoneNumber.string' => 'The phone number must be a string.',
            'PhoneNumber.max' => 'The phone number may not be greater than 15 characters.',
            'DistrictID.required' => 'The district ID is required.',
            'DistrictID.string' => 'The district ID must be a string.',
            'WardCode.required' => 'The ward code is required.',
            'WardCode.string' => 'The ward code must be a string.',
        ];
    }

    public function attributes()
    {
        return [
            'UserName' => 'user name',
            'Address' => 'address',
            'PhoneNumber' => 'phone number',
            'DistrictID' => 'district ID',
            'WardCode' => 'ward code',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422));
    }


}
//
