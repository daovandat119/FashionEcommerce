<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CartItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('cart_items')->insert([
            ['CartID' => 1, 'ProductID' => 1, 'VariantID' => 1, 'Quantity' => 1],
            ['CartID' => 1, 'ProductID' => 2, 'VariantID' => 2, 'Quantity' => 2],
        ]);
    }

}
