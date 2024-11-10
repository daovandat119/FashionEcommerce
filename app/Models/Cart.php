<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $primaryKey = 'CartID';

    public $timestamps = true;

    protected $fillable = [
        'UserID',
        'created_at',
        'updated_at',
    ];

    public function getCartByUserID($id)
    {
        $cart = Cart::where('UserID', $id)->first();
        return $cart;
    }

    public function createCart($userId)
    {
        $cartId = Cart::create(['UserID' => $userId]);

        return $cartId;
    }


}
