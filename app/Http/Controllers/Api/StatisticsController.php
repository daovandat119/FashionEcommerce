<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function getUserStatistics(Request $request)
    {
        $total = DB::table('users')
            ->join('roles as r', 'users.RoleID', '=', 'r.RoleID')
            ->where('r.RoleID', 2);
        $this->applyTimeFrame($total, $request, 'users');
        if ($request->UserName) {
            $total->where('users.UserName', 'like', '%' . $request->UserName . '%');
        }
        $total = $total->count();
        $limit = $request->input('Limit', 2);
        $page = $request->input('page', 1);
        $totalPage = ceil($total / $limit);

        $monthlyRegistrations = DB::table('users')
            ->select(DB::raw('DATE_FORMAT(users.created_at, "%m") as Month'), DB::raw('COUNT(*) as Total'))
            ->join('roles as r', 'users.RoleID', '=', 'r.RoleID')
            ->where('r.RoleID', 2)
            ->whereYear('users.created_at', $request->year ?? 2024);
        $monthlyRegistrations = $monthlyRegistrations->groupBy('Month')
            ->orderBy('Month')
            ->get();

        $activeCountQuery = DB::table('users')->where('IsActive', 1)
            ->join('roles as r', 'users.RoleID', '=', 'r.RoleID')
            ->where('r.RoleID', 2);
        $this->applyTimeFrame($activeCountQuery, $request, 'users');
        $activeCount = $activeCountQuery->count();

        $bannedCountQuery = DB::table('users')->where('IsActive', 0)
            ->join('roles as r', 'users.RoleID', '=', 'r.RoleID')
            ->where('r.RoleID', 2);
        $this->applyTimeFrame($bannedCountQuery, $request, 'users');
        $bannedCount = $bannedCountQuery->count();

        $queryUser = DB::table('users')->skip(($page - 1) * $limit)
            ->join('roles as r', 'users.RoleID', '=', 'r.RoleID')
            ->where('r.RoleID', 2);
        if ($request->UserName) {
            $queryUser->where('users.UserName', 'like', '%' . $request->UserName . '%');
        }
        $queryUser = $queryUser->take($limit)
            ->get();
        $this->applyTimeFrame($queryUser, $request, 'users');

        return response()->json(['data' => [
            'monthlyRegistrations' => $monthlyRegistrations,
            'activeCount' => $activeCount,
            'bannedCount' => $bannedCount,
            'queryUser' => [
                'data' => $queryUser,
                'total' => $total,
                'limit' => $limit,
                'page' => $page,
                'totalPage' => $totalPage
            ],
        ]]);
    }

    private function applyTimeFrame($query, $request, $table)
    {
        if ($request->timeFrame) {
            switch ($request->timeFrame) {
                case '1_month':
                    $query->where("{$table}.created_at", '>=', now()->subMonth());
                    break;
                case '6_months':
                    $query->where("{$table}.created_at", '>=', now()->subMonths(6));
                    break;
                case '1_year':
                    $query->where("{$table}.created_at", '>=', now()->subYear());
                    break;
            }
        } elseif ($request->startDate && $request->endDate) {
            $query->whereBetween(DB::raw("DATE({$table}.created_at)"), [$request->startDate, $request->endDate]);
        }
    }

    private function applyOrderStatus($query, $request)
    {
        if ($request->OrderStatusID) {
            $query->where('o.OrderStatusID', $request->OrderStatusID);
        } else {
            $query->whereIn('o.OrderStatusID', [1, 2, 3]);
        }
    }

    public function getProductStatistics(Request $request)
    {
        $total = DB::table('products as p')
            ->select(
                'p.ProductID',
                'p.ProductName',
                DB::raw('COALESCE(SUM(oi.Quantity), 0) AS TotalSold'),
                DB::raw('ROUND(COALESCE(SUM(oi.Quantity * COALESCE(v.Price, p.Price)), 0), 2) AS TotalRevenue'),
                DB::raw('(
                SELECT SUM(v2.Quantity)
                FROM product_variants AS v2
                WHERE v2.ProductID = p.ProductID AND v2.Quantity > 0
            ) AS Quantity')
            )
            ->Join('order_items as oi', 'p.ProductID', '=', 'oi.ProductID')
            ->Join('orders as o', 'oi.OrderID', '=', 'o.OrderID')
            ->Join('product_variants as v', 'oi.VariantID', '=', 'v.VariantID')
            ->groupBy('p.ProductID', 'p.ProductName')
            ->get();

        $total = count($total);
        $limit = $request->input('Limit', 3);
        $page = $request->input('page', 1);
        $totalPage = ceil($total / $limit);

        $queryProduct = DB::table('products as p')
            ->select(
                'p.ProductID',
                'p.ProductName',
                DB::raw('COALESCE(SUM(oi.Quantity), 0) AS TotalSold'),
                DB::raw('ROUND(COALESCE(SUM(oi.Quantity * COALESCE(v.Price, p.Price)), 0), 2) AS TotalRevenue'),
                DB::raw('(
                    SELECT SUM(v2.Quantity)
                    FROM product_variants AS v2
                    WHERE v2.ProductID = p.ProductID AND v2.Quantity > 0
                ) AS Quantity')
            )
            ->leftJoin('order_items as oi', 'p.ProductID', '=', 'oi.ProductID')
            ->leftJoin('orders as o', 'oi.OrderID', '=', 'o.OrderID')
            ->leftJoin('product_variants as v', 'oi.VariantID', '=', 'v.VariantID')
            ->groupBy('p.ProductID', 'p.ProductName');
        if ($request->ProductName) {
            $queryProduct->where('p.ProductName', 'like', '%' . $request->ProductName . '%');
        }
        $queryProduct->skip(($page - 1) * $limit)
            ->take($limit)
            ->groupBy('p.ProductID', 'p.ProductName');


        $this->applyOrderStatus($queryProduct, $request);
        $this->applyTimeFrame($queryProduct, $request, 'p');

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
        $this->applyTimeFrame($queryCategory, $request, 'p');

        $statisticsCategory = $queryCategory->get();

        return response()->json(['data' => [
            'statisticsProduct' => [
                'data' => $statisticsProduct,
                'limit' => $limit,
                'page' => $page,
                'total' => $totalPage,
                'yes' => $total
            ],
            'statisticsCategory' => $statisticsCategory
        ]]);
    }

    public function getOrderStatistics(Request $request)
    {
        $total = DB::table('payments as p');
        $this->applyTimeFrame($total, $request, 'p');
        $total = $total->join('orders as o', 'p.OrderID', '=', 'o.OrderID');
        if ($request->OrderCode) {
            $total->where('o.OrderCode', '=', $request->OrderCode);
        }
        $total = $total->count();
        $limit = $request->input('Limit', 2);
        $page = $request->input('Page', 1);
        $totalPage = ceil($total / $limit);

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
            ->leftJoin('payments as p', DB::raw('MONTH(p.created_at)'), '=', 'months.Month');
        if ($request->timeFrame || $request->startDate || $request->endDate) {
            $this->applyTimeFrame($queryStatistics, $request, 'p');
        }

        $queryStatistics->whereYear('p.created_at', $request->year == null ? 2024 : $request->year);

        $queryStatistics->orWhereNull('p.PaymentID')
            ->select(
                'months.Month',
                DB::raw('IFNULL(COUNT(p.PaymentID), 0) AS TotalTransactions'),
                DB::raw('IFNULL(SUM(p.Amount), 0) AS TotalRevenue')
            )
            ->whereYear('p.created_at', $request->year == null ? 2024 : $request->year)
            ->groupBy('months.Month')
            ->orderBy('months.Month');
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
                DB::raw('COALESCE(SUM(oi.Quantity), 0) AS Quantity')
            )
            ->join('payments as p', 'o.OrderID', '=', 'p.OrderID')
            ->join('order_statuses as os', 'o.OrderStatusID', '=', 'os.OrderStatusID')
            ->join('payment_methods as pm', 'p.PaymentMethodID', '=', 'pm.PaymentMethodID')
            ->join('payment_statuses as ps', 'p.PaymentStatusID', '=', 'ps.PaymentStatusID')
            ->join('order_items as oi', 'o.OrderID', '=', 'oi.OrderID')
            ->leftJoin('order_reviews as or', 'o.OrderID', '=', 'or.OrderID')
            ->groupBy('o.OrderID', 'o.OrderCode', 'os.StatusName', 'o.created_at', 'p.Amount', 'pm.MethodName', 'ps.StatusName');
        if ($request->OrderCode) {
            $queryStatisticsOrder->where('o.OrderCode', '=', $request->OrderCode);
        }
        $queryStatisticsOrder->skip(($page - 1) * $limit)
            ->take($limit);

        $this->applyTimeFrame($queryStatisticsOrder, $request, 'o');
        $statisticsOrder = $queryStatisticsOrder->get();

        $allOrderStatuses = DB::table('order_statuses')->select('StatusName')->get();

        $queryStatisticsOrderStatus = DB::table('order_statuses as os')
            ->select(
                'os.StatusName',
                DB::raw('IFNULL(COUNT(o.OrderID), 0) AS TotalOrders'),
                DB::raw('IFNULL(SUM(p.Amount), 0) AS TotalRevenue')
            )
            ->leftJoin('orders as o', 'o.OrderStatusID', '=', 'os.OrderStatusID')
            ->leftJoin('payments as p', 'o.OrderID', '=', 'p.OrderID');

        $this->applyTimeFrame($queryStatisticsOrderStatus, $request, 'o');

        $queryStatisticsOrderStatus->groupBy('os.StatusName')
            ->orderBy('TotalOrders', 'DESC');

        $statisticsOrderStatus = $queryStatisticsOrderStatus->get();

        $result = [];
        foreach ($allOrderStatuses as $status) {
            $statusData = $statisticsOrderStatus->firstWhere('StatusName', $status->StatusName);
            $result[] = [
                'StatusName' => $status->StatusName,
                'TotalOrders' => $statusData->TotalOrders ?? 0,
                'TotalRevenue' => $statusData->TotalRevenue ?? '0.00',
            ];
        }

        return response()->json(['data' => [
            'statistics' => $statistics,
            'statisticsOrder' => [
                'data' => $statisticsOrder,
                'limit' => $limit,
                'page' => $page,
                'totalPage' => $totalPage,
                'total' => $total
            ],
            'statisticsOrderStatus' => $result
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
                DB::raw('COALESCE(SUM(oi.Quantity), 0) AS TotalSold'),
                DB::raw('ROUND(COALESCE(AVG(COALESCE(v.Price, p.Price)), 0), 2) AS Price'),
                DB::raw('ROUND(COALESCE(SUM(oi.Quantity * COALESCE(v.Price, p.Price)), 0), 2) AS TotalRevenue')
            )
            ->leftJoin('product_variants as v', 'p.ProductID', '=', 'v.ProductID')
            ->leftJoin('order_items as oi', 'v.VariantID', '=', 'oi.VariantID')
            ->leftJoin('orders as o', 'oi.OrderID', '=', 'o.OrderID')
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
