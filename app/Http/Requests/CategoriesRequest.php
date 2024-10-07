<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoriesRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'CategoryName' => 'required|unique:categories,CategoryName,' . $id . ',CategoryID',
        ];
    }


    public function messages(): array
    {
        return [
            'CategoryName.required' => ':attribute không được bỏ trống',
            'CategoryName.unique' => ':attribute đã tồn tại',
        ];
    }


    public function attributes(): array
    {
        return [
            'CategoryName' => 'Tên danh mục',
        ];
    }
}
