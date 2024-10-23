<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductVariant extends Model
{
    use HasFactory;

    protected $table = 'product_variants';
    protected $primaryKey = 'VariantID';
    protected $fillable = [
        'ProductID',
        'SizeID',
        'ColorID',
        'Quantity',
        'Price',
    ];

    public function getAll($ProductID)
    {
        return DB::table($this->table)
            ->join('sizes', 'product_variants.SizeID', '=', 'sizes.SizeID')
            ->join('colors', 'product_variants.ColorID', '=', 'colors.ColorID')
            ->select('product_variants.*', 'sizes.SizeName', 'colors.ColorName')
            ->where('product_variants.ProductID', $ProductID)
            ->get();
    }

    public function getVariantByID($ProductID, $SizeID, $ColorID)
    {
        return DB::table('product_variants')
            ->where('ProductID', $ProductID)
            ->where('SizeID', $SizeID)
            ->where('ColorID', $ColorID)
            ->first();
    }

    public function getVariantByIDAdmin($VariantID)
    {
        return DB::table('product_variants')
            ->join('sizes', 'product_variants.SizeID', '=', 'sizes.SizeID')
            ->join('colors', 'product_variants.ColorID', '=', 'colors.ColorID')
            ->select('product_variants.*', 'sizes.SizeName', 'colors.ColorName')
            ->where('VariantID', $VariantID)
            ->first();
    }

    public function createVariant($data)
    {
        return DB::table('product_variants')
            ->insert([
                'ProductID' => $data['ProductID'],
                'SizeID' => $data['SizeID'],
                'ColorID' => $data['ColorID'],
                'Quantity' => $data['Quantity'],
                'Price' => $data['Price'],
                'Status' => $data['Status'] ?? 'ACTIVE',
            ]);
    }

    public function checkVariantExists($ProductID, $SizeID, $ColorID)
    {
        return DB::table('product_variants')
            ->where('ProductID', $ProductID)
            ->where('SizeID', $SizeID)
            ->where('ColorID', $ColorID)
            ->exists();
    }

    public function updateVariant($data)
    {
        return DB::table('product_variants')
            ->where('ProductID', $data['ProductID'])
            ->where('SizeID', $data['SizeID'])
            ->where('ColorID', $data['ColorID'])
            ->update([
                'Quantity' => $data['Quantity'],
                'Price' => $data['Price'],
            ]);
    }


    public function deleteVariant($id)
    {
        return DB::table('product_variants')
            ->where('VariantID', $id)
            ->delete();
    }

    public function updateStatus($id, $status)
    {
        return DB::table($this->table)
            ->where('VariantID', $id)
            ->update(['Status' => $status]);
    }
}
