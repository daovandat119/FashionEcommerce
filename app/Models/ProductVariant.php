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

    public function getProductVariantByID($ProductID,$SizeID,$ColorID)
    {
        $variantID = DB::table('product_variants')
            ->where('ProductID', $ProductID)
            ->where('SizeID', $SizeID)
            ->where('ColorID', $ColorID)
            ->first();
        return $variantID;
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'ProductID');
    }

    public function size()
    {
        return $this->belongsTo(Sizes::class, 'SizeID');
    }

    public function color()
    {
        return $this->belongsTo(Colors::class, 'ColorID');
    }
}
