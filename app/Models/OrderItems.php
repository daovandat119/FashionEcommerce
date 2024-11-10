<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $primaryKey = 'OrderItemID';

    public $timestamps = true;

    protected $fillable = [
        'OrderID',
        'ProductID',
        'VariantID',
        'Quantity',
    ];

    public function createOrderItem($dataOrderItem)
    {
        return OrderItems::create([
            'OrderID' => $dataOrderItem['OrderID'],
            'ProductID' => $dataOrderItem['ProductID'],
            'VariantID' => $dataOrderItem['VariantID'],
            'Quantity' => $dataOrderItem['Quantity'],
        ]);
    }
}
