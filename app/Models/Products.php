<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\ProductImage;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'ProductID';

    public function listProducts()
    {
        return DB::table($this->table)
            ->select("{$this->table}.*", 'categories.CategoryName as category_name', 'product_images.ImagePath as image_path')
            ->join('categories', 'categories.CategoryID', '=', "{$this->table}.CategoryID")
            ->leftJoin('product_images', 'products.ProductID', '=', 'product_images.ProductID')
            ->get();
    }

    public function addProduct($data)
    {
        return DB::table($this->table)->insertGetId([
            'ProductName'=> $data['ProductName'],
            'CategoryID'=> $data['CategoryID'],
            'Price'=> $data['Price'],
            'SalePrice'=> $data['SalePrice'],
            'Views'=> 0,
            'MainImageURL'=> $data['MainImageURL'],
            'ShortDescription'=> $data['ShortDescription'],
            'Description'=> $data['Description'],
            'Status'=> 'ACTIVE',
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

    public function updateProduct($id,$data)
    {
        return DB::table($this->table)->where('ProductID', $id)->update([
            'ProductName'=> $data['ProductName'],
            'CategoryID'=> $data['CategoryID'],
            'Price'=> $data['Price'],
            'SalePrice'=> $data['SalePrice'],
            'MainImageURL'=> $data['MainImageURL'],
            'ShortDescription'=> $data['ShortDescription'],
            'Description'=> $data['Description'],
        ]);
    }



    public function deleteCategoryAndRelatedData($categoryId)
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Delete product variants related to the products in the specified category
            DB::table('product_variants')
                ->join('products', 'product_variants.ProductID', '=', 'products.ProductID')
                ->where('products.CategoryID', $categoryId)
                ->delete();

            // Delete product images related to the products in the specified category
            DB::table('product_images')
                ->join('products', 'product_images.ProductID', '=', 'products.ProductID')
                ->where('products.CategoryID', $categoryId)
                ->delete();

            // Delete products related to the category
            DB::table('products')
                ->where('CategoryID', $categoryId)
                ->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Log the error or handle it as needed
            \Log::error('Error deleting category and related data: ' . $e->getMessage(), [
                'categoryId' => $categoryId,
                'error' => $e
            ]);

            return false;
        }
    }

}
