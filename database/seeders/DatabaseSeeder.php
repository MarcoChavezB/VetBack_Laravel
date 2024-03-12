<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => env('ADM_NAME'),
                'email' => env('ADM_EMAIL'),
                'email_verified' => true,
                'code_verified' => true,
                'account_active' => true,
                'role' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make(env('ADM_PASSWORD')), 
            ],
        ];

        DB::table('users')->insert($users);
    }
}
