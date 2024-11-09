<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
class Categories extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $primaryKey = 'CategoryID';

    public $timestamps = true;

    protected $fillable = [
        'CategoryName',
        'Status',
    ];

    public function countCategories()
    {
        return Categories::count();
    }

    public function listCategories($search, $offset, $limit)
    {

        return Categories::where('CategoryName', 'like', "%{$search}%")
        ->skip($offset)
        ->take($limit)
        ->get();
    }

    public function addCategory($data)
    {
        return Categories::create($data);
    }

    public function getDetail($id)
    {
        return Categories::where('CategoryID', $id)->first();
    }

    public function updateCategory($id, $dataUpdate)
    {
        return Categories::where('CategoryID', $id)->update($dataUpdate);
    }

    public function getCategoryByName($categoryName)
    {
        return Categories::where('CategoryName', $categoryName)->first();
    }

    public function deleteCategory($id)
    {
        return Categories::where('CategoryID', $id)->delete();
    }

    public function deleteCategoryAndRelatedData($categoryId)
    {
        DB::beginTransaction();

        try {
            ProductVariant::where('ProductID', 'products.ProductID')
                ->where('products.CategoryID', $categoryId)
                ->delete();


            ProductImage::where('ProductID', 'products.ProductID')
                ->where('products.CategoryID', $categoryId)
                ->delete();

            Product::where('CategoryID', $categoryId)
                ->delete();

            Categories::where('CategoryID', $categoryId)
                ->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting category and related data: ' . $e->getMessage());

            return false;
        }
    }

    public function updateCategoryAndRelatedStatus($categoryId, $status)
    {
        DB::beginTransaction();

        try {
            // Update status of product variants
            ProductVariant::where('ProductID', 'products.ProductID')
                ->where('products.CategoryID', $categoryId)
                ->update(['product_variants.Status' => $status]);

            // Update status of products
            Product::where('CategoryID', $categoryId)
                ->update(['Status' => $status]);

            // Update status of the category
            Categories::where('CategoryID', $categoryId)
                ->update(['Status' => $status]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating category and related data: ' . $e->getMessage());

            return false;
        }
    }

}






