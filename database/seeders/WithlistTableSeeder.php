<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class WithlistTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('wishlist')->insert([
            ['UserID' => 1, 'ProductID' => 1],
            ['UserID' => 2, 'ProductID' => 2],
        ]);
    }
}
