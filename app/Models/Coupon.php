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

        $coupons->where(function($query) use ($MinimumOrderValue) {
            $query->where('MinimumOrderValue', '<=', $MinimumOrderValue)
                  ->orWhere('MinimumOrderValue', '>', $MinimumOrderValue);
        });

        return $coupons->get()->map(function($coupon) use ($MinimumOrderValue) {
            if ($coupon->MinimumOrderValue > $MinimumOrderValue) {
                $coupon->usable = false;
            } else {
                $coupon->usable = true;
            }
            return $coupon;
        });
    }

    public function getCouponByID($CouponID)
    {
        return Coupon::where('CouponID', $CouponID)->first();
    }

    public function addCoupon($data)
    {
        return Coupon::create($data);
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

    public function updateDiscountPercentage($id, $discountPercentage)
    {
        return Coupon::where('CouponID', $id)->update(['DiscountPercentage' => $discountPercentage - 1]);
    }
}
