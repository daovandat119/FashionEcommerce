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
        } else if ($request->startDate && $request->endDate) {
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

    public function getOrderStatistics(Request $request) {
        //
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
        if ($request->timeFrame) {
            $date = now();
            switch ($request->timeFrame) {
                case '1_month':
                    $queryStatistics->where('o.created_at', '>=', now()->subMonth());
                    break;
                case '6_months':
                    $queryStatistics->where('o.created_at', '>=', now()->subMonths(6));
                    break;
                case '1_year':
                    $queryStatistics->where('o.created_at', '>=', now()->subYear());
                    break;
            }
        } else if ($request->startDate && $request->endDate) {
            $queryStatistics->whereBetween('o.created_at', [$request->startDate, $request->endDate]);
        }
        $statistics = $queryStatistics->get();

        //
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
        if ($request->timeFrame) {
            $date = now();
            switch ($request->timeFrame){
                case '1_month':
                    $queryStatisticsOrder->where('o.created_at', '>=', now()->subMonth());
                    break;
                case '6_months':
                    $queryStatisticsOrder->where('o.created_at', '>=', now()->subMonths(6));
                    break;
                case '1_year':
                    $queryStatisticsOrder->where('o.created_at', '>=', now()->subYear());
                    break;
            }
        } else if ($request->startDate && $request->endDate) {
            $queryStatisticsOrder->whereBetween('o.created_at', [$request->startDate, $request->endDate]);
        }
        $statisticsOrder = $queryStatisticsOrder->get();

        //
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
        if ($request->timeFrame) {
            $date = now();
            switch ($request->timeFrame) {
                case '1_month':
                    $queryStatisticsOrderStatus->where('o.created_at', '>=', now()->subMonth());
                    break;
                case '6_months':
                    $queryStatisticsOrderStatus->where('o.created_at', '>=', now()->subMonths(6));
                    break;
                case '1_year':
                    $queryStatisticsOrderStatus->where('o.created_at', '>=', now()->subYear());
                    break;
            }
        } else if ($request->startDate && $request->endDate) {
            $queryStatisticsOrderStatus->whereBetween('o.created_at', [$request->startDate, $request->endDate]);
        }
        $statisticsOrderStatus = $queryStatisticsOrderStatus->get();


        return response()->json(['data' => [
            'statistics' => $statistics,
            'statisticsOrder' => $statisticsOrder,
            'statisticsOrderStatus' => $statisticsOrderStatus
        ]]);
    }


    public function getOrderStatusStatistics() {
        $statistics = DB::table('order_statuses as os')
            ->select(
                'os.StatusName',
                DB::raw('IFNULL(COUNT(o.OrderID), 0) AS TotalOrders'),
                DB::raw('IFNULL(SUM(p.Amount), 0) AS TotalRevenue')
            )
            ->leftJoin('orders as o', 'o.OrderStatusID', '=', 'os.OrderStatusID')
            ->leftJoin('payments as p', 'o.OrderID', '=', 'p.OrderID')
            ->groupBy('os.StatusName')
            ->orderBy('TotalOrders', 'DESC')
            ->get();

        return response()->json(['data' => $statistics]);
    }


}


