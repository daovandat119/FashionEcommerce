<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderReview::create([
            'OrderID' => 1,
            'UserID' => 1,
            'RatingLevelID' => 1,
            'Review' => 'Good',
        ]);
    }
}
