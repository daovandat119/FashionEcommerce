<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProductVariantRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Colors;
use App\Models\Products;
use App\Models\Sizes;

class ProductVariantController extends Controller
{
    public function index()
    {
        $variants = ProductVariant::all();
        return response()->json(['message' => 'Success', 'data' => $variants], 200);
    }

    public function store(ProductVariantRequest $request)
    {
        $addedVariants = [];
        $existingVariants = [];
        $errorVariants = [];

        $product = Products::findOrFail($request->ProductID);
        $priceValidation = $this->validateAndProcessPrice($request->Price, $product);

        if (!$priceValidation['isValid']) {
            return response()->json([
                'message' => 'Lỗi giá',
                'errors' => ['Price' => [$priceValidation['errorMessage']]]
            ], 400);
        }

        foreach ($request->SizeID as $sizeID) {
            foreach ($request->ColorIDs as $colorID) {
                if (!$this->checkVariantExists($request->ProductID, $sizeID, $colorID)) {
                    ProductVariant::create([
                        'ProductID' => $request->ProductID,
                        'SizeID' => $sizeID,
                        'ColorID' => $colorID,
                        'Quantity' => $request->Quantity,
                        'Price' => $request->Price,
                    ]);
                    $addedVariants[] = "ProductID: {$request->ProductID}, SizeID: {$sizeID}, ColorID: {$colorID}";
                } else {
                    $existingVariants[] = "ProductID: {$request->ProductID}, SizeID: {$sizeID}, ColorID: {$colorID}";
                }
            }
        }

        return response()->json([
            'message' => 'Thêm biến thể sản phẩm thành công.',
            'Đã Thêm Thành Công' => $addedVariants,
            'Đã Tồn Tại' => $existingVariants,
        ]);
    }

    public function show($id)
    {
        $variant = ProductVariant::find($id);

        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }

        return response()->json($variant);
    }

    public function update(Request $request, $id)
    {
        $variant = ProductVariant::find($id);
    
        if (!$variant) {
            return response()->json(['message' => 'Biến thể không tồn tại'], 404);
        }
    
        $product = Products::findOrFail($variant->ProductID);
        $priceValidation = $this->validateAndProcessPrice($request->input('Price'), $product);
    
        if (!$priceValidation['isValid']) {
            return response()->json([
                'message' => 'Có lỗi xảy ra!',
                'errors' => ['Price' => [$priceValidation['errorMessage']]]
            ], 400);
        }
    
        $variant->update([
            'Quantity' => $request->input('Quantity'),
            'Price' => $request->input('Price'),
        ]);
    
        return response()->json(['message' => 'Cập nhật thành công!', 'data' => $variant], 200);
    }

    public function delete($id)
    {
        $variant = ProductVariant::find($id);

        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }

        $variant->delete();

        return response()->json(['message' => 'Variant deleted successfully!'], 200);
    }

    private function checkVariantExists($productId, $sizeId, $colorId)
    {
        return ProductVariant::where('ProductID', $productId)
            ->where('SizeID', $sizeId)
            ->where('ColorID', $colorId)
            ->exists();
    }

    private function validateAndProcessPrice($price, $product)
    {
        $minPrice = min($product->Price, $product->SalePrice);
        $maxPrice = max($product->Price, $product->SalePrice);

        if ($price < $minPrice || $price > $maxPrice) {
            return [
                'isValid' => false,
                'errorMessage' => "Giá ({$price}) nằm ngoài khoảng cho phép ($minPrice - $maxPrice)"
            ];
        }

        return ['isValid' => true];
    }
}