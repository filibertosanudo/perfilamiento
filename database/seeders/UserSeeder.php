<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Institution;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Institución compartida
        $institution = Institution::firstOrCreate(
            ['name' => 'Universidad Tecnológica'],
            [
                'type'    => 'universidad',
                'city'    => 'Ciudad de México',
                'address' => 'Av. Universidad 3000',
                'phone'   => '5550000000',
                'active'  => true,
            ]
        );

        $adminRoleId    = \DB::table('roles')->where('name', 'Administrator')->value('id');
        $advisorRoleId  = \DB::table('roles')->where('name', 'Advisor')->value('id');
        $userRoleId     = \DB::table('roles')->where('name', 'User')->value('id');

        User::insert([

            // Admin — sin institución (admin global)
            [
                'role_id'          => $adminRoleId,
                'institution_id'   => null,
                'first_name'       => 'Admin',
                'last_name'        => 'System',
                'second_last_name' => null,
                'email'            => 'admin@perfilamiento.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1990-01-01',
                'phone'            => '5550000001',
                'active'           => true,
                'registered_at'    => now(),
            ],

            // Orientador — pertenece a Universidad Tecnológica
            [
                'role_id'          => $advisorRoleId,
                'institution_id'   => $institution->id,
                'first_name'       => 'Advisor',
                'last_name'        => 'User',
                'second_last_name' => null,
                'email'            => 'advisor@perfilamiento.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1988-06-15',
                'phone'            => '5550000002',
                'active'           => true,
                'registered_at'    => now(),
            ],

            // Usuario regular — misma institución que el orientador
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $institution->id,
                'first_name'       => 'Normal',
                'last_name'        => 'User',
                'second_last_name' => null,
                'email'            => 'user@perfilamiento.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1995-03-03',
                'phone'            => '5550000003',
                'active'           => true,
                'registered_at'    => now(),
            ],

        ]);
    }
}