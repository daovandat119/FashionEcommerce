<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class StatisticsController extends Controller
{
    public function getProductStatistics(Request $request)
    {

        $query = DB::table('products as p')
            ->select(
                'p.ProductID',
                'p.ProductName',
                DB::raw('SUM(oi.Quantity) AS TotalSold'),
                DB::raw('ROUND(SUM(oi.Quantity * COALESCE(v.Price, p.Price)), 2) AS TotalRevenue'),
                DB::raw('COALESCE(SUM(v.Quantity), 0) AS Quantity')
            )
            ->join('order_items as oi', 'p.ProductID', '=', 'oi.ProductID')
            ->join('orders as o', 'oi.OrderID', '=', 'o.OrderID')
            ->leftJoin('product_variants as v', 'oi.VariantID', '=', 'v.VariantID')
            ->groupBy('p.ProductID', 'p.ProductName')
            ->orderBy('TotalRevenue', 'DESC');

        if($request->OrderStatusID) {
            $query->where('o.OrderStatusID', $request->OrderStatusID);
        } else {
            $query->whereIn('o.OrderStatusID', [1, 2, 3]);
        }

        if ($request->timeFrame) {
            $date = now();
            switch ($request->timeFrame) {
                case '1_month':
                    $query->where('o.created_at', '>=', now()->subMonth());
                    break;
                case '6_months':
                    $query->where('o.created_at', '>=', now()->subMonths(6));
                    break;
                case '1_year':
                    $query->where('o.created_at', '>=', now()->subYear());
                    break;
            }
        } elseif ($request->startDate && $request->endDate) {
            $query->whereBetween('o.created_at', [$request->startDate, $request->endDate]);
        }

        $statistics = $query->get();

        return response()->json(['data' => $statistics]);
    }


    public function getProductVariantsStatistics($productId)
    {
        $query = DB::table('products as p')
            ->select(
                'p.ProductName',
                DB::raw('COALESCE(s.SizeName, "N/A") AS Size'),  // Tên kích thước (nếu có)
                DB::raw('COALESCE(c.ColorName, "N/A") AS Color'),  // Tên màu sắc (nếu có)
                DB::raw('COALESCE(v.Quantity, 0) AS StockQuantity'),
                DB::raw('SUM(oi.Quantity) AS TotalSold'),
                DB::raw('ROUND(AVG(COALESCE(v.Price, p.Price)), 2) AS Price'),
                DB::raw('ROUND(SUM(oi.Quantity * COALESCE(v.Price, p.Price)), 2) AS TotalRevenue')
            )
            ->join('order_items as oi', 'p.ProductID', '=', 'oi.ProductID')
            ->join('orders as o', 'oi.OrderID', '=', 'o.OrderID')
            ->leftJoin('product_variants as v', 'oi.VariantID', '=', 'v.VariantID')
            ->leftJoin('sizes as s', 'v.SizeID', '=', 's.SizeID')
            ->leftJoin('colors as c', 'v.ColorID', '=', 'c.ColorID');

        if ($productId) {
            $query->where('p.ProductID', $productId);
        }

        $query->groupBy('p.ProductName', 'v.Quantity', 's.SizeName', 'c.ColorName')
              ->orderBy('TotalRevenue', 'DESC');

        return response()->json(['data' => $query->get()]);
    }

    public function getOrderStatistics() {
        $statistics = DB::table(DB::raw('(SELECT 1 AS Month UNION ALL
                                           SELECT 2 AS Month UNION ALL
                                           SELECT 3 AS Month UNION ALL
                                           SELECT 4 AS Month UNION ALL
                                           SELECT 5 AS Month UNION ALL
                                           SELECT 6 AS Month UNION ALL
                                           SELECT 7 AS Month UNION ALL
                                           SELECT 8 AS Month UNION ALL
                                           SELECT 9 AS Month UNION ALL
                                           SELECT 10 AS Month UNION ALL
                                           SELECT 11 AS Month UNION ALL
                                           SELECT 12 AS Month) AS months'))
            ->leftJoin('payments as p', DB::raw('MONTH(p.created_at)'), '=', 'months.Month')
            ->whereYear('p.created_at', 2024)
            ->orWhereNull('p.PaymentID')
            ->select(
                'months.Month',
                DB::raw('IFNULL(COUNT(p.PaymentID), 0) AS TotalTransactions'),
                DB::raw('IFNULL(SUM(p.Amount), 0) AS TotalRevenue')
            )
            ->groupBy('months.Month')
            ->orderBy('months.Month')
            ->get();

        return response()->json(['data' => $statistics]);
    }


    public function getOrderStatusStatistics() {
        $statistics = DB::table('order_statuses as os')
            ->select(
                'os.StatusName', // Tên trạng thái đơn hàng
                DB::raw('IFNULL(COUNT(o.OrderID), 0) AS TotalOrders'), // Tổng số đơn hàng trong trạng thái (nếu không có thì là 0)
                DB::raw('IFNULL(SUM(p.Amount), 0) AS TotalRevenue') // Tổng doanh thu (nếu không có thì là 0)
            )
            ->leftJoin('orders as o', 'o.OrderStatusID', '=', 'os.OrderStatusID') // Liên kết với bảng đơn hàng
            ->leftJoin('payments as p', 'o.OrderID', '=', 'p.OrderID') // Liên kết với bảng thanh toán
            ->groupBy('os.StatusName') // Nhóm theo trạng thái đơn hàng
            ->orderBy('TotalOrders', 'DESC') // Sắp xếp theo tổng số đơn hàng
            ->get();

        return response()->json(['data' => $statistics]);
    }



    //thống kê tổng doanh thu theo từng danh mục sản phẩm,
    public function getRevenueByCategory(Request $request)
{
    $query = DB::table('categories as c')
        ->select(
            'c.CategoryName as Category',
            DB::raw('ROUND(SUM(oi.Quantity * COALESCE(v.Price, p.Price)), 2) as TotalRevenue')
        )
        ->join('products as p', 'c.CategoryID', '=', 'p.CategoryID')
        ->join('order_items as oi', 'p.ProductID', '=', 'oi.ProductID')
        ->join('orders as o', 'oi.OrderID', '=', 'o.OrderID')
        ->leftJoin('product_variants as v', 'oi.VariantID', '=', 'v.VariantID')
        ->groupBy('c.CategoryName')
        ->orderByDesc('TotalRevenue');

    // Lọc theo trạng thái đơn hàng (mặc định là trạng thái hợp lệ)
    if ($request->OrderStatusID) {
        $query->where('o.OrderStatusID', $request->OrderStatusID);
    } else {
        $query->whereIn('o.OrderStatusID', [1, 2, 3]);
    }

    // Lọc theo khung thời gian
    if ($request->timeFrame) {
        switch ($request->timeFrame) {
            case '1_month':
                $query->where('o.created_at', '>=', now()->subMonth());
                break;
            case '6_months':
                $query->where('o.created_at', '>=', now()->subMonths(6));
                break;
            case '1_year':
                $query->where('o.created_at', '>=', now()->subYear());
                break;
        }
    } elseif ($request->startDate && $request->endDate) {
        $query->whereBetween('o.created_at', [$request->startDate, $request->endDate]);
    }

    // Lấy dữ liệu thống kê
    $statistics = $query->get();

    return response()->json(['data' => $statistics]);
}

}


