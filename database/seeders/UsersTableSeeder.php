<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'RoleID' => 1,
                'Username' => 'admin',
                'Email' => 'admin@example.com',
                'Password' => bcrypt('password'),
                'IsActive' => true,
                'CodeId' => null,
                'CodeExpired' => null,
            ],
            [
                'RoleID' => 2,
                'Username' => 'user1',
                'Email' => 'user1@example.com',
                'Password' => bcrypt('password'),
                'IsActive' => true,
                'CodeId' => null,
                'CodeExpired' => null
            ],
        ]);
    }

}
