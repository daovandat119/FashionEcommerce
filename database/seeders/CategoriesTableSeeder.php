<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('categories')->insert([
            ['CategoryName' => 'Điện tử', 'Status' => 'ACTIVE'],
            ['CategoryName' => 'Quần áo', 'Status' => 'ACTIVE'],
            ['CategoryName' => 'Sách', 'Status' => 'ACTIVE'],
        ]);
    }
}
