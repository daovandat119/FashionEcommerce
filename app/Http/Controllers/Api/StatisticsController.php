<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function getUserStatistics()
    {
        $statistics = DB::table('users')
            ->select(
                DB::raw('COUNT(*) as TotalUsers'),
                DB::raw('SUM(CASE WHEN IsActive = 1 THEN 1 ELSE 0 END) as ActiveUsers'),
                DB::raw('SUM(CASE WHEN IsActive = 0 THEN 1 ELSE 0 END) as InactiveUsers')
            )
            ->first();

        return response()->json($statistics);
    }

    public function getUserDetails($id)
    {
        $userDetails = DB::table('users as u')
            ->leftJoin('orders as o', 'u.UserID', '=', 'o.UserID')
            ->select(
                'u.UserID',
                'u.Username',
                'u.Email',
                DB::raw('COUNT(o.OrderID) as TotalOrders')
            )
            ->where('u.UserID', $id)
            ->groupBy('u.UserID', 'u.Username', 'u.Email')
            ->first();

        if (!$userDetails) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($userDetails);
    }

    public function getProductStatistics()
    {
        $productStats = DB::table('products')
            ->select(
                DB::raw('COUNT(*) as TotalProducts'),
                DB::raw('SUM(Views) as TotalViews'),
                DB::raw('SUM(CASE WHEN Status = "active" THEN 1 ELSE 0 END) as ActiveProducts'),
                DB::raw('SUM(CASE WHEN Status = "inactive" THEN 1 ELSE 0 END) as InactiveProducts')
            )
            ->first();

        return response()->json($productStats);
    }
    // Thống kê theo dõi số lượng người dùng đk mỗi ngày 
    public function getDailyUserRegistrations()
    {
        $registrations = DB::table('users')
            ->select(
                DB::raw('DATE(created_at) as Date'),
                DB::raw('COUNT(*) as Registrations')
            )
            ->groupBy('Date')
            ->orderByDesc('Date')
            ->limit(30)
            ->get();

        return response()->json($registrations);
    }
        public function getOrderStatistics()
    {
        $orderStats = DB::table('orders')
            ->select(
                DB::raw('COUNT(OrderID) as TotalOrders') 
            )
            ->first();

        return response()->json($orderStats);
    }
    //Lấy danh sách sản phẩm bán chạy:
public function getTopSellingProducts()
{
    $topProducts = DB::table('products')
        ->join('order_items', 'products.ProductID', '=', 'order_items.ProductID')
        ->select('products.ProductID', 'products.ProductName', DB::raw('SUM(order_items.quantity) as total_sold'))
        ->groupBy('products.ProductID', 'products.ProductName')
        ->orderByDesc('total_sold')
        ->limit(10)
        ->get();

    return response()->json($topProducts);
}
// Lấy tổng doanh thu theo ngày
public function getTotalRevenueByDate()
{
    $revenue = DB::table('orders')
        ->join('order_items', 'orders.OrderID', '=', 'order_items.OrderID')
        ->join('products', 'order_items.ProductID', '=', 'products.ProductID')
        ->select(
            DB::raw('DATE(orders.created_at) as order_date'),
            DB::raw('SUM(order_items.Quantity * products.Price) as total_revenue')
        )
        ->where('orders.OrderStatusID', '=', 1) // Giả sử trạng thái "1" là hoàn tất
        ->groupBy(DB::raw('DATE(orders.created_at)'))
        ->orderByDesc('order_date')
        ->get();
    return response()->json($revenue);
}

// Lấy số lượng sản phẩm đã bán theo ngày
    public function getTotalProductsSoldByDate()
    {
        $productsSold = DB::table('orders')
            ->join('order_items', 'orders.OrderID', '=', 'order_items.OrderID')
            ->select(
                DB::raw('DATE(orders.created_at) as order_date'),
                DB::raw('SUM(order_items.Quantity) as total_products_sold')
            )
            ->where('orders.OrderStatusID', '=', 1)
            ->groupBy(DB::raw('DATE(orders.created_at)'))
            ->orderByDesc('order_date')
            ->get();
        return response()->json($productsSold);
    }
    // Tình trạng hàng tồn kho
    public function getInventoryStatus()
    {
        $inventory = DB::table('products')
            ->leftJoin('order_items', 'products.ProductID', '=', 'order_items.ProductID')
            ->select(
                'products.ProductID',
                'products.ProductName',
                DB::raw('SUM(order_items.Quantity) as total_sold'), 
                DB::raw('COUNT(order_items.OrderID) as total_orders') 
            )
            ->groupBy('products.ProductID', 'products.ProductName')
            ->orderBy('total_sold', 'desc') 
            ->get();
        return response()->json($inventory);
    }
}
