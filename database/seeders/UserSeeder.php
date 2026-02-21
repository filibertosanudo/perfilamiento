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
        $institutions = Institution::all()->keyBy('name');
        $uth = $institutions['Universidad Tecnológica de Hermosillo'];
        $prep = $institutions['Preparatoria Regional Norte'];
        $empresa = $institutions['Empresa Industrial del Norte SA'];

        $adminRoleId   = \DB::table('roles')->where('name', 'Administrator')->value('id');
        $advisorRoleId = \DB::table('roles')->where('name', 'Advisor')->value('id');
        $userRoleId    = \DB::table('roles')->where('name', 'User')->value('id');

        $users = [

            // ADMINISTRADOR GLOBAL
            [
                'role_id'          => $adminRoleId,
                'institution_id'   => null,
                'first_name'       => 'Admin',
                'last_name'        => 'System',
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

            // ORIENTADORES - Universidad Tecnológica
            [
                'role_id'          => $advisorRoleId,
                'institution_id'   => $uth->id,
                'first_name'       => 'Carlos',
                'last_name'        => 'Martínez',
                'second_last_name' => 'López',
                'email'            => 'carlos.martinez@uth.test',
                'password'         => Hash::make('Advisor123!@#'),
                'birth_date'       => '1982-06-20',
                'phone'            => '6621000002',
                'active'           => true,
                'registered_at'    => now()->subMonths(8),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'role_id'          => $advisorRoleId,
                'institution_id'   => $uth->id,
                'first_name'       => 'María',
                'last_name'        => 'Rodríguez',
                'second_last_name' => 'Sánchez',
                'email'            => 'maria.rodriguez@uth.test',
                'password'         => Hash::make('Advisor123!@#'),
                'birth_date'       => '1980-04-12',
                'phone'            => '6621000003',
                'active'           => true,
                'registered_at'    => now()->subMonths(7),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],

            // ORIENTADORES - Preparatoria
            [
                'role_id'          => $advisorRoleId,
                'institution_id'   => $prep->id,
                'first_name'       => 'Luis',
                'last_name'        => 'González',
                'second_last_name' => 'Pérez',
                'email'            => 'luis.gonzalez@prep.test',
                'password'         => Hash::make('Advisor123!@#'),
                'birth_date'       => '1978-09-05',
                'phone'            => '6629000001',
                'active'           => true,
                'registered_at'    => now()->subMonths(6),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],

            // ORIENTADORES - Empresa
            [
                'role_id'          => $advisorRoleId,
                'institution_id'   => $empresa->id,
                'first_name'       => 'Roberto',
                'last_name'        => 'Hernández',
                'second_last_name' => 'Gómez',
                'email'            => 'roberto.hernandez@empresa.test',
                'password'         => Hash::make('Advisor123!@#'),
                'birth_date'       => '1983-11-30',
                'phone'            => '8181000001',
                'active'           => true,
                'registered_at'    => now()->subMonths(5),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],

            // USUARIOS - Universidad Tecnológica
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $uth->id,
                'first_name'       => 'Ana',
                'last_name'        => 'García',
                'second_last_name' => 'Morales',
                'email'            => 'ana.garcia@uth.test',
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
                'institution_id'   => $uth->id,
                'first_name'       => 'José',
                'last_name'        => 'Fernández',
                'second_last_name' => 'Ruiz',
                'email'            => 'jose.fernandez@uth.test',
                'password'         => Hash::make('User123!@#'),
                'birth_date'       => '2001-07-22',
                'phone'            => '6621100002',
                'active'           => true,
                'registered_at'    => now()->subDays(55),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $uth->id,
                'first_name'       => 'Patricia',
                'last_name'        => 'Díaz',
                'second_last_name' => 'Castro',
                'email'            => 'patricia.diaz@uth.test',
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
                'institution_id'   => $uth->id,
                'first_name'       => 'Miguel',
                'last_name'        => 'Vega',
                'second_last_name' => 'Reyes',
                'email'            => 'miguel.vega@uth.test',
                'password'         => Hash::make('User123!@#'),
                'birth_date'       => '2002-11-05',
                'phone'            => '6621100004',
                'active'           => true,
                'registered_at'    => now()->subDays(45),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $uth->id,
                'first_name'       => 'Carmen',
                'last_name'        => 'Ortiz',
                'second_last_name' => 'Luna',
                'email'            => 'carmen.ortiz@uth.test',
                'password'         => Hash::make('User123!@#'),
                'birth_date'       => '2001-05-18',
                'phone'            => '6621100005',
                'active'           => false,
                'registered_at'    => now()->subDays(40),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],

            // USUARIOS - Preparatoria
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $prep->id,
                'first_name'       => 'Laura',
                'last_name'        => 'Méndez',
                'second_last_name' => 'Vargas',
                'email'            => 'laura.mendez@prep.test',
                'password'         => Hash::make('User123!@#'),
                'birth_date'       => '2006-02-28',
                'phone'            => '6629100001',
                'active'           => true,
                'registered_at'    => now()->subDays(35),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $prep->id,
                'first_name'       => 'Ricardo',
                'last_name'        => 'Silva',
                'second_last_name' => 'Torres',
                'email'            => 'ricardo.silva@prep.test',
                'password'         => Hash::make('User123!@#'),
                'birth_date'       => '2005-08-14',
                'phone'            => '6629100002',
                'active'           => true,
                'registered_at'    => now()->subDays(30),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $prep->id,
                'first_name'       => 'Daniela',
                'last_name'        => 'Paredes',
                'second_last_name' => 'Flores',
                'email'            => 'daniela.paredes@prep.test',
                'password'         => Hash::make('User123!@#'),
                'birth_date'       => '2006-06-12',
                'phone'            => '6629100003',
                'active'           => true,
                'registered_at'    => now()->subDays(25),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],

            // USUARIOS - Empresa
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $empresa->id,
                'first_name'       => 'Jorge',
                'last_name'        => 'Moreno',
                'second_last_name' => 'Cruz',
                'email'            => 'jorge.moreno@empresa.test',
                'password'         => Hash::make('User123!@#'),
                'birth_date'       => '1995-04-20',
                'phone'            => '8181100001',
                'active'           => true,
                'registered_at'    => now()->subDays(20),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'role_id'          => $userRoleId,
                'institution_id'   => $empresa->id,
                'first_name'       => 'Sandra',
                'last_name'        => 'Ruiz',
                'second_last_name' => 'Jiménez',
                'email'            => 'sandra.ruiz@empresa.test',
                'password'         => Hash::make('User123!@#'),
                'birth_date'       => '1992-12-08',
                'phone'            => '8181100002',
                'active'           => false,
                'registered_at'    => now()->subDays(15),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ];

        User::insert($users);
    }
}