<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colors extends Model
{
    use HasFactory;

    protected $table = 'colors';

    protected $primaryKey = 'ColorID';

    public $timestamps = true;

    protected $fillable = [
        'ColorName',
        'created_at',
        'updated_at',
    ];

    public function listColors($role)
    {
        if ($role === 'Admin') {
            return Colors::all(); 
        } else {
            return Colors::where('status', 'ACTIVE')->get();
        }
    }

    public function addColor($data)
    {
        return Colors::create([
            'ColorName' => $data['ColorName'],
        ]);
    }

    public function getDetail($id)
    {
        return Colors::where('ColorID', $id)->first();
    }

    public function updateColor($id, $dataUpdate)
    {
        return Colors::where('ColorID', $id)->update([
                'ColorName' => $dataUpdate['ColorName'],
            ]);
    }

    public function deleteColor($id)
    {
        $color = Colors::where('ColorID', $id)->first();

        return Colors::where('ColorID', $id)

        ->update(['status' => $color->status === "ACTIVE" ? "INACTIVE" : "ACTIVE"]);

    }

}

