<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('orders')->insert([
            [
                'UserID' => 1,
                'AddressID' => 1,
                'CartID' => 1,
                'OrderStatusID' => 1,
                'OrderCode' => '1234567890',
                'CancellationReason' => 'Khách hàng hủy đơn'
            ],
            [
                'UserID' => 2,
                'AddressID' => 1,
                'CartID' => 1,
                'OrderStatusID' => 2,
                'OrderCode' => '1234567890',
                'CancellationReason' => null
            ],
        ]);
    }

}
