<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Colors extends Model
{
    use HasFactory;

    protected $table = 'colors';

    public function listColors()
    {
        return DB::table($this->table)->get();
    }

    public function addColor($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function getDetail($id)
    {
        return DB::table($this->table)->where('ColorID', $id)->first();
    }

   
    public function updateColor($id, $dataUpdate)
    {
        return DB::table($this->table)
            ->where('ColorID', $id)
            ->update($dataUpdate);
    }

    public function deleteColor($id)
    {
        return DB::table($this->table)->where('ColorID', $id)->delete();
    }
    public static function deleteVariantsByColor($colorId)
    {
        return DB::table('product_variants')->where('ColorID', $colorId)->delete();
    }
}

