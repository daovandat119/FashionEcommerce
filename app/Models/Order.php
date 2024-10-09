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

    public function getOrder($id)
    {
        return DB::table($this->table)->where('OrderID', $id)->first();
    }

    public function updateOrder($id, $data)
    {
        return DB::table($this->table)->where('OrderID', $id)->update($data);
    }

    public function deleteOrder($id)
    {
        return DB::table($this->table)->where('OrderID', $id)->delete();
    }

}
