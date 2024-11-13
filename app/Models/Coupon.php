<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    protected $primaryKey = 'CouponID';

    public $timestamps = true;

    protected $fillable = [
        'Name',
        'Code',
        'DiscountPercentage',
        'MinimumOrderValue',
        'UsageLimit',
        'UsedCount',
        'ExpiresAt',
        'created_at',
        'updated_at',
    ];

    public function listAllCoupons($role, $MinimumOrderValue)
    {
        $coupons = Coupon::query();

        if ($role->RoleName != 'Admin') {
            $coupons->where('ExpiresAt', '>', Carbon::now());
        }

        if ($MinimumOrderValue) {
            $coupons->where('MinimumOrderValue', '<=', $MinimumOrderValue);
        }

        return $coupons->get();
    }

    public function getCouponByID($CouponID)
    {
        return Coupon::where('CouponID', $CouponID)->first();
    }

    public function addCoupon($data)
    {
        return Coupon::create($data);
    }

    public function getCouponByCode($code)
    {
        return Coupon::where('Code', $code)->first();
    }

    public function updateCoupon($CouponID, $data)
    {
        $updatedCoupon = Coupon::where('CouponID', $CouponID)->update($data);
        return $updatedCoupon;
    }

    public function deleteCoupon($CouponIDs)
    {
        return Coupon::whereIn('CouponID', $CouponIDs)->delete();
    }

    public function checkCouponExists($MinimumOrderValue)
    {
        return Coupon::where('MinimumOrderValue', '<=', $MinimumOrderValue)->get();
    }

}
