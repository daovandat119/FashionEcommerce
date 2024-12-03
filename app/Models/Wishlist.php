<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Wishlist extends Model
{
    use HasFactory;

    protected $table = 'wishlist';

    protected $primaryKey = 'WishlistID';

    public $timestamps = true;

    protected $fillable = [
        'UserID',
        'ProductID',
        'created_at',
        'updated_at',
    ];

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

        return Wishlist::select(
                'wishlist.WishlistID',
                'wishlist.UserID',
                'products.ProductID',
                'products.ProductName',
                'products.Price',
                'products.SalePrice',
                'categories.CategoryName',
                'products.MainImageURL',
                'products.created_at',
                DB::raw('COALESCE(SUM(order_items.Quantity), 0) AS total_sold'),
                DB::raw('CEIL(COALESCE(AVG(((products.Price - products.SalePrice) / products.Price) * 100), 0)) AS discount_percentage'),
                DB::raw('COALESCE(r.RatingLevelID, 5) AS average_rating')
            )
            ->leftJoin('products', 'wishlist.ProductID', '=', 'products.ProductID')
            ->leftJoin('categories', 'categories.CategoryID', '=', 'products.CategoryID')
            ->leftJoin('order_items', 'products.ProductID', '=', 'order_items.ProductID')
            ->leftJoin(DB::raw('(SELECT ProductID, AVG(RatingLevelID) AS RatingLevelID FROM reviews GROUP BY ProductID) AS r'), 'products.ProductID', '=', 'r.ProductID')
            ->where('wishlist.UserID', $userID)
            ->groupBy('wishlist.WishlistID', 'wishlist.UserID', 'products.ProductID', 'products.ProductName', 'products.Price', 'products.SalePrice', 'categories.CategoryName', 'products.MainImageURL', 'products.created_at')
            ->orderBy('products.ProductID')
            ->get();
    }


}
