<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class OrderItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('order_items')->insert([
            ['OrderID' => 1, 'ProductID' => 1, 'VariantID' => 1, 'Quantity' => 1],
            ['OrderID' => 1, 'ProductID' => 2, 'VariantID' => 2, 'Quantity' => 2],
        ]);
    }
}
