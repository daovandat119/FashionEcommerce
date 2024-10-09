<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    protected $table = 'orders';
    protected $primaryKey = 'OrderID';

    public function addOrder($data)
    {
        return DB::table($this->table)->insertGetId([
            'OrderID' => $data['OrderID'],
            'OrderDate' => $data['OrderDate'],
            'CustomerID' => $data['CustomerID'],
            'TotalAmount' => $data['TotalAmount'],
        ]);
    }

}
