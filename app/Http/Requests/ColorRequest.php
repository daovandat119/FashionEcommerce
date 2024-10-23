<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ColorRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->route('id');
        return [
            'ColorName' => 'required|unique:colors,ColorName,' . $id . ',ColorID',
        ];
    }

    public function messages()
    {
        return [
            'ColorName.required' => 'Vui lòng nhập tên màu.',
            'ColorName.unique' => 'Màu đã tồn tại.',
        ];
    }

    public function attributes()
    {
        return [
            'ColorName' => 'color name',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
