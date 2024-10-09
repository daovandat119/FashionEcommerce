<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
class Categories extends Model
{
    use HasFactory;

    protected $table = 'categories';
    public $timestamps = false;

    public function listCategories()
    {
        return DB::table($this->table)->get();
    }

    public function addCategory($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function getDetail($id)
    {
        return DB::table($this->table)->where('CategoryID', $id)->first();
    }

    public function updateCategory($id, $dataUpdate)
    {
        return DB::table($this->table)
            ->where('CategoryID', $id)
            ->update($dataUpdate);
    }

    public function deleteCategory($id)
    {
        return DB::table($this->table)->where('CategoryID', $id)->delete();
    }

    public function deleteCategoryAndRelatedData($categoryId)
    {
        DB::beginTransaction();

        try {
            DB::table('product_variants')
                ->join('products', 'product_variants.ProductID', '=', 'products.ProductID')
                ->where('products.CategoryID', $categoryId)
                ->delete();


            DB::table('product_images')
                ->join('products', 'product_images.ProductID', '=', 'products.ProductID')
                ->where('products.CategoryID', $categoryId)
                ->delete();

            DB::table('products')
                ->where('CategoryID', $categoryId)
                ->delete();

            DB::table($this->table)
                ->where('CategoryID', $categoryId)
                ->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting category and related data: ' . $e->getMessage());

            return false;
        }
    }
}






