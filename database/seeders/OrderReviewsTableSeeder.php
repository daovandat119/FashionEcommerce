<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('order_reviews')->insert([
            [
                'OrderID' => 1,
                'UserID' => 1,
                'RatingLevelID' => 1,
                'Review' => 'Good',
            ],
        ]);
    }
}
