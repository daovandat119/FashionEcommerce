<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $primaryKey = 'ProductID';

    public $timestamps = true;

    protected $fillable = [
        'ProductName',
        'CategoryID',
        'Price',
        'SalePrice',
        'Views',
        'MainImageURL',
        'ShortDescription',
        'Description',
        'Status',
        'created_at',
        'updated_at',
    ];

    public function listProducts($search, $offset, $limit, $category_id = null, $status = null)
    {
        $query = Products::select("{$this->table}.*",
                'categories.CategoryName as category_name',
                DB::raw('GROUP_CONCAT(product_images.ImagePath) as image_paths'),
                DB::raw('IF(Price > 0, CEIL(((Price - SalePrice) / Price) * 100), 0) as discount_percentage'),
                DB::raw('COALESCE(AVG(reviews.RatingLevelID), 5) as average_rating')
            )
            ->join('categories', 'categories.CategoryID', '=', "{$this->table}.CategoryID")
            ->leftJoin('product_images', 'products.ProductID', '=', 'product_images.ProductID')
            ->leftJoin('reviews', 'products.ProductID', '=', 'reviews.ProductID')
            ->where('products.ProductName', 'like', "%{$search}%")
            ->groupBy("{$this->table}.ProductID", 'categories.CategoryName')
            ->skip($offset)
            ->take($limit);

        if ($category_id) {
            $query->where("categories.CategoryID", "=", $category_id);
        }

        if($status){
            $query->where("{$this->table}.Status", $status);
        }

        return $query->get();
    }


    public function addProduct($data)
    {
        $product = Products::create([
            'ProductName' => $data['ProductName'],
            'CategoryID' => $data['CategoryID'],
            'Price' => $data['Price'],
            'SalePrice' => $data['SalePrice'],
            'Views' => 0,
            'MainImageURL' => $data['MainImageURL'],
            'ShortDescription' => $data['ShortDescription'],
            'Description' => $data['Description'],
            'Status' => 'ACTIVE',
        ]);

        return $product->ProductID;
    }

    public function getDetail($id)
    {
        return Products::select("{$this->table}.*",
            'categories.CategoryName as category_name',
            DB::raw('GROUP_CONCAT(product_images.ImagePath) as image_paths'),
            DB::raw('IF(Price > 0, CEIL(((Price - SalePrice) / Price) * 100), 0) as discount_percentage'),
            DB::raw('COALESCE(AVG(reviews.RatingLevelID), 5) as average_rating'),
            DB::raw('COALESCE(SUM(DISTINCT order_items.Quantity), 0) as total_sold')
        )
            ->join('categories', 'categories.CategoryID', '=', "{$this->table}.CategoryID")
            ->leftJoin('product_images', 'products.ProductID', '=', 'product_images.ProductID')
            ->leftJoin('reviews', 'products.ProductID', '=', 'reviews.ProductID')
            ->leftJoin('order_items', 'products.ProductID', '=', 'order_items.ProductID')
            ->where('products.ProductID', $id)
            ->groupBy("{$this->table}.ProductID", 'categories.CategoryName')
            ->first();
    }

    public function updateProduct($id, $data)
    {
        return Products::where('ProductID', $id)->update([
            'ProductName' => $data['ProductName'],
            'CategoryID' => $data['CategoryID'],
            'Price' => $data['Price'],
            'SalePrice' => $data['SalePrice'],
            'MainImageURL' => $data['MainImageURL'],
            'ShortDescription' => $data['ShortDescription'],
            'Description' => $data['Description'],
        ]);
    }

    public function deleteProductAndRelatedData($id)
    {
        // Start a database transaction
        DB::beginTransaction();

        try {

            ProductVariants::join('products', 'product_variants.ProductID', '=', 'products.ProductID')
                ->where('products.ProductID', $id)
                ->delete();

            ProductImage::join('products', 'product_images.ProductID', '=', 'products.ProductID')
                ->where('products.ProductID', $id)
                ->delete();

            Products::where('ProductID', $id)->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting product and related data: ' . $e->getMessage(), [
                'ProductID' => $id,
                'error' => $e
            ]);

            return false;
        }
    }


    public function viewProduct($id)
    {
        return Products::where('ProductID', $id)->update([
            'Views' => DB::raw('Views + 1')
        ]);
    }

    public function updateProductAndRelatedStatus($id, $status)
    {
        DB::beginTransaction();

        try {
            Products::where('ProductID', $id)->update(['Status' => $status]);

            ProductVariants::join('products', 'product_variants.ProductID', '=', 'products.ProductID')
                ->where('products.ProductID', $id)
                ->update(['product_variants.Status' => $status]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating product and related status: ' . $e->getMessage());
            return false;
        }
    }

    public function countProducts($status)
    {
        $query = Products::query();

        if($status){
            $query->where("{$this->table}.Status", $status);
        }

        return $query->count();
    }
}
