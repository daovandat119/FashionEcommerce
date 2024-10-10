<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'OrderID';

    public function createOrder($data)
    {
        return DB::table($this->table)->insertGetId([
            'UserID' => $data['UserID'],
            'AddressID' => $data['AddressID'],
            'CartID' => $data['CartID'],
            'OrderStatusID' => $data['OrderStatusID'],
            'OrderCode' => $data['OrderCode'],
        ]);
    }

    public function getOrder($userId)
    {
        return DB::table('orders as o')
            ->leftJoin('order_items as oi', 'o.OrderID', '=', 'oi.OrderID')
            ->leftJoin('products as p', 'oi.ProductID', '=', 'p.ProductID')
            ->leftJoin('product_variants as pv', 'oi.VariantID', '=', 'pv.VariantID')
            ->leftJoin('order_statuses as os', 'o.OrderStatusID', '=', 'os.OrderStatusID')
            ->leftJoin('payments as pay', 'o.OrderID', '=', 'pay.OrderID')
            ->leftJoin('payment_methods as pm', 'pay.PaymentMethodID', '=', 'pm.PaymentMethodID')
            ->leftJoin('payment_statuses as ps', 'pay.PaymentStatusID', '=', 'ps.PaymentStatusID')
            ->leftJoin('addresses as ua', 'o.AddressID', '=', 'ua.AddressID')
            ->selectRaw('
                o.OrderID,
                os.StatusName AS OrderStatus,
                pm.MethodName AS PaymentMethod,
                ps.StatusName AS PaymentStatus,
                SUM(oi.Quantity) AS TotalQuantity,
                SUM(oi.Quantity * pv.Price) AS TotalPrice,
                o.OrderCode,
                ua.AddressID
            ')
            ->where('o.UserID', $userId)
            ->groupBy(
                'o.OrderID',
                'os.StatusName',
                'pm.MethodName',
                'ps.StatusName',
                'o.OrderCode',
                'ua.AddressID'
            )
            ->get();
    }

    public function getOrderById($userId, $id)
    {
        return DB::table('orders as o')
            ->leftJoin('order_items as oi', 'o.OrderID', '=', 'oi.OrderID')
            ->leftJoin('products as p', 'oi.ProductID', '=', 'p.ProductID')
            ->leftJoin('product_variants as pv', 'oi.VariantID', '=', 'pv.VariantID')
            ->leftJoin('order_statuses as os', 'o.OrderStatusID', '=', 'os.OrderStatusID')
            ->leftJoin('colors as c', 'pv.ColorID', '=', 'c.ColorID')
            ->leftJoin('sizes as s', 'pv.SizeID', '=', 's.SizeID')
            ->selectRaw('
                o.OrderID,
                os.StatusName AS OrderStatus,
                SUM(oi.Quantity) AS TotalQuantity,
                p.ProductName,
                p.MainImageURL,
                pv.Price AS VariantPrice,
                c.ColorName AS VariantColor,
                s.SizeName AS VariantSize,
                SUM(oi.Quantity * pv.Price) AS TotalPrice
            ')
            ->where('o.UserID', $userId)
            ->where('o.OrderID', $id)
            ->groupBy(
                'o.OrderID',
                'os.StatusName',
                'p.ProductName',
                'p.MainImageURL',
                'pv.Price',
                'c.ColorName',
                's.SizeName'
            )
            ->get();
    }


    public function updateOrderStatus($userId, $id, $orderStatusID)
    {
        return DB::table('orders')
            ->where('UserID', $userId)
            ->where('OrderID', $id)
            ->update(['OrderStatusID' => $orderStatusID]);
    }

}
