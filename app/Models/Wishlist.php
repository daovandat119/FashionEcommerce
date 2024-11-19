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
        $query = Products::select("{$this->table}.WishlistID",

                'categories.CategoryName as CategoryName',
                'products.ProductName as ProductName',
                'products.MainImageURL as MainImageURL',
                'products.Price as Price',
                'products.SalePrice as SalePrice',
                'products.created_at as created_at',
                DB::raw('IF(products.Price > 0, CEIL(((products.Price - products.SalePrice) / products.Price) * 100), 0) as discount_percentage'),
                DB::raw('COALESCE(AVG(reviews.RatingLevelID), 5) as average_rating')
            )
            ->join('categories', 'categories.CategoryID', '=', 'products.CategoryID')
            ->leftJoin('product_images', 'products.ProductID', '=', 'product_images.ProductID')
            ->leftJoin('reviews', 'products.ProductID', '=', 'reviews.ProductID')
            ->leftJoin('product_variants', 'products.ProductID', '=', 'product_variants.ProductID')
            ->leftJoin('wishlist', 'products.ProductID', '=', 'wishlist.ProductID')
            ->where('wishlist.UserID', $userID)
            ->groupBy("{$this->table}.WishlistID", 'products.ProductName', 'products.MainImageURL', 'products.Price', 'products.SalePrice', 'categories.CategoryName');

        return $query->get();
    }

}
