<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Products;
use App\Http\Requests\ProductRequest;

class ProductsController extends Controller {
    protected $repoProducts;

    public function __construct() {
        $this->repoProducts = new Products();
    }

    public function index() {
        $list_products = $this->repoProducts->listProducts();
        return response()->json($list_products);
    }
    public function store(ProductRequest $request)
    {
        // Tạo dữ liệu cho sản phẩm
        $dataInsert = [
            'ProductName' => $request->input('ProductName'),
            'CategoryID' => $request->input('CategoryID'),
            'Price' => $request->input('Price'),
            'SalePrice' => $request->input('SalePrice'),
            'ShortDescription' => $request->input('ShortDescription'),
            'Description' => $request->input('Description'),
            'Status' => $request->input('Status'),
        ];
    
        // Xử lý ảnh chính nếu có
        if ($request->hasFile('MainImageURL') && $request->file('MainImageURL')->isValid()) {
            $file = $request->file('MainImageURL');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $path = public_path('product_images');
            $file->move($path, $filename);
    
            // Cập nhật đường dẫn hình ảnh vào mảng dữ liệu
            $dataInsert['MainImageURL'] = 'product_images/' . $filename;
        }
    
        // Thêm sản phẩm vào bảng products và lấy ID của sản phẩm mới tạo
        $productId = $this->repoProducts->addProduct($dataInsert);
    
        // Xử lý ảnh nếu có
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) { // Kiểm tra từng file riêng biệt
                    $extension = $image->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $path = public_path('product_images');
                    $image->move($path, $filename);
    
                    // Thêm ảnh vào bảng product_images
                    DB::table('product_images')->insert([
                        'ProductID' => $productId,
                        'ImagePath' => 'product_images/' . $filename,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    
        return response()->json(['success' => true, 'message' => 'Product added successfully!'], 201);
    }
    
    
    
    
    
    

    public function edit($id) {

        $Products = $this->repoProducts->getDetail($id);
        
        if (!$Products) {
            return response()->json(['message' => 'Products not found'], 404);
        }
    
        return response()->json($Products);
    }



    public function update(ProductRequest $request, $id)
    {
        // Tìm sản phẩm theo ID
        $product = Products::find($id);
        if (!$product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại.'], 404);
        }
    
        // Cập nhật dữ liệu
        $dataUpdate = [
            'ProductName' => $request->input('ProductName'),
            'CategoryID' => $request->input('CategoryID'),
            'MainImageURL' => $request->input('MainImageURL'),
            'Price' => $request->input('Price'),
            'SalePrice' => $request->input('SalePrice'),
            'ShortDescription' => $request->input('ShortDescription'),
            'Description' => $request->input('Description'),
            'Status' => $request->input('Status'),
        ];
    
        // Gọi phương thức updateProduct
        $this->repoProducts->updateProduct($dataUpdate, $id); // Cập nhật thông tin sản phẩm
    
        // Xử lý ảnh chính nếu có
        if ($request->hasFile('MainImageURL') && $request->file('MainImageURL')->isValid()) {
            $file = $request->file('MainImageURL');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $path = public_path('product_images'); // Đường dẫn thư mục ảnh
            $file->move($path, $filename);
            
            // Cập nhật lại đường dẫn ảnh chính
            $dataUpdate['MainImageURL'] = 'product_images/' . $filename;
            $product->MainImageURL = $dataUpdate['MainImageURL'];
            $product->save(); // Lưu lại thay đổi
        }
    
        // Xử lý các ảnh khác nếu có
        if ($request->hasFile('images') && is_array($request->file('images'))) {
            foreach ($request->file('images') as $file) {
                if ($file->isValid()) {
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . rand(1, 1000) . '.' . $extension; // Đảm bảo tên file duy nhất
                    $path = public_path('product_images');
                    $file->move($path, $filename);
    
                    // Thêm ảnh vào bảng product_images
                    DB::table('product_images')->insert([
                        'ProductID' => $id, // Liên kết với sản phẩm
                        'ImagePath' => 'product_images/' . $filename,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    
        return response()->json(['message' => 'Cập nhật sản phẩm thành công!', 'data' => $dataUpdate], 200);
    }
    
    
    public function delete($id)
    {
        // Tìm sản phẩm bằng ProductID
        $product = Products::find($id);
        
        // Kiểm tra xem sản phẩm có tồn tại hay không
        if ($product) {
            // Xóa sản phẩm
            $product->delete(); // Gọi phương thức delete của Eloquent
        
            return response()->json(['success' => true, 'message' => 'Xóa sản phẩm thành công'], 200);
        }
        
        return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại'], 404);
    }
    
    
    
    
    

    

}

