<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $primaryKey = 'PaymentID';

    public $timestamps = true;

    protected $fillable = [
        'OrderID',
        'PaymentMethodID',
        'PaymentStatusID',
        'Amount',
        'TransactionID',
        'BankCode',
        'CardType',
        'OrderInfo',
        'ResponseCode',
    ];

    public function createPayment($data)
    {
        return Payments::create([
            'OrderID' => $data['OrderID'],
            'PaymentMethodID' => $data['PaymentMethodID'],
            'PaymentStatusID' => $data['PaymentStatusID'],
            'Amount' => $data['Amount'],
            'TransactionID' => $data['TransactionID'],
            'BankCode' => $data['BankCode'],
            'CardType' => $data['CardType'],
            'OrderInfo' => $data['OrderInfo'],
            'ResponseCode' => $data['ResponseCode'],
        ]);
    }

    public function getPaymentByOrderID($orderID)
    {
        return Payments::where('OrderID', $orderID)->first();
    }
}
