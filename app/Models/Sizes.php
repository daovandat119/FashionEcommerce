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
        return DB::table($this->table)->insert([
            'SizeName' => $data['SizeName'],
        ]);
    }

    public function getDetail($id)
    {
        return DB::table($this->table)->where('SizeID', $id)->first();
    }

    public function updateSize($id, $dataUpdate)
    {
        return DB::table($this->table)
            ->where('SizeID', $id)
            ->update([
                'SizeName' => $dataUpdate['SizeName'],
            ]);
    }

    public function deleteSize($id)
    {
        return DB::table($this->table)
            ->where('SizeID', $id)
            ->delete();
    }
}
