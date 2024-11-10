<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sizes extends Model
{
    use HasFactory;

    protected $table = 'sizes';

    protected $primaryKey = 'SizeID';

    public $timestamps = true;

    protected $fillable = [
        'SizeName',
        'created_at',
        'updated_at',
    ];

    public function listSizes()
    {
        return Sizes::get();
    }

    public function addSize($data)
    {
        return Sizes::create([
            'SizeName' => $data['SizeName'],
        ]);
    }

    public function getDetail($id)
    {
        return Sizes::where('SizeID', $id)->first();
    }

    public function updateSize($id, $dataUpdate)
    {
        return Sizes::where('SizeID', $id)->update([
                'SizeName' => $dataUpdate['SizeName'],
            ]);
    }

    public function deleteSize($id)
    {
        return Sizes::where('SizeID', $id)->delete();
    }
}
