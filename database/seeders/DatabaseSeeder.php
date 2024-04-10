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
                
                'account_active' => true,
                'role' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make(env('ADM_PASSWORD')), 
            ],
        ];
        DB::table('users')->insert($users);

        $now = now();

        // Usuario 1
        DB::table('users')->insert([
            'name' => 'user1',
            'email' => 'user1@gmail.com',
            'email_verified' => true,
            
            'account_active' => true,
            'role' => 'guest',
            'email_verified_at' => $now,
            'password' => Hash::make('password123'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Usuario 2
        DB::table('users')->insert([
            'name' => 'user2',
            'email' => 'user2@gmail.com',
            'email_verified' => true,
            
            'account_active' => true,
            'role' => 'admin',
            'email_verified_at' => $now,
            'password' => Hash::make('securepass'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Usuario 3
        DB::table('users')->insert([
            'name' => 'user3',
            'email' => 'user3@gmail.com',
            'email_verified' => true,
            
            'account_active' => true,
            'role' => 'user',
            'email_verified_at' => $now,
            'password' => Hash::make('testpass'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Usuario 4
        DB::table('users')->insert([
            'name' => 'user4',
            'email' => 'user4@gmail.com',
            'email_verified' => true,
            
            'account_active' => true,
            'role' => 'user',
            'email_verified_at' => $now,
            'password' => Hash::make('userpass'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Usuario 5
        DB::table('users')->insert([
            'name' => 'user5',
            'email' => 'user5@gmail.com',
            'email_verified' => true,
            
            'account_active' => true,
            'role' => 'user',
            'email_verified_at' => $now,
            'password' => Hash::make('password123'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Usuario 6
        DB::table('users')->insert([
            'name' => 'user6',
            'email' => 'user6@gmail.com',
            'email_verified' => true,
            
            'account_active' => true,
            'role' => 'user',
            'email_verified_at' => $now,
            'password' => Hash::make('securepass'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Usuario 7
        DB::table('users')->insert([
            'name' => 'user7',
            'email' => 'user7@gmail.com',
            'email_verified' => true,
            
            'account_active' => true,
            'role' => 'user',
            'email_verified_at' => $now,
            'password' => Hash::make('testpass'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Usuario 8
        DB::table('users')->insert([
            'name' => 'user8',
            'email' => 'user8@gmail.com',
            'email_verified' => true,
            
            'account_active' => true,
            'role' => 'user',
            'email_verified_at' => $now,
            'password' => Hash::make('userpass'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Usuario 9
        DB::table('users')->insert([
            'name' => 'user9',
            'email' => 'user9@gmail.com',
            'email_verified' => true,
            
            'account_active' => true,
            'role' => 'user',
            'email_verified_at' => $now,
            'password' => Hash::make('password123'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Usuario 10
        DB::table('users')->insert([
            'name' => 'user10',
            'email' => 'user10@gmail.com',
            'email_verified' => true,
            
            'account_active' => true,
            'role' => 'user',
            'email_verified_at' => $now,
            'password' => Hash::make('securepass'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
