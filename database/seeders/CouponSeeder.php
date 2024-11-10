<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('coupons')->insert([
            ['Name' => 'Coupon 1', 'Code' => 'COUPON1', 'DiscountPercentage' => 10, 'MinimumOrderValue' => 100, 'UsageLimit' => 10, 'UsedCount' => 0, 'ExpiresAt' => now()->addDays(30)],
        ]);
    }
}
