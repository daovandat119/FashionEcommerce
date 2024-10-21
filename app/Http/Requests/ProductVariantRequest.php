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
            'ProductID.required' => 'Trường ID sản phẩm là bắt buộc.',
            'ProductID.exists' => 'Sản phẩm đã chọn không tồn tại.',
            'SizeID.required' => 'Trường ID kích thước là bắt buộc.',
            'ColorID.required' => 'Trường ID màu sắc là bắt buộc.',
            'Quantity.required' => 'Trường số lượng là bắt buộc.',
            'Quantity.integer' => 'Số lượng phải là một số nguyên.',
            'Quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 0.',
            'Price.required' => 'Trường giá là bắt buộc.',
            'Price.numeric' => 'Giá phải là một số.',
            'Price.min' => 'Giá phải lớn hơn hoặc bằng 0.',
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
