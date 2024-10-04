<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Colors;
use App\Models\Products;
use Illuminate\Http\Request;

class ColorsController extends Controller
{
    protected $repoColors;

    public function __construct()
    {
        $this->repoColors = new Colors();
    }

    public function index()
    {
        $colors = $this->repoColors->listColors();
        return response()->json(['message' => 'Success', 'data' => $colors], 200);
    }

    public function store(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'ColorName' => 'required|unique:colors|max:255',
        ], [
            'ColorName.required' => 'Tên màu không được bỏ trống.',
            'ColorName.unique' => 'Tên màu đã tồn tại.',
        ]);
    
        // Tạo dữ liệu mới
        $color = new Colors();
        $color->ColorName = $request->input('ColorName');
        $color->save(); // Lưu màu mới vào cơ sở dữ liệu
    
        return response()->json(['message' => 'Màu sắc đã được thêm thành công!', 'data' => $color], 201);
    }
    

    public function edit($id)
    {
        $color = $this->repoColors->getDetail($id);

        if (!$color) {
            return response()->json(['message' => 'Color not found'], 404);
        }

        return response()->json($color);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ColorID' => 'required|unique:colors,ColorID,' . $id . ',ColorID',
            'ColorName' => 'required',
        ], [
            'ColorID.required' => ':attribute không được bỏ trống',
            'ColorID.unique' => ':attribute đã tồn tại',
            'ColorName.required' => ':attribute không được bỏ trống',
        ], [
            'ColorID' => 'Mã màu',
            'ColorName' => 'Tên màu',
        ]);

        $dataUpdate = [
            'ColorID' => $request->input('ColorID'),
            'ColorName' => $request->input('ColorName'),
        ];

        $this->repoColors->updateColor($id, $dataUpdate);

        return response()->json(['message' => 'Cập nhật thành công!', 'data' => $dataUpdate], 200);
    }

    public function delete($id)
    {
        // Xóa các biến thể sản phẩm liên quan (nếu có)
        DB::table('product_variants')->where('ColorID', $id)->delete();
    
        // Xóa color
        $deletedColorCount = $this->repoColors->deleteColor($id);
    
        if ($deletedColorCount > 0) {
            return response()->json(['message' => 'Color and related product variants deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Color not found or cannot be deleted'], 404);
        }
    }
    
    
}
