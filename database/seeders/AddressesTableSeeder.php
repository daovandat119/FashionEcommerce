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
            ['UserID' => 1,'Username'=>'dat', 'Address' => '123 Main St, City A', 'ProvinceID' => '250', 'DistrictID' => '3440', 'WardCode' => '1', 'PhoneNumber' => '1234567890', 'IsDefault' => 1, 'Status' => 'ACTIVE'],
            ['UserID' => 2,'Username'=>'yến' ,'Address' => '456 Main St, City B', 'ProvinceID' => '250', 'DistrictID' => '3281', 'WardCode' => '260735', 'PhoneNumber' => '0987654321', 'IsDefault' => 1, 'Status' => 'ACTIVE'],
        ]);
    }

}
