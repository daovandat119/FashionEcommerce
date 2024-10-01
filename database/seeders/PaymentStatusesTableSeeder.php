<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PaymentStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('payment_statuses')->insert([
            ['StatusName' => 'Paid'],
            ['StatusName' => 'Failed'],
            ['StatusName' => 'Refunded'],
        ]);
    }
}
