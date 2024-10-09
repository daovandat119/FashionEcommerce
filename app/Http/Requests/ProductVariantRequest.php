<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Products;
use Illuminate\Validation\Rule;

class ProductVariantRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $product = Products::findOrFail($this->input('ProductID'));
        $minPrice = min($product->Price, $product->SalePrice);
        $maxPrice = max($product->Price, $product->SalePrice);

        return [
            'ProductID' => 'required|exists:products,ProductID',
            'SizeID' => 'required|array',
            'SizeID.*' => 'exists:sizes,SizeID',
            'ColorIDs' => 'required|array',
            'ColorIDs.*' => 'exists:colors,ColorID',
            'Quantity' => 'required|integer|min:0',
            'Price' => [
                'required',
                'numeric',
                'min:0',
                "between:$minPrice,$maxPrice",
            ],
        ];
    }

    public function messages()
    {
        return [
            'ProductID.required' => 'ID sản phẩm là bắt buộc.',
            'ProductID.exists' => 'ID sản phẩm không tồn tại.',
            'SizeID.required' => 'ID kích thước là bắt buộc.',
            'SizeID.array' => 'ID kích thước phải là một mảng.',
            'SizeID.*.exists' => 'ID kích thước không tồn tại.',
            'ColorIDs.required' => 'ID màu sắc là bắt buộc.',
            'ColorIDs.array' => 'ID màu sắc phải là một mảng.',
            'ColorIDs.*.exists' => 'ID màu sắc không tồn tại.',
            'Quantity.required' => 'Số lượng là bắt buộc.',
            'Quantity.integer' => 'Số lượng phải là số nguyên.',
            'Quantity.min' => 'Số lượng không được âm.',
            'Price.required' => 'Giá là bắt buộc.',
            'Price.numeric' => 'Giá phải là số.',
            'Price.min' => 'Giá không được âm.',
            'Price.between' => 'Giá sản phẩm biến thể phải nằm trong khoảng từ :min đến :max.',
        ];
    }
}