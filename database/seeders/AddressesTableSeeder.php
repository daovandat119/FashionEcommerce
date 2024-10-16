<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('addresses')->insert([
            ['UserID' => 1,'Username'=>'dat', 'Address' => '123 Main St, City A', 'PhoneNumber' => '1234567890', 'IsDefault' => 1],
            ['UserID' => 2,'Username'=>'yến' ,'Address' => '456 Main St, City B', 'PhoneNumber' => '0987654321', 'IsDefault' => 0],
        ]);
    }

}
