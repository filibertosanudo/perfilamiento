<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Area;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $areas = Area::all()->keyBy('name');
        $ingenieria = $areas['Ingeniería'];
        $salud = $areas['Ciencias de la Salud'];
        $administracion = $areas['Administración'];

        $adminRoleId   = \DB::table('roles')->where('name', 'Administrator')->value('id');
        $advisorRoleId = \DB::table('roles')->where('name', 'Advisor')->value('id');
        $userRoleId    = \DB::table('roles')->where('name', 'User')->value('id');

        $users = [

            // ADMINISTRADOR GLOBAL (Psicología / Dirección)
            [
                'role_id'          => $adminRoleId,
                'area_id'          => null,
                'first_name'       => 'Admin',
                'last_name'        => 'Psicología',
                'second_last_name' => null,
                'email'            => 'admin@siesi.test',
                'password'         => Hash::make('Admin123!@#'),
                'birth_date'       => '1985-01-15',
                'phone'            => '6621000001',
                'active'           => true,
                'registered_at'    => now()->subYear(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],

            // ORIENTADORES - Ingeniería
            [
                'role_id'          => $advisorRoleId,
                'area_id'          => $ingenieria->id,
                'first_name'       => 'Carlos',
                'last_name'        => 'Martínez',
                'second_last_name' => 'López',
                'email'            => 'carlos.martinez@siesi.test',
                'password'         => Hash::make('Advisor123!@#'),
                'birth_date'       => '1982-06-20',
                'phone'            => '6621000002',
                'active'           => true,
                'registered_at'    => now()->subMonths(8),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],

            // ORIENTADORES - Ciencias de la Salud
            [
                'role_id'          => $advisorRoleId,
                'area_id'          => $salud->id,
                'first_name'       => 'María',
                'last_name'        => 'López',
                'second_last_name' => 'Sánchez',
                'email'            => 'maria.lopez@siesi.test',
                'password'         => Hash::make('Advisor123!@#'),
                'birth_date'       => '1980-04-12',
                'phone'            => '6621000003',
                'active'           => true,
                'registered_at'    => now()->subMonths(7),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],

            // ORIENTADORES - Administración
            [
                'role_id'          => $advisorRoleId,
                'area_id'          => $administracion->id,
                'first_name'       => 'Juan',
                'last_name'        => 'Pérez',
                'second_last_name' => 'Gómez',
                'email'            => 'juan.perez@siesi.test',
                'password'         => Hash::make('Advisor123!@#'),
                'birth_date'       => '1978-09-05',
                'phone'            => '6629000001',
                'active'           => true,
                'registered_at'    => now()->subMonths(6),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],

            // USUARIOS - Ingeniería
            [
                'role_id'          => $userRoleId,
                'area_id'          => $ingenieria->id,
                'first_name'       => 'Ana',
                'last_name'        => 'García',
                'second_last_name' => 'Morales',
                'email'            => 'ana.garcia@siesi.test',
                'password'         => Hash::make('User123!@#'),
                'birth_date'       => '2002-03-15',
                'phone'            => '6621100001',
                'active'           => true,
                'registered_at'    => now()->subDays(60),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'role_id'          => $userRoleId,
                'area_id'          => $ingenieria->id,
                'first_name'       => 'José',
                'last_name'        => 'Fernández',
                'second_last_name' => 'Ruiz',
                'email'            => 'jose.fernandez@siesi.test',
                'password'         => Hash::make('User123!@#'),
                'birth_date'       => '2001-07-22',
                'phone'            => '6621100002',
                'active'           => true,
                'registered_at'    => now()->subDays(55),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],

            // USUARIOS - Ciencias de la Salud
            [
                'role_id'          => $userRoleId,
                'area_id'          => $salud->id,
                'first_name'       => 'Patricia',
                'last_name'        => 'Díaz',
                'second_last_name' => 'Castro',
                'email'            => 'patricia.diaz@siesi.test',
                'password'         => Hash::make('User123!@#'),
                'birth_date'       => '2003-01-10',
                'phone'            => '6621100003',
                'active'           => true,
                'registered_at'    => now()->subDays(50),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'role_id'          => $userRoleId,
                'area_id'          => $salud->id,
                'first_name'       => 'Alejandro',
                'last_name'        => 'Mendoza',
                'second_last_name' => 'Torres',
                'email'            => 'alejandro.mendoza@siesi.test',
                'password'         => Hash::make('User123!@#'),
                'birth_date'       => '2000-11-05',
                'phone'            => '6621100004',
                'active'           => true,
                'registered_at'    => now()->subDays(45),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],

            // USUARIOS - Administración
            [
                'role_id'          => $userRoleId,
                'area_id'          => $administracion->id,
                'first_name'       => 'Laura',
                'last_name'        => 'Ramírez',
                'second_last_name' => 'Suárez',
                'email'            => 'laura.ramirez@siesi.test',
                'password'         => Hash::make('User123!@#'),
                'birth_date'       => '2002-09-18',
                'phone'            => '6621100005',
                'active'           => true,
                'registered_at'    => now()->subDays(40),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'role_id'          => $userRoleId,
                'area_id'          => $administracion->id,
                'first_name'       => 'Andrés',
                'last_name'        => 'Ortiz',
                'second_last_name' => 'Ríos',
                'email'            => 'andres.ortiz@siesi.test',
                'password'         => Hash::make('User123!@#'),
                'birth_date'       => '2001-02-28',
                'phone'            => '6621100006',
                'active'           => true,
                'registered_at'    => now()->subDays(35),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ];

        User::insert($users);
    }
}