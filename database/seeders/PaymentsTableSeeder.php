<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PaymentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('payments')->insert([
            ['OrderID' => 1, 'PaymentMethodID' => 1, 'PaymentStatusID' => 1, 'Amount' => 100000, 'TransactionID' => null, 'BankCode' => null, 'CardType' => null, 'OrderInfo' => null, 'ResponseCode' => null],
        ]);
    }
}
