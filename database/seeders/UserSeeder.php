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
        // Instituciones
        $utec = Institution::firstOrCreate(
            ['name' => 'Universidad Tecnológica'],
            [
                'type'    => 'universidad',
                'city'    => 'Ciudad de México',
                'address' => 'Av. Universidad 3000',
                'phone'   => '5550000000',
                'active'  => true,
            ]
        );

        $empresaX = Institution::firstOrCreate(
            ['name' => 'Empresa Industrial SA'],
            [
                'type'    => 'empresa',
                'city'    => 'Monterrey',
                'address' => 'Calle Industria 500',
                'phone'   => '8181234567',
                'active'  => true,
            ]
        );

        $adminRoleId   = \DB::table('roles')->where('name', 'Administrator')->value('id');
        $advisorRoleId = \DB::table('roles')->where('name', 'Advisor')->value('id');
        $userRoleId    = \DB::table('roles')->where('name', 'User')->value('id');

        $users = [

            // USUARIOS PRINCIPALES

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
                'registered_at'    => now()->subMonths(6),
            ],
            [
                'role_id'          => $advisorRoleId,
                'institution_id'   => $utec->id,
                'first_name'       => 'Advisor',
                'last_name'        => 'User',
                'second_last_name' => null,
                'email'            => 'advisor@perfilamiento.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1988-06-15',
                'phone'            => '5550000002',
                'active'           => true,
                'registered_at'    => now()->subMonths(5),
            ],
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $utec->id,
                'first_name'       => 'Normal',
                'last_name'        => 'User',
                'second_last_name' => null,
                'email'            => 'user@perfilamiento.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1995-03-03',
                'phone'            => '5550000003',
                'active'           => true,
                'registered_at'    => now()->subMonths(3),
            ],

            // ORIENTADORES EXTRA

            [
                'role_id'          => $advisorRoleId,
                'institution_id'   => $utec->id,
                'first_name'       => 'María',
                'last_name'        => 'Rodríguez',
                'second_last_name' => 'Sánchez',
                'email'            => 'maria.rodriguez@utec.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1987-04-20',
                'phone'            => '5550000004',
                'active'           => true,
                'registered_at'    => now()->subMonths(4),
            ],
            [
                'role_id'          => $advisorRoleId,
                'institution_id'   => $empresaX->id,
                'first_name'       => 'Roberto',
                'last_name'        => 'Hernández',
                'second_last_name' => 'Gómez',
                'email'            => 'roberto.hernandez@empresa.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1985-08-10',
                'phone'            => '8181111111',
                'active'           => true,
                'registered_at'    => now()->subMonths(2),
            ],

            // USUARIOS REGULARES

            [
                'role_id'          => $userRoleId,
                'institution_id'   => $utec->id,
                'first_name'       => 'Luis',
                'last_name'        => 'Fernández',
                'second_last_name' => 'Morales',
                'email'            => 'luis.fernandez@utec.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1998-01-15',
                'phone'            => '5550000005',
                'active'           => true,
                'registered_at'    => now()->subDays(45),
            ],
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $utec->id,
                'first_name'       => 'Patricia',
                'last_name'        => 'González',
                'second_last_name' => 'Ramírez',
                'email'            => 'patricia.gonzalez@utec.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1997-05-22',
                'phone'            => '5550000006',
                'active'           => true,
                'registered_at'    => now()->subDays(40),
            ],
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $empresaX->id,
                'first_name'       => 'Jorge',
                'last_name'        => 'Silva',
                'second_last_name' => 'Torres',
                'email'            => 'jorge.silva@empresa.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1992-09-08',
                'phone'            => '8181222222',
                'active'           => true,
                'registered_at'    => now()->subDays(35),
            ],
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $empresaX->id,
                'first_name'       => 'Sandra',
                'last_name'        => 'Díaz',
                'second_last_name' => 'Castro',
                'email'            => 'sandra.diaz@empresa.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1996-11-30',
                'phone'            => '8181333333',
                'active'           => true,
                'registered_at'    => now()->subDays(30),
            ],
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $utec->id,
                'first_name'       => 'Miguel',
                'last_name'        => 'Ruiz',
                'second_last_name' => 'Jiménez',
                'email'            => 'miguel.ruiz@utec.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1999-02-14',
                'phone'            => '5550000007',
                'active'           => true,
                'registered_at'    => now()->subDays(25),
            ],
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $utec->id,
                'first_name'       => 'Laura',
                'last_name'        => 'Méndez',
                'second_last_name' => 'Vargas',
                'email'            => 'laura.mendez@utec.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1998-07-19',
                'phone'            => '5550000008',
                'active'           => false,
                'registered_at'    => now()->subDays(20),
            ],
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $empresaX->id,
                'first_name'       => 'Ricardo',
                'last_name'        => 'Ortiz',
                'second_last_name' => 'Luna',
                'email'            => 'ricardo.ortiz@empresa.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1994-12-05',
                'phone'            => '8181444444',
                'active'           => true,
                'registered_at'    => now()->subDays(15),
            ],
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $utec->id,
                'first_name'       => 'Carmen',
                'last_name'        => 'Vega',
                'second_last_name' => 'Reyes',
                'email'            => 'carmen.vega@utec.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1997-03-28',
                'phone'            => '5550000009',
                'active'           => true,
                'registered_at'    => now()->subDays(10),
            ],
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $empresaX->id,
                'first_name'       => 'Fernando',
                'last_name'        => 'Moreno',
                'second_last_name' => 'Cruz',
                'email'            => 'fernando.moreno@empresa.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1993-10-17',
                'phone'            => '8181555555',
                'active'           => false,
                'registered_at'    => now()->subDays(8),
            ],
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $utec->id,
                'first_name'       => 'Daniela',
                'last_name'        => 'Paredes',
                'second_last_name' => 'Flores',
                'email'            => 'daniela.paredes@utec.test',
                'password'         => Hash::make('password'),
                'birth_date'       => '1999-06-12',
                'phone'            => '5550000010',
                'active'           => true,
                'registered_at'    => now()->subDays(5),
            ],
        ];

        User::insert($users);
    }
}