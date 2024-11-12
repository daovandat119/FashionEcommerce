<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Http\Requests\ProductVariantRequest;
use Illuminate\Http\Request;
use App\Models\Products;

class ProductVariantController extends Controller
{
    protected $repoProductVariant;

    public function __construct()
    {
        $this->repoProductVariant = new ProductVariant();
    }

    public function index(Request $request)
    {
        $variants = $this->repoProductVariant->getAll($request->ProductID);
        return response()->json(['message' => 'Success', 'data' => $variants], 200);
    }

    public function store(ProductVariantRequest $request)
    {
        $addedVariants = [];

        $existingVariants = [];
        $product = (new Products())->getDetail($request->ProductID);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        if ($request->Price > $product->Price) {
            return response()->json(['message' => 'Giá không thể lớn hơn giá sản phẩm'], 404);
        }

        $sizeIDs = explode(',', $request->SizeID);
        $colorIDs = explode(',', $request->ColorID);

        foreach ($sizeIDs as $sizeID) {
            foreach ($colorIDs as $colorID) {
                if (!$this->repoProductVariant->checkVariantExists($request->ProductID, $sizeID, $colorID)) {
                    $data = [
                        'ProductID' => $request->ProductID,
                        'SizeID' => $sizeID,
                        'ColorID' => $colorID,
                        'Quantity' => $request->Quantity,
                        'Price' => $request->Price,
                        'Status' => 'ACTIVE',
                    ];
                    $this->repoProductVariant->createVariant($data);
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

    public function show(Request $request)
    {
        $variant = $this->repoProductVariant->getVariantByID(
            $request->ProductID,
            $request->SizeID,
            $request->ColorID
        );

        if (!$variant) {
            return response()->json(['message' => 'Success', 'data' => [
                'Quantity' => 0,
            ]], 200);
        }

        return response()->json(['message' => 'Success', 'data' => [
            'Quantity' => $variant->Quantity,
            'Price' => $variant->Price,
        ]], 200);
    }

    public function showAdmin(Request $request)
    {
        $variant = $this->repoProductVariant->getVariantByIDAdmin($request->VariantID);

        if (!$variant) {
            return response()->json(['message' => 'Variant not found'], 404);
        }

        return response()->json(['message' => 'Success', 'data' => $variant], 200);
    }

    public function update(ProductVariantRequest $request)
    {
        $product = (new Products())->getDetail($request->ProductID);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $data = [
            'ProductID' => $request->ProductID,
            'SizeID' => $request->SizeID,
            'ColorID' => $request->ColorID,
            'Quantity' => $request->Quantity,
            'Price' => $request->Price,
        ];

        $variant = $this->repoProductVariant->updateVariant($data);

        return response()->json(['message' => 'Cập nhật thành công!', 'data' => $data], 200);
    }

    public function delete(Request $request)
    {
        $ids = explode(',', $request->ids);

        $this->repoProductVariant->deleteVariant($ids);

        return response()->json([
            'success' => true,
            'message' => 'Variants deleted successfully',
        ], 200);
    }


}
