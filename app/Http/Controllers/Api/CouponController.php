<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Http\Requests\CouponRequest;

class CouponController extends Controller
{
    protected $repoCoupon;

    public function __construct()
    {
        $this->repoCoupon = new Coupon();
    }

    public function index(Request $request)
    {
        $role = auth()->user()->role;

        $MinimumOrderValue = $request->MinimumOrderValue;

        $coupons = $this->repoCoupon->listAllCoupons($role, $MinimumOrderValue);

        return response()->json([
            'message' => 'Success',
            'data' => $coupons,
        ], 200);
    }

    public function store(CouponRequest $request)
    {
        $data = [
            'Name' => $request->Name,
            'Code' => $request->Code,
            'DiscountPercentage' => $request->DiscountPercentage,
            'MinimumOrderValue' => $request->MinimumOrderValue,
            'UsedCount' => 0,
            'UsageLimit' => $request->UsageLimit,
            'ExpiresAt' => $request->ExpiresAt,
        ];

        $coupon = $this->repoCoupon->addCoupon($data);

        return response()->json([
            'message' => 'Success',
            'data' => $coupon,
        ], 201);
    }

    public function show($id)
    {
        $coupon = $this->repoCoupon->getCouponByID($id);

        return response()->json([
            'message' => 'Success',
            'data' => $coupon,
        ], 200);
    }

    public function update($id, Request $request)
    {
        $data = [
            'Name' => $request->Name,
            'Code' => $request->Code,
            'DiscountPercentage' => $request->DiscountPercentage,
            'MinimumOrderValue' => $request->MinimumOrderValue,
            'UsageLimit' => $request->UsageLimit,
            'ExpiresAt' => $request->ExpiresAt,
        ];

        $coupon = $this->repoCoupon->updateCoupon($id, $data);

        return response()->json([
            'message' => 'Success',
            'data' => $coupon,
        ], 200);
    }

    public function delete(Request $request)
    {
        $ids = explode(',', $request->ids);

        $coupon = $this->repoCoupon->deleteCoupon($ids);

        return response()->json([
            'success' => true,
            'message' => 'Coupons deleted successfully',
        ], 200);
    }

    public function getDetailsCoupon(Request $request)
    {
        $coupon = $this->repoCoupon->getCouponByCode($request->Code);

        return response()->json([
            'message' => 'Success',
            'data' => $coupon,
        ], 200);
    }

}
