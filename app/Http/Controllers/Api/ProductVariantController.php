<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Http\Requests\ProductVariantRequest;
class ProductVariantController extends Controller
{
    public function index()
    {
        $variants = ProductVariant::all();
        return response()->json(['message' => 'Success', 'data' => $variants], 200);
    }

   

    public function store(ProductVariantRequest $request)
    {
        // Nếu đến đây không có lỗi, dữ liệu đã hợp lệ
        $data = $request->validated();
        $variant = ProductVariant::create($data);
    
        return response()->json(['message' => 'Sản phẩm biến thể đã được thêm thành công!', 'data' => $variant], 201);
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
    
        // Cập nhật thông tin
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
