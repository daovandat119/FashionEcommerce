<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Models\ProductImage;
use App\Models\ProductVariant;
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

    public function listProducts($search, $offset, $limit, $category_id = null, $status = null, $color_id = null, $size_id = null)
    {
        $query = Products::select("{$this->table}.*",
                'categories.CategoryName as category_name',
                DB::raw('GROUP_CONCAT(product_images.ImagePath) as image_paths'),
                DB::raw('IF(products.Price > 0, CEIL(((products.Price - products.SalePrice) / products.Price) * 100), 0) as discount_percentage'),
                DB::raw('COALESCE(AVG(reviews.RatingLevelID), 5) as average_rating'),
                DB::raw('COALESCE(SUM(order_items.Quantity), 0) as total_sold')
            )
            ->join('categories', 'categories.CategoryID', '=', "{$this->table}.CategoryID")
            ->leftJoin('product_images', 'products.ProductID', '=', 'product_images.ProductID')
            ->leftJoin('reviews', 'products.ProductID', '=', 'reviews.ProductID')
            ->leftJoin('order_items', 'products.ProductID', '=', 'order_items.ProductID')
            ->where('products.ProductName', 'like', "%{$search}%")
            ->groupBy("{$this->table}.ProductID", 'categories.CategoryName')
            ->skip($offset)
            ->take($limit);

        if ($category_id) {
            $query->where("categories.CategoryID", "=", $category_id);
        }

        if ($color_id) {
            $query->where("product_variants.ColorID", $color_id);
        }

        if ($size_id) {
            $query->where("product_variants.SizeID", $size_id);
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
            DB::raw('COALESCE(SUM(order_items.Quantity), 0) as total_sold')
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

    public function deleteProductAndRelatedData($productId)
    {
        DB::transaction(function () use ($productId) {

            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            $productIds = DB::table('products')->where('ProductID', $productId)->pluck('ProductID');

            DB::table('reviews')->whereIn('ProductID', $productIds)->delete();
            DB::table('wishlist')->whereIn('ProductID', $productIds)->delete();
            DB::table('cart_items')->whereIn('ProductID', $productIds)->delete();

            $variantIds = DB::table('product_variants')->whereIn('ProductID', $productIds)->pluck('VariantID');
            DB::table('order_items')->whereIn('VariantID', $variantIds)->delete();

            DB::table('product_variants')->whereIn('ProductID', $productIds)->delete();

            DB::table('product_images')->whereIn('ProductID', $productIds)->delete();

            DB::table('products')->where('ProductID', $productId)->delete();

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        });
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

            ProductVariant::where('ProductID', $id)
                ->update(['Status' => $status]);

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
