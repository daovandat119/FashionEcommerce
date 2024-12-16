<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Wishlist;
use App\Models\Review;
use App\Models\OrderItem;
use App\Models\CartItem;

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

        if ($search) {
            $categories = $categories->where('CategoryName', 'like', "%{$search}%");
        }

        if ($offset) {
            $categories = $categories->skip($offset);
        }

        if ($limit) {
            $categories = $categories->take($limit);
        }

        if ($status) {
            $categories = $categories->where('Status', $status);
        }

        return $categories->get();
    }


    public function addCategory($data)

    {
        return Categories::create($data);
    }

    //$id đại diện cho giá trị của CategoryID
    //truyền $id vào và nó so sánh với CategoryID trong bảng categories
    //Nếu có thì lấy ra bản ghi đó và trả về , nếu không có thì trả về null
    public function getDetail($id)
    {
        //Tức là tại bảng categories và lấy ra bản ghi có CategoryID
        //where : tìm đến bảng categories và lấy ra bản ghi có CategoryID = giá trị $id sẽ chứa giá trị của CategoryID đó
        //First : lấy ra bản ghi đầu tiên mà tìm được , nó là đối tượng
        return Categories::where('CategoryID', $id)->first();
    }

    public function updateCategory($id, $dataUpdate)
    {
        //where : tìm đến bảng categories và lấy ra bản ghi có CategoryID và gán vào $id
        //update : cập nhật dữ liệu vào bảng categories và data là dữ liệu truyền vào
        return Categories::where('CategoryID', $id)->update($dataUpdate);
    }

    public function getCategoryByName($categoryName)
    {
        return Categories::where('CategoryName', $categoryName)->first();
    }

    public function deleteCategoryAndRelatedData($categoryId)
    {

        DB::transaction(function () use ($categoryId) {

            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            $productIds = DB::table('products')->where('CategoryID', $categoryId)->pluck('ProductID');

            DB::table('reviews')->whereIn('ProductID', $productIds)->delete();
            DB::table('wishlist')->whereIn('ProductID', $productIds)->delete();
            DB::table('cart_items')->whereIn('ProductID', $productIds)->delete();

            $variantIds = DB::table('product_variants')->whereIn('ProductID', $productIds)->pluck('VariantID');
            DB::table('order_items')->whereIn('VariantID', $variantIds)->delete();

            DB::table('product_variants')->whereIn('ProductID', $productIds)->delete();

            DB::table('product_images')->whereIn('ProductID', $productIds)->delete();

            DB::table('products')->where('CategoryID', $categoryId)->delete();

            DB::table('categories')->where('CategoryID', $categoryId)->delete();

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        });
    }

    public function updateCategoryAndRelatedStatus($categoryId, $status)
    {
        DB::beginTransaction();

        try {

            DB::table('product_variants')
                ->where('ProductID', function ($query) use ($categoryId) {
                    $query->select('ProductID')
                        ->from('products')
                        ->where('CategoryID', $categoryId);
                })
                ->update(['Status' => $status]);

            DB::table('products')
                ->where('CategoryID', $categoryId)
                ->update(['Status' => $status]);

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
