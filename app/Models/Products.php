<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'ProductID';

    public function listProducts()
    {
        return DB::table($this->table)
            ->select("{$this->table}.*", 'categories.CategoryName as category_name')
            ->join('categories', 'categories.CategoryID', '=', "{$this->table}.CategoryID")
            ->get();
    }

    public function addProduct(array $data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function getDetail($id)
    {
        return DB::table($this->table)->where('ProductID', $id)->first();
    }

    public function updateProduct(array $data, $id)
    {
        return DB::table($this->table)->where('ProductID', $id)->update($data);
    }

    public function deleteProduct($id)
    {
        return DB::table($this->table)->where('ProductID', $id)->delete();
    }

    public static function deleteOrderItemsByCategoryId($categoryId)
    {
        return DB::table('order_items')->whereIn('ProductID', function($query) use ($categoryId) {
            $query->select('ProductID')->from('products')->where('CategoryID', $categoryId);
        })->delete();
    }
}
