<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductImage extends Model
{
    use HasFactory;


    protected $table = 'product_images';
    public $timestamps = false;

    public function createProductImage($productId, $imagePath)
    {
        return DB::table($this->table)->insert([
            'ProductID' => $productId,
            'ImagePath' => $imagePath,
        ]);
    }

    public function updateProductImage($id, $imagePath)
    {
        return DB::table($this->table)->where('ProductID', $id)->update([
            'ProductID' => $id,
            'ImagePath' => $imagePath,
        ]);
    }
}
