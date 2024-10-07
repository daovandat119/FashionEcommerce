<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow all users to make this request. Adjust as needed.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'CategoryID' => 'required|exists:categories,CategoryID',
            'ProductName' => 'required|string|max:255',
            'MainImageURL' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ImagePath' => 'required|array',
            'ImagePath.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'Price' => 'required|numeric|min:0',
            'SalePrice' => 'nullable|numeric|min:0|lte:Price',
            'ShortDescription' => 'nullable|string|max:255',
            'Description' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'CategoryID.required' => 'Danh mục không được bỏ trống',
            'ProductName.required' => 'Tên sản phẩm không được bỏ trống',
            'MainImageURL.required' => 'URL hình ảnh chính không được bỏ trống',
            'MainImageURL.image' => 'URL hình ảnh chính phải là một tệp hình ảnh',
            'ImagePath.required' => 'URL hình ảnh không được bỏ trống',
            'ImagePath.array' => 'URL hình ảnh phải là một mảng',
            'ImagePath.*.image' => 'Mỗi URL hình ảnh phải là một tệp hình ảnh',
            'Price.required' => 'Giá sản phẩm không được bỏ trống',
            'SalePrice.lte' => 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'CategoryID' => 'Danh mục',
            'ProductName' => 'Tên sản phẩm',
            'MainImageURL' => 'URL hình ảnh chính',
            'ImagePath' => 'URL hình ảnh',
            'Price' => 'Giá sản phẩm',
            'SalePrice' => 'Giá khuyến mãi',
            'ShortDescription' => 'Mô tả ngắn',
            'Description' => 'Mô tả',
        ];
    }
}
