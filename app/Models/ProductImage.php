<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_images';

    protected $primaryKey = 'ProductImageID';

    public $timestamps = false;

    protected $fillable = [
        'ProductID',
        'ImagePath',
    ];

    public function createProductImage($productId, $imagePath)
    {
        return ProductImage::create([
            'ProductID' => $productId,
            'ImagePath' => $imagePath,
        ]);
    }

    public function updateProductImage($id, $imagePath)
    {
        return ProductImage::where('ProductID', $id)->update([
            'ProductID' => $id,
            'ImagePath' => $imagePath,
        ]);
    }
}
