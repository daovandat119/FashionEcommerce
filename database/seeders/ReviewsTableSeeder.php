<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('reviews')->insert([
            [
                'UserID' => 1,
                'ProductID' => 1,
                'RatingLevelID' => 1,
                'ReviewContent' => 'Great smartphone!'
            ],
            [
                'UserID' => 2,
                'ProductID' => 2,
                'RatingLevelID' => 2,
                'ReviewContent' => 'Nice t-shirt!'
            ],
        ]);
    }

}
