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
                'wishlist.*',
                'products.*',
                'categories.*',
                DB::raw('COALESCE(AVG(reviews.RatingLevelID), 5) as average_rating'),
                DB::raw('SUM(order_items.Quantity) as total_sold')
            )
            ->leftJoin('products', 'wishlist.ProductID', '=', 'products.ProductID')
            ->leftJoin('reviews', 'products.ProductID', '=', 'reviews.ProductID')
            ->leftJoin('categories', 'categories.CategoryID', '=', 'products.CategoryID')
            ->leftJoin('order_items', 'products.ProductID', '=', 'order_items.ProductID')
            ->leftJoin('orders', 'order_items.OrderID', '=', 'orders.OrderID')
            ->leftJoin('product_variants', 'order_items.VariantID', '=', 'product_variants.VariantID')
            ->where('wishlist.UserID', $userID)
            ->groupBy('wishlist.WishlistID', 'products.ProductID', 'products.ProductName', 'products.MainImageURL', 'products.Price', 'products.SalePrice', 'categories.CategoryName')
            ->get();
    }


}
