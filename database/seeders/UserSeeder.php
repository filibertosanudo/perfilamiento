<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRoleId = DB::table('roles')->where('name', 'Administrator')->value('id');
        $advisorRoleId = DB::table('roles')->where('name', 'Advisor')->value('id');
        $userRoleId = DB::table('roles')->where('name', 'Normal User')->value('id');

        DB::table('users')->insert([
            [
                'role_id' => $adminRoleId,
                'first_name' => 'Admin',
                'last_name' => 'System',
                'second_last_name' => null,
                'email' => 'admin@perfilamiento.test',
                'password' => Hash::make('password'),
                'birth_date' => '1990-01-01',
                'phone' => '5550000001',
                'active' => true,
                'registered_at' => now(),
            ],
            [
                'role_id' => $advisorRoleId,
                'first_name' => 'Advisor',
                'last_name' => 'User',
                'second_last_name' => null,
                'email' => 'advisor@perfilamiento.test',
                'password' => Hash::make('password'),
                'birth_date' => '1992-02-02',
                'phone' => '5550000002',
                'active' => true,
                'registered_at' => now(),
            ],
            [
                'role_id' => $userRoleId,
                'first_name' => 'Normal',
                'last_name' => 'User',
                'second_last_name' => null,
                'email' => 'user@perfilamiento.test',
                'password' => Hash::make('password'),
                'birth_date' => '1995-03-03',
                'phone' => '5550000003',
                'active' => true,
                'registered_at' => now(),
            ],
        ]);
    }
}
