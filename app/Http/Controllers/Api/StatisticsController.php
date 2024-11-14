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


}
