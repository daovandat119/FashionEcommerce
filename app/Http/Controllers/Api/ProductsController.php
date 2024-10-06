<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Products;
use App\Http\Requests\ProductRequest;
use App\Models\ProductVariant;

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
        $dataInsert = $this->prepareProductData($request);
        
        // Thêm sản phẩm vào bảng products và lấy ID của sản phẩm mới tạo
        $productId = $this->repoProducts->addProduct($dataInsert);
        
        $this->handleProductImages($request, $productId);
    
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
        $product = Products::find($id);
        if (!$product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại.'], 404);
        }
    
        $dataUpdate = $this->prepareProductData($request);
        
        // Gọi phương thức updateProduct
        $this->repoProducts->updateProduct($dataUpdate, $id);
        
        $this->handleProductImages($request, $id);
    
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

    private function prepareProductData(ProductRequest $request)
    {
        $data = [
            'ProductName' => $request->input('ProductName'),
            'CategoryID' => $request->input('CategoryID'),
            'Price' => $request->input('Price'),
            'SalePrice' => $request->input('SalePrice'),
            'ShortDescription' => $request->input('ShortDescription'),
            'Description' => $request->input('Description'),
            'Status' => $request->input('Status'),
        ];

        if ($request->hasFile('MainImageURL') && $request->file('MainImageURL')->isValid()) {
            $file = $request->file('MainImageURL');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $path = public_path('product_images');
            $file->move($path, $filename);
    
            $data['MainImageURL'] = 'product_images/' . $filename;
        }

        return $data;
    }

    private function handleProductImages(Request $request, $productId)
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $extension = $image->getClientOriginalExtension();
                    $filename = time() . rand(1, 1000) . '.' . $extension;
                    $path = public_path('product_images');
                    $image->move($path, $filename);
    
                    DB::table('product_images')->insert([
                        'ProductID' => $productId,
                        'ImagePath' => 'product_images/' . $filename,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
     public function getProductVariants($productId)
    {
        $product = Products::find($productId);

        if (!$product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        $variants = ProductVariant::where('ProductID', $productId)
            ->with(['size', 'color'])  // Eager load relationships
            ->get()
            ->map(function ($variant) {
                return [
                    'VariantID' => $variant->VariantID,
                    'Size' => $variant->size->SizeName,
                    'Color' => $variant->color->ColorName,
                    'Quantity' => $variant->Quantity,
                    'Price' => $variant->Price,
                ];
            });

        return response()->json([
            'product' => $product,
            'variants' => $variants
        ], 200);
    }
}