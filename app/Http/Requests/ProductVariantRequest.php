<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'SizeID' => 'required|array',
            'SizeID.*' => 'exists:sizes,SizeID',
            'ColorIDs' => 'required|array',
            'ColorIDs.*' => 'exists:colors,ColorID',
            'Quantity' => 'required|integer|min:1',
            'Price' => 'required|numeric|min:0',
        ];
    }
    
    


    public function messages()
    {
        return [
            'ProductID.required' => 'Mã sản phẩm không được bỏ trống.',
            'ProductID.exists' => 'Mã sản phẩm không tồn tại.',
            'SizeID.required' => 'Mã kích thước không được bỏ trống.',
            'SizeID.array' => 'Mã kích thước phải là một mảng.',
            'SizeID.*.exists' => 'Mã kích thước không tồn tại.',
            'ColorIDs.required' => 'Mã màu không được bỏ trống.',
            'ColorIDs.array' => 'Mã màu phải là một mảng.',
            'ColorIDs.*.exists' => 'Mã màu không tồn tại.',
            'Quantity.required' => 'Số lượng không được bỏ trống.',
            'Quantity.integer' => 'Số lượng phải là số nguyên.',
            'Quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 1.',
            'Price.required' => 'Giá không được bỏ trống.',
            'Price.numeric' => 'Giá phải là số.',
            'Price.min' => 'Giá phải lớn hơn hoặc bằng 0.',
        ];
    }
}
