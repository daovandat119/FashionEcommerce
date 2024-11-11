<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\ProductImage;

class Categories extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $primaryKey = 'CategoryID';

    public $timestamps = true;

    protected $fillable = [
        'CategoryName',
        'Status',
        'created_at',
        'updated_at',
    ];

    public function countCategories()
    {
        return Categories::count();
    }

    public function listCategories($search = null, $offset = null, $limit = null, $status = null)
    {

        $categories = Categories::query();

        if($search){
            $categories = $categories->where('CategoryName', 'like', "%{$search}%");
        }

        if($offset){
            $categories = $categories->skip($offset);
        }

        if($limit){
            $categories = $categories->take($limit);
        }

        if($status){
            $categories = $categories->where('Status', $status);
        }

        return $categories->get();
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

    public function deleteCategoryAndRelatedData($categoryId)
    {
        DB::beginTransaction();

        try {
            DB::table('product_variants')
                ->where('ProductID', function($query) use ($categoryId) {
                    $query->select('ProductID')
                          ->from('products')
                          ->where('CategoryID', $categoryId);
                })
                ->delete();

            DB::table('product_images')
                ->where('ProductID', function($query) use ($categoryId) {
                    $query->select('ProductID')
                          ->from('products')
                          ->where('CategoryID', $categoryId);
                })
                ->delete();

            DB::table('products')
                ->where('CategoryID', $categoryId)
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

            DB::table('reviews')
                ->where('ProductID', function($query) use ($categoryId) {
                    $query->select('ProductID')
                          ->from('products')
                          ->where('CategoryID', $categoryId);
                })
                ->delete();

            DB::table('wishlists')
                ->where('ProductID', function($query) use ($categoryId) {
                    $query->select('ProductID')
                          ->from('products')
                          ->where('CategoryID', $categoryId);
                })
                ->delete();

            DB::table('order_items')
                ->where('ProductID', function($query) use ($categoryId) {
                    $query->select('ProductID')
                          ->from('products')
                          ->where('CategoryID', $categoryId);
                })
                ->delete();

            DB::table('cart_items')
                ->where('ProductID', function($query) use ($categoryId) {
                    $query->select('ProductID')
                          ->from('products')
                          ->where('CategoryID', $categoryId);
                })
                ->delete();

            DB::table('product_variants')
                ->where('ProductID', function($query) use ($categoryId) {
                    $query->select('ProductID')
                          ->from('products')
                          ->where('CategoryID', $categoryId);
                })
                ->update(['status' => $status]);

            DB::table('products')
                ->where('CategoryID', $categoryId)
                ->update(['status' => $status]);

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






