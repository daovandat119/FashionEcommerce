<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $primaryKey = 'CartID';

    public $timestamps = true;

    public function getCartByUserID($id)
    {
        $cart = DB::table($this->table)->where('UserID', $id)->first();
        return $cart;
    }

    public function createCart($userId)
    {
        $cartId = DB::table($this->table)->insertGetId(['UserID' => $userId]);

        return $cartId;
    }


}
