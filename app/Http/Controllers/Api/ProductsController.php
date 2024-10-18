<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Products;
use Illuminate\Http\Request;
use App\Models\ProductImage;
use App\Http\Requests\ProductsRequest;

class ProductsController extends Controller
{
    protected $repoProducts;

    public function __construct()
    {
        $this->repoProducts = new Products();
    }

    public function index(Request $request)
    {
        $total = $this->repoProducts->countProducts();
        $page = $request->input('Page', 1);
        $limit = $request->input('Limit', 10);
        $categoryId = $request->input('CategoryID');

        $listProducts = $this->repoProducts->listProducts(
            $request->input('Search'),
            ($page - 1) * $limit,
            $limit,
            $categoryId
        );

        $totalPage = ceil($total / $limit);

        return response()->json([
            'message' => 'Success',
            'data' => $listProducts,
            'total' => $total,
            'totalPage' => $totalPage,
            'page' => $page,
            'limit' => $limit
        ], 200);
    }

    public function store(ProductsRequest $request)
    {

        $category = (new Categories())->getDetail($request->CategoryID);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $data = [
            'ProductName' => $request->ProductName,
            'CategoryID' => $request->CategoryID,
            'Price' => $request->Price,
            'SalePrice' => $request->SalePrice,
            'ShortDescription' => $request->ShortDescription,
            'Description' => $request->Description,
        ];

        if ($request->hasFile('MainImageURL') && $request->file('MainImageURL')->isValid()) {
            $file = $request->file('MainImageURL');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('product_images'), $filename);
            $data['MainImageURL'] = 'product_images/' . $filename;
        }

        $productId = $this->repoProducts->addProduct($data);

        if ($request->hasFile('ImagePath')) {
            $imagePaths = [];
            foreach ($request->file('ImagePath') as $image) {
                if ($image->isValid()) {
                    $filename = time() . rand(1, 1000) . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('product_images'), $filename);
                    $imagePaths[] = 'product_images/' . $filename;
                }
            }
            $imagePath = implode(',', $imagePaths);
            (new ProductImage())->createProductImage($productId, $imagePath);
            return response()->json([
                'success' => true,
                'message' => 'Operation completed successfully',
                'data' => [
                    'product' => $productId,
                ]
            ], 201);
        }
    }

    public function edit($id)
    {
        $product = $this->repoProducts->getDetail($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => [
                'product' => $product,
            ]
        ], 200);
    }

    public function update(ProductsRequest $request, $id)
    {

        $product = $this->repoProducts->getDetail($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $data = [
            'ProductName' => $request->ProductName,
            'CategoryID' => $request->CategoryID,
            'Price' => $request->Price,
            'SalePrice' => $request->SalePrice,
            'ShortDescription' => $request->ShortDescription,
            'Description' => $request->Description,
        ];

        if ($request->hasFile('MainImageURL') && $request->file('MainImageURL')->isValid()) {
            $file = $request->file('MainImageURL');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('product_images'), $filename);
            $data['MainImageURL'] = 'product_images/' . $filename;
        }

        $this->repoProducts->updateProduct($id, $data);

        if ($request->hasFile('ImagePath')) {
            $imagePaths = [];
            foreach ($request->file('ImagePath') as $image) {
                if ($image->isValid()) {
                    $filename = time() . rand(1, 1000) . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('product_images'), $filename);
                    $imagePaths[] = 'product_images/' . $filename;
                }
            }
            $imagePath = implode(',', $imagePaths);
            (new ProductImage())->updateProductImage($id, $imagePath);
            return response()->json([
                'success' => true,
                'message' => 'Operation completed successfully',
                'data' => [
                    'product' => $id,
                ]
            ], 201);
        }
    }

    public function delete(Request $request)
    {

        $ids = explode(',', $request->ids);

        foreach ($ids as $id) {
            $product = $this->repoProducts->getDetail($id);

            if (!$product) {
                return response()->json(['message' => "Product with ID $id not found"], 404);
            }


            $this->repoProducts->deleteProductAndRelatedData($id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Products deleted successfully',
        ], 200);
    }

    public function view($id)
    {
        $product = $this->repoProducts->getDetail($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $this->repoProducts->viewProduct($id);

        return response()->json([
            'success' => true,
            'message' => 'Product view count updated successfully',
        ], 200);
    }


    public function updateStatus(Request $request, $id)
    {
        $product = $this->repoProducts->getDetail($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $this->repoProducts->updateProductAndRelatedStatus($id, $request->input('Status'));

        return response()->json([
            'success' => true,
            'message' => 'Product status updated successfully',
        ], 200);
    }
}
