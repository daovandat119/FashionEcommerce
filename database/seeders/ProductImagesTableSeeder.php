<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ProductImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('product_images')->insert([
            ['ProductID' => 1, 'ImagePath' => 'images/smartphone_black.jpg'],
            ['ProductID' => 2, 'ImagePath' => 'images/tshirt_blue.jpg'],
        ]);
    }

}
