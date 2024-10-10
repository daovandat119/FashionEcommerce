<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // Add this line

class OrderItems extends Model
{
    use HasFactory;

    protected $table = 'order_items';
    protected $primaryKey = 'OrderItemID';

    public function createOrderItem($dataOrderItem)
    {
        DB::table('order_items')->insert([
            'OrderID' => $dataOrderItem['OrderID'],
            'ProductID' => $dataOrderItem['ProductID'],
            'VariantID' => $dataOrderItem['VariantID'],
            'Quantity' => $dataOrderItem['Quantity'],
        ]);
    }
}
