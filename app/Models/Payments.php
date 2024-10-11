<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Payments extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'PaymentID';

    public function createPayment($data)
    {
        return DB::table($this->table)->insert([
            'OrderID' => $data['OrderID'],
            'PaymentMethodID' => $data['PaymentMethodID'],
            'PaymentStatusID' => $data['PaymentStatusID'],
        ]);
    }

    public function getPaymentByOrderID($orderID)
    {
        return DB::table($this->table)
            ->where('OrderID', $orderID)
            ->first();
    }
}
