<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class RatingLevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('rating_levels')->insert([
            ['LevelValue' => 1],
            ['LevelValue' => 2],
            ['LevelValue' => 3],
            ['LevelValue' => 4],
            ['LevelValue' => 5],
        ]);
    }
}
