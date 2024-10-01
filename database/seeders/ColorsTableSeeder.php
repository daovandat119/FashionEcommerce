<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ColorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('colors')->insert([
            ['ColorName' => 'Red'],
            ['ColorName' => 'Blue'],
            ['ColorName' => 'Green'],
        ]);
    }
}
