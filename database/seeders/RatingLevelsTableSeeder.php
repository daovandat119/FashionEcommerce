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
            ['LevelName' => '1 Star'],
            ['LevelName' => '2 Stars'],
            ['LevelName' => '3 Stars'],
            ['LevelName' => '4 Stars'],
            ['LevelName' => '5 Stars'],
        ]);
    }
}
