<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartItems;
use App\Models\OrderItems;

class ProductVariant extends Model
{
    use HasFactory;

    protected $table = 'product_variants';

    protected $primaryKey = 'VariantID';

    public $timestamps = true;

    protected $fillable = [
        'ProductID',
        'SizeID',
        'ColorID',
        'Quantity',
        'Price',
        'Status',
        'created_at',
        'updated_at',
    ];

    public function getAll($ProductID)
    {
        return ProductVariant::join('sizes', 'product_variants.SizeID', '=', 'sizes.SizeID')
            ->join('colors', 'product_variants.ColorID', '=', 'colors.ColorID')
            ->select('product_variants.*', 'sizes.SizeName', 'colors.ColorName')
            ->where('product_variants.ProductID', $ProductID)
            ->get();
    }

    public function getVariantByID($ProductID, $SizeID, $ColorID)
    {
        return ProductVariant::where('ProductID', $ProductID)
            ->where('SizeID', $SizeID)
            ->where('ColorID', $ColorID)
            ->first();
    }

    public function getVariantByIDAdmin($VariantID)
    {
        return ProductVariant::join('sizes', 'product_variants.SizeID', '=', 'sizes.SizeID')
            ->join('colors', 'product_variants.ColorID', '=', 'colors.ColorID')
            ->select('product_variants.*', 'sizes.SizeName', 'colors.ColorName')
            ->where('VariantID', $VariantID)
            ->first();
    }

    public function createVariant($data)
    {
        return ProductVariant::create([
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
        return ProductVariant::where('ProductID', $ProductID)
            ->where('SizeID', $SizeID)
            ->where('ColorID', $ColorID)
            ->exists();
    }

    public function updateVariant($data)
    {
        return ProductVariant::where('ProductID', $data['ProductID'])
            ->where('SizeID', $data['SizeID'])
            ->where('ColorID', $data['ColorID'])
            ->update([
                'Quantity' => $data['Quantity'],
                'Price' => $data['Price'],
            ]);
    }


    public function deleteVariant($ids)
    {
        CartItems::whereIn('VariantID', $ids)->delete();

        OrderItems::whereIn('VariantID', $ids)->delete();

        ProductVariant::whereIn('VariantID', $ids)->delete();
    }

    public function updateStatus($id, $status)
    {
        return ProductVariant::where('VariantID', $id)->update(['Status' => $status]);
    }

    public function updateQuantity($variantID, $quantity)
    {
        return ProductVariant::where('VariantID', $variantID)->update(['Quantity' => $quantity]);
    }


}
