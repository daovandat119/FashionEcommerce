<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItems;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Http\Requests\CartItemRequest;
//
class CartItemsController extends Controller
{
    //
    protected $repoCartItems;

    public function __construct()
    {
        $this->repoCartItems = new CartItems();
    }

    public function index()
    {
        $userId = auth()->id();

        $cartItems = $this->repoCartItems->getCartItemByUserId($userId);

        return response()->json(['message' => 'Success', 'data' => $cartItems], 200);
    }
//
    public function store(CartItemRequest $request)
    {

        $userId = auth()->id();

        $cart = (new Cart())->getCartByUserID($userId);
        if (!$cart) {
            (new Cart())->createCart($userId);
            $cart = (new Cart())->getCartByUserID($userId);
        }

        $productVariant = (new ProductVariant())->getVariantByID($request->productID, $request->sizeID, $request->colorID);
        if (!$productVariant) {
            return response()->json(['message' => 'Không tìm thấy biến thể sản phẩm'], 404);
        }

        $cartItem = $this->repoCartItems->getCartItemByCartID($cart->CartID, $request->productID, $productVariant->VariantID);

        if ($request->quantity <= 0) {
            return response()->json(['message' => 'Số lượng phải lớn hơn 0'], 400);
        }

        if (!$cartItem) {
            if ($request->quantity > $productVariant->Quantity) {
                return response()->json(['message' => 'Số lượng là không đủ'], 400);
            }

            $data = [
                'CartID' => $cart->CartID,
                'ProductID' => $request->productID,
                'VariantID' => $productVariant->VariantID,
                'Quantity' => $request->quantity,
            ];

            $newCartItems = $this->repoCartItems->createCartItem($data);

            return response()->json(['message' => 'Success', 'data' => $newCartItems], 201);
        } else {
            $newQuantity = $cartItem->Quantity + $request->quantity;
            if ($newQuantity > $productVariant->Quantity) {
                return response()->json(['message' => 'Số lượng là không đủ'], 400);
            }

            $data = [
                'quantity' => $newQuantity,
            ];

            $updateCartItems = $this->repoCartItems->updateCartItem($cartItem->CartItemID, $data);

            if ($updateCartItems) {
                return response()->json(['message' => 'Success', 'data' => $updateCartItems], 200);
            } else {
                return response()->json(['message' => 'Không thể cập nhật mặt hàng trong giỏ hàng'], 500);
            }
        }
    }

    public function update(CartItemRequest $request, $id)
    {
        $userId = auth()->id();

        $cart = (new Cart())->getCartByUserID($userId);

        if (!$cart) {
            return response()->json(['message' => 'Không tìm thấy giỏ hàng'], 404);
        }

        $productVariant = (new ProductVariant())->getVariantByID($request->productID, $request->sizeID, $request->colorID);

        if (!$productVariant) {
            return response()->json(['message' => 'Không tìm thấy biến thể sản phẩm'], 404);
        }

        if ($request->quantity > $productVariant->Quantity) {
            return response()->json(['message' => 'Số lượng là không đủ'], 400);
        }

        $data = [
            'quantity' => $request->quantity,
        ];

        $updateCartItems = $this->repoCartItems->updateCartItem($id, $data);

        if ($updateCartItems) {
            return response()->json(['message' => 'Success', 'data' => $updateCartItems], 200);
        } else {
            return response()->json(['message' => 'Không thể cập nhật mặt hàng trong giỏ hàng'], 500);
        }
    }

    public function destroy(Request $request)
    {
        $userId = auth()->id();

        $ids = explode(',', $request->input('ids'));

        $deletedCartItemsCount = $this->repoCartItems->deleteCartItem($ids, $userId);

        return response()->json(['message' => 'Success', 'deleted_count' => $deletedCartItemsCount], 200);
    }

    public function updateStatusCartItem(Request $request) {
        $userId = auth()->id();

        $ids = explode(',', $request->input('ids'));

        $updateStatus = $this->repoCartItems->updateCartItemStatus($ids);

        if ($updateStatus) {
            return response()->json(['message' => 'Status updated successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to update status'], 500);
        }
    }



}
