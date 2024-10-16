<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Colors extends Model
{
    use HasFactory;

    protected $table = 'colors';
    protected $primaryKey = 'ColorID';  // Thêm dòng này
    public $incrementing = false;  // Thêm dòng này nếu ColorID không phải là auto-increment
    protected $keyType = 'string';
    public function listColors()
    {
        return DB::table($this->table)->get();
    }

    public function addColor($data)
    {
        return DB::table($this->table)->insert([
            'ColorName' => $data['ColorName'],
        ]);
    }

    public function getDetail($id)
    {
        return DB::table($this->table)->where('ColorID', $id)->first();
    }


    public function updateColor($id, $dataUpdate)
    {
        return DB::table($this->table)
            ->where('ColorID', $id)
            ->update([
                'ColorName' => $dataUpdate['ColorName'],
            ]);
    }

    public function deleteColor($id)
    {
        return DB::table($this->table)
            ->where('ColorID', $id)
            ->delete();
    }

}

