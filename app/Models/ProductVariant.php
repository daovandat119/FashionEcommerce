<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
