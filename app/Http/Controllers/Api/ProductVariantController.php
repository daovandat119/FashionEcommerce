<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProductVariantRequest;
use Illuminate\Support\Facades\Log;
use App\Models\Colors;
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
    
        foreach ($request->SizeID as $sizeID) {
            foreach ($request->ColorIDs as $colorID) {
                // Kiểm tra xem biến thể đã tồn tại chưa
                $exists = ProductVariant::where('ProductID', $request->ProductID)
                    ->where('SizeID', $sizeID)
                    ->where('ColorID', $colorID)
                    ->exists();
    
                if (!$exists) {
                    // Thêm biến thể mới
                    ProductVariant::create([
                        'ProductID' => $request->ProductID,
                        'SizeID' => $sizeID,
                        'ColorID' => $colorID,
                        'Quantity' => $request->Quantity,
                        'Price' => $request->Price,
                    ]);
                    $addedVariants[] = "ProductID: {$request->ProductID}, SizeID: {$sizeID}, ColorID: {$colorID}";
                } else {
                    // Lưu lại các biến thể đã tồn tại
                    $existingVariants[] = "ProductID: {$request->ProductID}, SizeID: {$sizeID}, ColorID: {$colorID}";
                }
            }
        }
    
        // Phản hồi lại thông tin
        $message = 'Thêm biến thể sản phẩm thành công.';
        if (!empty($existingVariants)) {
            $message .= ' Một số biến thể đã tồn tại: ' . implode(', ', $existingVariants);
        }
    
        return response()->json([
            'message' => $message,
            'added_variants' => $addedVariants,
            'existing_variants' => $existingVariants,
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

  

    public function update(ProductVariantRequest $request, $id)
    {
        // Tìm biến thể sản phẩm theo ID
        $variant = ProductVariant::find($id);
    
        if (!$variant) {
            return response()->json(['message' => 'Biến thể không tồn tại'], 404);
        }
    
        $variant->update([
            'ProductID' => $request->input('ProductID'),
            'SizeID' => $request->input('SizeID'),
            'ColorID' => $request->input('ColorID'),
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
}
