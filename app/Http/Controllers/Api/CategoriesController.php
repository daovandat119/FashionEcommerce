<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Products;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    protected $repoCategories;

    public function __construct()
    {
        $this->repoCategories = new Categories();
    }

    public function index()
    {
        $categories = $this->repoCategories->listCategories();
        return response()->json(['message' => 'Success', 'data' => $categories], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'CategoryID' => 'required|unique:categories',
            'CategoryName' => 'required|unique:categories',
            'Status' => 'required',
        ], [
            'CategoryID.required' => ':attribute không được bỏ trống',
            'CategoryID.unique' => ':attribute đã tồn tại',
            'CategoryName.required' => ':attribute không được bỏ trống',
            'CategoryName.unique' => ':attribute đã tồn tại',
            'Status.required' => ':attribute không được bỏ trống',
        ], [
            'CategoryID' => 'Mã danh mục',
            'CategoryName' => 'Tên danh mục',
            'Status' => 'Trạng thái'
        ]);

        $dataInsert = [
            'CategoryID' => $request->input('CategoryID'),
            'CategoryName' => $request->input('CategoryName'),
            'Status' => $request->input('Status'),
        ];

        $this->repoCategories->addCategory($dataInsert);

        return response()->json([
            'message' => 'Success',
            'data' => $dataInsert
        ], 201);
    }

    public function edit($id) {

        $category = $this->repoCategories->getDetail($id);
        
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
    
        return response()->json($category);
    }
    



    public function update(Request $request, $id)
{
    // Validate input
    $request->validate([
        'CategoryID' => 'required|unique:categories,CategoryID,' . $id . ',CategoryID',
        'CategoryName' => 'required',
        'Status' => 'required',
    ], [
        'CategoryID.required' => ':attribute không được bỏ trống',
        'CategoryID.unique' => ':attribute đã tồn tại',
        'CategoryName.required' => ':attribute không được bỏ trống',
        'Status.required' => ':attribute không được bỏ trống',
    ], [
        'CategoryID' => 'Mã danh mục',
        'CategoryName' => 'Tên danh mục',
        'Status' => 'Trạng thái'
    ]);

    // Update data
    $dataUpdate = [
        'CategoryID' => $request->input('CategoryID'),
        'CategoryName' => $request->input('CategoryName'),
        'Status' => $request->input('Status'),
    ];

    $this->repoCategories->updateCategory($id, $dataUpdate);

    // Tạo response với header
    return response()
        ->json(['message' => "Cập nhật thành công cho danh mục {$dataUpdate['CategoryID']}!", 'data' => $dataUpdate], 200)
        ->header('Content-Type', 'application/json')
        ->header('X-Custom-Header', 'Your Value Here'); // Thay đổi hoặc thêm header tùy ý
}
public function delete($id)
{
    // Xóa các sản phẩm thuộc danh mục này
    $deletedProductsCount = Products::where('CategoryID', $id)->delete();

    // Xóa các bản ghi liên quan trong bảng order_items cho các sản phẩm đã xóa
    DB::table('order_items')->whereIn('ProductID', function($query) use ($id) {
        $query->select('ProductID')->from('products')->where('CategoryID', $id);
    })->delete();

    // Xóa danh mục
    $deletedCategoryCount = $this->repoCategories->deleteCategory($id);

    if ($deletedCategoryCount > 0) {
        return response()->json(['message' => 'Category and related products deleted successfully'], 200);
    } else {
        return response()->json(['message' => 'Category not found or cannot be deleted'], 404);
    }
}


    
}
