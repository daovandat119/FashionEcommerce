<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Sizes;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    protected $repoSizes;

    public function __construct()
    {
        $this->repoSizes = new Sizes();
    }

    public function index()
    {
        $sizes = $this->repoSizes->listSizes();
        return response()->json(['message' => 'Success', 'data' => $sizes], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'SizeID' => 'required|unique:sizes',
            'SizeName' => 'required',
        ], [
            'SizeID.required' => ':attribute không được bỏ trống',
            'SizeID.unique' => ':attribute đã tồn tại',
            'SizeName.required' => ':attribute không được bỏ trống',
        ], [
            'SizeID' => 'Mã kích thước',
            'SizeName' => 'Tên kích thước',
        ]);

        $dataInsert = [
            'SizeID' => $request->input('SizeID'),
            'SizeName' => $request->input('SizeName'),
        ];

        $this->repoSizes->addSize($dataInsert);

        return response()->json(['message' => 'Success', 'data' => $dataInsert], 201);
    }

    public function edit($id)
    {
        $size = $this->repoSizes->getDetail($id);
        
        if (!$size) {
            return response()->json(['message' => 'Size not found'], 404);
        }

        return response()->json($size);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'SizeID' => 'required|unique:sizes,SizeID,' . $id . ',SizeID',
            'SizeName' => 'required',
        ], [
            'SizeID.required' => ':attribute không được bỏ trống',
            'SizeID.unique' => ':attribute đã tồn tại',
            'SizeName.required' => ':attribute không được bỏ trống',
        ], [
            'SizeID' => 'Mã kích thước',
            'SizeName' => 'Tên kích thước',
        ]);

        $dataUpdate = [
            'SizeID' => $request->input('SizeID'),
            'SizeName' => $request->input('SizeName'),
        ];

        $this->repoSizes->updateSize($id, $dataUpdate);

        return response()->json(['message' => 'Cập nhật thành công!', 'data' => $dataUpdate], 200);
    }

    public function delete($id)
    {
        // Xóa các biến thể sản phẩm liên quan (nếu có)
        DB::table('product_variants')->where('SizeID', $id)->delete();
    
        // Xóa size
        $deletedSizeCount = $this->repoSizes->deleteSize($id);
    
        if ($deletedSizeCount > 0) {
            return response()->json(['message' => 'Size and related product variants deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Size not found or cannot be deleted'], 404);
        }
    }
    
}
