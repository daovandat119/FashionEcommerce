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
        'created_at',
        'updated_at',
    ];

    public function getCartItemByUserId($userId)
    {
        $query = CartItems::join('carts as c', 'ci.CartID', '=', 'c.CartID')
            ->join('products as p', 'ci.ProductID', '=', 'p.ProductID')
            ->join('product_variants as pv', 'ci.VariantID', '=', 'pv.VariantID')
            ->join('colors as col', 'pv.ColorID', '=', 'col.ColorID')
            ->join('sizes as s', 'pv.SizeID', '=', 's.SizeID')
            ->where('c.UserID', $userId)
            ->select(
                'ci.CartItemID',
                'p.MainImageURL',
                'p.ProductName as product_name',
                'col.ColorName as color',
                's.SizeName as size',
                'ci.Quantity',
                'pv.Price',
                DB::raw('(ci.Quantity * pv.Price) as total_price')
            );

        $cartItems = $query->get();

        return $cartItems;
    }

    public function getCartItem($userId)
    {
        $cart = (new Cart())->getCartByUserID($userId);

        $getCartItemByCartID = CartItems::where('CartID', $cart->CartID)->get();

        return $getCartItemByCartID;
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

    public function deleteCartItemByCartID($cartID)
    {
        $deleteCartItemByCartID = CartItems::where('CartID', $cartID)->delete();
    }


}
