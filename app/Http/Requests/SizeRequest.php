<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SizeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');
        return [
            'SizeName' => 'required|unique:sizes,SizeName,' . $id . ',SizeID',
        ];
    }

    public function messages()
    {
        return [
            'SizeName.required' => 'Vui lòng nhập Size.',
            'SizeName.unique' => 'Size đã tồn tại.',
        ];
    }

    public function attributes()
    {
        return [
            'SizeName' => 'size name',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

}
