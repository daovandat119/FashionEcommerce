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

    public $timestamps = true;

    protected $fillable = [
        'UserID',
        'AddressID',
        'CartID',
        'OrderStatusID',
        'OrderCode',
        'created_at',
        'updated_at',
    ];

    public function createOrder($data)
    {
        $order = Order::create([
            'UserID' => $data['UserID'],
            'AddressID' => $data['AddressID'],
            'CartID' => $data['CartID'],
            'OrderStatusID' => 1,
            'OrderCode' => $data['OrderCode'],
        ]);

        return $order->OrderID;
    }

    public function getOrder($userId = null, $OrderCode = null, $OrderStatusID = null, $PaymentMethodID = null, $PaymentStatusID = null)
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
                COALESCE(SUM(oi.Quantity), 0) AS TotalQuantity,
                pay.Amount AS TotalAmount,
                o.OrderCode,
                ua.AddressID,
                o.created_at AS OrderDate
            ')
            ->when($userId, function ($query, $userId) {
                return $query->where('o.UserID', $userId);
            })
            ->when($OrderCode, function ($query, $OrderCode) {
                return $query->where('o.OrderCode', $OrderCode);
            })
            ->when($OrderStatusID, function ($query, $OrderStatusID) {
                return $query->where('os.OrderStatusID', $OrderStatusID);
            })
            ->when($PaymentMethodID, function ($query, $PaymentMethodID) {
                return $query->where('pm.PaymentMethodID', $PaymentMethodID);
            })
            ->when($PaymentStatusID, function ($query, $PaymentStatusID) {
                return $query->where('ps.PaymentStatusID', $PaymentStatusID);
            })
            ->groupBy(
                'o.OrderID',
                'os.StatusName',
                'pm.MethodName',
                'ps.StatusName',
                'o.OrderCode',
                'ua.AddressID',
                'o.created_at',
                'pay.Amount'
            )
            ->get();
    }

    public function getOrderDetails($OrderID)
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
                os.OrderStatusID,
                COALESCE(SUM(oi.Quantity), 0) AS TotalQuantity,
                COALESCE(SUM(oi.Quantity * pv.Price), 0) AS TotalAmount,
                o.OrderCode,
                ua.Username,
                ua.Address,
                o.created_at AS OrderDate
            ')

            ->where('o.OrderID', $OrderID)

            ->groupBy(
                'o.OrderID',
                'os.StatusName',
                'pm.MethodName',
                'ps.StatusName',
                'o.OrderCode',
                'ua.Username',
                'ua.Address',
                'o.created_at'
            )
            ->get();
    }



    public function getOrderById($id, $userId = null)
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
            ->when($userId, function ($query, $userId) {
                return $query->where('o.UserID', $userId);
            })
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


    public function updateOrderStatus($id, $orderStatusID, $userId = null)
    {
        return DB::table('orders')
            ->when($userId, function ($query, $userId) {
                return $query->where('UserID', $userId);
            })
            ->where('OrderID', $id)
            ->update(['OrderStatusID' => $orderStatusID]);
    }

    public function checkOrder($userId, $productId)
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
            ->where('oi.ProductID', $productId)
            ->groupBy(
                'o.OrderID',
                'os.StatusName',
                'p.ProductName',
                'p.MainImageURL',
                'pv.Price',
                'c.ColorName',
                's.SizeName'
            )
            ->exists();
    }

}
