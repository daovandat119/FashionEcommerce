<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class OrderStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('order_statuses')->insert([
            ['StatusName' => 'Đang xử lý'],
            ['StatusName' => 'Đang giao hàng'],
            ['StatusName' => 'Đã giao hàng'],
            ['StatusName' => 'Đã hủy'],
            ['StatusName' => 'Đã hoàn thành'],
        ]);
    }
}
