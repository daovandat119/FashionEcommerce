<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class StatisticsController extends Controller
{

    public function getUserStatistics(Request $request) {
        $monthlyRegistrations = DB::table('users')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as Month'), DB::raw('COUNT(*) as Total'))
            ->groupBy('Month')
            ->orderBy('Month')
            ->get();
        $this->applyTimeFrame($monthlyRegistrations, $request);


        $activeCount = DB::table('users')->where('IsActive', 1)->count();
        $this->applyTimeFrame($activeCount, $request);
        $bannedCount = DB::table('users')->where('IsActive', 0)->count();
        $this->applyTimeFrame($bannedCount, $request);

        $queryUser = DB::table('users')->get();
        $this->applyTimeFrame($queryUser, $request);

        return response()->json(['data' => [
            'monthlyRegistrations' => $monthlyRegistrations,
            'activeCount' => $activeCount,
            'bannedCount' => $bannedCount,
            'queryUser' => $queryUser
        ]]);
    }

    private function applyTimeFrame($query, $request) {
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
    }

    private function applyOrderStatus($query, $request) {
        if ($request->OrderStatusID) {
            $query->where('o.OrderStatusID', $request->OrderStatusID);
        } else {
            $query->whereIn('o.OrderStatusID', [1, 2, 3]);
        }
    }

    public function getProductStatistics(Request $request)
    {
        $queryProduct = DB::table('products as p')
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

        $this->applyOrderStatus($queryProduct, $request);
        $this->applyTimeFrame($queryProduct, $request);

        $statisticsProduct = $queryProduct->get();

        $queryCategory = DB::table('categories as c')
            ->select(
                'c.CategoryName as Category',
                DB::raw('SUM(oi.Quantity) as TotalSold'),
                DB::raw('ROUND(SUM(oi.Quantity * COALESCE(v.Price, p.Price)), 2) as TotalRevenue')
            )
            ->join('products as p', 'c.CategoryID', '=', 'p.CategoryID')
            ->join('order_items as oi', 'p.ProductID', '=', 'oi.ProductID')
            ->join('orders as o', 'oi.OrderID', '=', 'o.OrderID')
            ->leftJoin('product_variants as v', 'oi.VariantID', '=', 'v.VariantID')
            ->groupBy('c.CategoryName')
            ->orderByDesc('TotalRevenue');

        $this->applyOrderStatus($queryCategory, $request);
        $this->applyTimeFrame($queryCategory, $request);

        $statisticsCategory = $queryCategory->get();

        return response()->json(['data' => [
            'statisticsProduct' => $statisticsProduct,
            'statisticsCategory' => $statisticsCategory
        ]]);
    }

    public function getOrderStatistics(Request $request) {
        $queryStatistics = DB::table(DB::raw('(SELECT 1 AS Month UNION ALL
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
            ->whereYear('p.created_at', 2024)
            ->groupBy('months.Month')
            ->orderBy('months.Month');

        $this->applyTimeFrame($queryStatistics, $request);
        $statistics = $queryStatistics->get();

        $queryStatisticsOrder = DB::table('orders as o')
        ->select(
            'o.OrderID',
            'o.OrderCode',
            'os.StatusName as OrderStatusName',
            'o.created_at',
            'p.Amount',
            'pm.MethodName as PaymentMethodName',
            'ps.StatusName as PaymentStatusName',
            'oi.Quantity'
        )
        ->join('payments as p', 'o.OrderID', '=', 'p.OrderID')
        ->join('order_statuses as os', 'o.OrderStatusID', '=', 'os.OrderStatusID')
        ->join('payment_methods as pm', 'p.PaymentMethodID', '=', 'pm.PaymentMethodID')
        ->join('payment_statuses as ps', 'p.PaymentStatusID', '=', 'ps.PaymentStatusID')
        ->join('order_items as oi', 'o.OrderID', '=', 'oi.OrderID')
        ->leftJoin('order_reviews as or', 'o.OrderID', '=', 'or.OrderID');

        $this->applyTimeFrame($queryStatisticsOrder, $request);
        $statisticsOrder = $queryStatisticsOrder->get();

        $queryStatisticsOrderStatus = DB::table('order_statuses as os')
            ->select(
                'os.StatusName',
                DB::raw('IFNULL(COUNT(o.OrderID), 0) AS TotalOrders'),
                DB::raw('IFNULL(SUM(p.Amount), 0) AS TotalRevenue')
            )
            ->leftJoin('orders as o', 'o.OrderStatusID', '=', 'os.OrderStatusID')
            ->leftJoin('payments as p', 'o.OrderID', '=', 'p.OrderID')
            ->groupBy('os.StatusName')
            ->orderBy('TotalOrders', 'DESC');

        $this->applyTimeFrame($queryStatisticsOrderStatus, $request);
        $statisticsOrderStatus = $queryStatisticsOrderStatus->get();

        return response()->json(['data' => [
            'statistics' => $statistics,
            'statisticsOrder' => $statisticsOrder,
            'statisticsOrderStatus' => $statisticsOrderStatus
        ]]);
    }

    public function getProductVariantsStatistics($productId)
    {
        $query = DB::table('products as p')
            ->select(
                'p.ProductName',
                DB::raw('COALESCE(s.SizeName, "N/A") AS Size'),
                DB::raw('COALESCE(c.ColorName, "N/A") AS Color'),
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

}






