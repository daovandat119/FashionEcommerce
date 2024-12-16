<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;

class CartItems extends Model
{
    use HasFactory;

    protected $table = 'cart_items';

    protected $primaryKey = 'CartItemID';

    public $timestamps = true;

    protected $fillable = [
        'CartID',
        'ProductID',
        'VariantID',
        'Quantity',
        'Price',
        'Status',
        'created_at',
        'updated_at',
    ];

    public function getCartItemByUserId($userId)
    {
        $query = CartItems::join('carts as c', 'cart_items.CartID', '=', 'c.CartID')
            ->join('products as p', 'cart_items.ProductID', '=', 'p.ProductID')
            ->join('product_variants as pv', 'cart_items.VariantID', '=', 'pv.VariantID')
            ->join('colors as col', 'pv.ColorID', '=', 'col.ColorID')
            ->join('sizes as s', 'pv.SizeID', '=', 's.SizeID')
            ->where('c.UserID', $userId)
            ->select(
                'cart_items.*',
                's.SizeID',
                'col.ColorID',
                'p.ProductID',
                'col.ColorName as ColorName',
                's.SizeName as SizeName',
                'p.ProductName as ProductName',
                'p.MainImageURL as ImageUrl',
                'pv.Price as Price',
                'pv.Quantity as QuantityLimit'
            );

        $cartItems = $query->get();

        return $cartItems;
    }

    public function getCartItem($cartID, $Status = null)
    {
        $query = CartItems::where('CartID', $cartID);

        if ($Status !== null) {
            $query->where('Status', $Status);
        }

        $cartItems = $query->get();

        return $cartItems;
    }

    public function updateCartItem($cartItemID, $data)
    {

        $updateCartItem = CartItems::where('CartItemID', $cartItemID)->update(['Quantity' => $data['quantity']]);

        return $updateCartItem;
    }


    public function createCartItem($data)
    {
        $createCartItem = CartItems::create([
            'CartID' => $data['CartID'],
            'ProductID' => $data['ProductID'],
            'VariantID' => $data['VariantID'],
            'Quantity' => $data['Quantity'],
            'Status' => 'ACTIVE'
        ]);
        return $createCartItem;
    }

    public function getCartItemByCartID($cartID, $productID, $variantID)
    {
        $getCartItemByCartID = CartItems::where('CartID', $cartID)
            ->where('ProductID', $productID)
            ->where('VariantID', $variantID)
            ->first();

        return $getCartItemByCartID;
    }

    public function deleteCartItem($cartItemID, $userId)
    {
        return CartItems::whereIn('CartItemID', $cartItemID)
            ->where('CartID', $userId)
            ->delete();

    }

    public function deleteCartItemByCartID($cartID, $Status = null)
    {
        $deleteCartItemByCartID = CartItems::where('CartID', $cartID);
        if ($Status !== null) {
            $deleteCartItemByCartID->where('Status', $Status);
        }
        $deleteCartItemByCartID->delete();
    }

    public function countCartItemsByUserId($userId)
    {
        return CartItems::join('carts as c', 'cart_items.CartID', '=', 'c.CartID')
            ->where('cart_items.Status', 'ACTIVE')
            ->where('c.UserID', $userId)
            ->sum('cart_items.Quantity');
    }

    public function updateCartItemStatus($cartItemIDs)
    {
        $updateStatus = CartItems::whereIn('CartItemID', $cartItemIDs)
        ->update(['Status' => 'INACTIVE']);

        return $updateStatus;
    }

}
