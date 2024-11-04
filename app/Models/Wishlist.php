<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $table = 'wishlist';

    protected $primaryKey = 'WishlistID';

    public $timestamps = true;

    protected $fillable = ['UserID', 'ProductID'];

    public function addToWishlist($data){
        return Wishlist::create([
                'UserID' => $data['UserID'],
                'ProductID' => $data['ProductID'],
            ]);
    }

    public function getWishlist($data){
        return Wishlist::where('UserID', $data['UserID'])
            ->where('ProductID', $data['ProductID'])
            ->first();
    }

    public function getWishlistByID($data){
        return Wishlist::where('WishlistID', $data['WishlistID'])
            ->where('UserID', $data['UserID'])
            ->first();
    }

    public function deleteWishlist($data){
        return Wishlist::where('UserID', $data['UserID'])
            ->where('WishlistID', $data['WishlistID'])
            ->delete();
    }

    public function getWishlistByUserID($userID){
        return Wishlist::join('products', 'wishlist.ProductID', '=', 'products.ProductID')
            ->where('UserID', $userID)
            ->select('wishlist.WishlistID', 'products.ProductName', 'products.MainImageURL', 'products.Price')
            ->get();
    }

}
