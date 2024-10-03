<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Categories extends Model
{
    use HasFactory;

    protected $table = 'categories';

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
}
