<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SizesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('sizes')->insert([
            ['SizeName' => 'Small'],
            ['SizeName' => 'Medium'],
            ['SizeName' => 'Large'],
        ]);
    }
}
