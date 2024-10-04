<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cho phép tất cả người dùng
    }

    public function rules()
    {
        return [
            'ProductName' => 'required|string|max:255',
            'CategoryID' => 'required|exists:categories,CategoryID', // Kiểm tra CategoryID có tồn tại trong bảng categories
            'MainImageURL' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:255',
            'Price' => 'required|numeric|min:0', // Giá không âm
            'SalePrice' => 'nullable|required|numeric|min:0', // Giá khuyến mãi không âm
            'ShortDescription' => 'nullable|required|string|max:500', // Mô tả ngắn tối đa 500 ký tự
            'Description' => 'nullable|required|string', // Mô tả chi tiết có thể rỗng
            'Status' => 'required|string|in:active,inactive', // Trạng thái phải là 'active' hoặc 'inactive'
        ];
    }

    public function messages()
    {
        return [
            'ProductName.required' => 'Tên sản phẩm là bắt buộc.',
            'CategoryID.required' => 'Danh mục sản phẩm là bắt buộc.',
            'CategoryID.exists' => 'Danh mục sản phẩm không tồn tại.',
            'MainImageURL.required' => 'URL hình ảnh chính là bắt buộc.',
            'Price.required' => 'Giá sản phẩm là bắt buộc.',
            'Price.numeric' => 'Giá sản phẩm phải là một số.',
            'Price.min' => 'Giá sản phẩm không được âm.',
            'SalePrice.numeric' => 'Giá khuyến mãi phải là một số.',
            'SalePrice.min' => 'Giá khuyến mãi không được âm.',
            'ShortDescription.max' => 'Mô tả ngắn không được vượt quá 500 ký tự.',
            'Status.required' => 'Trạng thái sản phẩm là bắt buộc.',
            'Status.in' => 'Trạng thái phải là active hoặc inactive.',
        ];
    }
}

