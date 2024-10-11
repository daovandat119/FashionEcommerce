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
    protected $fillable = ['UserID', 'ProductID'];

    public function addToWishlist($data){
        return DB::table($this->table)
            ->insert([
                'UserID' => $data['UserID'],
                'ProductID' => $data['ProductID'],
            ]);
    }

    public function getWishlist($data){
        return DB::table($this->table)
            ->where('UserID', $data['UserID'])
            ->where('ProductID', $data['ProductID'])
            ->first();
    }

    public function getWishlistByID($data){
        return DB::table($this->table)
            ->where('WishlistID', $data['WishlistID'])
            ->where('UserID', $data['UserID'])
            ->first();
    }

    public function deleteWishlist($data){
        return DB::table($this->table)
            ->where('UserID', $data['UserID'])
            ->where('WishlistID', $data['WishlistID'])
            ->delete();
    }

    public function getWishlistByUserID($userID){
        return DB::table($this->table)
            ->join('products', $this->table.'.ProductID', '=', 'products.ProductID')
            ->where('UserID', $userID)
            ->select($this->table.'.WishlistID', 'products.ProductName', 'products.MainImageURL', 'products.Price')
            ->get();
    }

}
