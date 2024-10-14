<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = [
            [
                'RoleID' => 1,
                'Username' => 'admin',
                'Email' => 'admin@example.com',
                'Password' => Hash::make('password'),
                'IsActive' => true,
                'CodeId' => null,
                'CodeExpired' => null,
            ],
            [
                'RoleID' => 2,
                'Username' => 'user1',
                'Email' => 'user1@example.com',
                'Password' => Hash::make('password'),
                'IsActive' => true,
                'CodeId' => null,
                'CodeExpired' => null
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['Email' => $user['Email']],
                $user
            );
        }
    }
}