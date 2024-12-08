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
        'MaxAmount',
        'ExpiresAt',
        'created_at',
        'updated_at',
    ];

    public function listAllCoupons($role, $MinimumOrderValue, $Code = null, $offset = null, $limit = null)
    {
        if ($role->RoleName != 'Admin') {
            $coupons = Coupon::query();

            $coupons->where('ExpiresAt', '>', Carbon::now());

            $coupons->where(function($query) use ($MinimumOrderValue) {
                $query->where('MinimumOrderValue', '<=', $MinimumOrderValue)
                  ->orWhere('MinimumOrderValue', '>', $MinimumOrderValue);
            });

            if ($offset !== null && $limit !== null) {
                $coupons->offset($offset)->limit($limit);
            }

            return $coupons->get()->map(function($coupon) use ($MinimumOrderValue) {
                if ($coupon->MinimumOrderValue > $MinimumOrderValue) {
                    $coupon->usable = false;
                } else {
                    $coupon->usable = true;
                }
                return $coupon;
            });
        }else {
            $coupons = Coupon::query();

            if($Code){
                $coupons->where('Code', 'like', '%' . $Code . '%');
            }

            if ($offset !== null && $limit !== null) {
                $coupons->offset($offset)->limit($limit);
            }

            return $coupons->get();
        }
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

    public function updateDiscountPercentage($id, $UsageLimit)
    {
        return Coupon::where('CouponID', $id)->update(['UsageLimit' => $UsageLimit - 1]);
    }

    public function calculateTotalCoupons($role, $Code = null)
    {
        if ($role->RoleName != 'Admin') {
            return Coupon::where('ExpiresAt', '>', Carbon::now())->count();
        } else {
            $coupons = Coupon::query();

            if($Code){
                $coupons->where('Code', 'like', '%' . $Code . '%');
            }

            return $coupons->count();
        }
    }
}
