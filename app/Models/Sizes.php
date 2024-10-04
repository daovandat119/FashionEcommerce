<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sizes extends Model
{
    use HasFactory;

    protected $table = 'sizes';

    public function listSizes()
    {
        return DB::table($this->table)->get();
    }

    public function addSize($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function getDetail($id)
    {
        return DB::table($this->table)->where('SizeID', $id)->first();
    }

    public function updateSize($id, $dataUpdate)
    {
        return DB::table($this->table)
            ->where('SizeID', $id)
            ->update($dataUpdate);
    }

    public function deleteSize($id)
    {
        return DB::table($this->table)->where('SizeID', $id)->delete();
    }
}
