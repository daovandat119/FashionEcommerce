<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Products;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    protected $repoProducts;

    public function __construct()
    {
        $this->repoProducts = new Products();
    }

    public function index()
    {
        $listProducts = $this->repoProducts->listProducts();
        return response()->json($listProducts);
    }

    public function store(ProductRequest $request)
    {
        $dataInsert = $this->prepareProductData($request);
        $productId = $this->repoProducts->addProduct($dataInsert);
        $this->handleProductImages($request, $productId);

        return response()->json(['success' => true, 'message' => 'Product added successfully!'], 201);
    }

    public function edit($id)
    {
        $product = $this->repoProducts->getDetail($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    public function update(ProductRequest $request, $id)
    {
        $product = Products::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $dataUpdate = $this->prepareProductData($request);
        $this->repoProducts->updateProduct($dataUpdate, $id);
        $this->handleProductImages($request, $id);

        return response()->json(['message' => 'Product updated successfully!', 'data' => $dataUpdate], 200);
    }

    public function delete($id)
    {
        $product = Products::find($id);

        if ($product) {
            $product->delete();
            return response()->json(['success' => true, 'message' => 'Product deleted successfully'], 200);
        }

        return response()->json(['success' => false, 'message' => 'Product not found'], 404);
    }

    private function prepareProductData(ProductRequest $request)
    {
        $data = $request->only([
            'ProductName', 'CategoryID', 'Price', 'SalePrice',
            'ShortDescription', 'Description', 'Status'
        ]);

        if ($request->hasFile('MainImageURL') && $request->file('MainImageURL')->isValid()) {
            $file = $request->file('MainImageURL');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('product_images'), $filename);
            $data['MainImageURL'] = 'product_images/' . $filename;
        }

        return $data;
    }

    private function handleProductImages(Request $request, $productId)
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $filename = time() . rand(1, 1000) . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('product_images'), $filename);

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
            return response()->json(['message' => 'Product not found'], 404);
        }

        $variants = ProductVariant::where('ProductID', $productId)
            ->with(['size', 'color'])
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
