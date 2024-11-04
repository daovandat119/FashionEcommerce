<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CartItems extends Model
{
    use HasFactory;

    protected $table = 'cart_items';

    protected $primaryKey = 'CartItemID';

    public $timestamps = true;

    protected $fillable = [
        'CartID',
        'ProductID',
        'Quantity',
        'Price',
    ];

    public function getCartItemByUserId($userId)
    {
        $query = DB::table('cart_items as ci')
            ->join('carts as c', 'ci.CartID', '=', 'c.CartID')
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

    public function getCartItem($cartID)
    {
        $getCartItemByCartID = DB::table($this->table)
            ->where('CartID', $cartID)
            ->get();

        return $getCartItemByCartID;
    }

    public function updateCartItem($cartItemID, $data)
    {
        $updateCartItem = DB::table($this->table)
            ->where('CartItemID', $cartItemID)
            ->update(['Quantity' => $data['quantity']]);

        return $updateCartItem;
    }


    public function createCartItem($data)
    {
        $createCartItem = DB::table($this->table)->insert([
            'CartID' => $data['CartID'],
            'ProductID' => $data['ProductID'],
            'VariantID' => $data['VariantID'],
            'Quantity' => $data['Quantity'],
        ]);
        return $createCartItem;
    }

    public function getCartItemByCartID($cartID, $productID, $variantID)
    {
        $getCartItemByCartID = DB::table($this->table)
            ->where('CartID', $cartID)
            ->where('ProductID', $productID)
            ->where('VariantID', $variantID)
            ->first();

        return $getCartItemByCartID;
    }

    public function deleteCartItem($cartItemID)
    {
        $deleteCartItem = DB::table($this->table)
            ->where('CartItemID', $cartItemID)
            ->delete();

        return $deleteCartItem;
    }

    public function deleteCartItemByCartID($cartID)
    {
        $deleteCartItemByCartID = DB::table($this->table)
            ->where('CartID', $cartID)
            ->delete();
    }


}
