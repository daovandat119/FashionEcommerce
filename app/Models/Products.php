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

    public function listProducts($search, $offset, $limit, $category_id = null, $status = null, $color_id = null, $size_id = null, $sortBy = null)
    {
        $query = Products::select("{$this->table}.*",
                'categories.CategoryName as category_name',
                DB::raw('CEIL(COALESCE(((products.Price - products.SalePrice) / products.Price) * 100, 0)) as discount_percentage'),
                DB::raw('COALESCE(AVG(r.RatingLevelID), 5) as average_rating'),
                DB::raw('COALESCE(SUM(order_items.Quantity), 0) as total_sold')
            )
            ->join('categories', 'categories.CategoryID', '=', "{$this->table}.CategoryID")
            ->leftJoin('order_items', 'products.ProductID', '=', 'order_items.ProductID')
            ->leftJoin('product_variants', 'order_items.VariantID', '=', 'product_variants.VariantID')
            ->leftJoin(DB::raw('(SELECT ProductID, AVG(RatingLevelID) AS RatingLevelID FROM reviews GROUP BY ProductID) as r'), 'products.ProductID', '=', 'r.ProductID')
            ->where('products.ProductName', 'like', "%{$search}%")
            ->groupBy("{$this->table}.ProductID", 'categories.CategoryName')
            ->skip($offset)
            ->take($limit);

        if ($sortBy) {
            $query->where('created_at', '>=', now()->subMonths(2));

            if ($sortBy === 'average_rating') {
                $query
                ->orderBy('average_rating', 'desc')->take(10);
            } elseif ($sortBy === 'created_at') {
                $query->orderBy('created_at', 'desc')->take(10);
            } elseif ($sortBy === 'total_sold'){
                $query->orderBy('total_sold', 'desc')->take(10);
            } elseif ($sortBy === 'view') {
                $query->orderBy('Views', 'desc')->take(10);
            }
        }

        if ($category_id) {
            $query->where("categories.CategoryID", "=", $category_id);
        }

        if ($color_id && $size_id) {
            $query->whereExists(function($query) use ($color_id, $size_id) {
                $query->select(DB::raw(1))
                      ->from('product_variants')
                      ->whereRaw('product_variants.ProductID = products.ProductID')
                      ->where('product_variants.ColorID', $color_id)
                      ->where('product_variants.SizeID', $size_id);
            });
        } elseif ($color_id) {
            $query->whereExists(function($query) use ($color_id) {
                $query->select(DB::raw(1))
                      ->from('product_variants')
                      ->whereRaw('product_variants.ProductID = products.ProductID')
                      ->where('product_variants.ColorID', $color_id);
            });
        } elseif ($size_id) {
            $query->whereExists(function($query) use ($size_id) {
                $query->select(DB::raw(1))
                      ->from('product_variants')
                      ->whereRaw('product_variants.ProductID = products.ProductID')
                      ->where('product_variants.SizeID', $size_id);
            });
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
            'product_images.ImagePath as image_paths',
            DB::raw('CEIL(COALESCE((products.Price - products.SalePrice) / products.Price * 100, 0)) as discount_percentage'),
            DB::raw('COALESCE(AVG(r.RatingLevelID), 5) as average_rating'),
            DB::raw('COALESCE(SUM(order_items.Quantity), 0) as total_sold')
        )
            ->join('categories', 'categories.CategoryID', '=', "{$this->table}.CategoryID")
            ->leftJoin('order_items', 'products.ProductID', '=', 'order_items.ProductID')
            ->leftJoin('product_variants', 'order_items.VariantID', '=', 'product_variants.VariantID')
            ->leftJoin(DB::raw('(SELECT ProductID, AVG(RatingLevelID) AS RatingLevelID FROM reviews GROUP BY ProductID) as r'), 'products.ProductID', '=', 'r.ProductID')
            ->leftJoin('product_images', 'products.ProductID', '=', 'product_images.ProductID')
            ->where('products.ProductID', $id)
            ->groupBy("{$this->table}.ProductID", 'categories.CategoryName', 'product_images.ImagePath')
            ->orderBy("{$this->table}.ProductID")
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

    public function countProducts($status, $category_id, $color_id, $size_id )
    {
        $query = Products::join('categories', 'categories.CategoryID', '=', "{$this->table}.CategoryID")
            ->leftJoin('order_items', 'products.ProductID', '=', 'order_items.ProductID')
            ->leftJoin('product_variants', 'order_items.VariantID', '=', 'product_variants.VariantID')
            ->leftJoin(DB::raw('(SELECT ProductID, AVG(RatingLevelID) AS RatingLevelID FROM reviews GROUP BY ProductID) as r'), 'products.ProductID', '=', 'r.ProductID');

        if ($category_id) {
            $query->where("categories.CategoryID", "=", $category_id);
        }

        if ($color_id && $size_id) {
            $query->whereExists(function($query) use ($color_id, $size_id) {
                $query->select(DB::raw(1))
                      ->from('product_variants')
                      ->whereRaw('product_variants.ProductID = products.ProductID')
                      ->where('product_variants.ColorID', $color_id)
                      ->where('product_variants.SizeID', $size_id);
            });
        } elseif ($color_id) {
            $query->whereExists(function($query) use ($color_id) {
                $query->select(DB::raw(1))
                      ->from('product_variants')
                      ->whereRaw('product_variants.ProductID = products.ProductID')
                      ->where('product_variants.ColorID', $color_id);
            });
        } elseif ($size_id) {
            $query->whereExists(function($query) use ($size_id) {
                $query->select(DB::raw(1))
                      ->from('product_variants')
                      ->whereRaw('product_variants.ProductID = products.ProductID')
                      ->where('product_variants.SizeID', $size_id);
            });
        }

        if($status){
            $query->where("{$this->table}.Status", $status);
        }

        return $query->count();
    }
}
