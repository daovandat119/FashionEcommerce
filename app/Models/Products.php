<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProductImage;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'ProductID';

    public function listProducts($search, $offset, $limit, $category_id = null)
    {
        $query = DB::table($this->table)
            ->select("{$this->table}.*", 'categories.CategoryName as category_name', 'product_images.ImagePath as image_path')
            ->join('categories', 'categories.CategoryID', '=', "{$this->table}.CategoryID")
            ->leftJoin('product_images', 'products.ProductID', '=', 'product_images.ProductID')
            ->where('products.ProductName', 'like', "%{$search}%")
            ->skip($offset)
            ->take($limit);

        if ($category_id) {
            $query->where("categories.CategoryID", "=", $category_id);
        }

        return $query->get();
    }


    public function addProduct($data)
    {
        return DB::table($this->table)->insertGetId([
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
    }

    public function getDetail($id)
    {
        return DB::table($this->table)
            ->select("{$this->table}.*", 'categories.CategoryName as category_name', 'product_images.ImagePath as image_path')
            ->join('categories', 'categories.CategoryID', '=', "{$this->table}.CategoryID")
            ->leftJoin('product_images', 'products.ProductID', '=', 'product_images.ProductID')
            ->where('products.ProductID', $id)
            ->first();
    }

    public function updateProduct($id, $data)
    {
        return DB::table($this->table)->where('ProductID', $id)->update([
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

            DB::table('product_variants')
                ->join('products', 'product_variants.ProductID', '=', 'products.ProductID')
                ->where('products.ProductID', $id)
                ->delete();

            DB::table('product_images')
                ->join('products', 'product_images.ProductID', '=', 'products.ProductID')
                ->where('products.ProductID', $id)
                ->delete();

            DB::table('products')
                ->where('ProductID', $id)
                ->delete();

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
        return DB::table($this->table)->where('ProductID', $id)->update([
            'Views' => DB::raw('Views + 1')
        ]);
    }

    public function updateProductAndRelatedStatus($id, $status)
    {
        DB::beginTransaction();

        try {
            DB::table($this->table)
                ->where('ProductID', $id)
                ->update(['Status' => $status]);

            DB::table('product_variants')
                ->join('products', 'product_variants.ProductID', '=', 'products.ProductID')
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

    public function countProducts()
    {
        return DB::table($this->table)->count();
    }
}
