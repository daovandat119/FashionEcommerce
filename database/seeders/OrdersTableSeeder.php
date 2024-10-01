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
                'TotalAmount' => 719.98,
                'OrderStatusID' => 1
            ],
            [
                'UserID' => 2,
                'AddressID' => 1,
                'CartID' => 1,
                'TotalAmount' => 29.98,
                'OrderStatusID' => 2
            ],
        ]);
    }

}
