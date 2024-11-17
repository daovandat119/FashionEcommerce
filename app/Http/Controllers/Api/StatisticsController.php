<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class StatisticsController extends Controller
{
    public function getProductStatistics(Request $request)
    {
        // Lấy các tham số bộ lọc từ yêu cầu
        $status = $request->input('status');  
        $startDate = $request->input('start_date');  
        $endDate = $request->input('end_date'); 
    
        // Thống kê tổng số sản phẩm, tổng lượt xem và trạng thái sản phẩm
        $productStats = DB::table('products')
            ->selectRaw('
                COUNT(*) as TotalProducts, 
                SUM(Views) as TotalViews, 
                SUM(CASE WHEN Status = "active" THEN 1 ELSE 0 END) as ActiveProducts, 
                SUM(CASE WHEN Status = "inactive" THEN 1 ELSE 0 END) as InactiveProducts,
                SUM(CASE WHEN order_items.ProductID IS NOT NULL THEN order_items.Quantity ELSE 0 END) as TotalSold
            ')
            ->leftJoin('order_items', 'products.ProductID', '=', 'order_items.ProductID');
    
        // Áp dụng bộ lọc nếu có
        if ($status) {
            $productStats->where('products.Status', $status);
        }
        if ($startDate) {
            $productStats->where('order_items.CreatedAt', '>=', $startDate);
        }
        if ($endDate) {
            $productStats->where('order_items.CreatedAt', '<=', $endDate);
        }
    
        $productStats = $productStats->first();
    
        // Top 10 sản phẩm bán chạy nhất
        $topProducts = DB::table('products')
            ->join('order_items', 'products.ProductID', '=', 'order_items.ProductID')
            ->select('products.ProductID', 'products.ProductName', DB::raw('SUM(order_items.Quantity) as total_sold'))
            ->groupBy('products.ProductID', 'products.ProductName')
            ->orderByDesc('total_sold')
            ->limit(10);
    
        // Áp dụng bộ lọc cho top sản phẩm
        if ($startDate) {
            $topProducts->where('order_items.CreatedAt', '>=', $startDate);
        }
        if ($endDate) {
            $topProducts->where('order_items.CreatedAt', '<=', $endDate);
        }
    
        $topProducts = $topProducts->get();
    
        // Doanh thu sản phẩm theo ngày
        $revenueByProduct = DB::table('orders')
            ->join('order_items', 'orders.OrderID', '=', 'order_items.OrderID')
            ->join('products', 'order_items.ProductID', '=', 'products.ProductID')
            ->selectRaw('
                products.ProductID, 
                products.ProductName, 
                SUM(order_items.Quantity * products.Price) as TotalRevenue
            ')
            ->where('orders.OrderStatusID', '=', 1);  // Chỉ tính doanh thu cho các đơn hàng đã hoàn thành
    
        // Áp dụng bộ lọc cho doanh thu sản phẩm
        if ($startDate) {
            $revenueByProduct->where('orders.CreatedAt', '>=', $startDate);
        }
        if ($endDate) {
            $revenueByProduct->where('orders.CreatedAt', '<=', $endDate);
        }
    
        $revenueByProduct = $revenueByProduct
            ->groupBy('products.ProductID', 'products.ProductName')
            ->orderByDesc('TotalRevenue')
            ->get();
    
        // Trạng thái tồn kho (tên sản phẩm, tổng số lượng bán ra, đơn giá, tổng thành tiền)
        $inventoryStatus = DB::table('products')
            ->leftJoin('order_items', 'products.ProductID', '=', 'order_items.ProductID')
            ->selectRaw('
                products.ProductID, 
                products.ProductName, 
                products.Price as UnitPrice, 
                SUM(order_items.Quantity) as TotalSold, 
                (SUM(order_items.Quantity) * products.Price) as TotalRevenue
            ')
            ->groupBy('products.ProductID', 'products.ProductName', 'products.Price')
            ->orderByDesc('TotalSold');
    
        // Áp dụng bộ lọc cho trạng thái tồn kho
        if ($status) {
            $inventoryStatus->where('products.Status', $status);
        }
        if ($startDate) {
            $inventoryStatus->where('order_items.CreatedAt', '>=', $startDate);
        }
        if ($endDate) {
            $inventoryStatus->where('order_items.CreatedAt', '<=', $endDate);
        }
    
        $inventoryStatus = $inventoryStatus->get();
    
        // Thống kê chi tiết sản phẩm (tên sản phẩm, số lượng bán ra, đơn giá, tổng doanh thu)
        $productDetails = DB::table('products')
            ->leftJoin('order_items', 'products.ProductID', '=', 'order_items.ProductID')
            ->select('products.ProductID', 'products.ProductName', 'products.Price as UnitPrice', 
                     DB::raw('SUM(order_items.Quantity) as TotalSold'), 
                     DB::raw('(SUM(order_items.Quantity) * products.Price) as TotalRevenue'))
            ->groupBy('products.ProductID', 'products.ProductName', 'products.Price');
    
        // Áp dụng bộ lọc cho chi tiết sản phẩm
        if ($status) {
            $productDetails->where('products.Status', $status);
        }
        if ($startDate) {
            $productDetails->where('order_items.CreatedAt', '>=', $startDate);
        }
        if ($endDate) {
            $productDetails->where('order_items.CreatedAt', '<=', $endDate);
        }
    
        $productDetails = $productDetails->get();
    
        // Trả về tất cả thống kê liên quan đến sản phẩm
        return response()->json([
            'product_statistics' => $productStats,
            'top_selling_products' => $topProducts,
            'revenue_by_product' => $revenueByProduct,
            'inventory_status' => $inventoryStatus,
            'product_details' => $productDetails,
        ]);
    }
    public function getOrderStatistics()
    {
        $statistics = [
            'totalOrders' => DB::table('orders')
                ->select(DB::raw('COUNT(*) as TotalOrders'))
                ->first(),

            'orderStatus' => DB::table('orders')
                ->select(
                    'OrderStatusID',
                    DB::raw('COUNT(*) as TotalOrders')
                )
                ->groupBy('OrderStatusID')
                ->get(),

            'topSellingProducts' => DB::table('products')
                ->join('order_items', 'products.ProductID', '=', 'order_items.ProductID')
                ->select('products.ProductID', 'products.ProductName', DB::raw('SUM(order_items.quantity) as total_sold'))
                ->groupBy('products.ProductID', 'products.ProductName')
                ->orderByDesc('total_sold')
                ->limit(10)
                ->get(),

            'totalRevenueByDate' => DB::table('orders')
                ->join('order_items', 'orders.OrderID', '=', 'order_items.OrderID')
                ->join('products', 'order_items.ProductID', '=', 'products.ProductID')
                ->select(
                    DB::raw('DATE(orders.created_at) as order_date'),
                    DB::raw('SUM(order_items.Quantity * products.Price) as total_revenue')
                )
                ->where('orders.OrderStatusID', '=', 1) // Giả sử trạng thái "1" là hoàn tất
                ->groupBy(DB::raw('DATE(orders.created_at)'))
                ->orderByDesc('order_date')
                ->get(),

            'totalProductsSoldByDate' => DB::table('orders')
                ->join('order_items', 'orders.OrderID', '=', 'order_items.OrderID')
                ->select(
                    DB::raw('DATE(orders.created_at) as order_date'),
                    DB::raw('SUM(order_items.Quantity) as total_products_sold')
                )
                ->where('orders.OrderStatusID', '=', 1)
                ->groupBy(DB::raw('DATE(orders.created_at)'))
                ->orderByDesc('order_date')
                ->get(),

            'monthlyRevenue' => DB::table('orders')
                ->join('order_items', 'orders.OrderID', '=', 'order_items.OrderID')
                ->join('products', 'order_items.ProductID', '=', 'products.ProductID')
                ->select(
                    DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as month'),
                    DB::raw('SUM(order_items.Quantity * products.Price) as total_revenue')
                )
                ->where('orders.OrderStatusID', '=', 2)
                ->groupBy('month')
                ->orderByDesc('month')
                ->get(),

            'yearlyRevenue' => DB::table('orders')
                ->join('order_items', 'orders.OrderID', '=', 'order_items.OrderID')
                ->join('products', 'order_items.ProductID', '=', 'products.ProductID')
                ->select(
                    DB::raw('DATE_FORMAT(orders.created_at, "%Y") as year'),
                    DB::raw('SUM(order_items.Quantity * products.Price) as total_revenue')
                )
                ->where('orders.OrderStatusID', '=', 2)
                ->groupBy('year')
                ->orderByDesc('year')
                ->get(),

            'orderCompletionTime' => DB::table('orders')
                ->select(
                    DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as AverageCompletionTime')
                )
                ->where('OrderStatusID', '=', 2)
                ->first(),
        ];

        return response()->json($statistics);
    }

    // Thống kê người dùng
    public function getUserStatistics()
    {
        $statistics = [
            // Tổng số người dùng
            'totalUsers' => DB::table('users')
                ->select(DB::raw('COUNT(*) as TotalUsers'))
                ->first(),

            // Thống kê người dùng theo trạng thái kích hoạt
            'userStatus' => DB::table('users')
                ->select('IsActive', DB::raw('COUNT(*) as TotalUsers'))
                ->groupBy('IsActive')
                ->get(),

            // Thống kê người dùng theo vai trò
            'userRole' => DB::table('users')
                ->join('roles', 'users.RoleID', '=', 'roles.RoleID')
                ->select('roles.RoleName', DB::raw('COUNT(*) as TotalUsers'))
                ->groupBy('roles.RoleName')
                ->get(),

            // Người dùng đăng ký theo tháng
            'monthlyRegistrations' => DB::table('users')
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as TotalRegistrations')
                )
                ->groupBy('month')
                ->orderByDesc('month')
                ->get(),
        ];
        return response()->json($statistics);
    }

    public function getRevenueByTimeframe(Request $request)
    {
        // Lấy tham số "timeframe" từ request, mặc định là "month"
        $timeframe = $request->input('timeframe', 'month');
        
        // Kiểm tra tham số "timeframe" để quyết định loại thống kê
        if ($timeframe == 'month') {
            // Thống kê doanh thu theo tháng
            $revenue = DB::table('orders')
                ->join('order_items', 'orders.OrderID', '=', 'order_items.OrderID')
                ->join('products', 'order_items.ProductID', '=', 'products.ProductID')
                ->select(
                    DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as month'),
                    DB::raw('SUM(order_items.Quantity * products.Price) as total_revenue')
                )
                ->where('orders.OrderStatusID', '=', 2) // Giả sử trạng thái "2" là đã hoàn tất
                ->groupBy('month')
                ->orderByDesc('month')
                ->get();
        } elseif ($timeframe == 'year') {
            // Thống kê doanh thu theo năm
            $revenue = DB::table('orders')
                ->join('order_items', 'orders.OrderID', '=', 'order_items.OrderID')
                ->join('products', 'order_items.ProductID', '=', 'products.ProductID')
                ->select(
                    DB::raw('DATE_FORMAT(orders.created_at, "%Y") as year'),
                    DB::raw('SUM(order_items.Quantity * products.Price) as total_revenue')
                )
                ->where('orders.OrderStatusID', '=', 2) // Giả sử trạng thái "2" là đã hoàn tất
                ->groupBy('year')
                ->orderByDesc('year')
                ->get();
        } else {
            // Trả về lỗi nếu tham số không hợp lệ
            return response()->json(['error' => 'Invalid timeframe parameter'], 400);
        }
    
        // Trả về kết quả dưới dạng JSON
        return response()->json($revenue);
    }
    




}


