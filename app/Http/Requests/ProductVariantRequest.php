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
  
    }
}